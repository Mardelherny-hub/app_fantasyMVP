<?php

namespace App\Livewire\Manager\Fixtures;

use App\Models\Fixture;
use App\Models\FantasyTeam;
use App\Models\FantasyRosterScore;
use Illuminate\Support\Collection;
use Livewire\Component;

class FixtureDetail extends Component
{
    // Props
    public int $fixtureId;
    public Fixture $fixture;
    public FantasyTeam $userTeam;
    
    // Datos del partido
    public bool $isHomeTeam;
    public FantasyTeam $myTeam;
    public FantasyTeam $opponentTeam;
    public int $myGoals;
    public int $opponentGoals;
    public ?string $result = null; // W, D, L
    
    // Alineaciones
    public Collection $myStarters;
    public Collection $myBench;
    public Collection $opponentStarters;
    public Collection $opponentBench;
    
    // Estadísticas
    public int $myTotalPoints = 0;
    public int $opponentTotalPoints = 0;
    public ?FantasyRosterScore $myCaptain = null;
    public ?FantasyRosterScore $myViceCaptain = null;
    
    // UI State
    public bool $showOpponentLineup = false;
    public bool $loading = false;

    /**
     * Mount component
     */
    public function mount(int $fixtureId)
    {
        $this->fixtureId = $fixtureId;
        $this->loading = true;
        
        // Cargar fixture
        $this->fixture = Fixture::with([
            'gameweek',
            'homeTeam.user',
            'awayTeam.user',
            'league'
        ])->findOrFail($fixtureId);
        
        // Obtener equipo del usuario
        $user = auth()->user();
        $this->userTeam = FantasyTeam::where('user_id', $user->id)
            ->where('league_id', $this->fixture->league_id)
            ->firstOrFail();
        
        // Determinar si es local o visitante
        $this->isHomeTeam = $this->fixture->home_fantasy_team_id === $this->userTeam->id;
        
        // Asignar equipos
        $this->myTeam = $this->isHomeTeam ? $this->fixture->homeTeam : $this->fixture->awayTeam;
        $this->opponentTeam = $this->isHomeTeam ? $this->fixture->awayTeam : $this->fixture->homeTeam;
        
        // Asignar goles
        $this->myGoals = $this->isHomeTeam ? $this->fixture->home_goals : $this->fixture->away_goals;
        $this->opponentGoals = $this->isHomeTeam ? $this->fixture->away_goals : $this->fixture->home_goals;
        
        // Determinar resultado
        if ($this->fixture->status === Fixture::STATUS_FINISHED) {
            if ($this->myGoals > $this->opponentGoals) {
                $this->result = 'W';
            } elseif ($this->myGoals < $this->opponentGoals) {
                $this->result = 'L';
            } else {
                $this->result = 'D';
            }
        }
        
        // Cargar alineaciones y puntos
        $this->loadLineups();
        
        $this->loading = false;
    }

    /**
     * Cargar alineaciones y puntos
     */
    protected function loadLineups(): void
    {
        // Mi alineación
        $myScores = FantasyRosterScore::where('gameweek_id', $this->fixture->gameweek_id)
            ->where('fantasy_team_id', $this->myTeam->id)
            ->with(['player', 'fantasyRoster'])
            ->get();
        
        $this->myStarters = $myScores->where('is_starter', true)->sortBy('fantasyRoster.slot');
        $this->myBench = $myScores->where('is_starter', false)->sortBy('fantasyRoster.slot');
        
        // Capitanes
        $this->myCaptain = $myScores->where('is_captain', true)->first();
        $this->myViceCaptain = $myScores->where('is_vice_captain', true)->first();
        
        // Total de puntos
        $this->myTotalPoints = $this->myStarters->sum('final_points');
        
        // Alineación del oponente
        $opponentScores = FantasyRosterScore::where('gameweek_id', $this->fixture->gameweek_id)
            ->where('fantasy_team_id', $this->opponentTeam->id)
            ->with(['player', 'fantasyRoster'])
            ->get();
        
        $this->opponentStarters = $opponentScores->where('is_starter', true)->sortBy('fantasyRoster.slot');
        $this->opponentBench = $opponentScores->where('is_starter', false)->sortBy('fantasyRoster.slot');
        
        $this->opponentTotalPoints = $this->opponentStarters->sum('final_points');
    }

    /**
     * Toggle mostrar alineación del oponente
     */
    public function toggleOpponentLineup(): void
    {
        $this->showOpponentLineup = !$this->showOpponentLineup;
    }

    /**
     * Volver al calendario
     */
    public function backToCalendar(): void
    {
        redirect()->route('manager.fixtures.index', ['locale' => app()->getLocale()]);
    }

    /**
     * Obtener clase de badge según resultado
     */
    public function getResultBadgeClass(): string
    {
        return match($this->result) {
            'W' => 'bg-green-100 text-green-800',
            'D' => 'bg-yellow-100 text-yellow-800',
            'L' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Obtener texto del resultado
     */
    public function getResultText(): string
    {
        return match($this->result) {
            'W' => __('Victoria'),
            'D' => __('Empate'),
            'L' => __('Derrota'),
            default => __('Pendiente')
        };
    }

    /**
     * Obtener posición del jugador
     */
    public function getPositionLabel(int $position): string
    {
        return match($position) {
            1 => 'GK',
            2 => 'DF',
            3 => 'MF',
            4 => 'FW',
            default => '??'
        };
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.fixtures.fixture-detail');
    }
}