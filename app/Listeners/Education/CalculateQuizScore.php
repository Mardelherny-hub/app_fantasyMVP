<?php

namespace App\Listeners\Education;

use App\Events\Education\QuizCompleted;
use App\Services\Education\QuizScoringService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener que calcula la puntuación cuando se completa un quiz.
 * 
 * Se ejecuta de forma síncrona para que el usuario vea
 * inmediatamente su puntuación.
 */
class CalculateQuizScore
{
    protected QuizScoringService $scoringService;

    /**
     * Create the event listener.
     */
    public function __construct(QuizScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Handle the event.
     */
    public function handle(QuizCompleted $event): void
    {
        try {
            // Calcular puntuación
            $scoreData = $this->scoringService->calculateScore($event->attempt);

            // Actualizar el attempt
            $this->scoringService->updateAttemptScore($event->attempt, $scoreData);

            // Calcular y guardar duración
            $duration = $this->scoringService->calculateDuration($event->attempt);
            if ($duration) {
                $event->attempt->update(['duration_seconds' => $duration]);
            }

            Log::info('Quiz score calculated', [
                'user_id' => $event->user->id,
                'attempt_id' => $event->attempt->id,
                'score' => $scoreData['total_points'],
                'correct' => $scoreData['total_correct'],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to calculate quiz score', [
                'user_id' => $event->user->id,
                'attempt_id' => $event->attempt->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}