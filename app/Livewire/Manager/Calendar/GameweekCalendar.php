<?php

namespace App\Livewire\Manager\Calendar;

use App\Models\FantasyTeam;
use App\Models\League;
use App\Models\Gameweek;
use App\Models\Fixture;
use App\Models\FantasyRosterScore;
use Illuminate\Support\Collection;
use Livewire\Component;

class GameweekCalendar extends Component
{
    // Equipo del usuario
    public FantasyTeam $team;
    public League $league;
    
    // Gameweeks
    public Collection $gameweeks;
    public ?Gameweek $currentGameweek = null;
    public ?Gameweek $nextGameweek = null;
    
    // Datos enriquecidos
    public array $gameweeksData = [];
    
    // UI State
    public bool $loading = false;
    public string $viewMode = 'grid'; // grid, list

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
        
        // Cargar gameweeks
        $this->loadGameweeks();
        
        // Identificar gameweek actual y próxima
        $this->identifyCurrentAndNext();
        
        // Enriquecer datos
        $this->enrichGameweeksData();
    }

    /**
     * Cargar todas las gameweeks
     */
    protected function loadGameweeks(): void
    {
        $this->gameweeks = Gameweek::where('season_id', $this->league->season_id)
            ->orderBy('number')
            ->get();
    }

    /**
     * Identificar gameweek actual y próxima
     */
    protected function identifyCurrentAndNext(): void
    {
        $now = now();
        
        // Gameweek actual (en curso)
        $this->currentGameweek = $this->gameweeks
            ->first(fn($gw) => $gw->starts_at <= $now && $gw->ends_at >= $now);
        
        // Próxima gameweek (futura más cercana)
        $this->nextGameweek = $this->gameweeks
            ->first(fn($gw) => $gw->starts_at > $now);
    }

    /**
     * Enriquecer datos de cada gameweek
     */
    protected function enrichGameweeksData(): void
    {
        $this->gameweeksData = [];
        
        foreach ($this->gameweeks as $gameweek) {
            // Contar fixtures
            $fixturesCount = Fixture::where('gameweek_id', $gameweek->id)
                ->where(function($query) {
                    $query->where('home_fantasy_team_id', $this->team->id)
                          ->orWhere('away_fantasy_team_id', $this->team->id);
                })
                ->count();
            
            // Obtener mi fixture
            $myFixture = Fixture::where('gameweek_id', $gameweek->id)
                ->where(function($query) {
                    $query->where('home_fantasy_team_id', $this->team->id)
                          ->orWhere('away_fantasy_team_id', $this->team->id);
                })
                ->with(['homeTeam', 'awayTeam'])
                ->first();
            
            // Calcular mis puntos en esta GW
            $myPoints = 0;
            if ($gameweek->is_closed) {
                $myPoints = FantasyRosterScore::where('gameweek_id', $gameweek->id)
                    ->where('fantasy_team_id', $this->team->id)
                    ->where('is_starter', true)
                    ->sum('final_points');
            }
            
            // Determinar estado
            $status = $this->getGameweekStatus($gameweek);
            
            // Tiempo hasta inicio/fin
            $timeInfo = $this->getTimeInfo($gameweek);
            
            $this->gameweeksData[] = [
                'gameweek' => $gameweek,
                'fixtures_count' => $fixturesCount,
                'my_fixture' => $myFixture,
                'my_points' => $myPoints,
                'status' => $status,
                'time_info' => $timeInfo,
                'is_current' => $this->currentGameweek && $this->currentGameweek->id === $gameweek->id,
                'is_next' => $this->nextGameweek && $this->nextGameweek->id === $gameweek->id,
            ];
        }
    }

    /**
     * Obtener estado de la gameweek
     */
    protected function getGameweekStatus(Gameweek $gameweek): string
    {
        if ($gameweek->is_closed) {
            return 'completed';
        }
        
        if ($gameweek->isActive()) {
            return 'live';
        }
        
        if ($gameweek->isUpcoming()) {
            return 'upcoming';
        }
        
        return 'pending';
    }

    /**
     * Obtener información de tiempo
     */
    protected function getTimeInfo(Gameweek $gameweek): array
    {
        $now = now();
        
        if ($gameweek->is_closed) {
            return [
                'label' => __('Finalizada'),
                'class' => 'text-gray-600',
            ];
        }
        
        if ($gameweek->isActive()) {
            $endsIn = $now->diffForHumans($gameweek->ends_at, true);
            return [
                'label' => __('Termina en') . ' ' . $endsIn,
                'class' => 'text-green-600',
            ];
        }
        
        if ($gameweek->isUpcoming()) {
            $startsIn = $now->diffForHumans($gameweek->starts_at, true);
            return [
                'label' => __('Comienza en') . ' ' . $startsIn,
                'class' => 'text-blue-600',
            ];
        }
        
        return [
            'label' => __('Pendiente'),
            'class' => 'text-gray-500',
        ];
    }

    /**
     * Cambiar modo de vista
     */
    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    /**
     * Navegar a fixtures de una GW
     */
    public function viewFixtures(int $gameweekId): void
    {
        // Redirigir a fixtures con filtro
        $this->redirect(route('manager.fixtures.index', ['locale' => app()->getLocale()]) . '?gw=' . $gameweekId);
    }

    /**
     * Navegar a scores de una GW
     */
    public function viewScores(int $gameweekId): void
    {
        // Emitir evento para abrir modal en scores
        $this->dispatch('openGameweekDetail', gameweekId: $gameweekId);
        $this->redirect(route('manager.scores.index', ['locale' => app()->getLocale()]));
    }

    /**
     * Navegar a lineup management
     */
    public function manageLineup(): void
    {
        $this->redirect(route('manager.lineup.index', ['locale' => app()->getLocale()]));
    }

    /**
     * Obtener clase de badge según estado
     */
    public function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'live' => 'bg-green-100 text-green-800 border-green-200',
            'completed' => 'bg-gray-100 text-gray-600 border-gray-200',
            'upcoming' => 'bg-blue-100 text-blue-600 border-blue-200',
            default => 'bg-yellow-100 text-yellow-600 border-yellow-200'
        };
    }

    /**
     * Obtener texto del badge según estado
     */
    public function getStatusBadgeText(string $status): string
    {
        return match($status) {
            'live' => '🔴 ' . __('En Vivo'),
            'completed' => '✓ ' . __('Completada'),
            'upcoming' => '⏰ ' . __('Próxima'),
            default => '⏳ ' . __('Pendiente')
        };
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.calendar.gameweek-calendar');
    }
}