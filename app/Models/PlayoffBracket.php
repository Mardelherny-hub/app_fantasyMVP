<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayoffBracket extends Model
{
    use HasFactory;

    // ========================================
    // CONSTANTES
    // ========================================
    const PHASE_QUARTERS = 1; // GW28: 4° vs 5°
    const PHASE_SEMIS = 2; // GW29: 2° vs 3° y Winner(Q) vs 1°
    const PHASE_FINAL = 3; // GW30: Winner(S1) vs Winner(S2)

    const PHASES = [
        self::PHASE_QUARTERS => 'Quarter Final',
        self::PHASE_SEMIS => 'Semi Final',
        self::PHASE_FINAL => 'Final',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'season_id',
        'phase',
        'match_label',
        'home_fantasy_team_id',
        'away_fantasy_team_id',
        'winner_fantasy_team_id',
        'fixture_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phase' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this bracket.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the season for this bracket.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
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

    /**
     * Get the winner fantasy team.
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class, 'winner_fantasy_team_id');
    }

    /**
     * Get the fixture for this bracket match.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
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
     * Scope by season.
     */
    public function scopeSeason($query, int $seasonId)
    {
        return $query->where('season_id', $seasonId);
    }

    /**
     * Scope by phase.
     */
    public function scopePhase($query, int $phase)
    {
        return $query->where('phase', $phase);
    }

    /**
     * Scope quarters.
     */
    public function scopeQuarters($query)
    {
        return $query->where('phase', self::PHASE_QUARTERS);
    }

    /**
     * Scope semifinals.
     */
    public function scopeSemis($query)
    {
        return $query->where('phase', self::PHASE_SEMIS);
    }

    /**
     * Scope final.
     */
    public function scopeFinal($query)
    {
        return $query->where('phase', self::PHASE_FINAL);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the phase name.
     */
    public function getPhaseNameAttribute(): string
    {
        return self::PHASES[$this->phase] ?? 'Unknown';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if match is completed.
     */
    public function isCompleted(): bool
    {
        return !is_null($this->winner_fantasy_team_id);
    }

    /**
     * Check if match is pending.
     */
    public function isPending(): bool
    {
        return is_null($this->winner_fantasy_team_id);
    }

    /**
     * Set winner from fixture result.
     */
    public function setWinnerFromFixture(): void
    {
        if (!$this->fixture || !$this->fixture->isFinished()) {
            return;
        }

        $winner = $this->fixture->winner();
        
        if ($winner) {
            $this->update(['winner_fantasy_team_id' => $winner->id]);
        }
    }

    /**
     * Generate playoff brackets for a league (Page Playoff format).
     */
    public static function generateForLeague(League $league): void
    {
        // Obtener top 5
        $standings = LeagueStanding::league($league->id)
            ->current()
            ->byPosition()
            ->limit(5)
            ->get();

        if ($standings->count() < 5) {
            return; // No hay suficientes equipos
        }

        $first = $standings[0]->fantasy_team_id;
        $second = $standings[1]->fantasy_team_id;
        $third = $standings[2]->fantasy_team_id;
        $fourth = $standings[3]->fantasy_team_id;
        $fifth = $standings[4]->fantasy_team_id;

        // GW28: Cuartos - 4° vs 5°
        self::create([
            'league_id' => $league->id,
            'season_id' => $league->season_id,
            'phase' => self::PHASE_QUARTERS,
            'match_label' => '4th vs 5th',
            'home_fantasy_team_id' => $fourth,
            'away_fantasy_team_id' => $fifth,
        ]);

        // GW29: Semifinales
        // Semi 1: 2° vs 3°
        self::create([
            'league_id' => $league->id,
            'season_id' => $league->season_id,
            'phase' => self::PHASE_SEMIS,
            'match_label' => '2nd vs 3rd',
            'home_fantasy_team_id' => $second,
            'away_fantasy_team_id' => $third,
        ]);

        // Semi 2: Winner(Q) vs 1° - se completará después
        self::create([
            'league_id' => $league->id,
            'season_id' => $league->season_id,
            'phase' => self::PHASE_SEMIS,
            'match_label' => 'Winner Q vs 1st',
            'away_fantasy_team_id' => $first,
            // home_fantasy_team_id se completa cuando termine cuartos
        ]);

        // GW30: Final - se completará después
        self::create([
            'league_id' => $league->id,
            'season_id' => $league->season_id,
            'phase' => self::PHASE_FINAL,
            'match_label' => 'Grand Final',
            // Ambos equipos se completan cuando terminen semis
        ]);
    }
}