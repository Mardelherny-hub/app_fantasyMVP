<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FantasyRoster extends Model
{
    use HasFactory;

    // ========================================
    // CONSTANTES DE CAPITANÍA
    // ========================================
    const CAPTAINCY_NONE = 0;
    const CAPTAINCY_CAPTAIN = 1;
    const CAPTAINCY_VICE = 2;

    const CAPTAINCIES = [
        self::CAPTAINCY_NONE => 'None',
        self::CAPTAINCY_CAPTAIN => 'Captain',
        self::CAPTAINCY_VICE => 'Vice Captain',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fantasy_team_id',
        'player_id',
        'gameweek_id',
        'slot',
        'is_starter',
        'captaincy',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'slot' => 'integer',
        'is_starter' => 'boolean',
        'captaincy' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fantasy team for this roster.
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    /**
     * Get the player for this roster.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the gameweek for this roster.
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
     * Scope starters only.
     */
    public function scopeStarters($query)
    {
        return $query->where('is_starter', true);
    }

    /**
     * Scope bench only.
     */
    public function scopeBench($query)
    {
        return $query->where('is_starter', false);
    }

    /**
     * Scope captains.
     */
    public function scopeCaptains($query)
    {
        return $query->where('captaincy', self::CAPTAINCY_CAPTAIN);
    }

    /**
     * Scope vice-captains.
     */
    public function scopeViceCaptains($query)
    {
        return $query->where('captaincy', self::CAPTAINCY_VICE);
    }

    /**
     * Scope ordered by slot.
     */
    public function scopeBySlot($query)
    {
        return $query->orderBy('slot');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the captaincy name.
     */
    public function getCaptaincyNameAttribute(): string
    {
        return self::CAPTAINCIES[$this->captaincy] ?? 'None';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if player is starter.
     */
    public function isStarter(): bool
    {
        return $this->is_starter;
    }

    /**
     * Check if player is on bench.
     */
    public function isBench(): bool
    {
        return !$this->is_starter;
    }

    /**
     * Check if player is captain.
     */
    public function isCaptain(): bool
    {
        return $this->captaincy === self::CAPTAINCY_CAPTAIN;
    }

    /**
     * Check if player is vice-captain.
     */
    public function isViceCaptain(): bool
    {
        return $this->captaincy === self::CAPTAINCY_VICE;
    }

    /**
     * Set as captain.
     */
    public function makeCaptain(): void
    {
        // Remover capitanía de otros jugadores del mismo equipo/gameweek
        static::where('fantasy_team_id', $this->fantasy_team_id)
              ->where('gameweek_id', $this->gameweek_id)
              ->where('captaincy', self::CAPTAINCY_CAPTAIN)
              ->update(['captaincy' => self::CAPTAINCY_NONE]);

        $this->update(['captaincy' => self::CAPTAINCY_CAPTAIN]);
    }

    /**
     * Set as vice-captain.
     */
    public function makeViceCaptain(): void
    {
        // Remover vice-capitanía de otros jugadores del mismo equipo/gameweek
        static::where('fantasy_team_id', $this->fantasy_team_id)
              ->where('gameweek_id', $this->gameweek_id)
              ->where('captaincy', self::CAPTAINCY_VICE)
              ->update(['captaincy' => self::CAPTAINCY_NONE]);

        $this->update(['captaincy' => self::CAPTAINCY_VICE]);
    }

    /**
     * Remove captaincy.
     */
    public function removeCaptaincy(): void
    {
        $this->update(['captaincy' => self::CAPTAINCY_NONE]);
    }

    /**
     * Move to starting lineup.
     */
    public function moveToStarting(int $slot): void
    {
        $this->update([
            'is_starter' => true,
            'slot' => $slot,
        ]);
    }

    /**
     * Move to bench.
     */
    public function moveToBench(int $slot): void
    {
        $this->update([
            'is_starter' => false,
            'slot' => $slot,
            'captaincy' => self::CAPTAINCY_NONE, // No hay capitanes en el banco
        ]);
    }

    /**
     * Swap slots with another roster entry.
     */
    public function swapWith(FantasyRoster $other): void
    {
        $tempSlot = $this->slot;
        $tempStarter = $this->is_starter;

        $this->update([
            'slot' => $other->slot,
            'is_starter' => $other->is_starter,
        ]);

        $other->update([
            'slot' => $tempSlot,
            'is_starter' => $tempStarter,
        ]);
    }
}