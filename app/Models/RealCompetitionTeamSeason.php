<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealCompetitionTeamSeason extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'real_competition_team_season';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'real_competition_id',
        'season_id',
        'real_team_id',
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
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the competition for this participation.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(RealCompetition::class, 'real_competition_id');
    }

    /**
     * Get the season for this participation.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the team for this participation.
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
     * Scope by competition and season.
     */
    public function scopeCompetitionSeason($query, int $competitionId, int $seasonId)
    {
        return $query->where('real_competition_id', $competitionId)
                     ->where('season_id', $seasonId);
    }

    /**
     * Scope teams participating in multiple competitions in a season.
     */
    public function scopeMultipleCompetitions($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId)
                     ->select('real_team_id')
                     ->groupBy('real_team_id')
                     ->havingRaw('COUNT(DISTINCT real_competition_id) > 1');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if this team-competition-season combination exists.
     */
    public static function isRegistered(int $competitionId, int $seasonId, int $teamId): bool
    {
        return static::where('real_competition_id', $competitionId)
                     ->where('season_id', $seasonId)
                     ->where('real_team_id', $teamId)
                     ->exists();
    }

    /**
     * Register a team in a competition for a season.
     */
    public static function register(int $competitionId, int $seasonId, int $teamId): self
    {
        return static::firstOrCreate([
            'real_competition_id' => $competitionId,
            'season_id' => $seasonId,
            'real_team_id' => $teamId,
        ]);
    }

    /**
     * Unregister a team from a competition in a season.
     */
    public static function unregister(int $competitionId, int $seasonId, int $teamId): bool
    {
        return static::where('real_competition_id', $competitionId)
                     ->where('season_id', $seasonId)
                     ->where('real_team_id', $teamId)
                     ->delete();
    }

    /**
     * Get all teams in a competition for a season.
     */
    public static function getTeamsInCompetition(int $competitionId, int $seasonId)
    {
        return static::with('team')
                     ->where('real_competition_id', $competitionId)
                     ->where('season_id', $seasonId)
                     ->get()
                     ->pluck('team');
    }

    /**
     * Get all competitions a team is participating in for a season.
     */
    public static function getTeamCompetitions(int $teamId, int $seasonId)
    {
        return static::with('competition')
                     ->where('real_team_id', $teamId)
                     ->where('season_id', $seasonId)
                     ->get()
                     ->pluck('competition');
    }

    /**
     * Count teams in a competition for a season.
     */
    public static function countTeamsInCompetition(int $competitionId, int $seasonId): int
    {
        return static::where('real_competition_id', $competitionId)
                     ->where('season_id', $seasonId)
                     ->count();
    }

    /**
     * Count competitions a team is participating in for a season.
     */
    public static function countTeamCompetitions(int $teamId, int $seasonId): int
    {
        return static::where('real_team_id', $teamId)
                     ->where('season_id', $seasonId)
                     ->count();
    }
}