<?php

namespace App\Services\Education;

use App\Models\User;
use App\Models\QuizAttempt;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar el ranking/leaderboard del módulo educativo.
 * 
 * Responsabilidades:
 * - Generar ranking global
 * - Filtrar por período (all-time, semanal, mensual)
 * - Cachear resultados
 * - Obtener posición de usuario
 */
class LeaderboardService
{
    /**
     * Períodos válidos para el ranking.
     */
    const PERIOD_ALL_TIME = 'all_time';
    const PERIOD_WEEKLY = 'weekly';
    const PERIOD_MONTHLY = 'monthly';

    /**
     * Tiempo de caché en minutos.
     */
    const CACHE_TTL = 10;

    /**
     * Obtiene el ranking global de usuarios.
     * 
     * @param string $period 'all_time', 'weekly', 'monthly'
     * @param int $limit Número de usuarios a retornar
     * @return Collection
     */
    public function getLeaderboard(string $period = self::PERIOD_ALL_TIME, int $limit = 100): Collection
    {
        $cacheKey = "education_leaderboard_{$period}_{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($period, $limit) {
            $query = DB::table('quiz_attempts')
                ->select(
                    'user_id',
                    DB::raw('SUM(score) as total_score'),
                    DB::raw('COUNT(*) as total_attempts'),
                    DB::raw('SUM(correct_count) as total_correct'),
                    DB::raw('SUM(correct_count + wrong_count) as total_questions')
                )
                ->where('status', QuizAttempt::STATUS_FINISHED);

            // Aplicar filtro de período
            $query = $this->applyPeriodFilter($query, $period);

            $results = $query->groupBy('user_id')
                ->orderByDesc('total_score')
                ->limit($limit)
                ->get();

            // Enriquecer con datos del usuario
            return $results->map(function ($item, $index) {
                $user = User::find($item->user_id);

                if (!$user) {
                    return null;
                }

                $accuracy = $item->total_questions > 0 
                    ? round(($item->total_correct / $item->total_questions) * 100, 2)
                    : 0;

                return [
                    'rank' => $index + 1,
                    'user_id' => $item->user_id,
                    'username' => $user->name,
                    'total_score' => (int) $item->total_score,
                    'total_attempts' => (int) $item->total_attempts,
                    'total_correct' => (int) $item->total_correct,
                    'total_questions' => (int) $item->total_questions,
                    'accuracy' => $accuracy,
                ];
            })->filter()->values();
        });
    }

    /**
     * Obtiene la posición de un usuario en el ranking.
     * 
     * @param User $user
     * @param string $period
     * @return int Posición (1-based), 0 si no está en el ranking
     */
    public function getUserPosition(User $user, string $period = self::PERIOD_ALL_TIME): int
    {
        $cacheKey = "user_leaderboard_position_{$user->id}_{$period}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($user, $period) {
            // Obtener el score total del usuario
            $query = QuizAttempt::where('user_id', $user->id)
                ->where('status', QuizAttempt::STATUS_FINISHED);

            $query = $this->applyPeriodFilterEloquent($query, $period);

            $userTotalScore = $query->sum('score');

            if ($userTotalScore == 0) {
                return 0;
            }

            // Contar cuántos usuarios tienen más score
            $query = DB::table('quiz_attempts')
                ->select('user_id', DB::raw('SUM(score) as total_score'))
                ->where('status', QuizAttempt::STATUS_FINISHED);

            $query = $this->applyPeriodFilter($query, $period);

            $usersAbove = $query->groupBy('user_id')
                ->havingRaw('SUM(score) > ?', [$userTotalScore])
                ->count();

            return $usersAbove + 1;
        });
    }

    /**
     * Obtiene estadísticas del usuario para el ranking.
     * 
     * @param User $user
     * @param string $period
     * @return array
     */
    public function getUserStats(User $user, string $period = self::PERIOD_ALL_TIME): array
    {
        $cacheKey = "user_leaderboard_stats_{$user->id}_{$period}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($user, $period) {
            $query = QuizAttempt::where('user_id', $user->id)
                ->where('status', QuizAttempt::STATUS_FINISHED);

            $query = $this->applyPeriodFilterEloquent($query, $period);

            $attempts = $query->get();

            $totalAttempts = $attempts->count();
            $totalScore = $attempts->sum('score');
            $totalCorrect = $attempts->sum('correct_count');
            $totalQuestions = $attempts->sum(function ($attempt) {
                return $attempt->correct_count + $attempt->wrong_count;
            });

            $accuracy = $totalQuestions > 0 
                ? round(($totalCorrect / $totalQuestions) * 100, 2)
                : 0;

            $position = $this->getUserPosition($user, $period);

            return [
                'user_id' => $user->id,
                'username' => $user->name,
                'total_score' => $totalScore,
                'total_attempts' => $totalAttempts,
                'total_correct' => $totalCorrect,
                'total_questions' => $totalQuestions,
                'accuracy' => $accuracy,
                'position' => $position,
                'period' => $period,
            ];
        });
    }

    /**
     * Obtiene el top N de usuarios alrededor de un usuario específico.
     * 
     * @param User $user
     * @param string $period
     * @param int $context Número de usuarios arriba y abajo
     * @return Collection
     */
    public function getLeaderboardAroundUser(User $user, string $period = self::PERIOD_ALL_TIME, int $context = 5): Collection
    {
        $position = $this->getUserPosition($user, $period);

        if ($position === 0) {
            return collect([]);
        }

        $startRank = max(1, $position - $context);
        $endRank = $position + $context;

        $leaderboard = $this->getLeaderboard($period, $endRank);

        return $leaderboard->slice($startRank - 1, ($context * 2) + 1)->values();
    }

    /**
     * Aplica filtro de período a una query de DB.
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $period
     * @return \Illuminate\Database\Query\Builder
     */
    protected function applyPeriodFilter($query, string $period)
    {
        switch ($period) {
            case self::PERIOD_WEEKLY:
                return $query->where('created_at', '>=', now()->startOfWeek());
            
            case self::PERIOD_MONTHLY:
                return $query->where('created_at', '>=', now()->startOfMonth());
            
            case self::PERIOD_ALL_TIME:
            default:
                return $query;
        }
    }

    /**
     * Aplica filtro de período a una query Eloquent.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $period
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyPeriodFilterEloquent($query, string $period)
    {
        switch ($period) {
            case self::PERIOD_WEEKLY:
                return $query->where('created_at', '>=', now()->startOfWeek());
            
            case self::PERIOD_MONTHLY:
                return $query->where('created_at', '>=', now()->startOfMonth());
            
            case self::PERIOD_ALL_TIME:
            default:
                return $query;
        }
    }

    /**
     * Invalida el caché del ranking.
     * 
     * @param string|null $period Si es null, invalida todos los períodos
     * @return void
     */
    public function clearCache(?string $period = null): void
    {
        if ($period) {
            Cache::forget("education_leaderboard_{$period}_100");
        } else {
            Cache::forget("education_leaderboard_all_time_100");
            Cache::forget("education_leaderboard_weekly_100");
            Cache::forget("education_leaderboard_monthly_100");
        }
    }

    /**
     * Invalida el caché de un usuario específico.
     * 
     * @param int $userId
     * @return void
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("user_leaderboard_position_{$userId}_all_time");
        Cache::forget("user_leaderboard_position_{$userId}_weekly");
        Cache::forget("user_leaderboard_position_{$userId}_monthly");
        Cache::forget("user_leaderboard_stats_{$userId}_all_time");
        Cache::forget("user_leaderboard_stats_{$userId}_weekly");
        Cache::forget("user_leaderboard_stats_{$userId}_monthly");
    }

    /**
     * Invalida todos los cachés relacionados con el leaderboard.
     * 
     * @return void
     */
    public function clearAllCaches(): void
    {
        $this->clearCache();
        
        // También invalidar el caché de contador de intentos para rate limiting
        Cache::flush();
    }
}