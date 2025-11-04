<?php

namespace App\Services\Education;

use App\Models\User;
use App\Models\QuizAttempt;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Servicio de recompensas educativas.
 * 
 * VERSIÓN CORREGIDA v3.0:
 * ✅ Usa Setting::get() en lugar de settings()
 * ✅ Usa Wallet en lugar de user_balances
 * ✅ Usa wallet_transactions automáticamente
 * 
 * Responsabilidades:
 * - Convertir puntos educativos a monedas virtuales
 * - Registrar transacciones de recompensas
 * - Actualizar balance del usuario usando Wallet
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
        if (!Setting::get('quiz.coins.award_on_completion', true)) {
            return [
                'coins_awarded' => 0,
                'new_balance' => $this->getUserBalance($user),
            ];
        }

        // Calcular monedas a otorgar
        $conversionRate = (float) Setting::get('quiz.coins.conversion_rate', 0.1);
        $coinsToAward = floor($points * $conversionRate);

        if ($coinsToAward <= 0) {
            return [
                'coins_awarded' => 0,
                'new_balance' => $this->getUserBalance($user),
            ];
        }

        DB::beginTransaction();
        
        try {
            // Actualizar balance del usuario usando Wallet
            $newBalance = $this->incrementUserBalance($user, $coinsToAward, $attempt);

            // Log adicional (wallet->credit ya crea WalletTransaction)
            $this->logTransaction($user, $coinsToAward, $attempt);

            // Marcar como pagado
            $attempt->update(['reward_paid' => true]);

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
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Incrementa el balance usando el modelo Wallet.
     * 
     * @param User $user
     * @param int $amount
     * @param QuizAttempt $attempt
     * @return float Nuevo balance
     */
    protected function incrementUserBalance(User $user, int $amount, QuizAttempt $attempt): float
    {
        // Obtener o crear wallet para el usuario (moneda CAN)
        $wallet = Wallet::getOrCreateForUser($user->id, 'CAN');
        
        // Acreditar usando el método del modelo Wallet
        // Esto automáticamente crea un WalletTransaction
        $wallet->credit(
            $amount, 
            "Quiz completion reward - Attempt #{$attempt->id}",
            QuizAttempt::class,
            $attempt->id
        );

        // Retornar el nuevo balance
        return $wallet->fresh()->balance;
    }

    /**
     * Obtiene el balance actual del usuario.
     * 
     * @param User $user
     * @return float
     */
    protected function getUserBalance(User $user): float
    {
        $wallet = Wallet::where('user_id', $user->id)
            ->where('currency', 'CAN')
            ->first();

        return $wallet ? $wallet->balance : 0.0;
    }

    /**
     * Registra log adicional de la transacción.
     * 
     * Nota: wallet->credit() ya crea un WalletTransaction automáticamente,
     * este método es solo para logging adicional.
     * 
     * @param User $user
     * @param int $amount
     * @param QuizAttempt $attempt
     * @return void
     */
    protected function logTransaction(User $user, int $amount, QuizAttempt $attempt): void
    {
        // Log adicional para economía
        Log::channel('economy')->info('Quiz reward transaction', [
            'user_id' => $user->id,
            'amount' => $amount,
            'quiz_attempt_id' => $attempt->id,
            'quiz_id' => $attempt->quiz_id,
            'score' => $attempt->score,
            'correct_count' => $attempt->correct_count,
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
        $conversionRate = (float) Setting::get('quiz.coins.conversion_rate', 0.1);
        return floor($points * $conversionRate);
    }

    /**
     * Obtiene el total de monedas ganadas por quizzes.
     * 
     * @param User $user
     * @return float
     */
    public function getTotalCoinsEarnedFromQuizzes(User $user): float
    {
        if (!DB::getSchemaBuilder()->hasTable('wallet_transactions')) {
            return 0.0;
        }

        // Obtener wallet del usuario
        $wallet = Wallet::where('user_id', $user->id)
            ->where('currency', 'CAN')
            ->first();

        if (!$wallet) {
            return 0.0;
        }

        // Sumar todas las transacciones de tipo quiz
        return DB::table('wallet_transactions')
            ->where('wallet_id', $wallet->id)
            ->where('reference_type', QuizAttempt::class)
            ->where('type', 'credit') // Solo créditos
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
            ->where('status', 1) // finished
            ->sum('score');

        return [
            'total_points_earned' => $totalPoints,
            'total_coins_earned' => $totalCoinsEarned,
            'current_balance' => $this->getUserBalance($user),
            'conversion_rate' => (float) Setting::get('quiz.coins.conversion_rate', 0.1),
        ];
    }
}