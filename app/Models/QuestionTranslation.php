<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'locale',
        'text',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the question for this translation.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
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
     * Scope by locale.
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}