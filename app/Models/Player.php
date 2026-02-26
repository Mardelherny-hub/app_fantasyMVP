<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    // ========================================
    // CONSTANTES DE POSICIONES
    // ========================================
    const POSITION_GK = 1; // Goalkeeper
    const POSITION_DF = 2; // Defender
    const POSITION_MF = 3; // Midfielder
    const POSITION_FW = 4; // Forward

    const POSITIONS = [
        self::POSITION_GK => 'GK',
        self::POSITION_DF => 'DF',
        self::POSITION_MF => 'MF',
        self::POSITION_FW => 'FW',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'known_as',
        'position',
        'nationality',
        'birthdate',
        'height_cm',
        'weight_kg',
        'photo_url',
        'real_player_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'birthdate' => 'date',
        'height_cm' => 'integer',
        'weight_kg' => 'integer',
        'is_active' => 'boolean',
        'real_player_id' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the team history for this player.
     */
    public function teamHistory(): HasMany
    {
        return $this->hasMany(PlayerTeamHistory::class);
    }

    /**
     * Get the match stats for this player.
     */
    public function matchStats(): HasMany
    {
        return $this->hasMany(PlayerMatchStats::class);
    }

    /**
     * Get the fantasy points for this player.
     */
    public function fantasyPoints(): HasMany
    {
        return $this->hasMany(FantasyPoint::class);
    }

    /**
     * Get the player valuations.
     */
    public function valuations(): HasMany
    {
        return $this->hasMany(PlayerValuation::class);
    }

    /**
     * Get the fantasy rosters for this player.
     */
    public function fantasyRosters(): HasMany
    {
        return $this->hasMany(FantasyRoster::class);
    }

    /**
     * Get the listings where this player is for sale.
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Get the transfers involving this player.
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * Get the loans involving this player.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get the real player associated with this fantasy player.
     */
    public function realPlayer(): BelongsTo
    {
        return $this->belongsTo(RealPlayer::class, 'real_player_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to only include active players.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by position.
     */
    public function scopePosition($query, int $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope by nationality.
     */
    public function scopeNationality($query, string $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    /**
     * Scope goalkeepers.
     */
    public function scopeGoalkeepers($query)
    {
        return $query->where('position', self::POSITION_GK);
    }

    /**
     * Scope defenders.
     */
    public function scopeDefenders($query)
    {
        return $query->where('position', self::POSITION_DF);
    }

    /**
     * Scope midfielders.
     */
    public function scopeMidfielders($query)
    {
        return $query->where('position', self::POSITION_MF);
    }

    /**
     * Scope forwards.
     */
    public function scopeForwards($query)
    {
        return $query->where('position', self::POSITION_FW);
    }

    /**
     * Scope players with real player data.
     */
    public function scopeWithRealPlayer($query)
    {
        return $query->whereNotNull('real_player_id');
    }

    /**
     * Scope players without real player data.
     */
    public function scopeWithoutRealPlayer($query)
    {
        return $query->whereNull('real_player_id');
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    public function teamHistories(): HasMany
{
    return $this->hasMany(PlayerTeamHistory::class);
}



    public function getPositionName(): string
    {
        return match($this->position) {
            self::POSITION_GK => 'GK',
            self::POSITION_DF => 'DF',
            self::POSITION_MF => 'MF',
            self::POSITION_FW => 'FW',
            default => 'N/A'
        };
    }

    /**
     * Get the player's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->known_as ?: $this->full_name;
    }

    /**
     * Get the position name.
     */
    public function getPositionNameAttribute(): string
    {
        return self::POSITIONS[$this->position] ?? 'Unknown';
    }

    /**
     * Get the player's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if player is a goalkeeper.
     */
    public function isGoalkeeper(): bool
    {
        return $this->position === self::POSITION_GK;
    }

    /**
     * Check if player is a defender.
     */
    public function isDefender(): bool
    {
        return $this->position === self::POSITION_DF;
    }

    /**
     * Check if player is a midfielder.
     */
    public function isMidfielder(): bool
    {
        return $this->position === self::POSITION_MF;
    }

    /**
     * Check if player is a forward.
     */
    public function isForward(): bool
    {
        return $this->position === self::POSITION_FW;
    }

    /**
     * Get current team (latest team history record).
     */
    public function currentTeam()
    {
        return $this->teamHistory()
                    ->whereNull('to_date')
                    ->latest('from_date')
                    ->first()?->realTeam;
    }

    /**
     * Get market value for a specific season.
     */
    public function marketValue(int $seasonId): ?float
    {
        return $this->valuations()
                    ->where('season_id', $seasonId)
                    ->value('market_value');
    }

    /**
     * Update or create player valuation for a specific season.
     */
    public function updateValuation(array $data, int $seasonId)
    {
        $valuation = $this->valuations()->firstOrNew(['season_id' => $seasonId]);

        if (isset($data['market_value']) && $data['market_value'] !== '') {
            $valuation->market_value = max(0.50, (float) str_replace(',', '.', (string) $data['market_value']));
        }

        $valuation->player_id = $this->id;
        $valuation->season_id = $seasonId;
        // updated_at lo maneja el modelo
        $valuation->save();

        return $valuation;
    }

    /**
     * Check if player has real player data.
     */
    public function hasRealPlayer(): bool
    {
        return $this->real_player_id !== null;
    }

    /**
     * Sync data from real player (for anonimization).
     */
    public function syncFromRealPlayer(): void
    {
        if (!$this->realPlayer) {
            return;
        }

        $real = $this->realPlayer;
        
        // Mapeo de posiciones: GK/DF/MF/FW (string) → 1/2/3/4 (int)
        $positionMap = [
            'GK' => self::POSITION_GK,
            'DF' => self::POSITION_DF,
            'MF' => self::POSITION_MF,
            'FW' => self::POSITION_FW,
        ];

        $this->update([
            'position' => $positionMap[strtoupper($real->position)] ?? $this->position,
            'nationality' => $real->nationality ?? $this->nationality,
            'birthdate' => $real->birthdate ?? $this->birthdate,
            // NO copiamos full_name ni photo_url para mantener anonimización
        ]);
    }

}