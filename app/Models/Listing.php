<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    const STATUS_ACTIVE = 0;
    const STATUS_SOLD = 1;
    const STATUS_WITHDRAWN = 2;
    const STATUS_EXPIRED = 3;

    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_SOLD => 'Sold',
        self::STATUS_WITHDRAWN => 'Withdrawn',
        self::STATUS_EXPIRED => 'Expired',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'fantasy_team_id',
        'player_id',
        'price',
        'status',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'integer',
        'expires_at' => 'datetime',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this listing.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the fantasy team (seller) for this listing.
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    /**
     * Get the player for this listing.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the offers for this listing.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
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
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Scope sold listings.
     */
    public function scopeSold($query)
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    /**
     * Scope expired listings.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope by price range.
     */
    public function scopePriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope ordered by price (ascending).
     */
    public function scopeByPrice($query)
    {
        return $query->orderBy('price');
    }

    /**
     * Scope ordered by recent.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the status name.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if listing is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE 
            && (is_null($this->expires_at) || $this->expires_at->isFuture());
    }

    /**
     * Check if listing is sold.
     */
    public function isSold(): bool
    {
        return $this->status === self::STATUS_SOLD;
    }

    /**
     * Check if listing is withdrawn.
     */
    public function isWithdrawn(): bool
    {
        return $this->status === self::STATUS_WITHDRAWN;
    }

    /**
     * Check if listing is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED 
            || ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Check if listing has expired by time.
     */
    public function hasExpiredByTime(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark as sold.
     */
    public function markAsSold(): void
    {
        $this->update(['status' => self::STATUS_SOLD]);
    }

    /**
     * Withdraw listing.
     */
    public function withdraw(): void
    {
        $this->update(['status' => self::STATUS_WITHDRAWN]);
    }

    /**
     * Mark as expired.
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Get active offers count.
     */
    public function getActiveOffersCount(): int
    {
        return $this->offers()->where('status', Offer::STATUS_PENDING)->count();
    }

    /**
     * Get highest offer.
     */
    public function getHighestOffer(): ?Offer
    {
        return $this->offers()
                    ->where('status', Offer::STATUS_PENDING)
                    ->orderBy('offered_price', 'desc')
                    ->first();
    }

    /**
     * Check if price is valid according to market settings.
     */
    public function isPriceValid(): bool
    {
        $marketSettings = $this->league->marketSettings;
        $valuation = $this->player->valuations()
                                  ->where('season_id', $this->league->season_id)
                                  ->first();

        if (!$valuation || !$marketSettings) {
            return true; // No restrictions
        }

        return $marketSettings->isValidPrice($this->price, $valuation->market_value);
    }

    /**
     * Auto-expire old listings.
     */
    public static function expireOldListings(): int
    {
        return static::active()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => self::STATUS_EXPIRED]);
    }

    /**
     * Check if listing can be withdrawn.
     */
    public function canBeWithdrawn(): bool
    {
        return !$this->offers()
                    ->where('status', Offer::STATUS_ACCEPTED)
                    ->exists();
    }

    /**
     * Get price with commission (5%).
     */
    public function getPriceWithCommission(): float
    {
        return $this->price * 1.05;
    }
}