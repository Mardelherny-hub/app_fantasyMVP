<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'is_correct',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the question for this option.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the translations for this option.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(QuestionOptionTranslation::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by question.
     */
    public function scopeQuestion($query, int $questionId)
    {
        return $query->where('question_id', $questionId);
    }

    /**
     * Scope correct options.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope incorrect options.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    /**
     * Scope ordered by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if option is correct.
     */
    public function isCorrect(): bool
    {
        return $this->is_correct;
    }

    /**
     * Get option text for specific locale.
     */
    public function getText(string $locale = 'es'): ?string
    {
        return $this->translations()
                    ->where('locale', $locale)
                    ->value('text');
    }
}