<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealPlayerEvent extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE TIPOS DE EVENTOS
    // ========================================
    const TYPE_GOAL = 'goal';
    const TYPE_ASSIST = 'assist';
    const TYPE_YELLOW = 'yellow';
    const TYPE_RED = 'red';
    const TYPE_OWN_GOAL = 'own_goal';
    const TYPE_SUB_IN = 'sub_in';
    const TYPE_SUB_OUT = 'sub_out';
    const TYPE_PENALTY_SCORED = 'penalty_scored';
    const TYPE_PENALTY_MISSED = 'penalty_missed';

    const TYPES = [
        self::TYPE_GOAL => 'Goal',
        self::TYPE_ASSIST => 'Assist',
        self::TYPE_YELLOW => 'Yellow Card',
        self::TYPE_RED => 'Red Card',
        self::TYPE_OWN_GOAL => 'Own Goal',
        self::TYPE_SUB_IN => 'Substitution In',
        self::TYPE_SUB_OUT => 'Substitution Out',
        self::TYPE_PENALTY_SCORED => 'Penalty Scored',
        self::TYPE_PENALTY_MISSED => 'Penalty Missed',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'real_match_id',
        'real_team_id',
        'real_player_id',
        'type',
        'minute',
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
        'minute' => 'integer',
        'data' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the match for this event.
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(RealMatch::class, 'real_match_id');
    }

    /**
     * Get the team for this event.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'real_team_id');
    }

    /**
     * Get the player for this event.
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
     * Scope by event type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope goals only.
     */
    public function scopeGoals($query)
    {
        return $query->where('type', self::TYPE_GOAL);
    }

    /**
     * Scope assists only.
     */
    public function scopeAssists($query)
    {
        return $query->where('type', self::TYPE_ASSIST);
    }

    /**
     * Scope yellow cards only.
     */
    public function scopeYellowCards($query)
    {
        return $query->where('type', self::TYPE_YELLOW);
    }

    /**
     * Scope red cards only.
     */
    public function scopeRedCards($query)
    {
        return $query->where('type', self::TYPE_RED);
    }

    /**
     * Scope cards (yellow or red).
     */
    public function scopeCards($query)
    {
        return $query->whereIn('type', [self::TYPE_YELLOW, self::TYPE_RED]);
    }

    /**
     * Scope own goals only.
     */
    public function scopeOwnGoals($query)
    {
        return $query->where('type', self::TYPE_OWN_GOAL);
    }

    /**
     * Scope substitutions (in or out).
     */
    public function scopeSubstitutions($query)
    {
        return $query->whereIn('type', [self::TYPE_SUB_IN, self::TYPE_SUB_OUT]);
    }

    /**
     * Scope penalties (scored or missed).
     */
    public function scopePenalties($query)
    {
        return $query->whereIn('type', [self::TYPE_PENALTY_SCORED, self::TYPE_PENALTY_MISSED]);
    }

    /**
     * Scope ordered by minute.
     */
    public function scopeOrderedByMinute($query)
    {
        return $query->orderBy('minute');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the event type name.
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Check if event is a goal.
     */
    public function isGoal(): bool
    {
        return $this->type === self::TYPE_GOAL;
    }

    /**
     * Check if event is an assist.
     */
    public function isAssist(): bool
    {
        return $this->type === self::TYPE_ASSIST;
    }

    /**
     * Check if event is a yellow card.
     */
    public function isYellowCard(): bool
    {
        return $this->type === self::TYPE_YELLOW;
    }

    /**
     * Check if event is a red card.
     */
    public function isRedCard(): bool
    {
        return $this->type === self::TYPE_RED;
    }

    /**
     * Check if event is a card (yellow or red).
     */
    public function isCard(): bool
    {
        return in_array($this->type, [self::TYPE_YELLOW, self::TYPE_RED]);
    }

    /**
     * Check if event is an own goal.
     */
    public function isOwnGoal(): bool
    {
        return $this->type === self::TYPE_OWN_GOAL;
    }

    /**
     * Check if event is a substitution.
     */
    public function isSubstitution(): bool
    {
        return in_array($this->type, [self::TYPE_SUB_IN, self::TYPE_SUB_OUT]);
    }

    /**
     * Check if event is a penalty.
     */
    public function isPenalty(): bool
    {
        return in_array($this->type, [self::TYPE_PENALTY_SCORED, self::TYPE_PENALTY_MISSED]);
    }

    /**
     * Get minute display with apostrophe.
     */
    public function getMinuteDisplayAttribute(): string
    {
        return $this->minute ? $this->minute . "'" : 'N/A';
    }

    /**
     * Get event icon (for display purposes).
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_GOAL => '⚽',
            self::TYPE_ASSIST => '🅰️',
            self::TYPE_YELLOW => '🟨',
            self::TYPE_RED => '🟥',
            self::TYPE_OWN_GOAL => '🥅',
            self::TYPE_SUB_IN => '🔼',
            self::TYPE_SUB_OUT => '🔽',
            self::TYPE_PENALTY_SCORED => '⚽🎯',
            self::TYPE_PENALTY_MISSED => '❌',
            default => '📌',
        };
    }
}