<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Listing;
use App\Models\Gameweek;
use App\Services\Manager\Market\ListingService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class MyListings extends Component
{
    public FantasyTeam $team;
    public ?Gameweek $currentGameweek = null;
    public bool $marketOpen = false;
    public bool $loading = false;

    protected $listeners = ['listingCreated' => '$refresh', 'listingWithdrawn' => '$refresh'];

    public function mount(FantasyTeam $team, ?Gameweek $gameweek, bool $marketOpen)
    {
        $this->team = $team;
        $this->currentGameweek = $gameweek;
        $this->marketOpen = $marketOpen;
    }

    public function withdrawListing(int $listingId)
    {
        try {
            $this->loading = true;
            
            $listing = Listing::findOrFail($listingId);
            // Autorización: verificar que el user puede retirar este listing
            $this->authorize('withdraw', $listing);
            $listingService = app(ListingService::class);
            
            $listingService->withdrawListing($listing);
            
            $this->dispatch('notify', message: __('Listing retirado.'), type: 'success');
            $this->dispatch('listingWithdrawn');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', message: __('No autorizado para retirar este listing.'), type: 'error');
        } catch (\Exception $e) {
            Log::error('Error withdrawing listing: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $listings = Listing::where('fantasy_team_id', $this->team->id)
            ->with(['player', 'offers'])
            ->orderByRaw("FIELD(status, 0, 1, 2, 3)")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.manager.market.my-listings', [
            'listings' => $listings,
        ]);
    }
}