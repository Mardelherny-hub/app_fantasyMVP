<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerValuation extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    protected $table = 'player_valuations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'player_id',
        'season_id',
        'market_value',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'market_value' => 'decimal:2',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to auto-set updated_at.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->updated_at = now();
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the player for this valuation.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the season for this valuation.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by player.
     */
    public function scopePlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope by season.
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope expensive players (high value).
     */
    public function scopeExpensive($query, float $minValue = 10.00)
    {
        return $query->where('market_value', '>=', $minValue);
    }

    /**
     * Scope cheap players (low value).
     */
    public function scopeCheap($query, float $maxValue = 5.00)
    {
        return $query->where('market_value', '<=', $maxValue);
    }

    /**
     * Scope ordered by value (descending).
     */
    public function scopeByValue($query)
    {
        return $query->orderBy('market_value', 'desc');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Increase market value.
     */
    public function increase(float $amount): void
    {
        $this->update(['market_value' => $this->market_value + $amount]);
    }

    /**
     * Decrease market value.
     */
    public function decrease(float $amount): void
    {
        $newValue = max(0.50, $this->market_value - $amount); // Mínimo 0.50
        $this->update(['market_value' => $newValue]);
    }

    /**
     * Set market value.
     */
    public function setValue(float $value): void
    {
        $this->update(['market_value' => max(0.50, $value)]);
    }

    /**
     * Get formatted market value.
     */
    public function getFormattedValue(): string
    {
        return number_format($this->market_value, 2) . 'M';
    }
}