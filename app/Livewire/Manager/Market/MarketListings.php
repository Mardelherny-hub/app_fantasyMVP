<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Listing;
use App\Models\Gameweek;
use App\Models\Offer;
use App\Services\Manager\Market\OfferService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class MarketListings extends Component
{
    use WithPagination;

    public FantasyTeam $team;
    public ?Gameweek $currentGameweek = null;
    public bool $marketOpen = false;
    
    // Filtros
    public string $search = '';
    public ?int $positionFilter = null;
    public ?float $maxPrice = null;
    
    // Modal de oferta
    public bool $showOfferModal = false;
    public ?int $selectedListingId = null;
    public float $offerPrice = 0;
    
    // Estado
    public bool $loading = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'positionFilter' => ['except' => null],
    ];

    public function mount(FantasyTeam $team, ?Gameweek $gameweek, bool $marketOpen)
    {
        $this->team = $team;
        $this->currentGameweek = $gameweek;
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

    public function openOfferModal(int $listingId)
    {
        $listing = Listing::with('player')->findOrFail($listingId);
        
        $this->selectedListingId = $listingId;
        $this->offerPrice = $listing->price;
        $this->showOfferModal = true;
    }

    public function closeOfferModal()
    {
        $this->showOfferModal = false;
        $this->selectedListingId = null;
        $this->offerPrice = 0;
    }

    public function makeOffer()
    {
        $this->validate([
            'offerPrice' => 'required|numeric|min:0.50',
        ]);

        try {
            $this->loading = true;
            
            $listing = Listing::findOrFail($this->selectedListingId);
            // Autorización: verificar que el user puede crear oferta para este listing
            $this->authorize('create', [Offer::class, $listing, $this->team]);
            $offerService = app(OfferService::class);
            
            $offer = $offerService->createOffer($this->team, $listing, $this->offerPrice);
            
            $this->dispatch('notify', message: __('Oferta enviada correctamente.'), type: 'success');
            $this->dispatch('offerSent', data: ['offer' => $offer]);
            $this->closeOfferModal();
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', message: __('No autorizado para crear esta oferta.'), type: 'error');
        } catch (\Exception $e) {
            Log::error('Error making offer: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $query = Listing::where('league_id', $this->team->league_id)
            ->where('status', Listing::STATUS_ACTIVE)
            ->where('fantasy_team_id', '!=', $this->team->id)
            ->with(['player.valuations', 'fantasyTeam']);

        if ($this->search) {
            $query->whereHas('player', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('known_as', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->positionFilter) {
            $query->whereHas('player', function($q) {
                $q->where('position', $this->positionFilter);
            });
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.manager.market.market-listings', [
            'listings' => $listings,
        ]);
    }
}