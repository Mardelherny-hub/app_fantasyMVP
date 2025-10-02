<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketSettings extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'max_multiplier',
        'trade_window_open',
        'loan_allowed',
        'min_offer_cooldown_h',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_multiplier' => 'decimal:2',
        'trade_window_open' => 'boolean',
        'loan_allowed' => 'boolean',
        'min_offer_cooldown_h' => 'integer',
        'data' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for these settings.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if trade window is open.
     */
    public function isTradeWindowOpen(): bool
    {
        return $this->trade_window_open;
    }

    /**
     * Check if loans are allowed.
     */
    public function areLoansAllowed(): bool
    {
        return $this->loan_allowed;
    }

    /**
     * Open trade window.
     */
    public function openTradeWindow(): void
    {
        $this->update(['trade_window_open' => true]);
    }

    /**
     * Close trade window.
     */
    public function closeTradeWindow(): void
    {
        $this->update(['trade_window_open' => false]);
    }

    /**
     * Enable loans.
     */
    public function enableLoans(): void
    {
        $this->update(['loan_allowed' => true]);
    }

    /**
     * Disable loans.
     */
    public function disableLoans(): void
    {
        $this->update(['loan_allowed' => false]);
    }

    /**
     * Get maximum price for a player based on market value.
     */
    public function getMaxPrice(float $marketValue): float
    {
        return $marketValue * $this->max_multiplier;
    }

    /**
     * Check if price is valid for a player.
     */
    public function isValidPrice(float $price, float $marketValue): bool
    {
        $maxPrice = $this->getMaxPrice($marketValue);
        return $price > 0 && $price <= $maxPrice;
    }

    /**
     * Get minimum time between offers in seconds.
     */
    public function getOfferCooldownSeconds(): int
    {
        return $this->min_offer_cooldown_h * 3600;
    }
}