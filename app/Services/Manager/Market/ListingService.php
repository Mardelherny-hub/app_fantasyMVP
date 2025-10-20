<?php

namespace App\Services\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\Listing;
use App\Models\League;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ListingService
{
    public function __construct(
        private MarketValidationService $validationService,
    ) {}
    
    /**
     * Crear un listing
     */
    public function createListing(
        FantasyTeam $seller,
        Player $player,
        float $price
    ): Listing {
        return DB::transaction(function () use ($seller, $player, $price) {
            $this->validationService->validateCreateListing($seller, $player, $price);
            
            $listing = Listing::create([
                'league_id' => $seller->league_id,
                'fantasy_team_id' => $seller->id,
                'player_id' => $player->id,
                'price' => $price,
                'status' => Listing::STATUS_ACTIVE,
                'expires_at' => null,
            ]);
            
            return $listing;
        });
    }
    
    /**
     * Retirar un listing
     */
    public function withdrawListing(Listing $listing): void
    {
        DB::transaction(function () use ($listing) {
            if (!$listing->canBeWithdrawn()) {
                throw ValidationException::withMessages([
                    'listing' => __('No se puede retirar un listing con ofertas aceptadas.')
                ]);
            }
            
            $listing->withdraw();
            
            $listing->offers()
                ->where('status', \App\Models\Offer::STATUS_PENDING)
                ->update(['status' => \App\Models\Offer::STATUS_REJECTED]);
        });
    }
    
    /**
     * Obtener listings activos de una liga (excepto del equipo dado)
     */
    public function getActiveListings(
        League $league,
        ?FantasyTeam $excludeTeam = null,
        array $filters = []
    ): Collection {
        $query = Listing::where('league_id', $league->id)
            ->where('status', Listing::STATUS_ACTIVE)
            ->with(['player', 'fantasyTeam', 'offers']);
        
        if ($excludeTeam) {
            $query->where('fantasy_team_id', '!=', $excludeTeam->id);
        }
        
        if (!empty($filters['position'])) {
            $query->whereHas('player', fn($q) => $q->where('position', $filters['position']));
        }
        
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        
        if (!empty($filters['search'])) {
            $query->whereHas('player', fn($q) => 
                $q->where('full_name', 'like', '%' . $filters['search'] . '%')
            );
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * Obtener mis listings
     */
    public function getMyListings(FantasyTeam $team): Collection
    {
        return Listing::where('fantasy_team_id', $team->id)
            ->with(['player', 'offers'])
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}