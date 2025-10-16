<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class League extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    // ========================================
    // CONSTANTES
    // ========================================
    const TYPE_PRIVATE = 1;
    const TYPE_PUBLIC = 2;

    const PLAYOFF_FORMAT_PAGE = 1; // Page Playoff System
    const PLAYOFF_FORMAT_STANDARD = 2; // Standard bracket

    const TYPES = [
        self::TYPE_PRIVATE => 'Private',
        self::TYPE_PUBLIC => 'Public',
    ];

    const PLAYOFF_FORMATS = [
        self::PLAYOFF_FORMAT_PAGE => 'Page Playoff',
        self::PLAYOFF_FORMAT_STANDARD => 'Standard Bracket',
    ];

    // Status de aprobación de ligas
    const STATUS_PENDING_APPROVAL = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_ARCHIVED = 3;

    const STATUSES = [
        self::STATUS_PENDING_APPROVAL => 'Pending Approval',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_ARCHIVED => 'Archived',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_user_id',
        'season_id',
        'name',
        'code',
        'type',
        'max_participants',
        'auto_fill_bots',
        'is_locked',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'locale',
        'playoff_teams',
        'playoff_format',
        'regular_season_gameweeks',
        'total_gameweeks',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'integer',
        'max_participants' => 'integer',
        'auto_fill_bots' => 'boolean',
        'is_locked' => 'boolean',
        'status' => 'integer',
        'reviewed_at' => 'datetime',
        'playoff_teams' => 'integer',
        'playoff_format' => 'integer',
        'regular_season_gameweeks' => 'integer',
        'total_gameweeks' => 'integer',
        'settings' => 'array',
    ];

    /**
     * Boot method to generate code.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($league) {
            if (empty($league->code)) {
                $league->code = self::generateUniqueCode();
            }
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the owner of the league.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Get the season for this league.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    // Fallback: si no viene season_id, asigna (o crea) la temporada del año actual
    protected static function booted(): void
    {
        static::creating(function (League $league) {
            if (empty($league->season_id)) {
                $season = \App\Models\Season::firstOrCreate(
                    ['name' => (string) now()->year],
                    [] // completa si tu Season requiere otros campos con default
                );
                $league->season_id = $season->id;
            }
        });
    }

    /**
     * Get the members of the league.
     */
    public function members(): HasMany
    {
        return $this->hasMany(LeagueMember::class);
    }

    /**
     * Get the fantasy teams in this league.
     */
    public function fantasyTeams(): HasMany
    {
        return $this->hasMany(FantasyTeam::class);
    }

    /**
     * Get the fixtures for this league.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * Get the market settings for this league.
     */
    public function marketSettings(): HasOne
    {
        return $this->hasOne(MarketSettings::class);
    }

    /**
     * Get the standings for this league.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(LeagueStanding::class);
    }

    /**
     * Get the playoff brackets for this league.
     */
    public function playoffBrackets(): HasMany
    {
        return $this->hasMany(PlayoffBracket::class);
    }

    /**
     * Get the admin who reviewed the league.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by season.
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope by type.
     */
    public function scopeType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope private leagues.
     */
    public function scopePrivate($query)
    {
        return $query->where('type', self::TYPE_PRIVATE);
    }

    /**
     * Scope public leagues.
     */
    public function scopePublic($query)
    {
        return $query->where('type', self::TYPE_PUBLIC);
    }

    /**
     * Scope open leagues (not locked).
     */
    public function scopeOpen($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope locked leagues.
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending approval leagues.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    /**
     * Scope approved leagues.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope rejected leagues.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope archived leagues.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    /**
     * Scope active leagues (approved and not archived).
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // ========================================
    // MÉTODOS DE PLAYOFFS
    // ========================================

    /**
     * Check if league uses playoffs.
     */
    public function hasPlayoffs(): bool
    {
        return $this->playoff_teams > 0;
    }

    /**
     * Check if gameweek is in playoff phase.
     */
    public function isPlayoffGameweek(int $gameweekNumber): bool
    {
        return $gameweekNumber > $this->regular_season_gameweeks;
    }

    /**
     * Get playoff gameweek numbers.
     */
    public function getPlayoffGameweeks(): array
    {
        if (!$this->hasPlayoffs()) {
            return [];
        }

        return range(
            $this->regular_season_gameweeks + 1,
            $this->total_gameweeks
        );
    }

    /**
     * Get current standings (top playoff_teams).
     */
    public function getPlayoffQualifiers()
    {
        return $this->standings()
                    ->orderBy('position')
                    ->limit($this->playoff_teams)
                    ->get();
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Generate unique league code.
     */
    protected static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if league is full.
     */
    public function isFull(): bool
    {
        return $this->fantasyTeams()->count() >= $this->max_participants;
    }

    /**
     * Check if league can start.
     */
    public function canStart(): bool
    {
        $teamCount = $this->fantasyTeams()->count();
        
        // Necesita al menos 2 equipos para empezar
        if ($teamCount < 2) {
            return false;
        }

        // Si auto_fill_bots está activo, puede empezar con cualquier número
        if ($this->auto_fill_bots) {
            return true;
        }

        // Si no hay bots, necesita estar llena
        return $this->isFull();
    }

    /**
     * Lock league (close registration).
     */
    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Unlock league (open registration).
     */
    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
    }

    /**
     * Check if user is owner.
     */
    public function isOwner(int $userId): bool
    {
        return $this->owner_user_id === $userId;
    }

    /**
     * Check if user is member.
     */
    public function hasMember(int $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Get remaining spots.
     */
    public function getRemainingSpots(): int
    {
        return max(0, $this->max_participants - $this->fantasyTeams()->count());
    }

    // ========================================
    // MÉTODOS DE STATUS Y APROBACIÓN
    // ========================================

    /**
     * Check if league is pending approval.
     */
    public function isPendingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING_APPROVAL;
    }

    /**
     * Check if league is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if league is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if league is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Approve league.
     */
    public function approve(int $adminUserId, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $adminUserId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Reject league.
     */
    public function reject(int $adminUserId, string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $adminUserId,
            'reviewed_at' => now(),
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Archive league.
     */
    public function archive(int $adminUserId, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_ARCHIVED,
            'reviewed_by' => $adminUserId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Get status name.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    /**
     * Check if league can accept new members.
     */
    public function canAcceptMembers(): bool
    {
        return $this->isApproved() 
            && !$this->is_locked 
            && !$this->isFull();
    }

    /**
     * Check if league is open for new members.
     */
    public function isOpen(): bool
    {
        return !$this->is_locked 
            && $this->status === self::STATUS_APPROVED 
            && !$this->isFull();
    }
}