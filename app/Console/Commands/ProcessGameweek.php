<?php

namespace App\Console\Commands;

use App\Models\FantasyRoster;
use App\Models\FantasyRosterScore;
use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Models\League;
use App\Models\PlayerMatchStats;
use App\Models\ScoringRule;
use App\Models\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessGameweek extends Command
{
    protected $signature = 'fantasy:process-gameweek {gameweek_id} {--force : Recalcular aunque ya esté procesado}';

    protected $description = 'Procesar puntuación de un gameweek para todas las ligas activas';

    public function handle()
    {
        $gameweek = Gameweek::find($this->argument('gameweek_id'));

        if (!$gameweek) {
            $this->error('Gameweek no encontrado.');
            return 1;
        }

        $force = $this->option('force');
        $season = $gameweek->season;

        $this->info("Procesando GW{$gameweek->number} (id={$gameweek->id})");
        $this->info("Rango: {$gameweek->starts_at} → {$gameweek->ends_at}");

        // 1. Obtener real_matches de este gameweek por fechas
        $realMatches = $gameweek->real_matches;

        if ($realMatches->isEmpty()) {
            $this->error('No hay partidos reales en el rango de fechas de este gameweek.');
            return 1;
        }

        $realMatchIds = $realMatches->pluck('id')->toArray();
        $this->info("Partidos reales encontrados: " . count($realMatchIds) . " (ids: " . implode(',', $realMatchIds) . ")");

        // 2. Obtener scoring rules de la temporada
        $scoringRules = ScoringRule::where('season_id', $season->id)->get();

        if ($scoringRules->isEmpty()) {
            $this->error('No hay scoring rules para la temporada.');
            return 1;
        }

        // 3. Obtener ligas activas de esta temporada
        $leagues = League::where('season_id', $season->id)
            ->where('status', League::STATUS_APPROVED)
            ->get();

        if ($leagues->isEmpty()) {
            $this->error('No hay ligas activas para esta temporada.');
            return 1;
        }

        $this->info("Ligas activas: {$leagues->count()}");

        // 4. Verificar si ya fue procesado
        $existingScores = FantasyRosterScore::where('gameweek_id', $gameweek->id)->count();
        if ($existingScores > 0 && !$force) {
            $this->warn("Este gameweek ya tiene {$existingScores} scores. Usa --force para recalcular.");
            return 1;
        }

        // 5. Procesar
        DB::transaction(function () use ($gameweek, $leagues, $realMatchIds, $scoringRules, $force) {

            if ($force) {
                FantasyRosterScore::where('gameweek_id', $gameweek->id)->delete();
                $this->info("Scores previos eliminados.");
            }

            $teamsProcessed = 0;
            $scoresCreated = 0;

            foreach ($leagues as $league) {
                $fantasyTeams = FantasyTeam::where('league_id', $league->id)->get();

                foreach ($fantasyTeams as $team) {
                    $rosters = FantasyRoster::where('fantasy_team_id', $team->id)
                        ->where('gameweek_id', $gameweek->id)
                        ->where('is_starter', true)
                        ->with('player')
                        ->get();

                    if ($rosters->isEmpty()) {
                        continue;
                    }

                    $teamTotalPoints = 0;

                    foreach ($rosters as $roster) {
                        // Buscar stats del jugador en los matches de este GW
                        $stats = PlayerMatchStats::with('player')
                            ->where('player_id', $roster->player_id)
                            ->whereIn('real_match_id', $realMatchIds)
                            ->get();

                        $basePoints = 0;
                        $breakdown = [];

                        foreach ($stats as $stat) {
                            foreach ($scoringRules as $rule) {
                                $pts = $rule->applyTo($stat);
                                if ($pts !== 0) {
                                    $breakdown[] = [
                                        'rule' => $rule->code,
                                        'label' => $rule->label,
                                        'points' => $pts,
                                    ];
                                    $basePoints += $pts;
                                }
                            }
                        }

                        $isCaptain = $roster->captaincy === FantasyRoster::CAPTAINCY_CAPTAIN;
                        $isVice = $roster->captaincy === FantasyRoster::CAPTAINCY_VICE;
                        $finalPoints = $isCaptain ? $basePoints * 2 : $basePoints;

                        FantasyRosterScore::create([
                            'fantasy_roster_id' => $roster->id,
                            'player_id' => $roster->player_id,
                            'gameweek_id' => $gameweek->id,
                            'fantasy_team_id' => $team->id,
                            'is_starter' => true,
                            'is_captain' => $isCaptain,
                            'is_vice_captain' => $isVice,
                            'base_points' => $basePoints,
                            'final_points' => $finalPoints,
                            'breakdown' => $breakdown,
                        ]);

                        $teamTotalPoints += $finalPoints;
                        $scoresCreated++;
                    }

                    // Actualizar total_points del equipo
                    $team->increment('total_points', $teamTotalPoints);
                    $teamsProcessed++;

                    $this->line("  {$team->name}: +{$teamTotalPoints} pts (total: {$team->fresh()->total_points})");
                }
            }

            $this->info("Equipos procesados: {$teamsProcessed}");
            $this->info("Scores creados: {$scoresCreated}");

            // Cerrar gameweek
            $gameweek->close();
            $this->info("GW{$gameweek->number} cerrado.");
        });

        $this->info('Procesamiento completado.');
        return 0;
    }
}