<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class RealTeam extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',  // ⬅️ AGREGADO
        'name',
        'short_name',
        'country',
        'founded_year',
        'logo_url',
        'stadium',      // ⬅️ AGREGADO
        'meta',         // ⬅️ AGREGADO
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'external_id' => 'integer',   // ⬅️ AGREGADO
        'meta' => 'array',            // ⬅️ AGREGADO
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the player history for this team.
     */
    public function playerHistory(): HasMany
    {
        return $this->hasMany(PlayerTeamHistory::class);
    }

    /**
     * Get the team memberships (Real Players).
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(RealTeamMembership::class, 'real_team_id');
    }

    /**
     * Get home fixtures.
     */
    public function homeFixtures(): HasMany
    {
        return $this->hasMany(\App\Models\RealFixture::class, 'home_team_id');
    }

    /**
     * Get away fixtures.
     */
    public function awayFixtures(): HasMany
    {
        return $this->hasMany(\App\Models\RealFixture::class, 'away_team_id');
    }

    /**
     * Get all matches (home + away).
     */
    public function matches()
    {
        return \App\Models\Match::where('home_team_id', $this->id) // Changed from `::` to `:`
                    ->orWhere('away_team_id', $this->id);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by country.
     */
    public function scopeCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the team's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->short_name ?: $this->name;
    }
}