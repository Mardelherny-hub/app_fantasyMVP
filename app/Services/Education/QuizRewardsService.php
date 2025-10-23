<?php

namespace App\Services\Education;

use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de recompensas educativas.
 * 
 * VERSIÓN CORREGIDA - Usa los campos reales de la BD:
 * - score (no total_points)
 * - correct_count (no total_correct)
 * 
 * Responsabilidades:
 * - Convertir puntos educativos a monedas virtuales
 * - Registrar transacciones de recompensas
 * - Actualizar balance del usuario
 */
class QuizRewardsService
{
    /**
     * Otorga monedas virtuales al usuario basado en los puntos obtenidos.
     * 
     * @param User $user
     * @param int $points
     * @param QuizAttempt $attempt
     * @return array ['coins_awarded', 'new_balance']
     */
    public function awardCoins(User $user, int $points, QuizAttempt $attempt): array
    {
        // Verificar si el módulo está configurado para otorgar monedas
        if (!settings('quiz.coins.award_on_completion', true)) {
            return [
                'coins_awarded' => 0,
                'new_balance' => $this->getUserBalance($user),
            ];
        }

        // Calcular monedas a otorgar
        $conversionRate = (float) settings('quiz.coins.conversion_rate', 0.1);
        $coinsToAward = floor($points * $conversionRate);

        if ($coinsToAward <= 0) {
            return [
                'coins_awarded' => 0,
                'new_balance' => $this->getUserBalance($user),
            ];
        }

        DB::beginTransaction();
        
        try {
            // Actualizar balance del usuario
            $newBalance = $this->incrementUserBalance($user, $coinsToAward);

            // Registrar la transacción
            $this->logTransaction($user, $coinsToAward, $attempt);

            DB::commit();

            Log::info('Quiz rewards awarded', [
                'user_id' => $user->id,
                'quiz_attempt_id' => $attempt->id,
                'points' => $points,
                'coins_awarded' => $coinsToAward,
                'new_balance' => $newBalance,
            ]);

            return [
                'coins_awarded' => $coinsToAward,
                'new_balance' => $newBalance,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to award quiz rewards', [
                'user_id' => $user->id,
                'quiz_attempt_id' => $attempt->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Incrementa el balance de monedas virtuales del usuario.
     * 
     * @param User $user
     * @param int $amount
     * @return int Nuevo balance
     */
    protected function incrementUserBalance(User $user, int $amount): int
    {
        // Si el modelo User tiene un campo virtual_currency_balance
        if ($user->hasAttribute('virtual_currency_balance')) {
            $user->increment('virtual_currency_balance', $amount);
            return $user->fresh()->virtual_currency_balance;
        }

        // Si existe una tabla virtual_currency o user_balance
        // Ajustar según la estructura real del proyecto
        $balance = DB::table('user_balances')
            ->where('user_id', $user->id)
            ->increment('balance', $amount);
            
        return DB::table('user_balances')
            ->where('user_id', $user->id)
            ->value('balance') ?? 0;
    }

    /**
     * Obtiene el balance actual del usuario.
     * 
     * @param User $user
     * @return int
     */
    protected function getUserBalance(User $user): int
    {
        if ($user->hasAttribute('virtual_currency_balance')) {
            return $user->virtual_currency_balance ?? 0;
        }

        return DB::table('user_balances')
            ->where('user_id', $user->id)
            ->value('balance') ?? 0;
    }

    /**
     * Registra la transacción de recompensa.
     * 
     * @param User $user
     * @param int $amount
     * @param QuizAttempt $attempt
     * @return void
     */
    protected function logTransaction(User $user, int $amount, QuizAttempt $attempt): void
    {
        // Si existe una tabla de transacciones virtuales
        if (DB::getSchemaBuilder()->hasTable('virtual_transactions')) {
            DB::table('virtual_transactions')->insert([
                'user_id' => $user->id,
                'type' => 'quiz_reward',
                'amount' => $amount,
                'balance_after' => $this->getUserBalance($user),
                'description' => "Quiz completion reward - Attempt #{$attempt->id}",
                'metadata' => json_encode([
                    'quiz_attempt_id' => $attempt->id,
                    'quiz_id' => $attempt->quiz_id,
                    'score' => $attempt->score,                      // ← CORREGIDO
                    'correct_count' => $attempt->correct_count,      // ← CORREGIDO
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Log alternativo si no existe la tabla
        Log::channel('economy')->info('Quiz reward transaction', [
            'user_id' => $user->id,
            'amount' => $amount,
            'quiz_attempt_id' => $attempt->id,
        ]);
    }

    /**
     * Calcula la conversión de puntos a monedas sin otorgarlas.
     * 
     * @param int $points
     * @return int
     */
    public function calculateCoinsFromPoints(int $points): int
    {
        $conversionRate = (float) settings('quiz.coins.conversion_rate', 0.1);
        return floor($points * $conversionRate);
    }

    /**
     * Obtiene el total de monedas ganadas por educación.
     * 
     * @param User $user
     * @return int
     */
    public function getTotalCoinsEarnedFromQuizzes(User $user): int
    {
        if (!DB::getSchemaBuilder()->hasTable('virtual_transactions')) {
            return 0;
        }

        return DB::table('virtual_transactions')
            ->where('user_id', $user->id)
            ->where('type', 'quiz_reward')
            ->sum('amount');
    }

    /**
     * Obtiene estadísticas de recompensas del usuario.
     * 
     * @param User $user
     * @return array
     */
    public function getUserRewardStats(User $user): array
    {
        $totalCoinsEarned = $this->getTotalCoinsEarnedFromQuizzes($user);
        $totalPoints = QuizAttempt::where('user_id', $user->id)
            ->where('status', 1) // 1 = finished                    // ← CORREGIDO
            ->sum('score');                                          // ← CORREGIDO

        return [
            'total_points_earned' => $totalPoints,
            'total_coins_earned' => $totalCoinsEarned,
            'current_balance' => $this->getUserBalance($user),
            'conversion_rate' => (float) settings('quiz.coins.conversion_rate', 0.1),
        ];
    }
}