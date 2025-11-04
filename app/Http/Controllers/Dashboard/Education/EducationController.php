<?php

namespace App\Http\Controllers\Dashboard\Education;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Services\Education\LeaderboardService;
use App\Services\Education\QuizScoringService;
use App\Services\Education\QuizAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para el módulo educativo (Quick Quiz).
 * 
 * Responsabilidades:
 * - Mostrar hub educativo
 * - Mostrar resultados de quiz
 * - Mostrar ranking
 * - Mostrar estadísticas personales
 */
class EducationController extends Controller
{
    protected LeaderboardService $leaderboardService;
    protected QuizScoringService $scoringService;

    public function __construct(
        LeaderboardService $leaderboardService,
        QuizScoringService $scoringService
    ) {
        $this->leaderboardService = $leaderboardService;
        $this->scoringService = $scoringService;
    }

    /**
     * Muestra el hub educativo principal.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        $analyticsService = app(\App\Services\Education\QuizAnalyticsService::class);
        
        // Obtener estadísticas del usuario
        $stats = $analyticsService->getUserStats($user);
        
        // Mapear las claves para que coincidan con la vista
        $userStats = [
            'total_attempts' => $stats['total_attempts'] ?? 0,
            'total_score' => $stats['total_points_earned'] ?? 0,
            'average_score' => $stats['average_points_per_attempt'] ?? 0,
            'accuracy_rate' => $stats['overall_accuracy'] ?? 0,
            'correct_answers' => $stats['total_correct_answers'] ?? 0,
            'total_answers' => $stats['total_questions_answered'] ?? 0,
        ];
        
        // Top 5 del ranking
        $topUsers = $this->leaderboardService
            ->getLeaderboard('all_time', 5);
        
        // Posición del usuario
        $userPosition = $this->leaderboardService
            ->getUserPosition($user, 'all_time');
        
        // Último intento
        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('status', QuizAttempt::STATUS_FINISHED)
            ->latest('finished_at')
            ->first();
        
        return view('manager.education.index', compact(
            'userStats',
            'topUsers',
            'userPosition',
            'lastAttempt'
        ));
    }

    /**
     * Muestra los resultados de un intento de quiz.
     * 
     * @param QuizAttempt $attempt
     * @return \Illuminate\View\View
     */
    public function results(Request $request, $lang, $attempt)
    {
        
        $user = Auth::user();

        // Buscar el intento por ID
        $attempt = QuizAttempt::with('user')->findOrFail($attempt);

        // Verificar que el intento pertenece al usuario autenticado
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        // Verificar que el intento está finalizado
        if ($attempt->status !== QuizAttempt::STATUS_FINISHED) {
            return redirect()->route('manager.education.index')
                ->with('error', __('This quiz attempt is not finished yet.'));
        }

        // Cargar relaciones necesarias
        $attempt->load([
            'answers.question.translations',
            'answers.question.category',
            'answers.selectedOption.translations'
        ]);

        // Obtener posición actual del usuario en el ranking
        $userPosition = $this->leaderboardService->getUserPosition($user);

        // Estadísticas del intento
        $attemptStats = [
            'total_questions' => $attempt->correct_count + $attempt->wrong_count,
            'correct' => $attempt->correct_count,
            'wrong' => $attempt->wrong_count,
            'score' => $attempt->score,
            'accuracy' => $attempt->correct_count + $attempt->wrong_count > 0
                ? round(($attempt->correct_count / ($attempt->correct_count + $attempt->wrong_count)) * 100, 2)
                : 0,
            'reward_paid' => $attempt->reward_paid,
        ];

        return view('manager.education.results', compact(
            'attempt',
            'userPosition',
            'attemptStats'
        ));
    }

    /**
     * Muestra el ranking global.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function ranking(Request $request)
    {
        $user = Auth::user();

        // Obtener período desde query string (default: all_time)
        $period = $request->input('period', LeaderboardService::PERIOD_ALL_TIME);

        // Validar período
        if (!in_array($period, [
            LeaderboardService::PERIOD_ALL_TIME,
            LeaderboardService::PERIOD_WEEKLY,
            LeaderboardService::PERIOD_MONTHLY
        ])) {
            $period = LeaderboardService::PERIOD_ALL_TIME;
        }

        // Obtener ranking completo (top 100)
        $leaderboard = $this->leaderboardService->getLeaderboard($period, 100);

        // Obtener estadísticas del usuario para este período
        $userStats = $this->leaderboardService->getUserStats($user, $period);

        // Obtener usuarios alrededor del usuario actual (para destacar)
        $usersAroundUser = $this->leaderboardService->getLeaderboardAroundUser($user, $period, 2);

        return view('manager.education.ranking', compact(
            'leaderboard',
            'userStats',
            'period',
            'usersAroundUser'
        ));
    }

    /**
     * Muestra las estadísticas personales del usuario.
     * 
     * @return \Illuminate\View\View
     */
    public function stats()
    {
        $user = Auth::user();
        
        // Usar QuizAnalyticsService
        $analyticsService = app(\App\Services\Education\QuizAnalyticsService::class);
        
        // Estadísticas generales
        $userStats = $analyticsService->getUserStats($user);
        
        // Estadísticas por categoría
        $statsByCategory = $analyticsService->getUserStatsByCategory($user);
        
        // Progreso en el tiempo (últimos 30 días)
        $progressData = $analyticsService->getUserProgressOverTime($user, 30);
        
        return view('manager.education.stats', compact(
            'userStats',
            'statsByCategory',
            'progressData'
        ));
    }

    
}