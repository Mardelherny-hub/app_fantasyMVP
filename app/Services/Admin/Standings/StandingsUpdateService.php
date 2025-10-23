<?php

namespace App\Services\Admin\Standings;

use App\Models\League;
use App\Models\Gameweek;
use App\Models\FantasyTeam;
use App\Models\LeagueStanding;
use App\Models\Fixture;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StandingsUpdateService
{
    public function updateStandings(League $league, Gameweek $gameweek): void
    {
        Log::info("Actualizando standings para liga {$league->id}, gameweek {$gameweek->id}");

        $teams = $league->fantasyTeams;
        $fixtures = Fixture::where('league_id', $league->id)
            ->where('gameweek_id', '<=', $gameweek->id)
            ->where('status', Fixture::STATUS_FINISHED)
            ->get();

        DB::transaction(function () use ($league, $gameweek, $teams, $fixtures) {
            foreach ($teams as $team) {
                $stats = $this->calculateTeamStats($team, $fixtures);
                
                LeagueStanding::updateOrCreate(
                    [
                        'league_id' => $league->id,
                        'fantasy_team_id' => $team->id,
                        'gameweek_id' => $gameweek->id,
                    ],
                    $stats
                );
            }

            $this->applyTiebreakers($league, $gameweek);
        });
    }

    public function recalculateAllStandings(League $league, int $upToGameweek): void
    {
        $gameweeks = Gameweek::where('season_id', $league->season_id)
            ->where('number', '<=', $upToGameweek)
            ->orderBy('number')
            ->get();

        foreach ($gameweeks as $gw) {
            $this->updateStandings($league, $gw);
        }
    }

    public function getPlayoffQualifiers(League $league, int $atGameweek): Collection
    {
        return LeagueStanding::where('league_id', $league->id)
            ->where('gameweek_id', $atGameweek)
            ->orderBy('position')
            ->limit($league->playoff_teams ?? 5)
            ->get();
    }

    private function calculateTeamStats(FantasyTeam $team, Collection $fixtures): array
    {
        $stats = [
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'fantasy_points' => $team->total_points,
        ];

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

        $stats['goal_difference'] = $stats['goals_for'] - $stats['goals_against'];

        return $stats;
    }

    private function applyTiebreakers(League $league, Gameweek $gameweek): void
    {
        $standings = LeagueStanding::where('league_id', $league->id)
            ->where('gameweek_id', $gameweek->id)
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->orderBy('fantasy_points', 'desc')
            ->get();

        $position = 1;
        foreach ($standings as $standing) {
            $standing->update(['position' => $position++]);
        }
    }
}