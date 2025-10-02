<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditable;

    // ========================================
    // CONSTANTES DE DIFICULTAD
    // ========================================
    const DIFFICULTY_EASY = 1;
    const DIFFICULTY_MEDIUM = 2;
    const DIFFICULTY_HARD = 3;

    const DIFFICULTIES = [
        self::DIFFICULTY_EASY => 'Easy',
        self::DIFFICULTY_MEDIUM => 'Medium',
        self::DIFFICULTY_HARD => 'Hard',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'difficulty',
        'is_active',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'difficulty' => 'integer',
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the category for this question.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class, 'category_id');
    }

    /**
     * Get the translations for this question.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    /**
     * Get the options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Get the quiz questions (pivot).
     */
    public function quizQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Get the quiz attempt answers.
     */
    public function quizAttemptAnswers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
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
     * Scope by difficulty.
     */
    public function scopeDifficulty($query, int $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope easy questions.
     */
    public function scopeEasy($query)
    {
        return $query->where('difficulty', self::DIFFICULTY_EASY);
    }

    /**
     * Scope medium questions.
     */
    public function scopeMedium($query)
    {
        return $query->where('difficulty', self::DIFFICULTY_MEDIUM);
    }

    /**
     * Scope hard questions.
     */
    public function scopeHard($query)
    {
        return $query->where('difficulty', self::DIFFICULTY_HARD);
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the difficulty name.
     */
    public function getDifficultyNameAttribute(): string
    {
        return self::DIFFICULTIES[$this->difficulty] ?? 'Unknown';
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Get question text for specific locale.
     */
    public function getText(string $locale = 'es'): ?string
    {
        return $this->translations()
                    ->where('locale', $locale)
                    ->value('text');
    }

    /**
     * Get correct option.
     */
    public function getCorrectOption(): ?QuestionOption
    {
        return $this->options()->where('is_correct', true)->first();
    }

    /**
     * Check if option is correct.
     */
    public function isCorrectOption(int $optionId): bool
    {
        return $this->options()
                    ->where('id', $optionId)
                    ->where('is_correct', true)
                    ->exists();
    }

    /**
     * Check if question is easy.
     */
    public function isEasy(): bool
    {
        return $this->difficulty === self::DIFFICULTY_EASY;
    }

    /**
     * Check if question is medium.
     */
    public function isMedium(): bool
    {
        return $this->difficulty === self::DIFFICULTY_MEDIUM;
    }

    /**
     * Check if question is hard.
     */
    public function isHard(): bool
    {
        return $this->difficulty === self::DIFFICULTY_HARD;
    }
}