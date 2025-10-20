<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_EXPIRED = 3;

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_EXPIRED => 'Expired',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'listing_id',
        'buyer_fantasy_team_id',
        'offered_price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'offered_price' => 'decimal:2',
        'status' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the listing for this offer.
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get the buyer fantasy team.
     */
    public function buyerTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'buyer_fantasy_team_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by listing.
     */
    public function scopeListing($query, int $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    /**
     * Scope by buyer team.
     */
    public function scopeBuyer($query, int $buyerTeamId)
    {
        return $query->where('buyer_fantasy_team_id', $buyerTeamId);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending offers.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope accepted offers.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope rejected offers.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope recent offers.
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
     * Check if offer is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if offer is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if offer is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if offer is expired by status or time (48h).
     */
    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }
        
        // Check if 48 hours have passed since creation
        return $this->isPending() && $this->created_at->addHours(48)->isPast();
    }

    /**
     * Get total cost including commission (5%).
     */
    public function getTotalCost(): float
    {
        return $this->offered_price * 1.05;
    }

    /**
     * Accept this offer and create transfer.
     */
    public function accept(): ?Transfer
    {
        if (!$this->isPending()) {
            return null;
        }

        $listing = $this->listing;
        
        // Verificar que el listing sigue activo
        if (!$listing->isActive()) {
            return null;
        }

        // Verificar que el comprador tiene presupuesto
        if (!$this->buyerTeam->hasBudget($this->offered_price)) {
            return null;
        }

        // Marcar oferta como aceptada
        $this->update(['status' => self::STATUS_ACCEPTED]);

        // Marcar listing como vendido
        $listing->markAsSold();

        // Rechazar otras ofertas pendientes
        $listing->offers()
                ->where('id', '!=', $this->id)
                ->where('status', self::STATUS_PENDING)
                ->update(['status' => self::STATUS_REJECTED]);

        // Crear transfer
        $transfer = Transfer::create([
            'league_id' => $listing->league_id,
            'player_id' => $listing->player_id,
            'from_fantasy_team_id' => $listing->fantasy_team_id,
            'to_fantasy_team_id' => $this->buyer_fantasy_team_id,
            'price' => $this->offered_price,
            'type' => Transfer::TYPE_BUY,
            'effective_at' => now(),
        ]);

        // Actualizar presupuestos
        $listing->fantasyTeam->updateBudget($this->offered_price); // Vendedor recibe dinero
        $this->buyerTeam->updateBudget(-$this->offered_price); // Comprador paga

        return $transfer;
    }

    /**
     * Reject this offer.
     */
    public function reject(): void
    {
        if ($this->isPending()) {
            $this->update(['status' => self::STATUS_REJECTED]);
        }
    }

    /**
     * Mark as expired.
     */
    public function markAsExpired(): void
    {
        if ($this->isPending()) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }

    /**
     * Check if buyer can afford this offer.
     */
    public function canAfford(): bool
    {
        return $this->buyerTeam->hasBudget($this->offered_price);
    }

    /**
     * Get price difference with listing price.
     */
    public function getPriceDifference(): float
    {
        return $this->offered_price - $this->listing->price;
    }

    /**
     * Check if offer is higher than listing price.
     */
    public function isHigherThanListingPrice(): bool
    {
        return $this->offered_price > $this->listing->price;
    }
}