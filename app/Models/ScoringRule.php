<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoringRule extends Model
{
    use HasFactory;
    use Auditable;

    // ========================================
    // CONSTANTES DE CÓDIGOS COMUNES
    // ========================================
    // Goles por posición
    const CODE_GOAL_GK = 'goal_gk';
    const CODE_GOAL_DF = 'goal_df';
    const CODE_GOAL_MF = 'goal_mf';
    const CODE_GOAL_FW = 'goal_fw';

    // Asistencias
    const CODE_ASSIST = 'assist';

    // Clean sheets por posición
    const CODE_CS_GK = 'cs_gk';
    const CODE_CS_DF = 'cs_df';
    const CODE_CS_MF = 'cs_mf';

    // Minutos jugados
    const CODE_MINUTES_0_59 = 'minutes_0_59';
    const CODE_MINUTES_60_PLUS = 'minutes_60_plus';

    // Tarjetas
    const CODE_YELLOW = 'yellow_card';
    const CODE_RED = 'red_card';

    // Porteros
    const CODE_SAVE_3 = 'save_per_3'; // Por cada 3 atajadas
    const CODE_PENALTY_SAVED = 'penalty_saved';
    const CODE_PENALTY_MISSED = 'penalty_missed';

    // Goles recibidos
    const CODE_GOALS_CONCEDED_2_GK = 'goals_conceded_2_gk';
    const CODE_GOALS_CONCEDED_2_DF = 'goals_conceded_2_df';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'code',
        'label',
        'points',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the season for this rule.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
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
     * Scope by code.
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope positive points rules.
     */
    public function scopePositive($query)
    {
        return $query->where('points', '>', 0);
    }

    /**
     * Scope negative points rules.
     */
    public function scopeNegative($query)
    {
        return $query->where('points', '<', 0);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if rule gives positive points.
     */
    public function isPositive(): bool
    {
        return $this->points > 0;
    }

    /**
     * Check if rule gives negative points.
     */
    public function isNegative(): bool
    {
        return $this->points < 0;
    }

    /**
     * Get absolute points value.
     */
    public function getAbsolutePoints(): int
    {
        return abs($this->points);
    }

    /**
     * Apply this rule to player stats.
     * Returns points earned for this specific rule.
     */
    public function applyTo(PlayerMatchStats $stats): int
    {
        $player = $stats->player;
        $points = 0;

        switch ($this->code) {
            // Goles
            case self::CODE_GOAL_GK:
                if ($player->isGoalkeeper()) {
                    $points = $stats->goals * $this->points;
                }
                break;
            case self::CODE_GOAL_DF:
                if ($player->isDefender()) {
                    $points = $stats->goals * $this->points;
                }
                break;
            case self::CODE_GOAL_MF:
                if ($player->isMidfielder()) {
                    $points = $stats->goals * $this->points;
                }
                break;
            case self::CODE_GOAL_FW:
                if ($player->isForward()) {
                    $points = $stats->goals * $this->points;
                }
                break;

            // Asistencias
            case self::CODE_ASSIST:
                $points = $stats->assists * $this->points;
                break;

            // Clean sheets
            case self::CODE_CS_GK:
                if ($player->isGoalkeeper() && $stats->clean_sheet) {
                    $points = $this->points;
                }
                break;
            case self::CODE_CS_DF:
                if ($player->isDefender() && $stats->clean_sheet) {
                    $points = $this->points;
                }
                break;
            case self::CODE_CS_MF:
                if ($player->isMidfielder() && $stats->clean_sheet) {
                    $points = $this->points;
                }
                break;

            // Minutos jugados
            case self::CODE_MINUTES_0_59:
                if ($stats->minutes > 0 && $stats->minutes < 60) {
                    $points = $this->points;
                }
                break;
            case self::CODE_MINUTES_60_PLUS:
                if ($stats->minutes >= 60) {
                    $points = $this->points;
                }
                break;

            // Tarjetas
            case self::CODE_YELLOW:
                $points = $stats->yellow * $this->points;
                break;
            case self::CODE_RED:
                $points = $stats->red * $this->points;
                break;

            // Atajadas (porteros)
            case self::CODE_SAVE_3:
                if ($player->isGoalkeeper()) {
                    $points = floor($stats->saves / 3) * $this->points;
                }
                break;

            // Goles recibidos
            case self::CODE_GOALS_CONCEDED_2_GK:
                if ($player->isGoalkeeper()) {
                    $points = floor($stats->conceded / 2) * $this->points;
                }
                break;
            case self::CODE_GOALS_CONCEDED_2_DF:
                if ($player->isDefender()) {
                    $points = floor($stats->conceded / 2) * $this->points;
                }
                break;
        }

        return $points;
    }
}