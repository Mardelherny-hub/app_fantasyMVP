<?php

namespace App\Services\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Listing;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OfferService
{
    public function __construct(
        private MarketValidationService $validationService,
        private TransferService $transferService,
    ) {}
    
    /**
     * Crear una oferta
     */
    public function createOffer(
        FantasyTeam $buyer,
        Listing $listing,
        float $offeredPrice
    ): Offer {
        return DB::transaction(function () use ($buyer, $listing, $offeredPrice) {
            $this->validationService->validateCreateOffer($buyer, $listing, $offeredPrice);
            
            $offer = Offer::create([
                'listing_id' => $listing->id,
                'buyer_fantasy_team_id' => $buyer->id,
                'offered_price' => $offeredPrice,
                'status' => Offer::STATUS_PENDING,
            ]);
            
            return $offer;
        });
    }
    
    /**
     * Aceptar una oferta
     */
    public function acceptOffer(Offer $offer): array
    {
        return DB::transaction(function () use ($offer) {
            $this->validationService->validateAcceptOffer($offer);
            
            $listing = $offer->listing;
            $seller = $listing->fantasyTeam;
            $buyer = $offer->buyerTeam;
            $player = $listing->player;
            
            $price = $offer->offered_price;
            $commission = $price * config('market.commission_rate', 0.05);
            
            $transfer = $this->transferService->createTransfer(
                $buyer,
                $seller,
                $player,
                $price
            );
            
            $buyer->updateBudget(-($price + $commission));
            $seller->updateBudget($price);
            
            $offer->update(['status' => Offer::STATUS_ACCEPTED]);
            $listing->update(['status' => Listing::STATUS_SOLD]);
            
            $listing->offers()
                ->where('id', '!=', $offer->id)
                ->where('status', Offer::STATUS_PENDING)
                ->update(['status' => Offer::STATUS_REJECTED]);
            
            return [
                'success' => true,
                'transfer' => $transfer,
                'offer' => $offer->fresh(),
                'message' => __('Oferta aceptada y transferencia completada.')
            ];
        });
    }
    
    /**
     * Rechazar una oferta
     */
    public function rejectOffer(Offer $offer): void
    {
        $offer->reject();
    }
    
    /**
     * Obtener ofertas recibidas (para mis listings)
     */
    public function getReceivedOffers(FantasyTeam $team): Collection
    {
        return Offer::whereHas('listing', fn($q) => $q->where('fantasy_team_id', $team->id))
            ->with(['listing.player', 'buyerTeam'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Obtener ofertas enviadas
     */
    public function getSentOffers(FantasyTeam $team): Collection
    {
        return Offer::where('buyer_fantasy_team_id', $team->id)
            ->with(['listing.player', 'listing.fantasyTeam'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}