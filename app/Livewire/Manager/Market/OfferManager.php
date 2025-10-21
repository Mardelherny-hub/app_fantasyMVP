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
    public string $tab = 'received'; // received, sent
    public bool $loading = false;

    public function mount(FantasyTeam $team, ?Gameweek $gameweek, bool $marketOpen)
    {
        $this->team = $team;
        $this->currentGameweek = $gameweek;
        $this->marketOpen = $marketOpen;
    }

    /**
     * Cambiar entre tabs de ofertas recibidas y enviadas
     */
    public function switchTab(string $tab)
    {
        $this->tab = in_array($tab, ['received', 'sent']) ? $tab : 'received';
    }

    /**
     * Aceptar una oferta (vendedor)
     */
    public function acceptOffer(int $offerId)
    {
        try {
            $this->loading = true;
            
            $offer = Offer::findOrFail($offerId);
            
            // Autorización: verificar que el user puede aceptar esta oferta (debe ser el vendedor)
            $this->authorize('accept', $offer);
            
            $offerService = app(OfferService::class);
            $result = $offerService->acceptOffer($offer);
            
            $this->dispatch('notify', message: $result['message'] ?? __('Oferta aceptada. Transferencia completada.'), type: 'success');
            $this->dispatch('offerAccepted');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', message: __('No autorizado para aceptar esta oferta.'), type: 'error');
        } catch (\Exception $e) {
            Log::error('Error accepting offer: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Rechazar una oferta (vendedor)
     */
    public function rejectOffer(int $offerId)
    {
        try {
            $this->loading = true;
            
            $offer = Offer::findOrFail($offerId);
            
            // Autorización: verificar que el user puede rechazar esta oferta (debe ser el vendedor)
            $this->authorize('reject', $offer);
            
            $offerService = app(OfferService::class);
            $offerService->rejectOffer($offer);
            
            $this->dispatch('notify', message: __('Oferta rechazada.'), type: 'success');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', message: __('No autorizado para rechazar esta oferta.'), type: 'error');
        } catch (\Exception $e) {
            Log::error('Error rejecting offer: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Cancelar una oferta enviada (comprador)
     */
    public function cancelOffer(int $offerId)
    {
        try {
            $this->loading = true;
            
            $offer = Offer::findOrFail($offerId);
            
            // Autorización: verificar que el user puede cancelar esta oferta (debe ser el comprador)
            $this->authorize('cancel', $offer);
            
            // Cancelar = marcar como rejected
            $offer->update(['status' => Offer::STATUS_REJECTED]);
            
            $this->dispatch('notify', message: __('Oferta cancelada.'), type: 'success');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('notify', message: __('No autorizado para cancelar esta oferta.'), type: 'error');
        } catch (\Exception $e) {
            Log::error('Error canceling offer: ' . $e->getMessage());
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Renderizar componente
     */
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