<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizCategory extends Model
{
    use HasFactory;
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    // ========================================
    // RELACIONES
    // ========================================

    public function translations()
    {
        return $this->hasMany(\App\Models\QuizCategoryTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        return $this->translations
            ->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', $fallback);
    }

    // Accesor conveniente: $category->name
    public function getNameAttribute(): ?string
    {
        return optional($this->translation())->name;
    }


    /**
     * Get the questions for this category.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'category_id');
    }

    /**
     * Get the quizzes for this category.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'category_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by code.
     */
    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }


    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Get active questions count.
     */
    public function getActiveQuestionsCount(): int
    {
        return $this->questions()->where('is_active', true)->count();
    }
}