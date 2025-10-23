<?php

namespace App\Listeners\Education;

use App\Events\Education\QuizCompleted;
use App\Services\Education\QuizRewardsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener que otorga recompensas cuando se completa un quiz.
 * 
 * Se ejecuta de forma asíncrona (cola) para no bloquear
 * la respuesta al usuario.
 */
class AwardQuizRewards implements ShouldQueue
{
    use InteractsWithQueue;

    protected QuizRewardsService $rewardsService;

    /**
     * Create the event listener.
     */
    public function __construct(QuizRewardsService $rewardsService)
    {
        $this->rewardsService = $rewardsService;
    }

    /**
     * Handle the event.
     */
    public function handle(QuizCompleted $event): void
    {
        try {
            // Solo otorgar si el attempt tiene puntos
            if (!$event->attempt->score || $event->attempt->score <= 0) {
                Log::info('No rewards to award - zero score', [
                    'attempt_id' => $event->attempt->id,
                ]);
                return;
            }

            // Verificar si ya se otorgaron recompensas
            if ($event->attempt->reward_paid) {
                Log::warning('Rewards already paid for this attempt', [
                    'attempt_id' => $event->attempt->id,
                ]);
                return;
            }

            // Otorgar monedas
            $result = $this->rewardsService->awardCoins(
                $event->user,
                $event->attempt->score,
                $event->attempt
            );

            // Marcar como pagado
            $event->attempt->update(['reward_paid' => true]);

            Log::info('Quiz rewards awarded successfully', [
                'user_id' => $event->user->id,
                'attempt_id' => $event->attempt->id,
                'coins_awarded' => $result['coins_awarded'],
                'new_balance' => $result['new_balance'],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to award quiz rewards', [
                'user_id' => $event->user->id,
                'attempt_id' => $event->attempt->id,
                'error' => $e->getMessage(),
            ]);

            // Reintentar después de 1 minuto
            $this->release(60);
        }
    }

    /**
     * Determine the number of times the listener may be attempted.
     */
    public function tries(): int
    {
        return 3;
    }
}