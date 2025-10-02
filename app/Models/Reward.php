<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'label',
        'amount',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the awarded rewards.
     */
    public function awardedRewards(): HasMany
    {
        return $this->hasMany(AwardedReward::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by code.
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Award this reward to a user.
     */
    public function awardTo(int $userId, ?string $sourceType = null, ?int $sourceId = null): AwardedReward
    {
        // Crear awarded reward
        $awardedReward = AwardedReward::create([
            'reward_id' => $this->id,
            'user_id' => $userId,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'amount' => $this->amount,
            'paid_at' => now(),
        ]);

        // Creditar en la billetera del usuario
        $wallet = Wallet::getOrCreateForUser($userId);
        $wallet->credit(
            $this->amount,
            $this->label,
            AwardedReward::class,
            $awardedReward->id
        );

        return $awardedReward;
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 2) . ' CAN';
    }
}