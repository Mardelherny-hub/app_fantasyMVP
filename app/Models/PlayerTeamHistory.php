<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerTeamHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'player_id',
        'real_team_id',
        'from_date',
        'to_date',
        'shirt_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'shirt_number' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the player for this history record.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the real team for this history record.
     */
    public function realTeam(): BelongsTo
    {
        return $this->belongsTo(RealTeam::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get current team assignments (to_date is null).
     */
    public function scopeCurrent($query)
    {
        return $query->whereNull('to_date');
    }

    /**
     * Scope to get past team assignments.
     */
    public function scopePast($query)
    {
        return $query->whereNotNull('to_date');
    }

    /**
     * Scope to get history for a specific date.
     */
    public function scopeAtDate($query, $date)
    {
        return $query->where('from_date', '<=', $date)
                     ->where(function($q) use ($date) {
                         $q->whereNull('to_date')
                           ->orWhere('to_date', '>=', $date);
                     });
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if this is the current team.
     */
    public function isCurrent(): bool
    {
        return is_null($this->to_date);
    }

    /**
     * Get duration in days.
     */
    public function getDurationInDays(): ?int
    {
        if (!$this->to_date) {
            return $this->from_date->diffInDays(now());
        }
        
        return $this->from_date->diffInDays($this->to_date);
    }

    /**
     * Get duration in years (formatted).
     */
    public function getDurationFormatted(): string
    {
        $days = $this->getDurationInDays();
        
        if (!$days) {
            return 'N/A';
        }

        $years = floor($days / 365);
        $months = floor(($days % 365) / 30);

        if ($years > 0) {
            return $months > 0 
                ? "{$years} años, {$months} meses" 
                : "{$years} años";
        }

        return $months > 0 ? "{$months} meses" : "{$days} días";
    }
}