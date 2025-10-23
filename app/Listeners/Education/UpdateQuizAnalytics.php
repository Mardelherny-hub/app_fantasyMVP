<?php

namespace App\Listeners\Education;

use App\Events\Education\QuestionAnswered;
use App\Services\Education\QuizAnalyticsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener que actualiza analíticas cuando se responde una pregunta.
 * 
 * Se ejecuta de forma asíncrona (cola) para no impactar
 * la experiencia del usuario.
 */
class UpdateQuizAnalytics implements ShouldQueue
{
    use InteractsWithQueue;

    protected QuizAnalyticsService $analyticsService;

    /**
     * Create the event listener.
     */
    public function __construct(QuizAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle the event.
     */
    public function handle(QuestionAnswered $event): void
    {
        // Registrar la respuesta para analíticas
        $this->analyticsService->recordAnswer(
            $event->user,
            $event->question,
            $event->isCorrect,
            $event->answer->time_taken_ms ?? 0,
            $event->answer->quizAttempt
        );
    }

    /**
     * Determine the number of times the listener may be attempted.
     */
    public function tries(): int
    {
        return 2;
    }
}