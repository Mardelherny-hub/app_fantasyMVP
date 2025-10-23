<?php

namespace App\Livewire\Manager\Scores;

use App\Models\FantasyTeam;
use App\Models\Gameweek;
use App\Models\FantasyRosterScore;
use Illuminate\Support\Collection;
use Livewire\Component;

class ScoresOverview extends Component
{
    // Equipo del usuario
    public FantasyTeam $team;
    
    // Gameweeks
    public Collection $gameweeks;
    public ?Gameweek $selectedGameweek = null;
    
    // Estadísticas generales
    public int $totalPoints = 0;
    public float $averagePoints = 0;
    public ?int $bestGameweekPoints = null;
    public ?int $worstGameweekPoints = null;
    public ?int $bestGameweekNumber = null;
    public ?int $worstGameweekNumber = null;
    
    // Datos por gameweek
    public Collection $gameweekScores;
    
    // UI State
    public bool $loading = false;
    public ?int $selectedGameweekId = null;
    public bool $showDetail = false;

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

        // Cargar gameweeks
        $this->loadGameweeks();
        
        // Calcular estadísticas generales
        $this->calculateOverallStats();
        
        // Cargar scores por gameweek
        $this->loadGameweekScores();
    }

    /**
     * Cargar gameweeks de la temporada
     */
    protected function loadGameweeks(): void
    {
        $this->gameweeks = Gameweek::where('season_id', $this->team->league->season_id)
            ->orderBy('number', 'asc')
            ->get();
    }

    /**
     * Calcular estadísticas generales
     */
    protected function calculateOverallStats(): void
    {
        // Total de puntos del equipo
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
     * Cargar scores agrupados por gameweek
     */
    protected function loadGameweekScores(): void
    {
        $this->gameweekScores = collect();
        
        foreach ($this->gameweeks as $gameweek) {
            // Sumar puntos de titulares en este gameweek
            $totalPoints = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
                ->where('gameweek_id', $gameweek->id)
                ->where('is_starter', true)
                ->sum('final_points');
            
            // Contar jugadores que jugaron
            $playersPlayed = FantasyRosterScore::where('fantasy_team_id', $this->team->id)
                ->where('gameweek_id', $gameweek->id)
                ->where('is_starter', true)
                ->where('base_points', '>', 0)
                ->count();
            
            $this->gameweekScores->push([
                'gameweek' => $gameweek,
                'total_points' => $totalPoints,
                'players_played' => $playersPlayed,
                'is_closed' => $gameweek->is_closed,
            ]);
        }
    }

    /**
     * Ver detalle de un gameweek
     */
    public function viewGameweekDetail(int $gameweekId): void
    {
        $this->selectedGameweekId = $gameweekId;
        $this->selectedGameweek = Gameweek::find($gameweekId);
        $this->showDetail = true;
    }

    /**
     * Cerrar detalle
     */
    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedGameweekId = null;
        $this->selectedGameweek = null;
    }

    /**
     * Obtener datos para gráfico
     */
    public function getChartData(): array
    {
        $labels = [];
        $data = [];
        
        foreach ($this->gameweekScores as $score) {
            if ($score['is_closed']) {
                $labels[] = 'GW' . $score['gameweek']->number;
                $data[] = $score['total_points'];
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Obtener ranking en la liga (opcional)
     */
    public function getLeagueRanking(?int $gameweekId = null): ?int
    {
        if (!$gameweekId) {
            // Ranking general
            $teams = FantasyTeam::where('league_id', $this->team->league_id)
                ->orderBy('total_points', 'desc')
                ->pluck('id');
            
            return $teams->search($this->team->id) + 1;
        }
        
        // Ranking en gameweek específico
        $scores = FantasyRosterScore::where('gameweek_id', $gameweekId)
            ->selectRaw('fantasy_team_id, SUM(final_points) as total')
            ->groupBy('fantasy_team_id')
            ->orderByDesc('total')
            ->pluck('fantasy_team_id');
        
        $position = $scores->search($this->team->id);
        return $position !== false ? $position + 1 : null;
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.scores.scores-overview', [
            'chartData' => $this->getChartData(),
        ]);
    }
}