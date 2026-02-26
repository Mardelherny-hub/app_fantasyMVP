<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\FantasyRosterScore;

class FantasyTeam extends Model
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
        'league_id',
        'user_id',
        'name',
        'slug',
        'emblem_url',
        'total_points',
        'budget',
        'is_bot',
        'is_squad_complete', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_points' => 'integer',
        'budget' => 'decimal:2',
        'is_bot' => 'boolean',
        'is_squad_complete' => 'boolean',
    ];

    /**
     * Boot method to generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = self::generateUniqueSlug($team->name);
            }
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this team.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the user (owner) for this team.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rosters (player selections) for this team.
     */
    public function rosters(): HasMany
    {
        return $this->hasMany(FantasyRoster::class);
    }

    /**
     * Get the fantasy points for this team.
     */
    public function fantasyPoints(): HasMany
    {
        return $this->hasMany(FantasyPoint::class);
    }

    /**
     * Get home fixtures.
     */
    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_fantasy_team_id');
    }

    /**
     * Get away fixtures.
     */
    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_fantasy_team_id');
    }

    /**
     * Get listings (players for sale).
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Get offers made by this team.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'buyer_fantasy_team_id');
    }

    /**
     * Get transfers where this team is involved.
     */
    public function transfersReceived(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_fantasy_team_id');
    }

    /**
     * Get transfers where this team sold players.
     */
    public function transfersSold(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_fantasy_team_id');
    }

    /**
     * Get loans where this team lent players.
     */
    public function loansLent(): HasMany
    {
        return $this->hasMany(Loan::class, 'lender_fantasy_team_id');
    }

    /**
     * Get loans where this team borrowed players.
     */
    public function loansBorrowed(): HasMany
    {
        return $this->hasMany(Loan::class, 'borrower_fantasy_team_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by league.
     */
    public function scopeLeague($query, int $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope by user.
     */
    public function scopeUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope human teams (not bots).
     */
    public function scopeHuman($query)
    {
        return $query->where('is_bot', false);
    }

    /**
     * Scope bot teams.
     */
    public function scopeBots($query)
    {
        return $query->where('is_bot', true);
    }

    /**
     * Scope ordered by points (descending).
     */
    public function scopeByPoints($query)
    {
        return $query->orderBy('total_points', 'desc');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Generate unique slug from name.
     */
    protected static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Check if team is a bot.
     */
    public function isBot(): bool
    {
        return $this->is_bot;
    }

    /**
     * Check if team is human-controlled.
     */
    public function isHuman(): bool
    {
        return !$this->is_bot;
    }

    /**
     * Add points to total.
     */
    public function addPoints(int $points): void
    {
        $this->increment('total_points', $points);
    }

    /**
     * Subtract points from total.
     */
    public function subtractPoints(int $points): void
    {
        $this->decrement('total_points', $points);
    }

    /**
     * Update budget.
     */
    public function updateBudget(float $amount): void
    {
        $this->update(['budget' => $this->budget + $amount]);
    }

    /**
     * Check if team has enough budget.
     */
    public function hasBudget(float $amount): bool
    {
        return $this->budget >= $amount;
    }

    /**
     * Get roster for specific gameweek.
     */
    public function getRoster(int $gameweekId)
    {
        return $this->rosters()
                    ->where('gameweek_id', $gameweekId)
                    ->with('player')
                    ->get();
    }

    /**
     * Get starters for specific gameweek.
     */
    public function getStarters(int $gameweekId)
    {
        return $this->rosters()
                    ->where('gameweek_id', $gameweekId)
                    ->where('is_starter', true)
                    ->with('player')
                    ->orderBy('slot')
                    ->get();
    }

    /**
     * Get bench for specific gameweek.
     */
    public function getBench(int $gameweekId)
    {
        return $this->rosters()
                    ->where('gameweek_id', $gameweekId)
                    ->where('is_starter', false)
                    ->with('player')
                    ->orderBy('slot')
                    ->get();
    }

    /**
     * Get total points for specific gameweek.
     */
    public function getGameweekPoints(int $gameweekId): int
    {
        return (int) FantasyRosterScore::where('fantasy_team_id', $this->id)
                    ->where('gameweek_id', $gameweekId)
                    ->sum('final_points');
    }
}