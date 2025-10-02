<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    const STATUS_ONGOING = 0;
    const STATUS_FINISHED = 1;
    const STATUS_CANCELED = 2;

    const STATUSES = [
        self::STATUS_ONGOING => 'Ongoing',
        self::STATUS_FINISHED => 'Finished',
        self::STATUS_CANCELED => 'Canceled',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'player_id',
        'lender_fantasy_team_id',
        'borrower_fantasy_team_id',
        'starts_gw_id',
        'ends_gw_id',
        'fee',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fee' => 'decimal:2',
        'status' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this loan.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the player for this loan.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the lender fantasy team.
     */
    public function lenderTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'lender_fantasy_team_id');
    }

    /**
     * Get the borrower fantasy team.
     */
    public function borrowerTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'borrower_fantasy_team_id');
    }

    /**
     * Get the start gameweek.
     */
    public function startsGameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class, 'starts_gw_id');
    }

    /**
     * Get the end gameweek.
     */
    public function endsGameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class, 'ends_gw_id');
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
     * Scope by lender team.
     */
    public function scopeLender($query, int $lenderTeamId)
    {
        return $query->where('lender_fantasy_team_id', $lenderTeamId);
    }

    /**
     * Scope by borrower team.
     */
    public function scopeBorrower($query, int $borrowerTeamId)
    {
        return $query->where('borrower_fantasy_team_id', $borrowerTeamId);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope ongoing loans.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    /**
     * Scope finished loans.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    /**
     * Scope active in current gameweek.
     */
    public function scopeActiveInGameweek($query, int $gameweekId)
    {
        return $query->where('status', self::STATUS_ONGOING)
                     ->where('starts_gw_id', '<=', $gameweekId)
                     ->where('ends_gw_id', '>=', $gameweekId);
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
     * Check if loan is ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }

    /**
     * Check if loan is finished.
     */
    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Check if loan is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Check if loan is active in a specific gameweek.
     */
    public function isActiveInGameweek(int $gameweekId): bool
    {
        return $this->isOngoing()
            && $this->starts_gw_id <= $gameweekId
            && $this->ends_gw_id >= $gameweekId;
    }

    /**
     * Check if loan has started.
     */
    public function hasStarted(int $currentGameweekId): bool
    {
        return $currentGameweekId >= $this->starts_gw_id;
    }

    /**
     * Check if loan has ended.
     */
    public function hasEnded(int $currentGameweekId): bool
    {
        return $currentGameweekId > $this->ends_gw_id;
    }

    /**
     * Finish this loan.
     */
    public function finish(): void
    {
        $this->update(['status' => self::STATUS_FINISHED]);
    }

    /**
     * Cancel this loan.
     */
    public function cancel(): void
    {
        if ($this->isOngoing()) {
            $this->update(['status' => self::STATUS_CANCELED]);
            
            // Refund fee if applicable
            if ($this->fee > 0) {
                $this->lenderTeam->updateBudget(-$this->fee);
                $this->borrowerTeam->updateBudget($this->fee);
            }
        }
    }

    /**
     * Get loan duration in gameweeks.
     */
    public function getDuration(): int
    {
        return $this->ends_gw_id - $this->starts_gw_id + 1;
    }

    /**
     * Get remaining gameweeks.
     */
    public function getRemainingGameweeks(int $currentGameweekId): int
    {
        if ($this->hasEnded($currentGameweekId)) {
            return 0;
        }
        
        return max(0, $this->ends_gw_id - $currentGameweekId + 1);
    }

    /**
     * Auto-finish expired loans.
     */
    public static function finishExpiredLoans(int $currentGameweekId): int
    {
        return static::ongoing()
            ->where('ends_gw_id', '<', $currentGameweekId)
            ->update(['status' => self::STATUS_FINISHED]);
    }

    /**
     * Get formatted fee.
     */
    public function getFormattedFee(): string
    {
        if ($this->fee == 0) {
            return 'Free';
        }
        
        return number_format($this->fee, 2) . 'M';
    }
}