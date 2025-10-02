<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE TIPOS
    // ========================================
    const TYPE_BUY = 1;
    const TYPE_LOAN_OUT = 2;
    const TYPE_LOAN_IN = 3;
    const TYPE_RELEASE = 4;

    const TYPES = [
        self::TYPE_BUY => 'Purchase',
        self::TYPE_LOAN_OUT => 'Loan Out',
        self::TYPE_LOAN_IN => 'Loan In',
        self::TYPE_RELEASE => 'Release',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'player_id',
        'from_fantasy_team_id',
        'to_fantasy_team_id',
        'price',
        'type',
        'effective_at',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'type' => 'integer',
        'effective_at' => 'datetime',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this transfer.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the player for this transfer.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the origin fantasy team (seller).
     */
    public function fromTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'from_fantasy_team_id');
    }

    /**
     * Get the destination fantasy team (buyer).
     */
    public function toTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'to_fantasy_team_id');
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
     * Scope by player.
     */
    public function scopePlayer($query, int $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    /**
     * Scope by from team.
     */
    public function scopeFromTeam($query, int $teamId)
    {
        return $query->where('from_fantasy_team_id', $teamId);
    }

    /**
     * Scope by to team.
     */
    public function scopeToTeam($query, int $teamId)
    {
        return $query->where('to_fantasy_team_id', $teamId);
    }

    /**
     * Scope by type.
     */
    public function scopeType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope purchases.
     */
    public function scopePurchases($query)
    {
        return $query->where('type', self::TYPE_BUY);
    }

    /**
     * Scope loans out.
     */
    public function scopeLoansOut($query)
    {
        return $query->where('type', self::TYPE_LOAN_OUT);
    }

    /**
     * Scope loans in.
     */
    public function scopeLoansIn($query)
    {
        return $query->where('type', self::TYPE_LOAN_IN);
    }

    /**
     * Scope releases.
     */
    public function scopeReleases($query)
    {
        return $query->where('type', self::TYPE_RELEASE);
    }

    /**
     * Scope recent transfers.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('effective_at', 'desc');
    }

    /**
     * Scope effective transfers (already applied).
     */
    public function scopeEffective($query)
    {
        return $query->where('effective_at', '<=', now());
    }

    /**
     * Scope pending transfers (not yet applied).
     */
    public function scopePending($query)
    {
        return $query->where('effective_at', '>', now());
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
     * Get transfer direction for a team.
     */
    public function getDirectionForTeam(int $teamId): ?string
    {
        if ($this->from_fantasy_team_id === $teamId) {
            return 'out';
        }
        
        if ($this->to_fantasy_team_id === $teamId) {
            return 'in';
        }
        
        return null;
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if transfer is a purchase.
     */
    public function isPurchase(): bool
    {
        return $this->type === self::TYPE_BUY;
    }

    /**
     * Check if transfer is a loan out.
     */
    public function isLoanOut(): bool
    {
        return $this->type === self::TYPE_LOAN_OUT;
    }

    /**
     * Check if transfer is a loan in.
     */
    public function isLoanIn(): bool
    {
        return $this->type === self::TYPE_LOAN_IN;
    }

    /**
     * Check if transfer is a release.
     */
    public function isRelease(): bool
    {
        return $this->type === self::TYPE_RELEASE;
    }

    /**
     * Check if transfer is effective (already applied).
     */
    public function isEffective(): bool
    {
        return $this->effective_at->isPast();
    }

    /**
     * Check if transfer is pending (not yet applied).
     */
    public function isPending(): bool
    {
        return $this->effective_at->isFuture();
    }

    /**
     * Check if transfer was from free agent.
     */
    public function isFromFreeAgent(): bool
    {
        return is_null($this->from_fantasy_team_id);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPrice(): string
    {
        if ($this->price == 0) {
            return 'Free';
        }
        
        return number_format($this->price, 2) . 'M';
    }
}