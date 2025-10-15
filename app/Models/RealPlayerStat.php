<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealPlayerStat extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'real_match_id',
        'real_team_id',
        'real_player_id',
        'minutes',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'rating',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'real_match_id' => 'integer',
        'real_team_id' => 'integer',
        'real_player_id' => 'integer',
        'minutes' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'rating' => 'integer',
        'data' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the match for these stats.
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(RealMatch::class, 'real_match_id');
    }

    /**
     * Get the team for these stats.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'real_team_id');
    }

    /**
     * Get the player for these stats.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(RealPlayer::class, 'real_player_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by match.
     */
    public function scopeMatch($query, int $matchId)
    {
        return $query->where('real_match_id', $matchId);
    }

    /**
     * Scope by team.
     */
    public function scopeTeam($query, int $teamId)
    {
        return $query->where('real_team_id', $teamId);
    }

    /**
     * Scope by player.
     */
    public function scopePlayer($query, int $playerId)
    {
        return $query->where('real_player_id', $playerId);
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
     * Scope players with goals or assists.
     */
    public function scopeContributed($query)
    {
        return $query->where(function($q) {
            $q->where('goals', '>', 0)
              ->orWhere('assists', '>', 0);
        });
    }

    /**
     * Scope players who got carded.
     */
    public function scopeCarded($query)
    {
        return $query->where(function($q) {
            $q->where('yellow_cards', '>', 0)
              ->orWhere('red_cards', '>', 0);
        });
    }

    /**
     * Scope players with yellow cards.
     */
    public function scopeYellowCarded($query)
    {
        return $query->where('yellow_cards', '>', 0);
    }

    /**
     * Scope players with red cards.
     */
    public function scopeRedCarded($query)
    {
        return $query->where('red_cards', '>', 0);
    }

    /**
     * Scope players who played (minutes > 0).
     */
    public function scopePlayed($query)
    {
        return $query->where('minutes', '>', 0);
    }

    /**
     * Scope players who didn't play.
     */
    public function scopeDidNotPlay($query)
    {
        return $query->where('minutes', 0);
    }

    /**
     * Scope full match players (90 minutes).
     */
    public function scopeFullMatch($query)
    {
        return $query->where('minutes', '>=', 90);
    }

    /**
     * Scope by minimum rating.
     */
    public function scopeMinRating($query, int $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope top performers (rating >= 8).
     */
    public function scopeTopPerformers($query)
    {
        return $query->where('rating', '>=', 800); // Asumiendo rating x100
    }

    /**
     * Scope ordered by rating (descending).
     */
    public function scopeByRating($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Scope ordered by goals (descending).
     */
    public function scopeByGoals($query)
    {
        return $query->orderBy('goals', 'desc');
    }

    /**
     * Scope ordered by assists (descending).
     */
    public function scopeByAssists($query)
    {
        return $query->orderBy('assists', 'desc');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get total goal contributions (goals + assists).
     */
    public function getGoalContributionsAttribute(): int
    {
        return ($this->goals ?? 0) + ($this->assists ?? 0);
    }

    /**
     * Get total cards.
     */
    public function getTotalCardsAttribute(): int
    {
        return ($this->yellow_cards ?? 0) + ($this->red_cards ?? 0);
    }

    /**
     * Get rating as decimal (divide by 100).
     * Assumes rating is stored as integer (e.g., 850 = 8.5)
     */
    public function getRatingDecimalAttribute(): ?float
    {
        if ($this->rating === null) {
            return null;
        }

        return round($this->rating / 100, 2);
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
     * Check if player contributed to goals.
     */
    public function contributed(): bool
    {
        return $this->goal_contributions > 0;
    }

    /**
     * Check if player got carded.
     */
    public function wasCarded(): bool
    {
        return $this->total_cards > 0;
    }

    /**
     * Check if player got yellow card.
     */
    public function gotYellowCard(): bool
    {
        return $this->yellow_cards > 0;
    }

    /**
     * Check if player got red card.
     */
    public function gotRedCard(): bool
    {
        return $this->red_cards > 0;
    }

    /**
     * Check if player played.
     */
    public function played(): bool
    {
        return $this->minutes > 0;
    }

    /**
     * Check if player played full match.
     */
    public function playedFullMatch(): bool
    {
        return $this->minutes >= 90;
    }

    /**
     * Get performance level based on rating.
     */
    public function getPerformanceLevelAttribute(): ?string
    {
        if ($this->rating === null) {
            return null;
        }

        $ratingDecimal = $this->rating_decimal;

        return match(true) {
            $ratingDecimal >= 9.0 => 'Exceptional',
            $ratingDecimal >= 8.0 => 'Excellent',
            $ratingDecimal >= 7.0 => 'Good',
            $ratingDecimal >= 6.0 => 'Average',
            default => 'Poor',
        };
    }
}