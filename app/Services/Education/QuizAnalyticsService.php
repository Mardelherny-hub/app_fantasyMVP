<?php

namespace App\Services\Education;

use App\Models\User;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Servicio de analíticas del módulo educativo.
 * 
 * VERSIÓN CORREGIDA - Usa los campos reales de la BD:
 * - score (no total_points)
 * - correct_count (no total_correct)
 * - finished_at (no completed_at)
 * - status usa valores numéricos (0=in_progress, 1=finished, 2=abandoned)
 */
class QuizAnalyticsService
{
    /**
     * Registra una respuesta individual para analíticas.
     */
    public function recordAnswer(
        User $user,
        Question $question,
        bool $isCorrect,
        int $timeMs,
        QuizAttempt $attempt
    ): void {
        $metadata = [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'category_id' => $question->category_id,
            'difficulty' => $question->difficulty,
            'is_correct' => $isCorrect,
            'time_taken_ms' => $timeMs,
            'quiz_attempt_id' => $attempt->id,
            'timestamp' => now()->toIso8601String(),
        ];

        // Invalidar caches relacionados
        $this->invalidateUserStatsCache($user->id);
        $this->invalidateQuestionStatsCache($question->id);
    }

    /**
     * Obtiene estadísticas globales del usuario.
     */
    public function getUserStats(User $user): array
    {
        return Cache::remember(
            "user_quiz_stats_{$user->id}",
            now()->addMinutes(10),
            function () use ($user) {
                $attempts = QuizAttempt::where('user_id', $user->id)
                    ->where('status', 1) // 1 = finished
                    ->get();

                $totalAttempts = $attempts->count();
                $totalPoints = $attempts->sum('score');              // ← CORREGIDO
                $totalCorrect = $attempts->sum('correct_count');     // ← CORREGIDO
                
                // Calcular total de preguntas respondidas
                $totalQuestions = QuizAttemptAnswer::whereIn(
                    'quiz_attempt_id',
                    $attempts->pluck('id')
                )->count();

                return [
                    'total_attempts' => $totalAttempts,
                    'total_questions_answered' => $totalQuestions,
                    'total_correct_answers' => $totalCorrect,
                    'total_incorrect_answers' => $totalQuestions - $totalCorrect,
                    'total_points_earned' => $totalPoints,
                    'overall_accuracy' => $totalQuestions > 0 
                        ? round(($totalCorrect / $totalQuestions) * 100, 2) 
                        : 0,
                    'average_points_per_attempt' => $totalAttempts > 0 
                        ? round($totalPoints / $totalAttempts, 2) 
                        : 0,
                ];
            }
        );
    }

    /**
     * Obtiene estadísticas del usuario por categoría.
     */
    public function getUserStatsByCategory(User $user): array
    {
        return Cache::remember(
            "user_quiz_stats_by_category_{$user->id}",
            now()->addMinutes(10),
            function () use ($user) {
                $attempts = QuizAttempt::where('user_id', $user->id)
                    ->where('status', 1) // 1 = finished
                    ->get();

                $answers = QuizAttemptAnswer::with('question.category')
                    ->whereIn('quiz_attempt_id', $attempts->pluck('id'))
                    ->get();

                $statsByCategory = [];

                foreach ($answers->groupBy('question.category_id') as $categoryId => $categoryAnswers) {
                    $category = $categoryAnswers->first()->question->category;
                    $totalQuestions = $categoryAnswers->count();
                    $correctAnswers = $categoryAnswers->where('is_correct', true)->count();

                    $statsByCategory[] = [
                        'category_id' => $categoryId,
                        'category_name' => $category->getName(app()->getLocale()),
                        'total_questions' => $totalQuestions,
                        'correct_answers' => $correctAnswers,
                        'incorrect_answers' => $totalQuestions - $correctAnswers,
                        'accuracy' => $totalQuestions > 0 
                            ? round(($correctAnswers / $totalQuestions) * 100, 2) 
                            : 0,
                    ];
                }

                return $statsByCategory;
            }
        );
    }

