<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SquadDraft extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fantasy_team_id',
        'selected_players',
        'current_step',
        'budget_spent',
        'budget_remaining',
        'limits',
        'is_completed',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'selected_players' => 'array',
        'budget_spent' => 'decimal:2',
        'budget_remaining' => 'decimal:2',
        'limits' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'current_step' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the fantasy team for this draft.
     */
    public function fantasyTeam(): BelongsTo
    {
        return $this->belongsTo(FantasyTeam::class);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the count of selected players.
     */
    public function getPlayerCountAttribute(): int
    {
        return count($this->selected_players ?? []);
    }

    /**
     * Check if draft has minimum players (8).
     */
    public function hasMinimumPlayers(): bool
    {
        return $this->player_count >= 8;
    }

    /**
     * Check if draft is complete (23 players).
     */
    public function isComplete(): bool
    {
        return $this->player_count === 23;
    }

    /**
     * Get remaining slots.
     */
    public function getRemainingSlots(): int
    {
        return 23 - $this->player_count;
    }

    /**
     * Get selected player IDs as array.
     */
    public function getSelectedPlayerIds(): array
    {
        return collect($this->selected_players ?? [])
            ->pluck('player_id')
            ->toArray();
    }

    /**
     * Check if player is already selected.
     */
    public function hasPlayer(int $playerId): bool
    {
        return in_array($playerId, $this->getSelectedPlayerIds());
    }

    /**
     * Get count by position.
     */
    public function getCountByPosition(int $position): int
    {
        $limits = $this->limits ?? [];
        return $limits[$position] ?? 0;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope completed drafts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope incomplete drafts.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope by step.
     */
    public function scopeAtStep($query, int $step)
    {
        return $query->where('current_step', $step);
    }
}