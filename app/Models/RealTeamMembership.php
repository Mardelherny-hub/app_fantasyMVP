<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealTeamMembership extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'real_team_id',
        'real_player_id',
        'season_id',
        'shirt_number',
        'role',
        'from_date',
        'to_date',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'real_team_id' => 'integer',
        'real_player_id' => 'integer',
        'season_id' => 'integer',
        'shirt_number' => 'integer',
        'from_date' => 'date',
        'to_date' => 'date',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the team for this membership.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'real_team_id');
    }

    /**
     * Get the player for this membership.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(RealPlayer::class, 'real_player_id');
    }

    /**
     * Get the season for this membership.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope current memberships (to_date is null).
     */
    public function scopeCurrent($query)
    {
        return $query->whereNull('to_date');
    }

    /**
     * Scope past memberships (to_date is not null).
     */
    public function scopePast($query)
    {
        return $query->whereNotNull('to_date');
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
     * Scope by season.
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope active during a specific date.
     */
    public function scopeActiveOn($query, $date)
    {
        return $query->where('from_date', '<=', $date)
                     ->where(function($q) use ($date) {
                         $q->whereNull('to_date')
                           ->orWhere('to_date', '>=', $date);
                     });
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Check if membership is current (active).
     */
    public function isCurrent(): bool
    {
        return $this->to_date === null;
    }

    /**
     * Check if membership has ended.
     */
    public function hasEnded(): bool
    {
        return $this->to_date !== null;
    }

    /**
     * Get duration in days.
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if (!$this->from_date) {
            return null;
        }

        $endDate = $this->to_date ?? now();
        return $this->from_date->diffInDays($endDate);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * End this membership (set to_date to today).
     */
    public function end(): void
    {
        $this->update(['to_date' => now()]);
    }

    /**
     * Extend membership (remove to_date).
     */
    public function extend(): void
    {
        $this->update(['to_date' => null]);
    }
}