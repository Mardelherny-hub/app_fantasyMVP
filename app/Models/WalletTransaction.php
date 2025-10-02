<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    use HasFactory;

    // ========================================
    // CONSTANTES DE TIPOS
    // ========================================
    const TYPE_CREDIT = 1; // Ingreso
    const TYPE_DEBIT = 2; // Egreso

    const TYPES = [
        self::TYPE_CREDIT => 'Credit',
        self::TYPE_DEBIT => 'Debit',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'reason',
        'reference_type',
        'reference_id',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'integer',
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the wallet for this transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by wallet.
     */
    public function scopeWallet($query, int $walletId)
    {
        return $query->where('wallet_id', $walletId);
    }

    /**
     * Scope by type.
     */
    public function scopeType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope credits.
     */
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    /**
     * Scope debits.
     */
    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }

    /**
     * Scope by reason.
     */
    public function scopeReason($query, string $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Scope recent transactions.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the type name.
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    /**
     * Get signed amount (negative for debits).
     */
    public function getSignedAmountAttribute(): float
    {
        return $this->type === self::TYPE_DEBIT ? -$this->amount : $this->amount;
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if transaction is a credit.
     */
    public function isCredit(): bool
    {
        return $this->type === self::TYPE_CREDIT;
    }

    /**
     * Check if transaction is a debit.
     */
    public function isDebit(): bool
    {
        return $this->type === self::TYPE_DEBIT;
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmount(): string
    {
        $prefix = $this->isCredit() ? '+' : '-';
        return $prefix . number_format($this->amount, 2);
    }
}