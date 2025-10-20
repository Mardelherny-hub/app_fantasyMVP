<?php

namespace App\Console\Commands;

use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use App\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateExistingLeagueData extends Command
{
    protected $signature = 'squad:migrate-existing-data {--dry-run : Ver cambios sin aplicarlos}';
    protected $description = 'Migrar datos existentes de ligas al nuevo sistema de squad builder';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se aplicarán cambios');
        } else {
            $this->warn('⚠️  Este comando modificará datos existentes');
            if (!$this->confirm('¿Estás seguro de continuar?')) {
                $this->info('Operación cancelada');
                return Command::SUCCESS;
            }
        }

        DB::beginTransaction();
        
        try {
            $this->info('Iniciando migración de datos...');
            $this->newLine();

            // PASO 1: Marcar equipos existentes como completos
            $this->info('📋 PASO 1: Marcando equipos existentes como completos');
            $teamsToUpdate = FantasyTeam::whereNull('is_squad_complete')
                ->orWhere('is_squad_complete', false)
                ->count();
            
            if ($teamsToUpdate > 0) {
                $this->line("   Equipos a actualizar: {$teamsToUpdate}");
                
                if (!$dryRun) {
                    FantasyTeam::whereNull('is_squad_complete')
                        ->orWhere('is_squad_complete', false)
                        ->update(['is_squad_complete' => true]);
                    $this->info("   ✅ {$teamsToUpdate} equipos marcados como completos");
                }
            } else {
                $this->line("   ℹ️  No hay equipos para actualizar");
            }

            // PASO 2: Limpiar deadlines antiguos (ya no son necesarios)
            $this->newLine();
            $this->info('📋 PASO 2: Limpiando deadlines de equipos completos');
            $membersWithDeadline = LeagueMember::whereNotNull('squad_deadline_at')->count();
            
            if ($membersWithDeadline > 0) {
                $this->line("   Members con deadline: {$membersWithDeadline}");
                
                if (!$dryRun) {
                    LeagueMember::whereNotNull('squad_deadline_at')->update([
                        'squad_deadline_at' => null
                    ]);
                    $this->info("   ✅ Deadlines limpiados");
                }
            } else {
                $this->line("   ℹ️  No hay deadlines para limpiar");
            }

            // PASO 3: Verificar consistencia LeagueMember <-> FantasyTeam
            $this->newLine();
            $this->info('📋 PASO 3: Verificando consistencia de datos');
            
            // Equipos sin LeagueMember
            $teamsWithoutMember = FantasyTeam::whereNotNull('user_id')
                ->whereDoesntHave('league.members', function($query) {
                    $query->whereColumn('league_members.user_id', 'fantasy_teams.user_id');
                })
                ->get();
            
            if ($teamsWithoutMember->isNotEmpty()) {
                $this->warn("   ⚠️  Encontrados {$teamsWithoutMember->count()} equipos sin LeagueMember");
                
                foreach ($teamsWithoutMember as $team) {
                    $this->line("      - Team ID: {$team->id} | User: {$team->user_id} | League: {$team->league_id}");
                    
                    if (!$dryRun) {
                        LeagueMember::firstOrCreate([
                            'league_id' => $team->league_id,
                            'user_id' => $team->user_id,
                        ], [
                            'role' => LeagueMember::ROLE_PARTICIPANT,
                            'is_active' => true,
                            'joined_at' => $team->created_at ?? now(),
                        ]);
                        $this->info("      ✅ LeagueMember creado");
                    }
                }
            } else {
                $this->line("   ✅ Todos los equipos tienen LeagueMember");
            }

            // PASO 4: Resumen final
            $this->newLine();
            $this->info('📊 RESUMEN:');
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Total Ligas', League::count()],
                    ['Total FantasyTeams', FantasyTeam::count()],
                    ['Equipos Completos', FantasyTeam::where('is_squad_complete', true)->count()],
                    ['Total LeagueMembers', LeagueMember::count()],
                    ['Members con Deadline Activo', LeagueMember::whereNotNull('squad_deadline_at')->count()],
                ]
            );

            if ($dryRun) {
                DB::rollBack();
                $this->newLine();
                $this->warn('🔍 DRY-RUN COMPLETADO - No se aplicaron cambios');
                $this->info('Ejecuta sin --dry-run para aplicar los cambios');
            } else {
                DB::commit();
                $this->newLine();
                $this->info('✅ Migración completada exitosamente');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error durante la migración: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}