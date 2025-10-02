<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueMember extends Model
{
    use HasFactory;

    // ========================================
    // CONSTANTES DE ROLES
    // ========================================
    const ROLE_PARTICIPANT = 1; // Usuario normal
    const ROLE_MANAGER = 2; // Co-gestor de la liga
    const ROLE_MODERATOR = 3; // Moderador

    const ROLES = [
        self::ROLE_PARTICIPANT => 'Participant',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_MODERATOR => 'Moderator',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'league_id',
        'user_id',
        'role',
        'joined_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => 'integer',
        'joined_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method to set joined_at automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->joined_at)) {
                $member->joined_at = now();
            }
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the league for this member.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the user for this member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Scope by user.
     */
    public function scopeUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope active members.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by role.
     */
    public function scopeRole($query, int $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope managers.
     */
    public function scopeManagers($query)
    {
        return $query->where('role', self::ROLE_MANAGER);
    }

    /**
     * Scope moderators.
     */
    public function scopeModerators($query)
    {
        return $query->where('role', self::ROLE_MODERATOR);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the role name.
     */
    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if member is participant.
     */
    public function isParticipant(): bool
    {
        return $this->role === self::ROLE_PARTICIPANT;
    }

    /**
     * Check if member is manager.
     */
    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /**
     * Check if member is moderator.
     */
    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    /**
     * Check if member has management privileges.
     */
    public function canManage(): bool
    {
        return in_array($this->role, [self::ROLE_MANAGER, self::ROLE_MODERATOR]);
    }

    /**
     * Promote to manager.
     */
    public function promoteToManager(): void
    {
        $this->update(['role' => self::ROLE_MANAGER]);
    }

    /**
     * Promote to moderator.
     */
    public function promoteToModerator(): void
    {
        $this->update(['role' => self::ROLE_MODERATOR]);
    }

    /**
     * Demote to participant.
     */
    public function demoteToParticipant(): void
    {
        $this->update(['role' => self::ROLE_PARTICIPANT]);
    }

    /**
     * Deactivate member.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate member.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
}