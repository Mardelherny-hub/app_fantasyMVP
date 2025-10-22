<?php

namespace App\Services\Admin\Fixtures;

use App\Models\Fixture;
use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Models\League;
use App\Models\LeagueStanding;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixtureProcessingService
{
    /**
     * Procesar gameweek completo.
     */
    public function processCompletedGameweek(Gameweek $gameweek): array
    {
        return DB::transaction(function () use ($gameweek) {
            $results = ['gameweek_id' => $gameweek->id, 'fixtures_processed' => 0, 'standings_updated' => 0, 'errors' => []];
            
            $fixtures = Fixture::where('gameweek_id', $gameweek->id)
                ->where('status', Fixture::STATUS_PENDING)
                ->with(['homeTeam', 'awayTeam', 'league'])
                ->get();
            
            foreach ($fixtures as $fixture) {
                try {
                    $this->updateFixtureResult($fixture);
                    $results['fixtures_processed']++;
                } catch (\Exception $e) {
                    Log::error("Error processing fixture {$fixture->id}: {$e->getMessage()}");
                    $results['errors'][] = ['fixture_id' => $fixture->id, 'error' => $e->getMessage()];
                }
            }
            
            $leagues = $fixtures->pluck('league')->unique('id');
            foreach ($leagues as $league) {
                try {
                    $this->updateLeagueStandings($league, $gameweek);
                    $results['standings_updated']++;
                } catch (\Exception $e) {
                    Log::error("Error updating standings for league {$league->id}: {$e->getMessage()}");
                    $results['errors'][] = ['league_id' => $league->id, 'error' => $e->getMessage()];
                }
            }
            
            return $results;
        });
    }

    /**
     * Calcular goles: diferencia de 10 pts = 1 gol.
     */
    public function calculateFixtureGoals(int $homePoints, int $awayPoints): array
    {
        $difference = abs($homePoints - $awayPoints);
        
        if ($difference < 10) {
            return ['home_goals' => 0, 'away_goals' => 0, 'result' => 'draw'];
        }
        
        $goals = intval($difference / 10);
        
        if ($homePoints > $awayPoints) {
            return ['home_goals' => $goals, 'away_goals' => 0, 'result' => 'home_win'];
        } else {
            return ['home_goals' => 0, 'away_goals' => $goals, 'result' => 'away_win'];
        }
    }

    /**
     * Actualizar resultado del fixture.
     */
    public function updateFixtureResult(Fixture $fixture): void
    {
        $homePoints = $this->getTeamGameweekPoints($fixture->homeTeam, $fixture->gameweek_id);
        $awayPoints = $this->getTeamGameweekPoints($fixture->awayTeam, $fixture->gameweek_id);
        
        $result = $this->calculateFixtureGoals($homePoints, $awayPoints);
        
        $fixture->update([
            'home_goals' => $result['home_goals'],
            'away_goals' => $result['away_goals'],
            'status' => Fixture::STATUS_FINISHED,
        ]);
    }

    /**
     * Obtener puntos del equipo en gameweek.
     */
    protected function getTeamGameweekPoints(FantasyTeam $team, int $gameweekId): int
    {
        return $team->rosters()
            ->where('gameweek_id', $gameweekId)
            ->where('is_starter', true)
            ->with('player')
            ->get()
            ->sum(function ($roster) use ($gameweekId) {
                $score = $roster->scores()->where('gameweek_id', $gameweekId)->first();
                return $score ? $score->final_points : 0;
            });
    }

    /**
     * Actualizar standings de liga.
     */
    public function updateLeagueStandings(League $league, Gameweek $gameweek): void
    {
        $fixtures = Fixture::where('league_id', $league->id)
            ->where('gameweek_id', $gameweek->id)
            ->where('status', Fixture::STATUS_FINISHED)
            ->get();
        
        foreach ($league->teams as $team) {
            $this->updateTeamStanding($team, $gameweek, $fixtures);
        }
    }

    /**
     * Actualizar standing de equipo.
     */
    protected function updateTeamStanding(FantasyTeam $team, Gameweek $gameweek, $fixtures): void
    {
        $standing = LeagueStanding::firstOrNew([
            'league_id' => $team->league_id,
            'fantasy_team_id' => $team->id,
            'gameweek_id' => $gameweek->id,
        ]);
        
        $stats = $this->calculateTeamStats($team, $fixtures);
        
        $standing->fill([
            'played' => $stats['played'],
            'won' => $stats['won'],
            'drawn' => $stats['drawn'],
            'lost' => $stats['lost'],
            'goals_for' => $stats['goals_for'],
            'goals_against' => $stats['goals_against'],
            'goal_difference' => $stats['goals_for'] - $stats['goals_against'],
            'points' => $stats['points'],
            'fantasy_points' => $this->getTeamGameweekPoints($team, $gameweek->id),
        ]);
        
        $standing->save();
        $this->recalculatePositions($team->league_id, $gameweek->id);
    }

    /**
     * Calcular stats de equipo.
     */
    protected function calculateTeamStats(FantasyTeam $team, $fixtures): array
    {
        $stats = ['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0];
        
        foreach ($fixtures as $fixture) {
            $isHome = $fixture->home_fantasy_team_id === $team->id;
            $isAway = $fixture->away_fantasy_team_id === $team->id;
            
            if (!$isHome && !$isAway) continue;
            
            $stats['played']++;
            
            if ($isHome) {
                $stats['goals_for'] += $fixture->home_goals;
                $stats['goals_against'] += $fixture->away_goals;
                
                if ($fixture->home_goals > $fixture->away_goals) {
                    $stats['won']++;
                    $stats['points'] += 3;
                } elseif ($fixture->home_goals === $fixture->away_goals) {
                    $stats['drawn']++;
                    $stats['points'] += 1;
                } else {
                    $stats['lost']++;
                }
            } else {
                $stats['goals_for'] += $fixture->away_goals;
                $stats['goals_against'] += $fixture->home_goals;
                
                if ($fixture->away_goals > $fixture->home_goals) {
                    $stats['won']++;
                    $stats['points'] += 3;
                } elseif ($fixture->away_goals === $fixture->home_goals) {
                    $stats['drawn']++;
                    $stats['points'] += 1;
                } else {
                    $stats['lost']++;
                }
            }
        }
        
        return $stats;
    }

    /**
     * Recalcular posiciones.
     */
    protected function recalculatePositions(int $leagueId, int $gameweekId): void
    {
        $standings = LeagueStanding::where('league_id', $leagueId)
            ->where('gameweek_id', $gameweekId)
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->orderBy('fantasy_points', 'desc')
            ->get();
        
        $position = 1;
        foreach ($standings as $standing) {
            $standing->update(['position' => $position]);
            $position++;
        }
    }
}
