<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fixture extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES
    // ========================================
    const STATUS_PENDING = 0;
    const STATUS_FINISHED = 1;

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_FINISHED => 'Finished',
    ];

    const PLAYOFF_QUARTERS = 1;
    const PLAYOFF_SEMIS = 2;
    const PLAYOFF_FINAL = 3;

    const PLAYOFF_ROUNDS = [
        self::PLAYOFF_QUARTERS => 'Quarter Final',
        self::PLAYOFF_SEMIS => 'Semi Final',
        self::PLAYOFF_FINAL => 'Final',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'gameweek_id',
        'home_fantasy_team_id',
        'away_fantasy_team_id',
        'home_goals',
        'away_goals',
        'status',
        'is_playoff',
        'playoff_round',
        'playoff_dependency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'home_goals' => 'integer',
        'away_goals' => 'integer',
        'status' => 'integer',
        'is_playoff' => 'boolean',
        'playoff_round' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this fixture.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the gameweek for this fixture.
     */
    public function gameweek(): BelongsTo
    {
        return $this->belongsTo(Gameweek::class);
    }

    /**
     * Get the home fantasy team.
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'home_fantasy_team_id');
    }

    /**
     * Get the away fantasy team.
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'away_fantasy_team_id');
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
     * Scope by gameweek.
     */
    public function scopeGameweek($query, int $gameweekId)
    {
        return $query->where('gameweek_id', $gameweekId);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending fixtures.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope finished fixtures.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    /**
     * Scope regular season fixtures.
     */
    public function scopeRegularSeason($query)
    {
        return $query->where('is_playoff', false);
    }

    /**
     * Scope playoff fixtures.
     */
    public function scopePlayoffs($query)
    {
        return $query->where('is_playoff', true);
    }

    /**
     * Scope by playoff round.
     */
    public function scopePlayoffRound($query, int $round)
    {
        return $query->where('playoff_round', $round);
    }

    /**
     * Scope fixtures by team.
     */
    public function scopeByTeam($query, int $fantasyTeamId)
    {
        return $query->where('home_fantasy_team_id', $fantasyTeamId)
                     ->orWhere('away_fantasy_team_id', $fantasyTeamId);
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
     * Get the result (e.g., "2-1").
     */
    public function getResultAttribute(): string
    {
        return "{$this->home_goals}-{$this->away_goals}";
    }

    /**
     * Get the fixture title.
     */
    public function getTitleAttribute(): string
    {
        $homeTeam = $this->homeTeam->name ?? 'Home';
        $awayTeam = $this->awayTeam->name ?? 'Away';
        
        if ($this->is_playoff) {
            $round = $this->playoff_round_name;
            return "{$round}: {$homeTeam} vs {$awayTeam}";
        }
        
        return "{$homeTeam} vs {$awayTeam}";
    }

    /**
     * Get playoff round name.
     */
    public function getPlayoffRoundNameAttribute(): ?string
    {
        if (!$this->is_playoff || !$this->playoff_round) {
            return null;
        }
        
        return self::PLAYOFF_ROUNDS[$this->playoff_round] ?? 'Playoff';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if fixture is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if fixture is finished.
     */
    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Check if fixture is playoff.
     */
    public function isPlayoff(): bool
    {
        return $this->is_playoff;
    }

    /**
     * Check if fixture is regular season.
     */
    public function isRegularSeason(): bool
    {
        return !$this->is_playoff;
    }

    /**
     * Mark fixture as finished.
     */
    public function markAsFinished(): void
    {
        $this->update(['status' => self::STATUS_FINISHED]);
    }

    /**
     * Calculate and update goals based on point difference.
     * Regla: cada 10 puntos de diferencia = 1 gol.
     */
    public function calculateGoals(): void
    {
        $homePoints = $this->homeTeam->getGameweekPoints($this->gameweek_id);
        $awayPoints = $this->awayTeam->getGameweekPoints($this->gameweek_id);
        
        $pointDifference = abs($homePoints - $awayPoints);
        $goals = floor($pointDifference / 10);
        
        if ($homePoints > $awayPoints) {
            $this->update([
                'home_goals' => $goals,
                'away_goals' => 0,
            ]);
        } elseif ($awayPoints > $homePoints) {
            $this->update([
                'home_goals' => 0,
                'away_goals' => $goals,
            ]);
        } else {
            // Empate
            $this->update([
                'home_goals' => 0,
                'away_goals' => 0,
            ]);
        }
    }

    /**
     * Get the winner team.
     */
    public function winner(): ?FantasyTeam
    {
        if (!$this->isFinished()) {
            return null;
        }

        if ($this->home_goals > $this->away_goals) {
            return $this->homeTeam;
        }
        
        if ($this->away_goals > $this->home_goals) {
            return $this->awayTeam;
        }
        
        return null; // Draw
    }

    /**
     * Get the loser team.
     */
    public function loser(): ?FantasyTeam
    {
        if (!$this->isFinished()) {
            return null;
        }

        if ($this->home_goals < $this->away_goals) {
            return $this->homeTeam;
        }
        
        if ($this->away_goals < $this->home_goals) {
            return $this->awayTeam;
        }
        
        return null; // Draw
    }

    /**
     * Check if fixture is a draw.
     */
    public function isDraw(): bool
    {
        return $this->isFinished() && $this->home_goals === $this->away_goals;
    }

    /**
     * Check if team won this fixture.
     */
    public function didTeamWin(int $fantasyTeamId): bool
    {
        if (!$this->isFinished()) {
            return false;
        }

        $winner = $this->winner();
        return $winner && $winner->id === $fantasyTeamId;
    }

    /**
     * Check if team lost this fixture.
     */
    public function didTeamLose(int $fantasyTeamId): bool
    {
        if (!$this->isFinished()) {
            return false;
        }

        $loser = $this->loser();
        return $loser && $loser->id === $fantasyTeamId;
    }

    /**
     * Get home team total points for this gameweek.
     */
    public function getHomeTeamPoints(): int
    {
        return $this->homeTeam->getGameweekPoints($this->gameweek_id);
    }

    /**
     * Get away team total points for this gameweek.
     */
    public function getAwayTeamPoints(): int
    {
        return $this->awayTeam->getGameweekPoints($this->gameweek_id);
    }
}