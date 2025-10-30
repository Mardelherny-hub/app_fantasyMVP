<?php

namespace App\Livewire\Dashboard\Education;

use App\Services\Education\LeaderboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente Livewire para mostrar el ranking/leaderboard.
 * 
 * Responsabilidades:
 * - Mostrar ranking global
 * - Filtrar por período (all-time, weekly, monthly)
 * - Destacar posición del usuario
 * - Actualizar en tiempo real
 */
class Leaderboard extends Component
{
    public $period = 'all_time';
    public $leaderboard = [];
    public $userPosition = 0;
    public $userStats = [];
    public $limit = 50;

    protected LeaderboardService $leaderboardService;

    public function boot(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    public function mount()
    {
        $this->loadLeaderboard();
    }

    /**
     * Cambia el período del ranking.
     */
    public function setPeriod($period)
    {
        if (in_array($period, ['all_time', 'weekly', 'monthly'])) {
            $this->period = $period;
            $this->loadLeaderboard();
        }
    }

    /**
     * Carga los datos del ranking.
     */
    public function loadLeaderboard()
    {
        $user = Auth::user();

        // Obtener ranking
        $this->leaderboard = $this->leaderboardService
            ->getLeaderboard($this->period, $this->limit)
            ->toArray();

        // Obtener posición del usuario
        $this->userPosition = $this->leaderboardService
            ->getUserPosition($user, $this->period);

        // Obtener estadísticas del usuario
        $this->userStats = $this->leaderboardService
            ->getUserStats($user, $this->period);
    }

    /**
     * Actualiza el ranking (útil si se ejecuta en segundo plano).
     */
    public function refresh()
    {
        $this->loadLeaderboard();
        $this->dispatch('leaderboard-refreshed');
    }

    public function render()
    {
        return view('livewire.dashboard.education.leaderboard');
    }
}