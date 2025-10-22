<?php

namespace App\Services\Admin\Scoring;

use App\Models\FantasyRoster;
use App\Models\FantasyRosterScore;
use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Models\Player;
use App\Models\PlayerMatchStats;
use App\Models\ScoringRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoringCalculationService
{
    /**
     * Procesar puntuación completa de un gameweek.
     */
    public function processGameweekScoring(Gameweek $gameweek): array
    {
        return DB::transaction(function () use ($gameweek) {
            $season = $gameweek->season;
            $scoringRules = $season->scoringRules;
            
            $fantasyTeams = FantasyTeam::whereHas('league', function ($query) use ($season) {
                $query->where('season_id', $season->id);
            })->get();
            
            $results = [
                'gameweek_id' => $gameweek->id,
                'teams_processed' => 0,
                'total_points_calculated' => 0,
                'errors' => [],
            ];
            
            foreach ($fantasyTeams as $team) {
                try {
                    $teamPoints = $this->calculateTeamPoints($team, $gameweek, $scoringRules);
                    $team->increment('total_points', $teamPoints);
                    
                    $results['teams_processed']++;
                    $results['total_points_calculated'] += $teamPoints;
                } catch (\Exception $e) {
                    Log::error("Error calculating points for team {$team->id}: {$e->getMessage()}");
                    $results['errors'][] = ['team_id' => $team->id, 'error' => $e->getMessage()];
                }
            }
            
            return $results;
        });
    }

    /**
     * Calcular puntos totales de un equipo.
     */
    public function calculateTeamPoints(FantasyTeam $team, Gameweek $gameweek, Collection $scoringRules): int
    {
        $rosters = FantasyRoster::where('fantasy_team_id', $team->id)
            ->where('gameweek_id', $gameweek->id)
            ->with('player')
            ->get();
        
        if ($rosters->isEmpty()) {
            return 0;
        }
        
        $totalPoints = 0;
        $captainPlayed = false;
        
        foreach ($rosters as $roster) {
            if (!$roster->is_starter) continue;
            
            $playerPoints = $this->calculatePlayerPoints($roster->player, $gameweek, $scoringRules);
            $rosterScore = $this->createRosterScore($roster, $playerPoints);
            
            if ($roster->captaincy === FantasyRoster::CAPTAINCY_CAPTAIN) {
                $captainPlayed = $playerPoints['played'];
                $totalPoints += $captainPlayed ? $this->applyCaptainBonus($rosterScore) : $playerPoints['total'];
            } elseif ($roster->captaincy === FantasyRoster::CAPTAINCY_VICE) {
                $totalPoints += (!$captainPlayed && $playerPoints['played']) 
                    ? $this->applyCaptainBonus($rosterScore) 
                    : $playerPoints['total'];
            } else {
                $totalPoints += $playerPoints['total'];
            }
        }
        
        return $totalPoints;
    }

    /**
     * Calcular puntos de un jugador.
     */
    public function calculatePlayerPoints(Player $player, Gameweek $gameweek, Collection $scoringRules): array
    {
        $stats = PlayerMatchStats::where('player_id', $player->id)
            ->whereHas('realMatch', function ($query) use ($gameweek) {
                $query->whereHas('fixture', function ($q) use ($gameweek) {
                    $q->where('gameweek_id', $gameweek->id);
                });
            })
            ->first();
        
        if (!$stats || $stats->minutes === 0) {
            return ['total' => 0, 'breakdown' => [], 'played' => false];
        }
        
        $breakdown = [];
        $totalPoints = 0;
        
        foreach ($scoringRules as $rule) {
            $points = $this->applyRule($rule, $stats, $player);
            if ($points !== 0) {
                $breakdown[] = ['rule' => $rule->code, 'label' => $rule->label, 'points' => $points];
                $totalPoints += $points;
            }
        }
        
        return ['total' => $totalPoints, 'breakdown' => $breakdown, 'played' => true];
    }

    /**
     * Aplicar regla específica.
     */
    protected function applyRule(ScoringRule $rule, PlayerMatchStats $stats, Player $player): int
    {
        $points = 0;
        
        switch ($rule->code) {
            case ScoringRule::CODE_GOAL_GK:
                if ($player->position === Player::POSITION_GK) $points = $stats->goals * $rule->points;
                break;
            case ScoringRule::CODE_GOAL_DF:
                if ($player->position === Player::POSITION_DF) $points = $stats->goals * $rule->points;
                break;
            case ScoringRule::CODE_GOAL_MF:
                if ($player->position === Player::POSITION_MF) $points = $stats->goals * $rule->points;
                break;
            case ScoringRule::CODE_GOAL_FW:
                if ($player->position === Player::POSITION_FW) $points = $stats->goals * $rule->points;
                break;
            case ScoringRule::CODE_ASSIST:
                $points = $stats->assists * $rule->points;
                break;
            case ScoringRule::CODE_CS_GK:
                if ($player->position === Player::POSITION_GK && $stats->clean_sheet) $points = $rule->points;
                break;
            case ScoringRule::CODE_CS_DF:
                if ($player->position === Player::POSITION_DF && $stats->clean_sheet) $points = $rule->points;
                break;
            case ScoringRule::CODE_CS_MF:
                if ($player->position === Player::POSITION_MF && $stats->clean_sheet) $points = $rule->points;
                break;
            case ScoringRule::CODE_MINUTES_0_59:
                if ($stats->minutes > 0 && $stats->minutes < 60) $points = $rule->points;
                break;
            case ScoringRule::CODE_MINUTES_60_PLUS:
                if ($stats->minutes >= 60) $points = $rule->points;
                break;
            case ScoringRule::CODE_SAVE_3:
                if ($player->position === Player::POSITION_GK) $points = intval($stats->saves / 3) * $rule->points;
                break;
            case ScoringRule::CODE_PENALTY_SAVED:
                $points = ($stats->raw['penalty_saved'] ?? 0) * $rule->points;
                break;
            case ScoringRule::CODE_PENALTY_MISSED:
                $points = ($stats->raw['penalty_missed'] ?? 0) * $rule->points;
                break;
            case ScoringRule::CODE_GOALS_CONCEDED_2_GK:
                if ($player->position === Player::POSITION_GK) $points = intval($stats->conceded / 2) * $rule->points;
                break;
            case ScoringRule::CODE_GOALS_CONCEDED_2_DF:
                if ($player->position === Player::POSITION_DF) $points = intval($stats->conceded / 2) * $rule->points;
                break;
            case ScoringRule::CODE_YELLOW:
                $points = $stats->yellow * $rule->points;
                break;
            case ScoringRule::CODE_RED:
                $points = $stats->red * $rule->points;
                break;
        }
        
        return $points;
    }

    /**
     * Crear registro de score.
     */
    protected function createRosterScore(FantasyRoster $roster, array $playerPoints): FantasyRosterScore
    {
        return FantasyRosterScore::updateOrCreate(
            [
                'fantasy_roster_id' => $roster->id,
                'player_id' => $roster->player_id,
                'gameweek_id' => $roster->gameweek_id,
            ],
            [
                'fantasy_team_id' => $roster->fantasy_team_id,
                'is_starter' => $roster->is_starter,
                'is_captain' => $roster->captaincy === FantasyRoster::CAPTAINCY_CAPTAIN,
                'is_vice_captain' => $roster->captaincy === FantasyRoster::CAPTAINCY_VICE,
                'base_points' => $playerPoints['total'],
                'final_points' => $playerPoints['total'],
                'breakdown' => $playerPoints['breakdown'],
            ]
        );
    }

    /**
     * Aplicar bonus capitán (x2).
     */
    public function applyCaptainBonus(FantasyRosterScore $rosterScore): int
    {
        if ($rosterScore->is_captain || $rosterScore->is_vice_captain) {
            $finalPoints = $rosterScore->base_points * 2;
            $rosterScore->update(['final_points' => $finalPoints]);
            return $finalPoints;
        }
        return $rosterScore->base_points;
    }
}
