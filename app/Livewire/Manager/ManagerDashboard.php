<?php

namespace App\Livewire\Manager;

use App\Models\League;
use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use App\Models\LeagueStanding;
use App\Models\QuizAttempt;
use App\Services\Education\QuizRewardsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManagerDashboard extends Component
{
    public $leagueMembers;
    public $selectedLeagueId;
    public $selectedLeague;
    public $fantasyTeam;
    public $hasSquad = false;
    public $standings;
    public $stats = [];
    public $educationStats = [];

    public function mount()
    {
        $user = Auth::user();

        // Obtener todas las ligas del usuario
        $this->leagueMembers = LeagueMember::with(['league.season'])
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        // Seleccionar la primera liga por defecto
        if ($this->leagueMembers->isNotEmpty()) {
            $this->selectedLeagueId = $this->leagueMembers->first()->league_id;
            $this->loadLeagueData();
        }

        // Cargar stats de education
        $this->loadEducationStats();
    }

    public function selectLeague($leagueId)
    {
        $this->selectedLeagueId = $leagueId;
        $this->loadLeagueData();
    }

    protected function loadLeagueData()
    {
        $user = Auth::user();
        $this->selectedLeague = League::with('season')->find($this->selectedLeagueId);

        if (!$this->selectedLeague) {
            return;
        }

        // Obtener el fantasy team del usuario en esta liga
        $this->fantasyTeam = FantasyTeam::where('user_id', $user->id)
            ->where('league_id', $this->selectedLeagueId)
            ->first();

        // Verificar si tiene squad completo (usar el campo de BD)
        if ($this->fantasyTeam) {
            $this->hasSquad = $this->fantasyTeam->is_squad_complete ?? false;
        }

        // Obtener standings
        $this->standings = LeagueStanding::with('fantasyTeam')
            ->where('league_id', $this->selectedLeagueId)
            ->orderBy('position', 'asc')
            ->limit(10)
            ->get();

        // Stats del equipo
        if ($this->fantasyTeam) {
            $currentStanding = LeagueStanding::where('fantasy_team_id', $this->fantasyTeam->id)
                ->orderBy('gameweek_id', 'desc')
                ->first();

            $this->stats = [
                'total_points' => $this->fantasyTeam->total_points ?? 0,
                'position' => $currentStanding->position ?? '-',
                'budget' => $this->fantasyTeam->budget ?? 100.00,
                'team_value' => 0.00,
            ];
        } else {
            $this->stats = [
                'total_points' => 0,
                'position' => '-',
                'budget' => 100.00,
                'team_value' => 0.00,
            ];
        }
    }

    protected function loadEducationStats()
    {
        try {
            $user = Auth::user();
            
            // Obtener servicio de rewards
            $rewardsService = app(QuizRewardsService::class);
            $rewardStats = $rewardsService->getUserRewardStats($user);
            
            // Obtener total de quizzes completados
            $totalQuizzes = QuizAttempt::where('user_id', $user->id)
                ->where('status', QuizAttempt::STATUS_FINISHED)
                ->count();
            
            $this->educationStats = [
                'total_coins_earned' => $rewardStats['total_coins_earned'] ?? 0,
                'current_balance' => $rewardStats['current_balance'] ?? 0,
                'total_points_earned' => $rewardStats['total_points_earned'] ?? 0,
                'total_quizzes' => $totalQuizzes,
            ];
            
        } catch (\Exception $e) {
            // En caso de error, valores por defecto
            \Log::error('Error loading education stats: ' . $e->getMessage());
            
            $this->educationStats = [
                'total_coins_earned' => 0,
                'current_balance' => 0,
                'total_points_earned' => 0,
                'total_quizzes' => 0,
            ];
        }
    }

    public function render()
    {
        return view('livewire.manager.manager-dashboard');
    }
}