<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    // ========================================
    // CONSTANTES DE STATUS
    // ========================================
    const STATUS_IN_PROGRESS = 0;
    const STATUS_FINISHED = 1;
    const STATUS_ABANDONED = 2;

    const STATUSES = [
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_FINISHED => 'Finished',
        self::STATUS_ABANDONED => 'Abandoned',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'finished_at',
        'score',
        'correct_count',
        'wrong_count',
        'status',
        'reward_paid',
        'locale',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'score' => 'integer',
        'correct_count' => 'integer',
        'wrong_count' => 'integer',
        'status' => 'integer',
        'reward_paid' => 'boolean',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    /**
     * Get the quiz for this attempt.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user for this attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the answers for this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by quiz.
     */
    public function scopeQuiz($query, int $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    /**
     * Scope by user.
     */
    public function scopeUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by status.
     */
    public function scopeStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope in progress attempts.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope finished attempts.
     */
    public function scopeFinished($query)
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    /**
     * Scope recent attempts.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('started_at', 'desc');
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Get the status name.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    /**
     * Get accuracy percentage.
     */
    public function getAccuracyAttribute(): float
    {
        $total = $this->correct_count + $this->wrong_count;
        
        if ($total === 0) {
            return 0.0;
        }
        
        return round(($this->correct_count / $total) * 100, 2);
    }

    /**
     * Get duration in seconds.
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->finished_at) {
            return null;
        }
        
        return $this->started_at->diffInSeconds($this->finished_at);
    }

    // ========================================
    // MÉTODOS AUXILIARES
    // ========================================

    /**
     * Check if attempt is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if attempt is finished.
     */
    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * Check if attempt is abandoned.
     */
    public function isAbandoned(): bool
    {
        return $this->status === self::STATUS_ABANDONED;
    }

    /**
     * Finish this attempt and calculate score.
     */
    public function finish(): void
    {
        if (!$this->isInProgress()) {
            return;
        }

        $this->update([
            'finished_at' => now(),
            'status' => self::STATUS_FINISHED,
        ]);

        // Pagar recompensa si aplica
        if ($this->quiz->reward_amount > 0 && !$this->reward_paid) {
            $this->payReward();
        }
    }

    /**
     * Abandon this attempt.
     */
    public function abandon(): void
    {
        if ($this->isInProgress()) {
            $this->update([
                'status' => self::STATUS_ABANDONED,
                'finished_at' => now(),
            ]);
        }
    }

    /**
     * Pay reward to user.
     */
    public function payReward(): void
    {
        if ($this->reward_paid) {
            return;
        }

        $wallet = Wallet::getOrCreateForUser($this->user_id);
        $wallet->credit(
            $this->quiz->reward_amount,
            "Quiz reward: {$this->quiz->title}",
            QuizAttempt::class,
            $this->id
        );

        $this->update(['reward_paid' => true]);
    }

    /**
     * Record answer.
     */
    public function recordAnswer(
        int $questionId,
        ?int $selectedOptionId,
        bool $isCorrect,
        int $pointsAwarded = 0
    ): QuizAttemptAnswer {
        $answer = $this->answers()->create([
            'question_id' => $questionId,
            'selected_option_id' => $selectedOptionId,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
            'points_awarded' => $pointsAwarded,
        ]);

        // Actualizar contadores
        if ($isCorrect) {
            $this->increment('correct_count');
        } else {
            $this->increment('wrong_count');
        }

        $this->increment('score', $pointsAwarded);

        return $answer;
    }

    /**
     * Get total questions.
     */
    public function getTotalQuestions(): int
    {
        return $this->correct_count + $this->wrong_count;
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDuration(): string
    {
        $duration = $this->duration;
        
        if (!$duration) {
            return 'N/A';
        }

        $minutes = floor($duration / 60);
        $seconds = $duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}