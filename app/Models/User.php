<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    // use Auditable; // Descomentar si querés auditar cambios en users

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'locale',
        'avatar_url',
        'last_login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the quiz attempts for this user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the audit logs for the user's actions.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderBy('created_at', 'desc');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by locale.
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is manager.
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    /**
     * Check if user is operator.
     */
    public function isOperator(): bool
    {
        return $this->hasRole('operator');
    }

    /**
     * Get the user's preferred avatar (custom or social or Jetstream).
     */
    public function getAvatarAttribute(): ?string
    {
        return $this->avatar_url 
            ?? $this->socialAccounts()->first()?->avatar_url 
            ?? $this->profile_photo_url;
    }

    /**
     * Get the league memberships for this user.
     */
    public function leagueMembers(): HasMany
    {
        return $this->hasMany(LeagueMember::class);
    }

    /**
     * Get the leagues this user belongs to (through league_members).
     */
    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class, 'league_members')
            ->withPivot('role', 'joined_at', 'is_active')
            ->withTimestamps()
            ->wherePivot('is_active', true);
    }

    /**
     * Get all leagues (including inactive memberships).
     */
    public function allLeagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class, 'league_members')
            ->withPivot('role', 'joined_at', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get the fantasy teams owned by this user.
     */
    public function fantasyTeams(): HasMany
    {
        return $this->hasMany(FantasyTeam::class);
    }
}