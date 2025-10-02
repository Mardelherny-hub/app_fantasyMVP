<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    // ========================================
    // CONSTANTES DE TIPOS
    // ========================================
    const TYPE_QUICK = 1; // Trivia rápida
    const TYPE_THEMATIC = 2; // Quiz temático
    const TYPE_PVP = 3; // Player vs Player

    const TYPES = [
        self::TYPE_QUICK => 'Quick Trivia',
        self::TYPE_THEMATIC => 'Thematic Quiz',
        self::TYPE_PVP => 'PvP Challenge',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'type',
        'title',
        'locale',
        'questions_count',
        'time_limit_sec',
        'reward_amount',
        'settings',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'integer',
        'questions_count' => 'integer',
        'time_limit_sec' => 'integer',
        'reward_amount' => 'decimal:2',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the category for this quiz.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class, 'category_id');
    }

    /**
     * Get the quiz questions (pivot).
     */
    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Get the quiz attempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by category.
     */
    public function scopeCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope by type.
     */
    public function scopeType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope by locale.
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope active quizzes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope quick quizzes.
     */
    public function scopeQuick($query)
    {
        return $query->where('type', self::TYPE_QUICK);
    }

    /**
     * Scope thematic quizzes.
     */
    public function scopeThematic($query)
    {
        return $query->where('type', self::TYPE_THEMATIC);
    }

    /**
     * Scope PvP quizzes.
     */
    public function scopePvp($query)
    {
        return $query->where('type', self::TYPE_PVP);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the type name.
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if quiz is quick type.
     */
    public function isQuick(): bool
    {
        return $this->type === self::TYPE_QUICK;
    }

    /**
     * Check if quiz is thematic type.
     */
    public function isThematic(): bool
    {
        return $this->type === self::TYPE_THEMATIC;
    }

    /**
     * Check if quiz is PvP type.
     */
    public function isPvp(): bool
    {
        return $this->type === self::TYPE_PVP;
    }

    /**
     * Get random questions for this quiz.
     */
    public function getRandomQuestions()
    {
        $query = Question::active();

        // Filtrar por categoría si existe
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return $query->inRandomOrder()
                     ->limit($this->questions_count)
                     ->get();
    }

    /**
     * Get total attempts count.
     */
    public function getTotalAttemptsCount(): int
    {
        return $this->attempts()->count();
    }

    /**
     * Get completed attempts count.
     */
    public function getCompletedAttemptsCount(): int
    {
        return $this->attempts()
                    ->where('status', QuizAttempt::STATUS_FINISHED)
                    ->count();
    }

    /**
     * Get average score.
     */
    public function getAverageScore(): float
    {
        return $this->attempts()
                    ->where('status', QuizAttempt::STATUS_FINISHED)
                    ->avg('score') ?? 0.0;
    }

    /**
     * Get formatted reward.
     */
    public function getFormattedReward(): string
    {
        if ($this->reward_amount == 0) {
            return 'No reward';
        }
        
        return number_format($this->reward_amount, 2) . ' PES';
    }
}