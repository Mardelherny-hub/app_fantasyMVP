<?php

namespace App\Livewire\Manager\Fixtures;

use App\Models\FantasyTeam;
use App\Models\Fixture;
use App\Models\Gameweek;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class FixturesCalendar extends Component
{
    use WithPagination;

    // Equipo del usuario
    public FantasyTeam $team;
    
    // Filtros
    public ?int $selectedGameweekId = null;
    public string $statusFilter = 'all'; // all, pending, finished
    
    // Colecciones
    public Collection $gameweeks;
    public Collection $upcomingFixtures;
    public Collection $recentFixtures;
    
    // UI State
    public bool $loading = false;
    public ?int $selectedFixtureId = null;

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

        // Cargar gameweeks disponibles
        $this->loadGameweeks();
        
        // Cargar fixtures iniciales
        $this->loadFixtures();
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
     * Cargar fixtures del equipo
     */
    public function loadFixtures(): void
    {
        $this->loading = true;

        // Próximos fixtures (pending)
        $this->upcomingFixtures = $this->getFixturesQuery()
            ->where('status', Fixture::STATUS_PENDING)
            ->with([
                'gameweek',
                'homeTeam.user',
                'awayTeam.user'
            ])
            ->orderBy('gameweek_id', 'asc')
            ->limit(5)
            ->get();

        // Resultados recientes (finished)
        $this->recentFixtures = $this->getFixturesQuery()
            ->where('status', Fixture::STATUS_FINISHED)
            ->with([
                'gameweek',
                'homeTeam.user',
                'awayTeam.user'
            ])
            ->orderBy('gameweek_id', 'desc')
            ->limit(5)
            ->get();

        $this->loading = false;
    }

    /**
     * Query base de fixtures del equipo
     */
    protected function getFixturesQuery()
    {
        $query = Fixture::where('league_id', $this->team->league_id)
            ->where(function ($q) {
                $q->where('home_fantasy_team_id', $this->team->id)
                  ->orWhere('away_fantasy_team_id', $this->team->id);
            });

        // Aplicar filtro de gameweek
        if ($this->selectedGameweekId) {
            $query->where('gameweek_id', $this->selectedGameweekId);
        }

        return $query;
    }

    /**
     * Filtrar por gameweek
     */
    public function filterByGameweek(?int $gameweekId): void
    {
        $this->selectedGameweekId = $gameweekId;
        $this->loadFixtures();
    }

    /**
     * Cambiar filtro de status
     */
    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    /**
     * Ver detalle de fixture
     */
    public function viewFixture(int $fixtureId): void
    {
        $this->selectedFixtureId = $fixtureId;
        $this->dispatch('open-fixture-detail', fixtureId: $fixtureId);
    }

    /**
     * Limpiar filtros
     */
    public function clearFilters(): void
    {
        $this->selectedGameweekId = null;
        $this->statusFilter = 'all';
        $this->loadFixtures();
        $this->resetPage();
    }

    /**
     * Obtener todos los fixtures con filtros aplicados
     */
    public function getAllFixtures()
    {
        $query = $this->getFixturesQuery()
            ->with([
                'gameweek',
                'homeTeam.user',
                'awayTeam.user'
            ]);

        // Aplicar filtro de status
        if ($this->statusFilter === 'pending') {
            $query->where('status', Fixture::STATUS_PENDING);
        } elseif ($this->statusFilter === 'finished') {
            $query->where('status', Fixture::STATUS_FINISHED);
        }

        return $query->orderBy('gameweek_id', 'desc')
            ->paginate(10);
    }

    /**
     * Determinar si el equipo es local en un fixture
     */
    public function isHomeTeam(Fixture $fixture): bool
    {
        return $fixture->home_fantasy_team_id === $this->team->id;
    }

    /**
     * Obtener equipo rival
     */
    public function getOpponent(Fixture $fixture): FantasyTeam
    {
        return $this->isHomeTeam($fixture) 
            ? $fixture->awayTeam 
            : $fixture->homeTeam;
    }

    /**
     * Obtener resultado para mostrar
     */
    public function getResult(Fixture $fixture): string
    {
        if ($fixture->status === Fixture::STATUS_PENDING) {
            return 'vs';
        }

        $isHome = $this->isHomeTeam($fixture);
        $myGoals = $isHome ? $fixture->home_goals : $fixture->away_goals;
        $opponentGoals = $isHome ? $fixture->away_goals : $fixture->home_goals;

        return "{$myGoals} - {$opponentGoals}";
    }

    /**
     * Determinar resultado del partido (W/D/L)
     */
    public function getMatchOutcome(Fixture $fixture): ?string
    {
        if ($fixture->status === Fixture::STATUS_PENDING) {
            return null;
        }

        $isHome = $this->isHomeTeam($fixture);
        $myGoals = $isHome ? $fixture->home_goals : $fixture->away_goals;
        $opponentGoals = $isHome ? $fixture->away_goals : $fixture->home_goals;

        if ($myGoals > $opponentGoals) {
            return 'W'; // Victoria
        } elseif ($myGoals < $opponentGoals) {
            return 'L'; // Derrota
        } else {
            return 'D'; // Empate
        }
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.manager.fixtures.fixtures-calendar', [
            'allFixtures' => $this->getAllFixtures(),
        ]);
    }
}