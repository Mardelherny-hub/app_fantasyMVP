<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RealMatch extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'real_fixture_id',
        'status',
        'minute',
        'home_score',
        'away_score',
        'started_at_utc',
        'finished_at_utc',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'external_id' => 'integer',
        'real_fixture_id' => 'integer',
        'minute' => 'integer',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'started_at_utc' => 'datetime',
        'finished_at_utc' => 'datetime',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fixture for this match.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(RealFixture::class, 'real_fixture_id');
    }

    public function playerStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class, 'real_match_id');
    }

    /**
     * Get the lineups for this match.
     */
    public function lineups(): HasMany
    {
        return $this->hasMany(RealLineup::class, 'real_match_id');
    }

    /**
     * Get the player events for this match.
     */
    public function events(): HasMany
    {
        return $this->hasMany(RealPlayerEvent::class, 'real_match_id');
    }

    /**
     * Get the player stats for this match.
     */
    public function stats(): HasMany
    {
        return $this->hasMany(RealPlayerStat::class, 'real_match_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope live matches.
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    /**
     * Scope finished matches.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    /**
     * Scope halftime matches.
     */
    public function scopeHalftime($query)
    {
        return $query->where('status', 'ht');
    }

    /**
     * Scope fulltime matches.
     */
    public function scopeFulltime($query)
    {
        return $query->where('status', 'ft');
    }

    /**
     * Scope postponed matches.
     */
    public function scopePostponed($query)
    {
        return $query->where('status', 'postponed');
    }

    /**
     * Scope canceled matches.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    /**
     * Scope matches with goals.
     */
    public function scopeWithGoals($query)
    {
        return $query->where(function($q) {
            $q->where('home_score', '>', 0)
              ->orWhere('away_score', '>', 0);
        });
    }

    /**
     * Scope high-scoring matches (4+ goals).
     */
    public function scopeHighScoring($query)
    {
        return $query->whereRaw('(home_score + away_score) >= 4');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Check if match is live.
     */
    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    /**
     * Check if match is finished.
     */
    public function isFinished(): bool
    {
        return $this->status === 'finished' || $this->status === 'ft';
    }

    /**
     * Check if match is at halftime.
     */
    public function isHalftime(): bool
    {
        return $this->status === 'ht';
    }

    /**
     * Check if match is postponed.
     */
    public function isPostponed(): bool
    {
        return $this->status === 'postponed';
    }

    /**
     * Check if match is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Get total goals in the match.
     */
    public function getTotalGoalsAttribute(): int
    {
        return ($this->home_score ?? 0) + ($this->away_score ?? 0);
    }

    /**
     * Check if match is a draw.
     */
    public function isDraw(): bool
    {
        return $this->isFinished() && $this->home_score === $this->away_score;
    }

    /**
     * Check if home team won.
     */
    public function isHomeWin(): bool
    {
        return $this->isFinished() && $this->home_score > $this->away_score;
    }

    /**
     * Check if away team won.
     */
    public function isAwayWin(): bool
    {
        return $this->isFinished() && $this->away_score > $this->home_score;
    }

    /**
     * Get match duration in minutes.
     */
    public function getDurationInMinutesAttribute(): ?int
    {
        if (!$this->started_at_utc || !$this->finished_at_utc) {
            return null;
        }

        return $this->started_at_utc->diffInMinutes($this->finished_at_utc);
    }

    /**
     * Get the winner team id (from fixture relationship).
     */
    public function getWinnerTeamIdAttribute(): ?int
    {
        if (!$this->isFinished() || $this->isDraw()) {
            return null;
        }

        if ($this->isHomeWin()) {
            return $this->fixture->home_team_id ?? null;
        }

        return $this->fixture->away_team_id ?? null;
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Mark match as finished.
     */
    public function markAsFinished(): void
    {
        $this->update([
            'status' => 'finished',
            'finished_at_utc' => now(),
        ]);
    }

    /**
     * Update live score.
     */
    public function updateScore(int $homeScore, int $awayScore, ?int $minute = null): void
    {
        $data = [
            'home_score' => $homeScore,
            'away_score' => $awayScore,
        ];

        if ($minute !== null) {
            $data['minute'] = $minute;
        }

        $this->update($data);
    }
}