<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RealFixture extends Model
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
        'real_competition_id',
        'season_id',
        'home_team_id',
        'away_team_id',
        'round',
        'venue',
        'status',
        'match_date_utc',
        'match_time_utc',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'external_id' => 'integer',
        'real_competition_id' => 'integer',
        'season_id' => 'integer',
        'home_team_id' => 'integer',
        'away_team_id' => 'integer',
        'match_date_utc' => 'date',
        'match_time_utc' => 'datetime',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the competition for this fixture.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(RealCompetition::class, 'real_competition_id');
    }

    /**
     * Get the season for this fixture.
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

    /**
     * Get the match instance for this fixture.
     */
    public function match(): HasOne
    {
        return $this->hasOne(RealMatch::class, 'real_fixture_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by competition.
     */
    public function scopeCompetition($query, int $competitionId)
    {
        return $query->where('real_competition_id', $competitionId);
    }

    /**
     * Scope by season.
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope scheduled fixtures.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope live fixtures.
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    /**
     * Scope finished fixtures.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    /**
     * Scope postponed fixtures.
     */
    public function scopePostponed($query)
    {
        return $query->where('status', 'postponed');
    }

    /**
     * Scope canceled fixtures.
     */
    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    /**
     * Scope upcoming fixtures (scheduled and in the future).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('match_date_utc', '>=', now()->toDateString())
                     ->orderBy('match_date_utc')
                     ->orderBy('match_time_utc');
    }

    /**
     * Scope by team (home or away).
     */
    public function scopeByTeam($query, int $teamId)
    {
        return $query->where(function($q) use ($teamId) {
            $q->where('home_team_id', $teamId)
              ->orWhere('away_team_id', $teamId);
        });
    }

    /**
     * Scope by round.
     */
    public function scopeRound($query, string $round)
    {
        return $query->where('round', $round);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Check if fixture is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if fixture is live.
     */
    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    /**
     * Check if fixture is finished.
     */
    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    /**
     * Check if fixture is postponed.
     */
    public function isPostponed(): bool
    {
        return $this->status === 'postponed';
    }

    /**
     * Check if fixture is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Check if fixture has match data.
     */
    public function hasMatch(): bool
    {
        return $this->match()->exists();
    }

    /**
     * Get full match datetime.
     */
    public function getMatchDatetimeAttribute(): ?string
    {
        if (!$this->match_date_utc || !$this->match_time_utc) {
            return null;
        }

        return $this->match_date_utc->format('Y-m-d') . ' ' . $this->match_time_utc->format('H:i:s');
    }
}