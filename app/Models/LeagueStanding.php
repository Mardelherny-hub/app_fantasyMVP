<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueStanding extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'fantasy_team_id',
        'gameweek_id',
        'position',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points',
        'fantasy_points',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'played' => 'integer',
        'won' => 'integer',
        'drawn' => 'integer',
        'lost' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'goal_difference' => 'integer',
        'points' => 'integer',
        'fantasy_points' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this standing.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the fantasy team for this standing.
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    /**
     * Get the gameweek for this standing.
     */
    public function gameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by league.
     */
    public function scopeLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope by gameweek.
     */
    public function scopeGameweek($query, ?int $gameweekId)
    {
        return $query->where('gameweek_id', $gameweekId);
    }

    /**
     * Scope current standings (no specific gameweek).
     */
    public function scopeCurrent($query)
    {
        return $query->whereNull('gameweek_id');
    }

    /**
     * Scope ordered by position.
     */
    public function scopeByPosition($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Scope ordered by points and goal difference.
     */
    public function scopeByPoints($query)
    {
        return $query->orderBy('points', 'desc')
                     ->orderBy('goal_difference', 'desc')
                     ->orderBy('goals_for', 'desc');
    }

    /**
     * Scope playoff qualifiers.
     */
    public function scopePlayoffQualifiers($query, int $topN = 5)
    {
        return $query->orderBy('position')
                     ->limit($topN);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if team qualifies for playoffs.
     */
    public function qualifiesForPlayoffs(): bool
    {
        $league = $this->league;
        return $this->position <= $league->playoff_teams;
    }

    /**
     * Get win percentage.
     */
    public function getWinPercentage(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }
        
        return round(($this->won / $this->played) * 100, 2);
    }

    /**
     * Get points per game average.
     */
    public function getAveragePoints(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }
        
        return round($this->points / $this->played, 2);
    }

    /**
     * Get form (last 5 results).
     */
    public function getForm(int $lastN = 5): string
    {
        // Esto requeriría consultar fixtures recientes
        // Implementar cuando sea necesario
        return 'N/A';
    }

    /**
     * Calculate and update standings for a league/gameweek.
     */
    public static function calculateFor(int $leagueId, ?int $gameweekId = null): void
    {
        $fixtures = Fixture::where('league_id', $leagueId)
            ->finished()
            ->when($gameweekId, function($q) use ($gameweekId) {
                return $q->where('gameweek_id', '<=', $gameweekId);
            })
            ->get();

        $standings = [];

        foreach ($fixtures as $fixture) {
            $homeId = $fixture->home_fantasy_team_id;
            $awayId = $fixture->away_fantasy_team_id;

            // Inicializar si no existe
            if (!isset($standings[$homeId])) {
                $standings[$homeId] = self::initializeStanding($homeId);
            }
            if (!isset($standings[$awayId])) {
                $standings[$awayId] = self::initializeStanding($awayId);
            }

            // Actualizar estadísticas
            $standings[$homeId]['played']++;
            $standings[$awayId]['played']++;

            $standings[$homeId]['goals_for'] += $fixture->home_goals;
            $standings[$homeId]['goals_against'] += $fixture->away_goals;
            $standings[$awayId]['goals_for'] += $fixture->away_goals;
            $standings[$awayId]['goals_against'] += $fixture->home_goals;

            // Determinar resultado
            if ($fixture->home_goals > $fixture->away_goals) {
                $standings[$homeId]['won']++;
                $standings[$homeId]['points'] += 3;
                $standings[$awayId]['lost']++;
            } elseif ($fixture->away_goals > $fixture->home_goals) {
                $standings[$awayId]['won']++;
                $standings[$awayId]['points'] += 3;
                $standings[$homeId]['lost']++;
            } else {
                $standings[$homeId]['drawn']++;
                $standings[$awayId]['drawn']++;
                $standings[$homeId]['points']++;
                $standings[$awayId]['points']++;
            }
        }

        // Calcular diferencia de goles y ordenar
        foreach ($standings as $teamId => &$data) {
            $data['goal_difference'] = $data['goals_for'] - $data['goals_against'];
            $data['fantasy_points'] = FantasyTeam::find($teamId)->total_points;
        }

        // Ordenar por puntos, diferencia de goles, goles a favor
        uasort($standings, function($a, $b) {
            if ($a['points'] != $b['points']) {
                return $b['points'] - $a['points'];
            }
            if ($a['goal_difference'] != $b['goal_difference']) {
                return $b['goal_difference'] - $a['goal_difference'];
            }
            return $b['goals_for'] - $a['goals_for'];
        });

        // Guardar posiciones
        $position = 1;
        foreach ($standings as $teamId => $data) {
            // Quitar fantasy_team_id del data para evitar conflicto con unique constraint
            unset($data['fantasy_team_id']);
            
            self::updateOrCreate(
                [
                    'league_id' => $leagueId,
                    'fantasy_team_id' => $teamId,
                    'gameweek_id' => $gameweekId,
                ],
                array_merge($data, ['position' => $position++])
            );
        }
    }

    /**
     * Initialize standing array for a team.
     */
    protected static function initializeStanding(int $teamId): array
    {
        return [
            'fantasy_team_id' => $teamId,
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
            'points' => 0,
            'fantasy_points' => 0,
        ];
    }
}