<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the gameweeks for this season.
     */
    public function gameweeks(): HasMany
    {
        return $this->hasMany(Gameweek::class);
    }

    /**
     * Get the matches for this season.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(\App\Models\FootballMatch::class);
    }

    /**
     * Get the leagues for this season.
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    /**
     * Get the scoring rules for this season.
     */
    public function scoringRules(): HasMany
    {
        return $this->hasMany(ScoringRule::class);
    }

    /**
     * Get the player valuations for this season.
     */
    public function playerValuations(): HasMany
    {
        return $this->hasMany(PlayerValuation::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active season.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get current season (active or most recent).
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)
                     ->orWhere(function($q) {
                         $q->where('ends_at', '>=', now())
                           ->orderBy('starts_at', 'desc');
                     })
                     ->first();
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Activate this season and deactivate others.
     */
    public function activate(): void
    {
        // Desactivar todas las temporadas
        static::query()->update(['is_active' => false]);
        
        // Activar esta temporada
        $this->update(['is_active' => true]);
    }

    /**
     * Check if season is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if season is in progress (dates).
     */
    public function isInProgress(): bool
    {
        $now = now();
        return $now->between($this->starts_at, $this->ends_at);
    }

    /**
     * Check if season has finished.
     */
    public function hasFinished(): bool
    {
        return now()->isAfter($this->ends_at);
    }

    /**
     * Check if season hasn't started yet.
     */
    public function isPending(): bool
    {
        return now()->isBefore($this->starts_at);
    }
}