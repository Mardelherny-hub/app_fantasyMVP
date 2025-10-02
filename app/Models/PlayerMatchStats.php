<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatchStats extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'match_id',
        'player_id',
        'minutes',
        'goals',
        'assists',
        'shots',
        'saves',
        'yellow',
        'red',
        'clean_sheet',
        'conceded',
        'rating',
        'raw',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'minutes' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'shots' => 'integer',
        'saves' => 'integer',
        'yellow' => 'integer',
        'red' => 'integer',
        'clean_sheet' => 'boolean',
        'conceded' => 'integer',
        'rating' => 'decimal:2',
        'raw' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the match for these stats.
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(FootballMatch::class, 'match_id');
    }

    /**
     * Get the player for these stats.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by player.
     */
    public function scopePlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope by match.
     */
    public function scopeMatch($query, int $matchId)
    {
        return $query->where('match_id', $matchId);
    }

    /**
     * Scope players who scored.
     */
    public function scopeScored($query)
    {
        return $query->where('goals', '>', 0);
    }

    /**
     * Scope players who assisted.
     */
    public function scopeAssisted($query)
    {
        return $query->where('assists', '>', 0);
    }

    /**
     * Scope players with clean sheet.
     */
    public function scopeCleanSheet($query)
    {
        return $query->where('clean_sheet', true);
    }

    /**
     * Scope players who got carded.
     */
    public function scopeCarded($query)
    {
        return $query->where(function($q) {
            $q->where('yellow', '>', 0)
              ->orWhere('red', '>', 0);
        });
    }

    /**
     * Scope by season (through match).
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->whereHas('match', function($q) use ($seasonId) {
            $q->where('season_id', $seasonId);
        });
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if player played the full match (90+ minutes).
     */
    public function playedFullMatch(): bool
    {
        return $this->minutes >= 90;
    }

    /**
     * Check if player was subbed in.
     */
    public function wasSubbedIn(): bool
    {
        return $this->minutes > 0 && $this->minutes < 90;
    }

    /**
     * Check if player didn't play.
     */
    public function didNotPlay(): bool
    {
        return $this->minutes === 0;
    }

    /**
     * Check if player scored.
     */
    public function scored(): bool
    {
        return $this->goals > 0;
    }

    /**
     * Check if player assisted.
     */
    public function assisted(): bool
    {
        return $this->assists > 0;
    }

    /**
     * Check if player got yellow card.
     */
    public function gotYellow(): bool
    {
        return $this->yellow > 0;
    }

    /**
     * Check if player got red card.
     */
    public function gotRed(): bool
    {
        return $this->red > 0;
    }

    /**
     * Check if player kept clean sheet.
     */
    public function keptCleanSheet(): bool
    {
        return $this->clean_sheet;
    }

    /**
     * Get total goal contributions (goals + assists).
     */
    public function goalContributions(): int
    {
        return $this->goals + $this->assists;
    }

    /**
     * Calculate basic fantasy points (example logic).
     * This is a simple example - real calculation should use ScoringRules.
     */
    public function calculateBasicPoints(): int
    {
        $points = 0;

        // Minutes played
        if ($this->minutes >= 60) {
            $points += 2;
        } elseif ($this->minutes > 0) {
            $points += 1;
        }

        // Goals (varies by position)
        $player = $this->player;
        if ($player->isGoalkeeper() || $player->isDefender()) {
            $points += $this->goals * 6;
        } elseif ($player->isMidfielder()) {
            $points += $this->goals * 5;
        } else {
            $points += $this->goals * 4;
        }

        // Assists
        $points += $this->assists * 3;

        // Clean sheet
        if ($this->clean_sheet) {
            if ($player->isGoalkeeper() || $player->isDefender()) {
                $points += 4;
            } elseif ($player->isMidfielder()) {
                $points += 1;
            }
        }

        // Saves (GK)
        if ($player->isGoalkeeper()) {
            $points += floor($this->saves / 3);
        }

        // Cards
        $points -= $this->yellow * 1;
        $points -= $this->red * 3;

        return max(0, $points); // No negative points
    }
}