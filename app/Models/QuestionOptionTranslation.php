<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOptionTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_option_id',
        'locale',
        'text',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the question option for this translation.
     */
    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by question option.
     */
    public function scopeQuestionOption($query, int $questionOptionId)
    {
        return $query->where('question_option_id', $questionOptionId);
    }

    /**
     * Scope by locale.
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
}