<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RealMatch;
use App\Models\RealPlayer;
use App\Models\RealPlayerStat;
use App\Models\PlayerMatchStats;
use App\Models\ScoringRule;
use App\Models\Season;
use App\Models\Player;
use Illuminate\Http\Request;

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

        $existingStats = RealPlayerStat::where('real_match_id', $realMatch->id)
            ->get()
            ->keyBy('real_player_id');

        $homeTeamId = $realMatch->fixture->home_team_id;
        $awayTeamId = $realMatch->fixture->away_team_id;

        // Buscar jugadores REALES via real_team_memberships (membresías actuales)
        $homeTeamPlayers = RealPlayer::whereIn('id', function($query) use ($homeTeamId) {
                $query->select('real_player_id')
                    ->from('real_team_memberships')
                    ->where('real_team_id', $homeTeamId)
                    ->whereNull('to_date');
            })
            ->orderBy('position')
            ->orderBy('full_name')
            ->get();

        $awayTeamPlayers = RealPlayer::whereIn('id', function($query) use ($awayTeamId) {
                $query->select('real_player_id')
                    ->from('real_team_memberships')
                    ->where('real_team_id', $awayTeamId)
                    ->whereNull('to_date');
            })
            ->orderBy('position')
            ->orderBy('full_name')
            ->get();

        return view('admin.player-match-stats.manage', compact(
            'realMatch',
            'homeTeamPlayers',
            'awayTeamPlayers',
            'existingStats'
        ));
    }

    public function store(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'real_match_id' => 'required|exists:real_matches,id',
            'real_player_id' => 'required|exists:real_players,id',
            'real_team_id' => 'required|exists:real_teams,id',
            'minutes' => 'required|integer|min:0|max:120',
            'goals' => 'nullable|integer|min:0',
            'assists' => 'nullable|integer|min:0',
            'yellow_cards' => 'nullable|integer|min:0|max:2',
            'red_cards' => 'nullable|integer|min:0|max:1',
            'rating' => 'nullable|integer|min:0|max:10',
        ]);

        $data['goals'] = $data['goals'] ?? 0;
        $data['assists'] = $data['assists'] ?? 0;
        $data['yellow_cards'] = $data['yellow_cards'] ?? 0;
        $data['red_cards'] = $data['red_cards'] ?? 0;

        RealPlayerStat::updateOrCreate(
            [
                'real_match_id' => $data['real_match_id'],
                'real_player_id' => $data['real_player_id'],
            ],
            $data
        );

        return back()->with('success', __('Estadísticas guardadas correctamente.'));
    }

    public function storeBulk(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $request->validate([
            'real_match_id' => 'required|exists:real_matches,id',
            'stats' => 'required|array',
            'stats.*.real_player_id' => 'required|exists:real_players,id',
            'stats.*.real_team_id' => 'required|exists:real_teams,id',
            'stats.*.minutes' => 'required|integer|min:0|max:120',
            'stats.*.goals' => 'nullable|integer|min:0',
            'stats.*.assists' => 'nullable|integer|min:0',
            'stats.*.yellow_cards' => 'nullable|integer|min:0|max:2',
            'stats.*.red_cards' => 'nullable|integer|min:0|max:1',
            'stats.*.rating' => 'nullable|integer|min:0|max:10',
        ]);

        $saved = 0;
        $skipped = 0;

        foreach ($request->stats as $stat) {
            // Saltar jugadores con 0 minutos que no tienen stats previas
            $existing = RealPlayerStat::where('real_match_id', $request->real_match_id)
                ->where('real_player_id', $stat['real_player_id'])
                ->first();

            if (intval($stat['minutes']) === 0 && !$existing) {
                $skipped++;
                continue;
            }

            RealPlayerStat::updateOrCreate(
                [
                    'real_match_id' => $request->real_match_id,
                    'real_player_id' => $stat['real_player_id'],
                ],
                [
                    'real_team_id' => $stat['real_team_id'],
                    'minutes' => $stat['minutes'],
                    'goals' => $stat['goals'] ?? 0,
                    'assists' => $stat['assists'] ?? 0,
                    'yellow_cards' => $stat['yellow_cards'] ?? 0,
                    'red_cards' => $stat['red_cards'] ?? 0,
                    'rating' => $stat['rating'] ?? null,
                ]
            );
            $saved++;
        }

        return back()->with('success', __('Estadísticas guardadas: :saved jugadores. Omitidos (0 min): :skipped', [
            'saved' => $saved,
            'skipped' => $skipped,
        ]));
    }

    public function syncToFantasy(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $realMatch->load('fixture');

        $realStats = RealPlayerStat::where('real_match_id', $realMatch->id)->get();

        if ($realStats->isEmpty()) {
            return back()->with('error', __('No hay estadísticas reales cargadas para este partido.'));
        }

        $synced = 0;
        $created = 0;
        $errors = [];

        $positionMap = [
            'GK' => Player::POSITION_GK,
            'DF' => Player::POSITION_DF,
            'MF' => Player::POSITION_MF,
            'FW' => Player::POSITION_FW,
        ];

        foreach ($realStats as $stat) {
            $realPlayer = RealPlayer::find($stat->real_player_id);
            if (!$realPlayer) {
                $errors[] = "RealPlayer #{$stat->real_player_id} no encontrado";
                continue;
            }

            // Buscar o crear el Player fantasy
            $player = Player::where('real_player_id', $realPlayer->id)->first();

            if (!$player) {
                $player = Player::create([
                    'real_player_id' => $realPlayer->id,
                    'full_name' => $realPlayer->full_name,
                    'known_as' => $realPlayer->full_name,
                    'position' => $positionMap[strtoupper($realPlayer->position)] ?? Player::POSITION_MF,
                    'nationality' => $realPlayer->nationality,
                    'birthdate' => $realPlayer->birthdate,
                    'photo_url' => $realPlayer->photo_url,
                    'is_active' => true,
                ]);
                $created++;
            }

            // Determinar clean_sheet y conceded según equipo del jugador
            $isHome = ($stat->real_team_id == $realMatch->fixture->home_team_id);
            $goalsConc = $isHome ? ($realMatch->away_score ?? 0) : ($realMatch->home_score ?? 0);
            $cleanSheet = ($goalsConc == 0 && ($stat->minutes ?? 0) >= 60);

            // Crear/actualizar player_match_stats
            PlayerMatchStats::updateOrCreate(
                [
                    'real_match_id' => $realMatch->id,
                    'player_id' => $player->id,
                ],
                [
                    'minutes' => $stat->minutes ?? 0,
                    'goals' => $stat->goals ?? 0,
                    'assists' => $stat->assists ?? 0,
                    'shots' => 0,
                    'saves' => 0,
                    'yellow' => $stat->yellow_cards ?? 0,
                    'red' => $stat->red_cards ?? 0,
                    'clean_sheet' => $cleanSheet,
                    'conceded' => $goalsConc,
                    'rating' => $stat->rating ? round($stat->rating, 2) : null,
                ]
            );
            $synced++;
        }

        $message = __('Sincronización completada: :synced jugadores sincronizados.', ['synced' => $synced]);
        if ($created > 0) {
            $message .= ' ' . __(':created jugadores fantasy creados.', ['created' => $created]);
        }
        if (!empty($errors)) {
            $message .= ' ' . __('Errores: :count', ['count' => count($errors)]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, string $locale, RealPlayerStat $realPlayerStat)
    {
        app()->setLocale($locale);

        $realPlayerStat->delete();

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
            case 'minutes_0_59':
                if ($stats->minutes > 0 && $stats->minutes < 60) $points = $rule->points;
                break;
            case 'minutes_60_plus':
                if ($stats->minutes >= 60) $points = $rule->points;
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
            case 'save_per_3':
                if ($player->position === 1) $points = intval($stats->saves / 3) * $rule->points;
                break;
            case 'penalty_saved':
                $points = ($stats->raw['penalty_saved'] ?? 0) * $rule->points;
                break;
            case 'penalty_missed':
                $points = ($stats->raw['penalty_missed'] ?? 0) * $rule->points;
                break;
            case 'yellow_card':
                $points = $stats->yellow * $rule->points;
                break;
            case 'red_card':
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