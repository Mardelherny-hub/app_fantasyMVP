<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Offer;
use App\Models\Gameweek;
use App\Services\Manager\Market\OfferService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class OfferManager extends Component
{
    public FantasyTeam $team;
    public ?Gameweek $currentGameweek = null;
    public bool $marketOpen = false;
    public string $tab = 'received';
    public bool $loading = false;

    public function mount(FantasyTeam $team, ?Gameweek $gameweek, bool $marketOpen)
    {
        $this->team = $team;
        $this->currentGameweek = $gameweek;
        $this->marketOpen = $marketOpen;
    }

    public function switchTab(string $tab)
    {
        $this->tab = $tab;
    }

    public function acceptOffer(int $offerId)
    {
        try {
            $this->loading = true;
            
            $offer = Offer::findOrFail($offerId);
            $offerService = app(OfferService::class);
            
            $result = $offerService->acceptOffer($offer);
            
            $this->dispatch('notify', message: $result['message'], type: 'success');
            $this->dispatch('offerAccepted');
            
        } catch (\Exception $e) {
            Log::error('Error accepting offer: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function rejectOffer(int $offerId)
    {
        try {
            $this->loading = true;
            
            $offer = Offer::findOrFail($offerId);
            $offerService = app(OfferService::class);
            
            $offerService->rejectOffer($offer);
            
            $this->dispatch('notify', message: __('Oferta rechazada.'), type: 'success');
            
        } catch (\Exception $e) {
            Log::error('Error rejecting offer: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        $offerService = app(OfferService::class);
        
        $offers = $this->tab === 'received'
            ? $offerService->getReceivedOffers($this->team)
            : $offerService->getSentOffers($this->team);

        return view('livewire.manager.market.offer-manager', [
            'offers' => $offers,
        ]);
    }
}