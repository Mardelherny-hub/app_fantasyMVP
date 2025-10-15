<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RealPlayer extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'full_name',
        'position',
        'birthdate',
        'nationality',
        'photo_url',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'external_id' => 'integer',
        'birthdate' => 'date',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fantasy player associated with this real player.
     */
    public function fantasyPlayer(): HasOne
    {
        return $this->hasOne(Player::class, 'real_player_id');
    }

    /**
     * Get the team memberships for this player.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(RealTeamMembership::class, 'real_player_id');
    }

    /**
     * Get the lineups for this player.
     */
    public function lineups(): HasMany
    {
        return $this->hasMany(RealLineup::class, 'real_player_id');
    }

    /**
     * Get the events for this player.
     */
    public function events(): HasMany
    {
        return $this->hasMany(RealPlayerEvent::class, 'real_player_id');
    }

    /**
     * Get the stats for this player.
     */
    public function stats(): HasMany
    {
        return $this->hasMany(RealPlayerStat::class, 'real_player_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by position.
     */
    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope by nationality.
     */
    public function scopeNationality($query, string $nationality)
    {
        return $query->where('nationality', $nationality);
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

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the player's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? $this->birthdate->age : null;
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
     * Check if player has a fantasy counterpart.
     */
    public function hasFantasyPlayer(): bool
    {
        return $this->fantasyPlayer()->exists();
    }
}