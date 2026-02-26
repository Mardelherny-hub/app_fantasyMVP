<?php

namespace App\Services\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\League;
use App\Models\Season;
use App\Models\Gameweek;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class MarketService
{
    public function __construct(
        private ListingService $listingService,
        private OfferService $offerService,
        private TransferService $transferService,
        private MarketValidationService $validationService,
    ) {}
    
    /**
     * Comprar agente libre
     */
    public function buyFreeAgent(
        FantasyTeam $buyer,
        Player $player
    ): array {
        return DB::transaction(function () use ($buyer, $player) {
            $this->validationService->validateFreeAgentPurchase($buyer, $player);
            
            $price = $player->marketValue($buyer->league->season_id) ?? 0.50;
            $commission = $price * config('market.commission_rate', 0.05);
            $totalCost = $price + $commission;
            
            $transfer = $this->transferService->createFreeAgentTransfer($buyer, $player, $price);
            
            $buyer->updateBudget(-$totalCost);
            
            $currentGameweek = $this->getCurrentGameweek($buyer->league->season);
            if ($currentGameweek) {
                $this->transferService->incrementTransfersCount($buyer, $currentGameweek);
            }
            
            return [
                'success' => true,
                'transfer' => $transfer,
                'new_budget' => $buyer->fresh()->budget,
                'message' => __('Jugador fichado correctamente.')
            ];
        });
    }
    
    /**
     * Obtener agentes libres
     */
    public function getFreeAgents(
        League $league,
        ?Gameweek $currentGameweek,
        array $filters = []
    ): Collection {
        $query = Player::query()
            ->where('is_active', true)
            ->whereDoesntHave('fantasyRosters', function($q) use ($league, $currentGameweek) {
                $q->whereHas('fantasyTeam', fn($qt) => $qt->where('league_id', $league->id));
                if ($currentGameweek) {
                    $q->where('gameweek_id', $currentGameweek->id);
                }
            })
            ->with(['valuations' => fn($q) => $q->where('season_id', $league->season_id)]);
        
        if (!empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('full_name', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->get();
    }
    
    /**
     * Verificar si el mercado está abierto
     */
    public function isMarketOpen(?Gameweek $gameweek): bool
    {
        if (!$gameweek) {
            return false;
        }
        
        return !$gameweek->is_closed;
    }
    
    /**
     * Obtener gameweek actual
     */
    private function getCurrentGameweek(Season $season): ?Gameweek
    {
        return Gameweek::where('season_id', $season->id)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    /**
     * Obtener jugadores disponibles para compra (agentes libres + listings)
     */
    public function getAvailablePlayers(
        FantasyTeam $team,
        array $filters = []
    ): array {
        $league = $team->league;
        $season = $league->season;
        $currentGameweek = $this->getCurrentGameweek($season);
        
        $freeAgents = $this->getFreeAgents($league, $currentGameweek, $filters);
        $listings = $this->listingService->getActiveListings($league, $team, $filters);
        
        return [
            'free_agents' => $freeAgents,
            'listings' => $listings,
            'current_gameweek' => $currentGameweek,
            'market_open' => $this->isMarketOpen($currentGameweek),
            'transfers_used' => $this->getTransfersUsed($team, $currentGameweek),
            'transfers_limit' => config('market.max_transfers_per_gameweek', 3),
        ];
    }

    /**
     * Obtener transferencias usadas en el gameweek actual
     */
    private function getTransfersUsed(FantasyTeam $team, ?Gameweek $gameweek): int
    {
        if (!$gameweek) {
            return 0;
        }
        
        return $this->transferService->getTransfersUsed($team, $gameweek);
    }

    /**
     * Obtener estadísticas del mercado
     */
    public function getMarketStats(League $league): array
    {
        return [
            'total_transfers' => \App\Models\Transfer::where('league_id', $league->id)->count(),
            'active_listings' => \App\Models\Listing::where('league_id', $league->id)
                ->where('status', \App\Models\Listing::STATUS_ACTIVE)
                ->count(),
            'pending_offers' => \App\Models\Offer::whereHas('listing', fn($q) => 
                $q->where('league_id', $league->id))
                ->where('status', \App\Models\Offer::STATUS_PENDING)
                ->count(),
            'avg_transfer_price' => \App\Models\Transfer::where('league_id', $league->id)
                ->avg('price') ?? 0,
        ];
    }
}