    /**
     * Obtiene el progreso del usuario en los últimos N días.
     */
    public function getUserProgressOverTime(User $user, int $days = 30): array
    {
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('status', 1) // 1 = finished
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at')
            ->get();

        $progressData = [];

        foreach ($attempts as $attempt) {
            $date = $attempt->created_at->format('Y-m-d');
            
            if (!isset($progressData[$date])) {
                $progressData[$date] = [
                    'date' => $date,
                    'attempts' => 0,
                    'total_points' => 0,
                    'total_correct' => 0,
                    'total_questions' => 0,
                ];
            }

            $progressData[$date]['attempts']++;
            $progressData[$date]['total_points'] += $attempt->score ?? 0;          // ← CORREGIDO
            $progressData[$date]['total_correct'] += $attempt->correct_count ?? 0; // ← CORREGIDO
            
            // Contar preguntas del attempt
            $questionsCount = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->count();
            $progressData[$date]['total_questions'] += $questionsCount;
        }

        // Calcular accuracy por día
        foreach ($progressData as &$data) {
            $data['accuracy'] = $data['total_questions'] > 0
                ? round(($data['total_correct'] / $data['total_questions']) * 100, 2)
                : 0;
        }

        return array_values($progressData);
    }

    /**
     * Obtiene las preguntas más difíciles (menor % de acierto).
     */
    public function getHardestQuestions(int $limit = 10): array
    {
        return Cache::remember(
            "hardest_questions_{$limit}",
            now()->addHours(1),
            function () use ($limit) {
                return DB::table('quiz_attempt_answers')
                    ->select(
                        'question_id',
                        DB::raw('COUNT(*) as total_attempts'),
                        DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
                        DB::raw('ROUND((SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as success_rate')
                    )
                    ->groupBy('question_id')
                    ->having('total_attempts', '>=', 10)
                    ->orderBy('success_rate', 'asc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($item) {
                        $question = Question::with('translations', 'category.translations')->find($item->question_id);
                        return [
                            'question_id' => $item->question_id,
                            'question_text' => $question ? $question->getText(app()->getLocale()) : 'N/A',
                            'category' => $question ? $question->category->getName(app()->getLocale()) : 'N/A',
                            'difficulty' => $question ? $question->difficulty : 0,
                            'total_attempts' => $item->total_attempts,
                            'correct_answers' => $item->correct_answers,
                            'success_rate' => $item->success_rate,
                        ];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Obtiene el ranking de usuarios por puntos educativos.
     */
    public function getLeaderboard(int $limit = 100, string $period = 'all_time'): array
    {
        $cacheKey = "quiz_leaderboard_{$period}_{$limit}";
        $cacheDuration = (int) settings('quiz.leaderboard.cache_minutes', 10);

        return Cache::remember(
            $cacheKey,
            now()->addMinutes($cacheDuration),
            function () use ($limit, $period) {
                $query = DB::table('quiz_attempts')
                    ->select(
                        'user_id',
                        DB::raw('SUM(score) as total_points'),                    // ← CORREGIDO
                        DB::raw('COUNT(*) as total_attempts'),
                        DB::raw('SUM(correct_count) as total_correct'),           // ← CORREGIDO
                        DB::raw('SUM(
                            (SELECT COUNT(*) FROM quiz_attempt_answers 
                             WHERE quiz_attempt_answers.quiz_attempt_id = quiz_attempts.id)
                        ) as total_questions')
                    )
                    ->where('status', 1); // 1 = finished

                // Filtrar por período
                if ($period === 'weekly') {
                    $query->where('created_at', '>=', now()->subWeek());
                } elseif ($period === 'monthly') {
                    $query->where('created_at', '>=', now()->subMonth());
                }

                return $query->groupBy('user_id')
                    ->orderByDesc('total_points')
                    ->limit($limit)
                    ->get()
                    ->map(function ($item, $index) {
                        $user = User::find($item->user_id);
                        return [
                            'rank' => $index + 1,
                            'user_id' => $item->user_id,
                            'username' => $user ? $user->name : 'Unknown',
                            'total_points' => $item->total_points,
                            'total_attempts' => $item->total_attempts,
                            'total_correct' => $item->total_correct,
                            'accuracy' => $item->total_questions > 0
                                ? round(($item->total_correct / $item->total_questions) * 100, 2)
                                : 0,
                        ];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Invalida el cache de estadísticas del usuario.
     */
    protected function invalidateUserStatsCache(int $userId): void
    {
        Cache::forget("user_quiz_stats_{$userId}");
        Cache::forget("user_quiz_stats_by_category_{$userId}");
    }

    /**
     * Invalida el cache de estadísticas de una pregunta.
     */
    protected function invalidateQuestionStatsCache(int $questionId): void
    {
        Cache::forget("hardest_questions_10");
        Cache::forget("hardest_questions_20");
    }

    /**
     * Invalida todos los caches del leaderboard.
     */
    public function invalidateLeaderboardCache(): void
    {
        $periods = ['weekly', 'monthly', 'all_time'];
        $limits = [10, 50, 100];

        foreach ($periods as $period) {
            foreach ($limits as $limit) {
                Cache::forget("quiz_leaderboard_{$period}_{$limit}");
            }
        }
    }
}