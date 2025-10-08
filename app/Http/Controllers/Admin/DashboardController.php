<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\League;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        // Estadísticas principales
        $stats = [
            'total_users' => User::count(),
            'active_leagues' => League::where('is_locked', false)->count(), // Ligas abiertas
            'total_questions' => Question::where('is_active', true)->count(),
            'users_today' => User::whereDate('last_login_at', today())->count(),
        ];

        // Actividad reciente (últimos 7 días)
        $recentActivity = [
            'new_users_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'quiz_attempts_week' => QuizAttempt::where('created_at', '>=', now()->subWeek())->count(),
            'completed_quizzes_week' => QuizAttempt::where('status', QuizAttempt::STATUS_FINISHED)
                                                    ->where('created_at', '>=', now()->subWeek())
                                                    ->count(),
        ];

        // Top usuarios por quiz attempts (cualquier estado)
        $topUsers = User::withCount('quizAttempts')
                        ->having('quiz_attempts_count', '>', 0)
                        ->orderBy('quiz_attempts_count', 'desc')
                        ->limit(5)
                        ->get();

        // Últimas ligas creadas
        $recentLeagues = League::with('owner')
                               ->latest()
                               ->limit(5)
                               ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentActivity',
            'topUsers',
            'recentLeagues'
        ));
    }
}