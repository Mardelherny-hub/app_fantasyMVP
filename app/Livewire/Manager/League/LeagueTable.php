<?php

namespace App\Livewire\Manager\League;

use App\Models\FantasyTeam;
use App\Models\League;
use App\Models\LeagueStanding;
use App\Models\Gameweek;
use Illuminate\Support\Collection;
use Livewire\Component;

class LeagueTable extends Component
{
    // Equipo del usuario
    public FantasyTeam $team;
    public League $league;
    
    // Gameweeks
    public Collection $gameweeks;
    public ?Gameweek $selectedGameweek = null;
    public ?int $selectedGameweekId = null;
    
    // Standings
    public Collection $standings;
    public mixed $myStanding = null;
    
    // Estadísticas
    public int $totalTeams = 0;
    public int $playoffSpots = 0;
    public bool $inPlayoffZone = false;
    
    // UI State
    public bool $loading = false;
    public string $viewMode = 'current'; // current, gameweek

    /**
     * Inicializar componente
     */
    public function mount()
    {
        $user = auth()->user();
        
        // Obtener equipo del manager
        $this->team = FantasyTeam::where('user_id', $user->id)
            ->whereNotNull('league_id')
            ->with('league.season')
            ->firstOrFail();
        
        $this->league = $this->team->league;
        $this->totalTeams = FantasyTeam::where('league_id', $this->league->id)->count();
        $this->playoffSpots = $this->league->playoff_teams ?? 4;
        
        // Cargar gameweeks
        $this->loadGameweeks();
        
        // Cargar standings actuales
        $this->loadCurrentStandings();
    }

    /**
     * Cargar gameweeks de la temporada
     */
    protected function loadGameweeks(): void
    {
        $this->gameweeks = Gameweek::where('season_id', $this->league->season_id)
            ->where('is_closed', true)
            ->orderBy('number', 'desc')
            ->get();
    }

    /**
     * Cargar standings actuales (acumulados)
     */
    protected function loadCurrentStandings(): void
    {
        $this->loading = true;
        
        // Obtener último gameweek cerrado
        $latestGameweek = $this->gameweeks->first();
        
        if ($latestGameweek) {
            $this->standings = LeagueStanding::where('league_id', $this->league->id)
                ->where('gameweek_id', $latestGameweek->id)
                ->with('fantasyTeam.user')
                ->orderBy('position')
                ->get();
        } else {
            // Si no hay gameweeks cerrados, crear standings vacíos
            $this->standings = FantasyTeam::where('league_id', $this->league->id)
                ->with('user')
                ->get()
                ->map(function ($team, $index) {
                    return (object) [
                        'position' => $index + 1,
                        'fantasy_team_id' => $team->id,
                        'fantasyTeam' => $team,
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'goal_difference' => 0,
                        'points' => 0,
                        'fantasy_points' => $team->total_points,
                    ];
                });
        }
        
        // Encontrar mi posición
        $this->myStanding = $this->standings->firstWhere('fantasy_team_id', $this->team->id);
        
        // Determinar si estoy en zona de playoffs
        if ($this->myStanding) {
            $this->inPlayoffZone = $this->myStanding->position <= $this->playoffSpots;
        }
        
        $this->loading = false;
    }

    /**
     * Cargar standings de un gameweek específico
     */
    public function loadGameweekStandings(int $gameweekId): void
    {
        $this->loading = true;
        $this->selectedGameweekId = $gameweekId;
        $this->selectedGameweek = Gameweek::find($gameweekId);
        $this->viewMode = 'gameweek';
        
        $this->standings = LeagueStanding::where('league_id', $this->league->id)
            ->where('gameweek_id', $gameweekId)
            ->with('fantasyTeam.user')
            ->orderBy('position')
            ->get();
        
        $this->myStanding = $this->standings->firstWhere('fantasy_team_id', $this->team->id);
        
        if ($this->myStanding) {
            $this->inPlayoffZone = $this->myStanding->position <= $this->playoffSpots;
        }
        
        $this->loading = false;
    }

    /**
     * Volver a vista actual
     */
    public function viewCurrent(): void
    {
        $this->viewMode = 'current';
        $this->selectedGameweekId = null;
        $this->selectedGameweek = null;
        $this->loadCurrentStandings();
    }

    /**
     * Obtener clase de fila según posición
     */
    public function getRowClass(int $position, int $teamId): string
    {
        $classes = [];
        
        // Resaltar mi equipo
        if ($teamId === $this->team->id) {
            $classes[] = 'bg-blue-50 font-semibold';
        }
        
        // Zona de playoffs
        if ($position <= $this->playoffSpots) {
            $classes[] = 'border-l-4 border-green-500';
        }
        
        return implode(' ', $classes);
    }

    /**
     * Obtener badge de posición
     */
    public function getPositionBadge(int $position): array
    {
        if ($position <= $this->playoffSpots) {
            return [
                'class' => 'bg-green-100 text-green-800',
                'icon' => '🏆',
                'label' => __('Playoffs'),
            ];
        }
        
        return [
            'class' => 'bg-gray-100 text-gray-600',
            'icon' => '',
            'label' => '',
        ];
    }

    /**
     * Calcular porcentaje de victorias
     */
    public function getWinPercentage($standing): float
    {
        if ($standing->played == 0) {
            return 0;
        }
        
        return round(($standing->won / $standing->played) * 100, 1);
    }

    /**
     * Obtener forma del equipo (últimos 5 partidos)
     */
    public function getTeamForm(int $teamId): string
    {
        // Placeholder - esto requeriría consultar los últimos fixtures
        return '-';
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.league.league-table');
    }
}