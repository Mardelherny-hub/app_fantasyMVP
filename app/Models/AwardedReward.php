<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AwardedReward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reward_id',
        'user_id',
        'source_type',
        'source_id',
        'amount',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the reward for this awarded reward.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get the user who received this reward.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source model (polymorphic).
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by user.
     */
    public function scopeUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by reward.
     */
    public function scopeReward($query, int $rewardId)
    {
        return $query->where('reward_id', $rewardId);
    }

    /**
     * Scope recent awards.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('paid_at', 'desc');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Get formatted amount.
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2) . ' PES';
    }
}