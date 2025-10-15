<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RealCompetition extends Model
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
        'name',
        'country',
        'type',
        'active',
        'external_source',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'external_id' => 'integer',
        'active' => 'boolean',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fixtures for this competition.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(RealFixture::class, 'real_competition_id');
    }

    /**
     * Get the standings for this competition.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(RealCompetitionStanding::class, 'real_competition_id');
    }

    /**
     * Get the teams participating in this competition (through pivot).
     */
    public function teamSeasons(): HasMany
    {
        return $this->hasMany(RealCompetitionTeamSeason::class, 'real_competition_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope only active competitions.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope by country.
     */
    public function scopeCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope by type (league/cup).
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope leagues only.
     */
    public function scopeLeagues($query)
    {
        return $query->where('type', 'league');
    }

    /**
     * Scope cups only.
     */
    public function scopeCups($query)
    {
        return $query->where('type', 'cup');
    }

    /**
     * Scope by external source.
     */
    public function scopeSource($query, string $source)
    {
        return $query->where('external_source', $source);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Check if competition is a league.
     */
    public function isLeague(): bool
    {
        return $this->type === 'league';
    }

    /**
     * Check if competition is a cup.
     */
    public function isCup(): bool
    {
        return $this->type === 'cup';
    }

    /**
     * Check if competition is active.
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }
}