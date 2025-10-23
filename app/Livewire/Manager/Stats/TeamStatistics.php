<?php

namespace App\Livewire\Manager\Stats;

use App\Models\FantasyTeam;
use App\Models\League;
use App\Models\Player;
use App\Models\FantasyRosterScore;
use App\Models\Gameweek;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TeamStatistics extends Component
{
    // Equipo del usuario
    public FantasyTeam $team;
    public League $league;
    
    // Estadísticas generales
    public int $totalPoints = 0;
    public float $averagePoints = 0;
    public int $bestGameweekPoints = 0;
    public int $worstGameweekPoints = 0;
    public ?int $bestGameweekNumber = null;
    public ?int $worstGameweekNumber = null;
    
    // Top performers
    public Collection $topScorers;
    public Collection $topCaptains;
    public Collection $mostUsedPlayers;
    
    // Estadísticas por posición
    public array $statsByPosition = [];
    
    // Capitanes
    public array $captainStats = [];
    
    // Comparativa con la liga
    public ?int $leaguePosition = null;
    public float $leagueAverage = 0;
    public int $pointsAboveAverage = 0;
    
    // UI State
    public bool $loading = false;
    public string $selectedTab = 'overview'; // overview, players, captains, comparison

    /**
     * Inicializar componente
     */
    public function mount()
    {
        $this->loading = true;
        
        $user = auth()->user();
        
        // Obtener equipo del manager
        $this->team = FantasyTeam::where('user_id', $user->id)
            ->whereNotNull('league_id')
            ->with('league.season')
            ->firstOrFail();
        
        $this->league = $this->team->league;
        
        // Cargar todas las estadísticas
        $this->loadGeneralStats();
        $this->loadTopPerformers();
        $this->loadStatsByPosition();
        $this->loadCaptainStats();
        $this->loadLeagueComparison();
        
        $this->loading = false;
    }

    /**
     * Cargar estadísticas generales
     */
    protected function loadGeneralStats(): void
    {
        // Total de puntos
        $this->totalPoints = $this->team->total_points ?? 0;
        
        // Puntos por gameweek
        $gameweekPoints = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_starter', true)
            ->selectRaw('gameweek_id, SUM(final_points) as total')
            ->groupBy('gameweek_id')
            ->get();
        
        if ($gameweekPoints->isNotEmpty()) {
            // Promedio
            $this->averagePoints = round($gameweekPoints->avg('total'), 1);
            
            // Mejor gameweek
            $best = $gameweekPoints->sortByDesc('total')->first();
            $this->bestGameweekPoints = $best->total;
            $this->bestGameweekNumber = Gameweek::find($best->gameweek_id)?->number;
            
            // Peor gameweek
            $worst = $gameweekPoints->sortBy('total')->first();
            $this->worstGameweekPoints = $worst->total;
            $this->worstGameweekNumber = Gameweek::find($worst->gameweek_id)?->number;
        }
    }

    /**
     * Cargar top performers
     */
    protected function loadTopPerformers(): void
    {
        // Top 5 jugadores con más puntos (solo titulares)
        $this->topScorers = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_starter', true)
            ->select('player_id', DB::raw('SUM(final_points) as total_points'), DB::raw('COUNT(*) as appearances'))
            ->groupBy('player_id')
            ->orderByDesc('total_points')
            ->limit(5)
            ->with('player')
            ->get();
        
        // Top 5 capitanes más efectivos
        $this->topCaptains = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_captain', true)
            ->select('player_id', DB::raw('SUM(base_points) as total_base_points'), DB::raw('COUNT(*) as times_captain'))
            ->groupBy('player_id')
            ->orderByDesc('total_base_points')
            ->limit(5)
            ->with('player')
            ->get();
        
        // Jugadores más usados como titulares
        $this->mostUsedPlayers = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_starter', true)
            ->select('player_id', DB::raw('COUNT(*) as times_starter'), DB::raw('SUM(final_points) as total_points'))
            ->groupBy('player_id')
            ->orderByDesc('times_starter')
            ->limit(5)
            ->with('player')
            ->get();
    }

    /**
     * Cargar estadísticas por posición
     */
    protected function loadStatsByPosition(): void
    {
        $positions = [1 => 'GK', 2 => 'DF', 3 => 'MF', 4 => 'FW'];
        
        foreach ($positions as $posId => $posName) {
            $stats = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
                ->where('is_starter', true)
                ->whereHas('player', function($query) use ($posId) {
                    $query->where('position', $posId);
                })
                ->selectRaw('
                    SUM(final_points) as total_points,
                    AVG(final_points) as avg_points,
                    COUNT(*) as appearances
                ')
                ->first();
            
            $this->statsByPosition[$posName] = [
                'total_points' => $stats->total_points ?? 0,
                'avg_points' => $stats->avg_points ? round($stats->avg_points, 1) : 0,
                'appearances' => $stats->appearances ?? 0,
            ];
        }
    }

    /**
     * Cargar estadísticas de capitanes
     */
    protected function loadCaptainStats(): void
    {
        // Total de puntos extra por capitanes
        $captainBonus = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_captain', true)
            ->sum('base_points'); // El bonus es base_points (porque final = base * 2)
        
        // Cantidad de veces que el capitán jugó
        $captainAppearances = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_captain', true)
            ->where('base_points', '>', 0)
            ->count();
        
        // Cantidad de veces que el vice reemplazó
        $viceReplacements = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
            ->where('is_vice_captain', true)
            ->where('final_points', '>', DB::raw('base_points'))
            ->count();
        
        $this->captainStats = [
            'total_bonus' => $captainBonus,
            'captain_appearances' => $captainAppearances,
            'vice_replacements' => $viceReplacements,
            'avg_captain_points' => $captainAppearances > 0 ? round($captainBonus / $captainAppearances, 1) : 0,
        ];
    }

    /**
     * Cargar comparativa con la liga
     */
    protected function loadLeagueComparison(): void
    {
        // Mi posición en la liga
        $teams = FantasyTeam::where('league_id', $this->league->id)
            ->orderBy('total_points', 'desc')
            ->pluck('id');
        
        $this->leaguePosition = $teams->search($this->team->id) + 1;
        
        // Promedio de la liga
        $this->leagueAverage = round(
            FantasyTeam::where('league_id', $this->league->id)->avg('total_points'),
            1
        );
        
        // Puntos por encima/debajo del promedio
        $this->pointsAboveAverage = $this->totalPoints - (int)$this->leagueAverage;
    }

    /**
     * Cambiar tab activo
     */
    public function selectTab(string $tab): void
    {
        $this->selectedTab = $tab;
    }

    /**
     * Obtener clase de color según posición
     */
    public function getPositionColor(string $position): string
    {
        return match($position) {
            'GK' => 'bg-yellow-100 text-yellow-800',
            'DF' => 'bg-blue-100 text-blue-800',
            'MF' => 'bg-green-100 text-green-800',
            'FW' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Obtener label de posición
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
        return view('livewire.manager.stats.team-statistics');
    }
}