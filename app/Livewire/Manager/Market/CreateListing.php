<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\FantasyRoster;
use App\Models\Gameweek;
use App\Models\Listing;
use App\Services\Manager\Market\ListingService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CreateListing extends Component
{
    public FantasyTeam $team;
    public ?Gameweek $currentGameweek = null;
    public bool $marketOpen = false;
    
    // Datos del formulario
    public ?int $selectedPlayerId = null;
    public float $price = 0;
    public float $suggestedPrice = 0;
    
    // Estado
    public bool $loading = false;

    public function mount(FantasyTeam $team, ?Gameweek $gameweek, bool $marketOpen)
    {
        $this->team = $team;
        $this->currentGameweek = $gameweek;
        $this->marketOpen = $marketOpen;
    }

    public function updatedSelectedPlayerId($playerId)
    {
        if (!$playerId) {
            $this->price = 0;
            $this->suggestedPrice = 0;
            return;
        }

        $player = Player::with('valuations')->find($playerId);
        if ($player) {
            $marketValue = $player->marketValue($this->team->league->season_id) ?? 0.50;
            $this->suggestedPrice = round($marketValue * 1.05, 2);
            $this->price = $this->suggestedPrice;
        }
    }

    public function createListing()
    {
        // Autorización: verificar que el user puede crear listing para este team
        try {
            $this->authorize('create', [Listing::class, $this->team]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', message: __('No autorizado para crear listing.'), type: 'error');
            return;
        }
        
        $this->validate([
            'selectedPlayerId' => 'required|exists:players,id',
            'price' => 'required|numeric|min:0.50',
        ]);

        if (!$this->marketOpen) {
            $this->dispatch('notify', message: __('El mercado está cerrado.'), type: 'error');
            return;
        }

        try {
            $this->loading = true;
            
            $player = Player::findOrFail($this->selectedPlayerId);
            $listingService = app(ListingService::class);
            
            $listing = $listingService->createListing($this->team, $player, $this->price);
            
            $this->dispatch('notify', message: __('Jugador listado para venta.'), type: 'success');
            $this->dispatch('listingCreated', data: ['listing' => $listing]);
            
            $this->reset(['selectedPlayerId', 'price', 'suggestedPrice']);
            
        } catch (\Exception $e) {
            Log::error('Error creating listing: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $availablePlayers = [];

        if ($this->currentGameweek) {
            $availablePlayers = FantasyRoster::where('fantasy_team_id', $this->team->id)
                ->where('gameweek_id', $this->currentGameweek->id)
                ->where('is_starter', false)
                ->with(['player.valuations'])
                ->get()
                ->pluck('player')
                ->filter(function($player) {
                    return !$player->listings()
                        ->where('league_id', $this->team->league_id)
                        ->where('status', 0)
                        ->exists();
                });
        }

        return view('livewire.manager.market.create-listing', [
            'availablePlayers' => $availablePlayers,
        ]);
    }
}