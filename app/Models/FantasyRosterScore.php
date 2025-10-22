<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FantasyRosterScore extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fantasy_roster_id',
        'player_id',
        'gameweek_id',
        'fantasy_team_id',
        'is_starter',
        'is_captain',
        'is_vice_captain',
        'base_points',
        'final_points',
        'breakdown',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_starter' => 'boolean',
        'is_captain' => 'boolean',
        'is_vice_captain' => 'boolean',
        'base_points' => 'integer',
        'final_points' => 'integer',
        'breakdown' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fantasy roster for this score.
     */
    public function fantasyRoster(): BelongsTo
    {
        return $this->belongsTo(FantasyRoster::class);
    }

    /**
     * Get the player for this score.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the gameweek for this score.
     */
    public function gameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class);
    }

    /**
     * Get the fantasy team for this score.
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    /**
     * Get the scores for this roster.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(FantasyRosterScore::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by gameweek.
     */
    public function scopeGameweek($query, int $gameweekId)
    {
        return $query->where('gameweek_id', $gameweekId);
    }

    /**
     * Scope by fantasy team.
     */
    public function scopeFantasyTeam($query, int $fantasyTeamId)
    {
        return $query->where('fantasy_team_id', $fantasyTeamId);
    }

    /**
     * Scope starters only.
     */
    public function scopeStarters($query)
    {
        return $query->where('is_starter', true);
    }

    /**
     * Scope captains only.
     */
    public function scopeCaptains($query)
    {
        return $query->where('is_captain', true);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Apply captain multiplier (x2).
     */
    public function applyCaptainMultiplier(): void
    {
        if ($this->is_captain) {
            $this->final_points = $this->base_points * 2;
            $this->save();
        }
    }

    /**
     * Get points with captain bonus applied.
     */
    public function getPointsWithBonus(): int
    {
        return $this->is_captain ? ($this->base_points * 2) : $this->base_points;
    }
}
