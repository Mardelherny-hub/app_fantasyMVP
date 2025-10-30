<?php

namespace App\Livewire\Dashboard\Education;

use App\Models\QuizAttempt;
use App\Services\Education\LeaderboardService;
use App\Services\Education\QuizScoringService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente Livewire para mostrar estadísticas personales del usuario.
 * 
 * Responsabilidades:
 * - Mostrar estadísticas generales
 * - Mostrar mejor intento
 * - Mostrar historial reciente
 * - Mostrar progreso por período
 */
class UserStats extends Component
{
    public $generalStats = [];
    public $bestAttempt = null;
    public $recentAttempts = [];
    public $statsAllTime = [];
    public $statsWeekly = [];
    public $statsMonthly = [];
    public $selectedTab = 'overview'; // overview, history, rankings

    protected LeaderboardService $leaderboardService;
    protected QuizScoringService $scoringService;

    public function boot(
        LeaderboardService $leaderboardService,
        QuizScoringService $scoringService
    ) {
        $this->leaderboardService = $leaderboardService;
        $this->scoringService = $scoringService;
    }

    public function mount()
    {
        $this->loadStats();
    }

    /**
     * Cambia la pestaña activa.
     */
    public function setTab($tab)
    {
        if (in_array($tab, ['overview', 'history', 'rankings'])) {
            $this->selectedTab = $tab;
        }
    }

    /**
     * Carga todas las estadísticas del usuario.
     */
    public function loadStats()
    {
        $user = Auth::user();

        // Estadísticas generales
        $this->generalStats = $this->scoringService->getUserStats($user->id);

        // Mejor intento
        $this->bestAttempt = $this->scoringService->getUserBestAttempt($user->id);

        // Historial reciente (últimos 10)
        $this->recentAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('status', QuizAttempt::STATUS_FINISHED)
            ->with('quiz')
            ->latest('finished_at')
            ->limit(10)
            ->get()
            ->toArray();

        // Estadísticas por período
        $this->statsAllTime = $this->leaderboardService->getUserStats($user, 'all_time');
        $this->statsWeekly = $this->leaderboardService->getUserStats($user, 'weekly');
        $this->statsMonthly = $this->leaderboardService->getUserStats($user, 'monthly');
    }

    /**
     * Actualiza las estadísticas.
     */
    public function refresh()
    {
        $this->loadStats();
        $this->dispatch('stats-refreshed');
    }

    public function render()
    {
        return view('livewire.dashboard.education.user-stats');
    }
}