<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealCompetitionStanding extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'real_competition_id',
        'season_id',
        'stage',
        'group',
        'real_team_id',
        'rank',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_diff',
        'points',
        'form',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'real_competition_id' => 'integer',
        'season_id' => 'integer',
        'real_team_id' => 'integer',
        'rank' => 'integer',
        'played' => 'integer',
        'won' => 'integer',
        'drawn' => 'integer',
        'lost' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'goal_diff' => 'integer',
        'points' => 'integer',
        'form' => 'array',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the competition for this standing.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(RealCompetition::class, 'real_competition_id');
    }

    /**
     * Get the season for this standing.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the team for this standing.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class, 'real_team_id');
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
     * Scope by team.
     */
    public function scopeTeam($query, int $teamId)
    {
        return $query->where('real_team_id', $teamId);
    }

    /**
     * Scope by stage (Regular Season, Playoffs, etc.).
     */
    public function scopeStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    /**
     * Scope by group (Group A, Eastern, etc.).
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope ordered by rank (ascending).
     */
    public function scopeByRank($query)
    {
        return $query->orderBy('rank');
    }

    /**
     * Scope ordered by points (descending).
     */
    public function scopeByPoints($query)
    {
        return $query->orderBy('points', 'desc')
                     ->orderBy('goal_diff', 'desc')
                     ->orderBy('goals_for', 'desc');
    }

    /**
     * Scope top N teams.
     */
    public function scopeTop($query, int $limit = 5)
    {
        return $query->orderBy('rank')->limit($limit);
    }

    /**
     * Scope bottom N teams.
     */
    public function scopeBottom($query, int $limit = 3)
    {
        return $query->orderBy('rank', 'desc')->limit($limit);
    }

    /**
     * Scope teams with positive goal difference.
     */
    public function scopePositiveGoalDiff($query)
    {
        return $query->where('goal_diff', '>', 0);
    }

    /**
     * Scope teams with negative goal difference.
     */
    public function scopeNegativeGoalDiff($query)
    {
        return $query->where('goal_diff', '<', 0);
    }

    /**
     * Scope undefeated teams.
     */
    public function scopeUndefeated($query)
    {
        return $query->where('lost', 0)->where('played', '>', 0);
    }

    /**
     * Scope teams without wins.
     */
    public function scopeWinless($query)
    {
        return $query->where('won', 0)->where('played', '>', 0);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get win percentage.
     */
    public function getWinPercentageAttribute(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }

        return round(($this->won / $this->played) * 100, 2);
    }

    /**
     * Get draw percentage.
     */
    public function getDrawPercentageAttribute(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }

        return round(($this->drawn / $this->played) * 100, 2);
    }

    /**
     * Get loss percentage.
     */
    public function getLossPercentageAttribute(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }

        return round(($this->lost / $this->played) * 100, 2);
    }

    /**
     * Get points per game average.
     */
    public function getPointsPerGameAttribute(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }

        return round($this->points / $this->played, 2);
    }

    /**
     * Get goals per game average.
     */
    public function getGoalsPerGameAttribute(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }

        return round($this->goals_for / $this->played, 2);
    }

    /**
     * Get goals conceded per game average.
     */
    public function getGoalsConcededPerGameAttribute(): float
    {
        if ($this->played === 0) {
            return 0.0;
        }

        return round($this->goals_against / $this->played, 2);
    }

    /**
     * Check if team is undefeated.
     */
    public function isUndefeated(): bool
    {
        return $this->lost === 0 && $this->played > 0;
    }

    /**
     * Check if team is winless.
     */
    public function isWinless(): bool
    {
        return $this->won === 0 && $this->played > 0;
    }

    /**
     * Check if team has positive goal difference.
     */
    public function hasPositiveGoalDiff(): bool
    {
        return $this->goal_diff > 0;
    }

    /**
     * Check if team has negative goal difference.
     */
    public function hasNegativeGoalDiff(): bool
    {
        return $this->goal_diff < 0;
    }

    /**
     * Get form display (last 5 matches: W/D/L).
     */
    public function getFormDisplayAttribute(): ?string
    {
        if (empty($this->form)) {
            return null;
        }

        // Asumiendo que form es un array de resultados ['W', 'L', 'D', 'W', 'W']
        return implode(' ', array_slice($this->form, -5));
    }

    /**
     * Get recent form analysis (last 5 matches points).
     */
    public function getRecentFormPointsAttribute(): int
    {
        if (empty($this->form)) {
            return 0;
        }

        $recentForm = array_slice($this->form, -5);
        $points = 0;

        foreach ($recentForm as $result) {
            $points += match(strtoupper($result)) {
                'W' => 3,
                'D' => 1,
                'L' => 0,
                default => 0,
            };
        }

        return $points;
    }
}