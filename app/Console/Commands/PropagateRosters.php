<?php

namespace App\Console\Commands;

use App\Models\FantasyRoster;
use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Models\League;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PropagateRosters extends Command
{
    protected $signature = 'fantasy:propagate-rosters {gameweek_id : ID del gameweek DESTINO}';

    protected $description = 'Propagar rosters del gameweek anterior al gameweek destino para todos los equipos';

    public function handle()
    {
        $targetGw = Gameweek::find($this->argument('gameweek_id'));

        if (!$targetGw) {
            $this->error('Gameweek no encontrado.');
            return 1;
        }

        // Buscar GW anterior de la misma season
        $previousGw = Gameweek::where('season_id', $targetGw->season_id)
            ->where('number', '<', $targetGw->number)
            ->orderBy('number', 'desc')
            ->first();

        if (!$previousGw) {
            $this->error("No hay gameweek anterior al GW{$targetGw->number}.");
            return 1;
        }

        $this->info("Propagando rosters de GW{$previousGw->number} → GW{$targetGw->number}");

        // Obtener ligas activas de esta temporada
        $leagues = League::where('season_id', $targetGw->season_id)
            ->where('status', League::STATUS_APPROVED)
            ->get();

        if ($leagues->isEmpty()) {
            $this->error('No hay ligas activas para esta temporada.');
            return 1;
        }

        $propagated = 0;
        $skipped = 0;
        $cleaned = 0;

        DB::transaction(function () use ($targetGw, $previousGw, $leagues, &$propagated, &$skipped, &$cleaned) {
            foreach ($leagues as $league) {
                $teams = FantasyTeam::where('league_id', $league->id)->get();

                foreach ($teams as $team) {
                    // Contar rosters en destino
                    $existingCount = FantasyRoster::where('fantasy_team_id', $team->id)
                        ->where('gameweek_id', $targetGw->id)
                        ->count();

                    // Si ya tiene 23, skip
                    if ($existingCount === 23) {
                        $this->line("  {$team->name}: ya tiene 23 jugadores en GW{$targetGw->number} → skip");
                        $skipped++;
                        continue;
                    }

                    // Si tiene parcial, limpiar
                    if ($existingCount > 0) {
                        FantasyRoster::where('fantasy_team_id', $team->id)
                            ->where('gameweek_id', $targetGw->id)
                            ->delete();
                        $this->line("  {$team->name}: limpiados {$existingCount} registros parciales");
                        $cleaned++;
                    }

                    // Obtener roster del GW anterior
                    $previousRosters = FantasyRoster::where('fantasy_team_id', $team->id)
                        ->where('gameweek_id', $previousGw->id)
                        ->get();

                    if ($previousRosters->isEmpty()) {
                        $this->warn("  {$team->name}: sin roster en GW{$previousGw->number} → no se puede propagar");
                        continue;
                    }

                    // Copiar cada registro al nuevo GW
                    foreach ($previousRosters as $roster) {
                        FantasyRoster::create([
                            'fantasy_team_id' => $roster->fantasy_team_id,
                            'player_id' => $roster->player_id,
                            'gameweek_id' => $targetGw->id,
                            'slot' => $roster->slot,
                            'is_starter' => $roster->is_starter,
                            'captaincy' => $roster->captaincy,
                        ]);
                    }

                    $this->info("  {$team->name}: propagados {$previousRosters->count()} jugadores");
                    $propagated++;
                }
            }
        });

        $this->newLine();
        $this->info("Resultado: {$propagated} equipos propagados, {$skipped} ya completos, {$cleaned} limpiados");
        return 0;
    }
}