<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'selected_option_id',
        'is_correct',
        'answered_at',
        'time_taken_ms',
        'points_awarded',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
        'time_taken_ms' => 'integer',
        'points_awarded' => 'integer',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the quiz attempt for this answer.
     */
    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    /**
     * Get the question for this answer.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the selected option.
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by quiz attempt.
     */
    public function scopeAttempt($query, int $attemptId)
    {
        return $query->where('quiz_attempt_id', $attemptId);
    }

    /**
     * Scope correct answers.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope incorrect answers.
     */
    public function scopeIncorrect($query)
    {
        return $query->where('is_correct', false);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if answer is correct.
     */
    public function isCorrect(): bool
    {
        return $this->is_correct;
    }

    /**
     * Check if user answered (didn't skip).
     */
    public function wasAnswered(): bool
    {
        return !is_null($this->selected_option_id);
    }

    /**
     * Get time taken in seconds.
     */
    public function getTimeInSeconds(): ?float
    {
        if (is_null($this->time_taken_ms)) {
            return null;
        }
        
        return round($this->time_taken_ms / 1000, 2);
    }

    /**
     * Get formatted time.
     */
    public function getFormattedTime(): string
    {
        $seconds = $this->getTimeInSeconds();
        
        if (is_null($seconds)) {
            return 'N/A';
        }
        
        return number_format($seconds, 2) . 's';
    }
}