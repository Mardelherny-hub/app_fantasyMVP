<?php

namespace App\Console\Commands;

use App\Jobs\Manager\AutoAssignSquadJob;
use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessExpiredSquadDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squad:process-expired-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired squad building deadlines and auto-assign squads';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to process expired squad deadlines...');

        // Buscar league members con deadline vencido
        $expiredMembers = LeagueMember::query()
            ->whereNotNull('squad_deadline_at')
            ->where('squad_deadline_at', '<=', now())
            ->with(['league', 'user'])
            ->get();

        if ($expiredMembers->isEmpty()) {
            $this->info('No expired deadlines found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredMembers->count()} expired deadlines.");

        $processed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($expiredMembers as $member) {
            try {
                // Buscar el FantasyTeam asociado a este miembro
                $fantasyTeam = FantasyTeam::where('league_id', $member->league_id)
                    ->where('user_id', $member->user_id)
                    ->first();

                if (!$fantasyTeam) {
                    $this->warn("Fantasy team not found for member ID {$member->id}");
                    $skipped++;
                    
                    // Limpiar el deadline para evitar procesarlo nuevamente
                    $member->update(['squad_deadline_at' => null]);
                    continue;
                }

                // Verificar si ya completó el squad
                if ($fantasyTeam->is_squad_complete) {
                    $this->line("Team '{$fantasyTeam->name}' already has complete squad. Skipping.");
                    
                    // Limpiar el deadline
                    $member->update(['squad_deadline_at' => null]);
                    $skipped++;
                    continue;
                }

                // Despachar job de auto-asignación
                $this->line("Dispatching auto-assignment job for team: {$fantasyTeam->name}");
                
                AutoAssignSquadJob::dispatch($fantasyTeam);
                
                // Limpiar el deadline después de despachar el job
                $member->update(['squad_deadline_at' => null]);
                
                $processed++;

                Log::info('Squad deadline processed and job dispatched', [
                    'league_member_id' => $member->id,
                    'fantasy_team_id' => $fantasyTeam->id,
                    'team_name' => $fantasyTeam->name,
                    'deadline' => $member->squad_deadline_at,
                ]);

            } catch (\Exception $e) {
                $this->error("Failed to process member ID {$member->id}: {$e->getMessage()}");
                
                Log::error('Failed to process expired squad deadline', [
                    'league_member_id' => $member->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                $failed++;
            }
        }

        // Resumen
        $this->newLine();
        $this->info('Processing complete:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Processed', $processed],
                ['Skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        Log::info('Expired squad deadlines processing completed', [
            'processed' => $processed,
            'skipped' => $skipped,
            'failed' => $failed,
        ]);

        return Command::SUCCESS;
    }
}