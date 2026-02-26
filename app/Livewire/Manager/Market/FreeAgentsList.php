<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\Gameweek;
use App\Services\Manager\Market\MarketService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class FreeAgentsList extends Component
{
    use WithPagination;

    public int $teamId;
    public ?int $currentGameweekId = null;
    public bool $marketOpen = false;
    
    // Filtros
    public string $search = '';
    public ?int $positionFilter = null;
    public string $sortBy = 'price';
    public string $sortDirection = 'asc';
    
    // Estado
    public bool $loading = false;

    public function mount(int $teamId, $gameweek = null, bool $marketOpen = false)
    {
        $this->teamId = $teamId;
        $this->currentGameweekId = $gameweek?->id ?? ($gameweek ? (int)$gameweek : null);
        $this->marketOpen = $marketOpen;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPositionFilter()
    {
        $this->resetPage();
    }

    public function setPosition(?int $position)
    {
        $this->positionFilter = $position;
        $this->resetPage();
    }

    public function setSorting(string $field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function buyPlayer(int $playerId)
    {
        
        if (!$this->currentGameweekId) {
            $this->dispatch('notify', message: __('El mercado está cerrado.'), type: 'error');
            return;
        }

        try {
            $this->loading = true;
            
            $team = FantasyTeam::findOrFail($this->teamId);
            $player = Player::findOrFail($playerId);
            $marketService = app(MarketService::class);
            
            $result = $marketService->buyFreeAgent($team, $player);
            
            $this->dispatch('playerPurchased', data: $result);
            //$this->resetPage();
            
        } catch (\Exception $e) {
            Log::error('Error buying free agent: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $team = FantasyTeam::with('league')->findOrFail($this->teamId);
        $seasonId = $team->league->season_id;
        
        $query = Player::select('players.*')->where('is_active', true)
            ->with(['valuations' => function($q) use ($seasonId) {
                $q->where('season_id', $seasonId);
            }])
            ->whereDoesntHave('fantasyRosters', function($q) {
                $q->where('fantasy_team_id', $this->teamId)
                  ->where('gameweek_id', '>=', $this->currentGameweekId);
            });

        if ($this->search) {
            $query->where(function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('known_as', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->positionFilter) {
            $query->where('position', $this->positionFilter);
        }

        if ($this->sortBy === 'price') {
            $query->leftJoin('player_valuations', function($join) use ($seasonId) {
                $join->on('players.id', '=', 'player_valuations.player_id')
                     ->where('player_valuations.season_id', '=', $seasonId);
            })->orderBy('player_valuations.market_value', $this->sortDirection);
        } else {
            $query->orderBy('known_as', $this->sortDirection);
        }

        $players = $query->paginate(12);

        return view('livewire.manager.market.free-agents-list', [
            'players' => $players,
            'team' => $team,
        ]);
    }
}