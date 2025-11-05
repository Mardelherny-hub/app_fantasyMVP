<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FootballMatch extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'football_matches';

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    const STATUS_PENDING = 0;
    const STATUS_LIVE = 1;
    const STATUS_FINISHED = 2;
    const STATUS_POSTPONED = 3;

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_LIVE => 'Live',
        self::STATUS_FINISHED => 'Finished',
        self::STATUS_POSTPONED => 'Postponed',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'matchday',
        'home_team_id',
        'away_team_id',
        'starts_at',
        'status',
        'home_goals',
        'away_goals',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'status' => 'integer',
        'matchday' => 'integer',
        'home_goals' => 'integer',
        'away_goals' => 'integer',
        'data' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function playerStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class, 'match_id');
    }

    /**
     * Get the season for this match.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the home team.
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'home_team_id');
    }

    /**
     * Get the away team.
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'away_team_id');
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
     * Scope by matchday.
     */
    public function scopeMatchday($query, int $matchday)
    {
        return $query->where('matchday', $matchday);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending matches.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope live matches.
     */
    public function scopeLive($query)
    {
        return $query->where('status', self::STATUS_LIVE);
    }

    /**
     * Scope finished matches.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    /**
     * Scope upcoming matches.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())
                     ->where('status', self::STATUS_PENDING)
                     ->orderBy('starts_at');
    }

    /**
     * Scope recent matches.
     */
    public function scopeRecent($query)
    {
        return $query->where('status', self::STATUS_FINISHED)
                     ->orderBy('starts_at', 'desc');
    }

    /**
     * Scope matches by team (home or away).
     */
    public function scopeByTeam($query, int $teamId)
    {
        return $query->where('home_team_id', $teamId)
                     ->orWhere('away_team_id', $teamId);
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

    /**
     * Get the match result (e.g., "2-1").
     */
    public function getResultAttribute(): string
    {
        return "{$this->home_goals}-{$this->away_goals}";
    }

    /**
     * Get the match title.
     */
    public function getTitleAttribute(): string
    {
        return "{$this->homeTeam->display_name} vs {$this->awayTeam->display_name}";
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if match is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if match is live.
     */
    public function isLive(): bool
    {
        return $this->status === self::STATUS_LIVE;
    }

    /**
     * Check if match is finished.
     */
    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Check if match is postponed.
     */
    public function isPostponed(): bool
    {
        return $this->status === self::STATUS_POSTPONED;
    }

    /**
     * Mark match as live.
     */
    public function markAsLive(): void
    {
        $this->update(['status' => self::STATUS_LIVE]);
    }

    /**
     * Mark match as finished.
     */
    public function markAsFinished(): void
    {
        $this->update(['status' => self::STATUS_FINISHED]);
    }

    /**
     * Mark match as postponed.
     */
    public function markAsPostponed(): void
    {
        $this->update(['status' => self::STATUS_POSTPONED]);
    }

    /**
     * Update match score.
     */
    public function updateScore(int $homeGoals, int $awayGoals): void
    {
        $this->update([
            'home_goals' => $homeGoals,
            'away_goals' => $awayGoals,
        ]);
    }

    /**
     * Get the winner team (null if draw).
     */
    public function winner(): ?RealTeam
    {
        if ($this->home_goals > $this->away_goals) {
            return $this->homeTeam;
        }
        
        if ($this->away_goals > $this->home_goals) {
            return $this->awayTeam;
        }
        
        return null; // Draw
    }

    /**
     * Check if match is a draw.
     */
    public function isDraw(): bool
    {
        return $this->isFinished() && $this->home_goals === $this->away_goals;
    }

    /**
     * Check if team won this match.
     */
    public function didTeamWin(int $teamId): bool
    {
        if (!$this->isFinished()) {
            return false;
        }

        if ($this->home_team_id === $teamId) {
            return $this->home_goals > $this->away_goals;
        }

        if ($this->away_team_id === $teamId) {
            return $this->away_goals > $this->home_goals;
        }

        return false;
    }
}