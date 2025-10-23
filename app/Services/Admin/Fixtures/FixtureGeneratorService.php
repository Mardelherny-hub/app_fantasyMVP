<?php

namespace App\Services\Admin\Fixtures;

use App\Models\Fixture;
use App\Models\Gameweek;
use App\Models\League;
use App\Models\FantasyTeam;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FixtureGeneratorService
{
    /**
     * Generar fixtures para temporada regular (round-robin)
     */
    public function generateRegularSeasonFixtures(League $league, int $startGameweek = 1): Collection
    {
        Log::info("Generando fixtures regulares para liga: {$league->id}");

        // 1. Obtener equipos de la liga
        $teams = $league->fantasyTeams;
        
        if ($teams->count() < 2) {
            throw new Exception('Se requieren al menos 2 equipos para generar fixtures.');
        }

        // 2. Obtener gameweeks de temporada regular
        $gameweeks = Gameweek::where('season_id', $league->season_id)
            ->where('is_playoff', false)
            ->where('number', '>=', $startGameweek)
            ->orderBy('number')
            ->get();

        if ($gameweeks->isEmpty()) {
            throw new Exception('No hay gameweeks de temporada regular disponibles.');
        }

        // 3. Generar calendario round-robin
        $schedule = $this->roundRobinSchedule($teams, $league, $startGameweek);

        // 4. Crear fixtures en la base de datos
        $fixtures = collect();

        DB::transaction(function () use ($schedule, $league, $gameweeks, &$fixtures) {
            foreach ($schedule as $gwNumber => $matches) {
                $gameweek = $gameweeks->firstWhere('number', $gwNumber);
                
                if (!$gameweek) {
                    Log::warning("Gameweek {$gwNumber} no encontrado, omitiendo...");
                    continue;
                }

                foreach ($matches as $match) {
                    $fixture = $this->createFixture(
                        $league,
                        $gameweek,
                        $match['home'],
                        $match['away'],
                        false,
                        null
                    );
                    
                    $fixtures->push($fixture);
                }
            }
        });

        Log::info("Generados {$fixtures->count()} fixtures para temporada regular");

        return $fixtures;
    }

    /**
     * Generar fixtures de playoffs (Page Playoff System)
     */
    public function generatePlayoffFixtures(
        League $league,
        int $quarterGameweek = 28,
        int $semiGameweek = 29,
        int $finalGameweek = 30
    ): Collection {
        Log::info("Generando fixtures de playoffs para liga: {$league->id}");

        // Validar formato de playoffs
        if ($league->playoff_format !== League::PLAYOFF_FORMAT_PAGE) {
            throw new Exception('Esta liga no usa el formato Page Playoff.');
        }

        if ($league->playoff_teams < 5) {
            throw new Exception('Se requieren al menos 5 equipos clasificados para playoffs.');
        }

        // Obtener gameweeks de playoffs
        $quarterGW = Gameweek::where('season_id', $league->season_id)
            ->where('number', $quarterGameweek)
            ->first();

        $semiGW = Gameweek::where('season_id', $league->season_id)
            ->where('number', $semiGameweek)
            ->first();

        $finalGW = Gameweek::where('season_id', $league->season_id)
            ->where('number', $finalGameweek)
            ->first();

        if (!$quarterGW || !$semiGW || !$finalGW) {
            throw new Exception('No se encontraron todos los gameweeks de playoffs.');
        }

        // Obtener top 5 equipos (basado en standings del GW anterior a cuartos)
        $standings = DB::table('league_standings')
            ->where('league_id', $league->id)
            ->where('gameweek_id', $quarterGW->id - 1) // GW27
            ->orderBy('position')
            ->limit(5)
            ->get();

        if ($standings->count() < 5) {
            throw new Exception('No hay suficientes equipos clasificados en los standings.');
        }

        $team1 = FantasyTeam::find($standings[0]->fantasy_team_id);
        $team2 = FantasyTeam::find($standings[1]->fantasy_team_id);
        $team3 = FantasyTeam::find($standings[2]->fantasy_team_id);
        $team4 = FantasyTeam::find($standings[3]->fantasy_team_id);
        $team5 = FantasyTeam::find($standings[4]->fantasy_team_id);

        if (!$team1 || !$team2 || !$team3 || !$team4 || !$team5) {
            throw new Exception('No se pudieron cargar todos los equipos clasificados.');
        }

        $fixtures = collect();

        DB::transaction(function () use (
            $league, $quarterGW, $semiGW, $finalGW,
            $team1, $team2, $team3, $team4, $team5,
            &$fixtures
        ) {
            // GW28 - Cuartos: 4° vs 5°
            $quarterFixture = $this->createFixture(
                $league,
                $quarterGW,
                $team4,
                $team5,
                true,
                Fixture::PLAYOFF_QUARTERS
            );
            $fixtures->push($quarterFixture);

            // GW29 - Semi 1: 2° vs 3°
            $semi1 = $this->createFixture(
                $league,
                $semiGW,
                $team2,
                $team3,
                true,
                Fixture::PLAYOFF_SEMIS
            );
            $fixtures->push($semi1);

            // GW29 - Semi 2: Ganador Cuartos vs 1° (con dependencia)
            $semi2 = Fixture::create([
                'league_id' => $league->id,
                'gameweek_id' => $semiGW->id,
                'home_fantasy_team_id' => null, // Se resuelve después
                'away_fantasy_team_id' => $team1->id,
                'status' => Fixture::STATUS_PENDING,
                'is_playoff' => true,
                'playoff_round' => Fixture::PLAYOFF_SEMIS,
                'playoff_dependency' => json_encode([
                    'home_from' => 'winner_of_quarters',
                    'quarter_fixture_id' => $quarterFixture->id
                ])
            ]);
            $fixtures->push($semi2);

            // GW30 - Final: Ganadores de semis (con dependencias)
            $final = Fixture::create([
                'league_id' => $league->id,
                'gameweek_id' => $finalGW->id,
                'home_fantasy_team_id' => null,
                'away_fantasy_team_id' => null,
                'status' => Fixture::STATUS_PENDING,
                'is_playoff' => true,
                'playoff_round' => Fixture::PLAYOFF_FINAL,
                'playoff_dependency' => json_encode([
                    'home_from' => 'winner_of_semi_1',
                    'semi1_fixture_id' => $semi1->id,
                    'away_from' => 'winner_of_semi_2',
                    'semi2_fixture_id' => $semi2->id
                ])
            ]);
            $fixtures->push($final);
        });

        Log::info("Generados {$fixtures->count()} fixtures de playoffs");

        return $fixtures;
    }

    /**
     * Algoritmo Round-Robin para generar calendario equilibrado
     */
    private function roundRobinSchedule(Collection $teams, League $league, int $startGW): array
    {
        $teamsList = $teams->values()->all();
        $numTeams = count($teamsList);
        
        // Si hay número impar de equipos, agregar BYE
        $hasBye = $numTeams % 2 !== 0;
        if ($hasBye) {
            $teamsList[] = null; // BYE team
            $numTeams++;
        }

        $numRounds = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;
        
        // Calcular cuántas veces repetir el ciclo para llenar los GW disponibles
        $regularSeasonGWs = $league->regular_season_gameweeks ?? 27;
        $repetitions = ceil($regularSeasonGWs / $numRounds);

        $schedule = [];
        $gwCounter = $startGW;

        for ($rep = 0; $rep < $repetitions; $rep++) {
            for ($round = 0; $round < $numRounds; $round++) {
                if ($gwCounter > $regularSeasonGWs) {
                    break 2; // Salir de ambos loops
                }

                $roundMatches = [];

                for ($match = 0; $match < $matchesPerRound; $match++) {
                    $home = ($round + $match) % ($numTeams - 1);
                    $away = ($numTeams - 1 - $match + $round) % ($numTeams - 1);

                    // El último equipo rota entre home y away
                    if ($match == 0) {
                        $away = $numTeams - 1;
                    }

                    // Alternar home/away en repeticiones
                    if ($rep % 2 == 1) {
                        [$home, $away] = [$away, $home];
                    }

                    $homeTeam = $teamsList[$home];
                    $awayTeam = $teamsList[$away];

                    // Omitir si alguno es BYE
                    if ($homeTeam === null || $awayTeam === null) {
                        continue;
                    }

                    $roundMatches[] = [
                        'home' => $homeTeam,
                        'away' => $awayTeam
                    ];
                }

                if (!empty($roundMatches)) {
                    $schedule[$gwCounter] = $roundMatches;
                    $gwCounter++;
                }
            }
        }

        return $schedule;
    }

    /**
     * Crear un fixture individual
     */
    private function createFixture(
        League $league,
        Gameweek $gameweek,
        FantasyTeam $home,
        FantasyTeam $away,
        bool $isPlayoff = false,
        ?int $playoffRound = null
    ): Fixture {
        return Fixture::create([
            'league_id' => $league->id,
            'gameweek_id' => $gameweek->id,
            'home_fantasy_team_id' => $home->id,
            'away_fantasy_team_id' => $away->id,
            'status' => Fixture::STATUS_PENDING,
            'is_playoff' => $isPlayoff,
            'playoff_round' => $playoffRound,
            'home_goals' => 0,
            'away_goals' => 0,
        ]);
    }
}