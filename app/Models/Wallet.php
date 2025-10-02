<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'currency',
        'balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the user for this wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for this wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
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
     * Scope by currency.
     */
    public function scopeCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Add funds to wallet.
     */
    public function credit(float $amount, string $reason, ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        $this->increment('balance', $amount);

        return $this->transactions()->create([
            'type' => WalletTransaction::TYPE_CREDIT,
            'amount' => $amount,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Subtract funds from wallet.
     */
    public function debit(float $amount, string $reason, ?string $referenceType = null, ?int $referenceId = null): ?WalletTransaction
    {
        if ($this->balance < $amount) {
            return null; // Fondos insuficientes
        }

        $this->decrement('balance', $amount);

        return $this->transactions()->create([
            'type' => WalletTransaction::TYPE_DEBIT,
            'amount' => $amount,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Get formatted balance.
     */
    public function getFormattedBalance(): string
    {
        return number_format($this->balance, 2) . ' ' . $this->currency;
    }

    /**
     * Get or create wallet for user.
     */
    public static function getOrCreateForUser(int $userId, string $currency = 'PES'): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'currency' => $currency],
            ['balance' => 0]
        );
    }

    /**
     * Transfer funds between wallets.
     */
    public static function transfer(Wallet $from, Wallet $to, float $amount, string $reason): bool
    {
        if ($from->currency !== $to->currency) {
            return false; // Different currencies
        }

        if (!$from->hasSufficientBalance($amount)) {
            return false; // Insufficient funds
        }

        // Debit from source
        $debit = $from->debit($amount, $reason, Wallet::class, $to->id);
        
        if (!$debit) {
            return false;
        }

        // Credit to destination
        $to->credit($amount, $reason, Wallet::class, $from->id);

        return true;
    }
}