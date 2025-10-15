<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealLineup extends Model
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
        'starter',
        'minutes',
        'position',
        'shirt_number',
        'meta',
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
        'starter' => 'boolean',
        'minutes' => 'integer',
        'shirt_number' => 'integer',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the match for this lineup.
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(RealMatch::class, 'real_match_id');
    }

    /**
     * Get the team for this lineup.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'real_team_id');
    }

    /**
     * Get the player for this lineup.
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
     * Scope starters only.
     */
    public function scopeStarters($query)
    {
        return $query->where('starter', true);
    }

    /**
     * Scope substitutes only.
     */
    public function scopeSubstitutes($query)
    {
        return $query->where('starter', false);
    }

    /**
     * Scope by position.
     */
    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope goalkeepers.
     */
    public function scopeGoalkeepers($query)
    {
        return $query->where('position', 'GK');
    }

    /**
     * Scope defenders.
     */
    public function scopeDefenders($query)
    {
        return $query->where('position', 'DF');
    }

    /**
     * Scope midfielders.
     */
    public function scopeMidfielders($query)
    {
        return $query->where('position', 'MF');
    }

    /**
     * Scope forwards.
     */
    public function scopeForwards($query)
    {
        return $query->where('position', 'FW');
    }

    /**
     * Scope players who played (minutes > 0).
     */
    public function scopePlayed($query)
    {
        return $query->where('minutes', '>', 0);
    }

    /**
     * Scope players who didn't play (minutes = 0 or null).
     */
    public function scopeDidNotPlay($query)
    {
        return $query->where(function($q) {
            $q->where('minutes', 0)
              ->orWhereNull('minutes');
        });
    }

    /**
     * Scope full match players (90 minutes).
     */
    public function scopeFullMatch($query)
    {
        return $query->where('minutes', '>=', 90);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Check if player was a starter.
     */
    public function isStarter(): bool
    {
        return $this->starter === true;
    }

    /**
     * Check if player was a substitute.
     */
    public function isSubstitute(): bool
    {
        return $this->starter === false;
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
     * Check if player is a goalkeeper.
     */
    public function isGoalkeeper(): bool
    {
        return strtoupper($this->position) === 'GK';
    }

    /**
     * Check if player is a defender.
     */
    public function isDefender(): bool
    {
        return strtoupper($this->position) === 'DF';
    }

    /**
     * Check if player is a midfielder.
     */
    public function isMidfielder(): bool
    {
        return strtoupper($this->position) === 'MF';
    }

    /**
     * Check if player is a forward.
     */
    public function isForward(): bool
    {
        return strtoupper($this->position) === 'FW';
    }

    /**
     * Get display status (Starter/Substitute).
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->starter ? 'Starter' : 'Substitute';
    }

    /**
     * Get minutes display (with played indication).
     */
    public function getMinutesDisplayAttribute(): string
    {
        if ($this->minutes === null) {
            return 'N/A';
        }

        if ($this->minutes === 0) {
            return 'Unused';
        }

        return $this->minutes . "'";
    }
}