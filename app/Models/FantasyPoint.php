<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FantasyPoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fantasy_team_id',
        'player_id',
        'gameweek_id',
        'base_points',
        'bonus_points',
        'total_points',
        'breakdown',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_points' => 'integer',
        'bonus_points' => 'integer',
        'total_points' => 'integer',
        'breakdown' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fantasy team for these points.
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    /**
     * Get the player for these points.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the gameweek for these points.
     */
    public function gameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by fantasy team.
     */
    public function scopeTeam($query, int $fantasyTeamId)
    {
        return $query->where('fantasy_team_id', $fantasyTeamId);
    }

    /**
     * Scope by player.
     */
    public function scopePlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope by gameweek.
     */
    public function scopeGameweek($query, int $gameweekId)
    {
        return $query->where('gameweek_id', $gameweekId);
    }

    /**
     * Scope positive points.
     */
    public function scopePositive($query)
    {
        return $query->where('total_points', '>', 0);
    }

    /**
     * Scope negative points.
     */
    public function scopeNegative($query)
    {
        return $query->where('total_points', '<', 0);
    }

    /**
     * Scope ordered by points (descending).
     */
    public function scopeByPoints($query)
    {
        return $query->orderBy('total_points', 'desc');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Calculate and store fantasy points for this entry.
     * 
     * @param PlayerMatchStats $stats
     * @param FantasyRoster $roster
     * @param array $scoringRules
     */
    public static function calculateFor(
        PlayerMatchStats $stats,
        FantasyRoster $roster,
        array $scoringRules
    ): self {
        $basePoints = 0;
        $breakdown = [];

        // Aplicar cada regla de puntuación
        foreach ($scoringRules as $rule) {
            $points = $rule->applyTo($stats);
            
            if ($points != 0) {
                $basePoints += $points;
                $breakdown[$rule->code] = [
                    'label' => $rule->label,
                    'points' => $points,
                ];
            }
        }

        // Calcular bonus points (capitán, vice-capitán)
        $bonusPoints = 0;
        
        if ($roster->isCaptain()) {
            $bonusPoints = $basePoints; // Capitán duplica puntos
            $breakdown['captain_bonus'] = [
                'label' => 'Captain Bonus (x2)',
                'points' => $bonusPoints,
            ];
        } elseif ($roster->isViceCaptain()) {
            $bonusPoints = floor($basePoints * 0.5); // Vice-capitán 1.5x puntos
            $breakdown['vice_captain_bonus'] = [
                'label' => 'Vice Captain Bonus (x1.5)',
                'points' => $bonusPoints,
            ];
        }

        $totalPoints = $basePoints + $bonusPoints;

        return self::updateOrCreate(
            [
                'fantasy_team_id' => $roster->fantasy_team_id,
                'player_id' => $roster->player_id,
                'gameweek_id' => $roster->gameweek_id,
            ],
            [
                'base_points' => $basePoints,
                'bonus_points' => $bonusPoints,
                'total_points' => $totalPoints,
                'breakdown' => $breakdown,
            ]
        );
    }

    /**
     * Recalculate total points.
     */
    public function recalculate(): void
    {
        $this->update([
            'total_points' => $this->base_points + $this->bonus_points,
        ]);
    }

    /**
     * Add bonus points.
     */
    public function addBonus(int $points, string $reason = 'bonus'): void
    {
        $breakdown = $this->breakdown ?? [];
        $breakdown[$reason] = [
            'label' => ucfirst($reason),
            'points' => $points,
        ];

        $this->update([
            'bonus_points' => $this->bonus_points + $points,
            'total_points' => $this->base_points + $this->bonus_points + $points,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get breakdown as formatted array.
     */
    public function getFormattedBreakdown(): array
    {
        if (!$this->breakdown) {
            return [];
        }

        $formatted = [];
        foreach ($this->breakdown as $key => $item) {
            $formatted[] = [
                'key' => $key,
                'label' => $item['label'] ?? $key,
                'points' => $item['points'] ?? 0,
            ];
        }

        return $formatted;
    }
}