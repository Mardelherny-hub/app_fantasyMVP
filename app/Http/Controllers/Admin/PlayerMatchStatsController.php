<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlayerMatchStatsRequest;
use App\Http\Requests\Admin\UpdatePlayerMatchStatsRequest;
use App\Models\RealMatch;
use App\Models\PlayerMatchStats;
use App\Models\Player;
use App\Models\ScoringRule;
use App\Models\Season;
use App\Services\Admin\Scoring\ScoringCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PlayerMatchStatsController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealMatch::query()
            ->with(['fixture.homeTeam', 'fixture.awayTeam'])
            ->withCount('playerStats')
            ->latest('started_at_utc');

        $matches = $query->paginate(20)->withQueryString();

        return view('admin.player-match-stats.index', compact('matches'));
    }

    public function manage(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $existingStats = PlayerMatchStats::where('real_match_id', $realMatch->id)
            ->with('player')
            ->get()
            ->keyBy('player_id');

        $homeTeamId = $realMatch->fixture->home_team_id;
        $awayTeamId = $realMatch->fixture->away_team_id;

        // Buscar jugadores via real_team_memberships (membresías actuales)
        $homeTeamPlayers = Player::whereIn('real_player_id', function($query) use ($homeTeamId) {
                $query->select('real_player_id')
                    ->from('real_team_memberships')
                    ->where('real_team_id', $homeTeamId)
                    ->whereNull('to_date');
            })
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('known_as')
            ->get();

        $awayTeamPlayers = Player::whereIn('real_player_id', function($query) use ($awayTeamId) {
                $query->select('real_player_id')
                    ->from('real_team_memberships')
                    ->where('real_team_id', $awayTeamId)
                    ->whereNull('to_date');
            })
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('known_as')
            ->get();

        return view('admin.player-match-stats.manage', compact(
            'realMatch',
            'homeTeamPlayers',
            'awayTeamPlayers',
            'existingStats'
        ));
    }

    public function store(StorePlayerMatchStatsRequest $request, string $locale)
    {
        app()->setLocale($locale);

        $data = $request->validated();
        $data['clean_sheet'] = $request->boolean('clean_sheet');
        $data['goals'] = $data['goals'] ?? 0;
        $data['assists'] = $data['assists'] ?? 0;
        $data['shots'] = $data['shots'] ?? 0;
        $data['saves'] = $data['saves'] ?? 0;
        $data['yellow'] = $data['yellow'] ?? 0;
        $data['red'] = $data['red'] ?? 0;
        $data['conceded'] = $data['conceded'] ?? 0;

        // CAMBIAR ESTO:
        PlayerMatchStats::updateOrCreate(
            [
                'real_match_id' => $data['real_match_id'],
                'player_id' => $data['player_id']
            ],
            [
                'real_match_id' => $data['real_match_id'],  // ← AGREGAR
                'player_id' => $data['player_id'],          // ← AGREGAR
                'minutes' => $data['minutes'],
                'goals' => $data['goals'],
                'assists' => $data['assists'],
                'shots' => $data['shots'],
                'saves' => $data['saves'],
                'yellow' => $data['yellow'],
                'red' => $data['red'],
                'clean_sheet' => $data['clean_sheet'],
                'conceded' => $data['conceded'],
                'rating' => $data['rating'],
            ]
        );

        return back()->with('success', __('Estadísticas guardadas correctamente.'));
    }

    public function destroy(Request $request, string $locale, PlayerMatchStats $playerMatchStat)
    {
        app()->setLocale($locale);

        $playerMatchStat->delete();

        return back()->with('success', __('Estadísticas eliminadas correctamente.'));
    }

    public function scoring(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $stats = PlayerMatchStats::where('real_match_id', $realMatch->id)
            ->with('player')
            ->get();

        $season = Season::where('is_active', true)->first();
        $scoringRules = ScoringRule::where('season_id', $season->id)->get();

        $playersWithPoints = $stats->map(function($stat) use ($scoringRules) {
            $totalPoints = 0;
            $breakdown = [];

            foreach($scoringRules as $rule) {
                $points = $this->applyRule($rule, $stat, $stat->player);
                if ($points != 0) {
                    $breakdown[] = [
                        'rule' => $rule->code,
                        'label' => $rule->label,
                        'points' => $points
                    ];
                    $totalPoints += $points;
                }
            }

            return [
                'player' => $stat->player,
                'stats' => $stat,
                'points' => [
                    'total' => $totalPoints,
                    'breakdown' => $breakdown,
                    'played' => $stat->minutes > 0
                ]
            ];
        });

        return view('admin.player-match-stats.scoring', compact('realMatch', 'playersWithPoints'));
    }

    /**
     * Aplicar regla de puntuación
     */
    private function applyRule($rule, $stats, $player): int
    {
        $points = 0;

        switch($rule->code) {
            case 'minutes_played':
                if ($stats->minutes >= 60) $points = $rule->points;
                elseif ($stats->minutes > 0) $points = 1;
                break;
            case 'goal_gk':
                if ($player->position === 1) $points = $stats->goals * $rule->points;
                break;
            case 'goal_df':
                if ($player->position === 2) $points = $stats->goals * $rule->points;
                break;
            case 'goal_mf':
                if ($player->position === 3) $points = $stats->goals * $rule->points;
                break;
            case 'goal_fw':
                if ($player->position === 4) $points = $stats->goals * $rule->points;
                break;
            case 'assist':
                $points = $stats->assists * $rule->points;
                break;
            case 'cs_gk':
                if ($player->position === 1 && $stats->clean_sheet) $points = $rule->points;
                break;
            case 'cs_df':
                if ($player->position === 2 && $stats->clean_sheet) $points = $rule->points;
                break;
            case 'cs_mf':
                if ($player->position === 3 && $stats->clean_sheet) $points = $rule->points;
                break;
            case 'save_3':
                if ($player->position === 1) $points = intval($stats->saves / 3) * $rule->points;
                break;
            case 'yellow':
                $points = $stats->yellow * $rule->points;
                break;
            case 'red':
                $points = $stats->red * $rule->points;
                break;
            case 'goals_conceded_2_gk':
                if ($player->position === 1) $points = intval($stats->conceded / 2) * $rule->points;
                break;
            case 'goals_conceded_2_df':
                if ($player->position === 2) $points = intval($stats->conceded / 2) * $rule->points;
                break;
        }

        return $points;
    }
}