<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\RealMatch;

class Gameweek extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE PLAYOFF ROUNDS
    // ========================================
    const PLAYOFF_QUARTERS = 1; // Cuartos de final (GW28)
    const PLAYOFF_SEMIS = 2; // Semifinales (GW29)
    const PLAYOFF_FINAL = 3; // Final (GW30)

    const PLAYOFF_ROUNDS = [
        self::PLAYOFF_QUARTERS => 'Quarters',
        self::PLAYOFF_SEMIS => 'Semifinals',
        self::PLAYOFF_FINAL => 'Final',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'number',
        'starts_at',
        'ends_at',
        'is_closed',
        'is_playoff',
        'playoff_round',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'number' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_closed' => 'boolean',
        'is_playoff' => 'boolean',
        'playoff_round' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the season for this gameweek.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the rosters for this gameweek.
     */
    public function rosters(): HasMany
    {
        return $this->hasMany(FantasyRoster::class);
    }

    /**
     * Get the fantasy points for this gameweek.
     */
    public function fantasyPoints(): HasMany
    {
        return $this->hasMany(FantasyPoint::class);
    }

    /**
     * Get the fixtures for this gameweek.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * Get real matches that fall within this gameweek's date range.
     */
    public function getRealMatchesAttribute()
    {
        return RealMatch::whereHas('fixture', function ($q) {
            $q->whereBetween('match_date_utc', [
                $this->starts_at->toDateString(),
                $this->ends_at->toDateString(),
            ]);
        })->get();
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by season.
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope open gameweeks (not closed).
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    /**
     * Scope closed gameweeks.
     */
    public function scopeClosed($query)
    {
        return $query->where('is_closed', true);
    }

    /**
     * Scope current gameweek.
     */
    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('starts_at', '<=', $now)
                     ->where('ends_at', '>=', $now)
                     ->orderBy('number');
    }

    /**
     * Scope upcoming gameweeks.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())
                     ->orderBy('starts_at');
    }

    /**
     * Scope past gameweeks.
     */
    public function scopePast($query)
    {
        return $query->where('ends_at', '<', now())
                     ->orderBy('number', 'desc');
    }

    /**
     * Scope regular season gameweeks.
     */
    public function scopeRegularSeason($query)
    {
        return $query->where('is_playoff', false);
    }

    /**
     * Scope playoff gameweeks.
     */
    public function scopePlayoffs($query)
    {
        return $query->where('is_playoff', true)
                     ->orderBy('playoff_round');
    }

    /**
     * Scope by playoff round.
     */
    public function scopePlayoffRound($query, int $round)
    {
        return $query->where('playoff_round', $round);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the playoff round name.
     */
    public function getPlayoffRoundNameAttribute(): ?string
    {
        if (!$this->is_playoff || !$this->playoff_round) {
            return null;
        }
        
        return self::PLAYOFF_ROUNDS[$this->playoff_round] ?? 'Unknown';
    }

    /**
     * Get gameweek display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_playoff) {
            return "GW{$this->number} - {$this->playoff_round_name}";
        }
        
        return "Gameweek {$this->number}";
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if gameweek is currently active.
     */
    public function isActive(): bool
    {
        $now = now();
        return $now->between($this->starts_at, $this->ends_at);
    }

    /**
     * Check if gameweek has started.
     */
    public function hasStarted(): bool
    {
        return now()->greaterThanOrEqualTo($this->starts_at);
    }

    /**
     * Check if gameweek has ended.
     */
    public function hasEnded(): bool
    {
        return now()->greaterThan($this->ends_at);
    }

    /**
     * Check if gameweek is upcoming.
     */
    public function isUpcoming(): bool
    {
        return now()->lessThan($this->starts_at);
    }

    /**
     * Check if gameweek is playoff.
     */
    public function isPlayoff(): bool
    {
        return $this->is_playoff;
    }

    /**
     * Check if gameweek is regular season.
     */
    public function isRegularSeason(): bool
    {
        return !$this->is_playoff;
    }

    /**
     * Check if it's quarters.
     */
    public function isQuarters(): bool
    {
        return $this->is_playoff && $this->playoff_round === self::PLAYOFF_QUARTERS;
    }

    /**
     * Check if it's semifinals.
     */
    public function isSemifinals(): bool
    {
        return $this->is_playoff && $this->playoff_round === self::PLAYOFF_SEMIS;
    }

    /**
     * Check if it's final.
     */
    public function isFinal(): bool
    {
        return $this->is_playoff && $this->playoff_round === self::PLAYOFF_FINAL;
    }

    /**
     * Close this gameweek.
     */
    public function close(): void
    {
        $this->update(['is_closed' => true]);
    }

    /**
     * Open this gameweek.
     */
    public function open(): void
    {
        $this->update(['is_closed' => false]);
    }

    /**
     * Get next gameweek.
     */
    public function next()
    {
        return static::where('season_id', $this->season_id)
                     ->where('number', '>', $this->number)
                     ->orderBy('number')
                     ->first();
    }

    /**
     * Get previous gameweek.
     */
    public function previous()
    {
        return static::where('season_id', $this->season_id)
                     ->where('number', '<', $this->number)
                     ->orderBy('number', 'desc')
                     ->first();
    }
}