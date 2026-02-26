<?php

namespace App\Services\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Gameweek;
use App\Models\Season;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class MarketValidationService
{
    /**
     * Validar compra de agente libre
     */
    public function validateFreeAgentPurchase(
        FantasyTeam $buyer,
        Player $player
    ): void {
        $season = $buyer->league->season;
        $currentGameweek = $this->getCurrentGameweek($season);
        
        $this->ensureMarketOpen($currentGameweek);
        $this->ensureIsFreeAgent($player, $buyer->league, $currentGameweek);
        $this->ensureSquadLimit($buyer, $currentGameweek);
        $this->ensurePositionLimit($buyer, $player->position, $currentGameweek);
        
        $price = $player->marketValue($season->id);
        $commission = $price * config('market.commission_rate', 0.05);
        $totalCost = $price + $commission;
        $this->ensureBudget($buyer, $totalCost);
        
        $this->ensureTransferLimit($buyer, $currentGameweek);
    }
    
    /**
     * Validar creación de listing
     */
    public function validateCreateListing(
        FantasyTeam $seller,
        Player $player,
        float $price
    ): void {
        $season = $seller->league->season;
        $currentGameweek = $this->getCurrentGameweek($season);
        
        $this->ensureMarketOpen($currentGameweek);
        $this->ensurePlayerOwnership($seller, $player, $currentGameweek);
        $this->ensureNotInActiveRoster($seller, $player, $currentGameweek);
        $this->ensureNoActiveListing($seller->league, $player);
        
        $marketValue = $player->marketValue($season->id);
        $this->ensureValidPrice($price, $marketValue, $seller->league);
    }
    
    /**
     * Validar creación de oferta
     */
    public function validateCreateOffer(
        FantasyTeam $buyer,
        Listing $listing,
        float $offeredPrice
    ): void {
        $currentGameweek = $this->getCurrentGameweek($buyer->league->season);
        
        $this->ensureMarketOpen($currentGameweek);
        
        if (!$listing->isActive()) {
            throw ValidationException::withMessages([
                'listing' => __('El listing no está activo.')
            ]);
        }
        
        if ($listing->fantasy_team_id === $buyer->id) {
            throw ValidationException::withMessages([
                'listing' => __('No puedes ofertar por tu propio jugador.')
            ]);
        }
        
        $commission = $offeredPrice * config('market.commission_rate', 0.05);
        $totalCost = $offeredPrice + $commission;
        $this->ensureBudget($buyer, $totalCost);
        
        $this->ensureOfferCooldown($buyer, $listing);
        $this->ensureSquadLimit($buyer, $currentGameweek);
        $this->ensurePositionLimit($buyer, $listing->player->position, $currentGameweek);
        $this->ensureTransferLimit($buyer, $currentGameweek);
    }
    
    /**
     * Validar aceptación de oferta
     */
    public function validateAcceptOffer(Offer $offer): void
    {
        if (!$offer->isPending()) {
            throw ValidationException::withMessages([
                'offer' => __('La oferta ya no está disponible.')
            ]);
        }
        
        if (!$offer->listing->isActive()) {
            throw ValidationException::withMessages([
                'listing' => __('El listing ya no está activo.')
            ]);
        }
        
        $commission = $offer->offered_price * config('market.commission_rate', 0.05);
        $totalCost = $offer->offered_price + $commission;
        $this->ensureBudget($offer->buyerTeam, $totalCost);
        
        $currentGameweek = $this->getCurrentGameweek($offer->buyerTeam->league->season);
        $this->ensureMarketOpen($currentGameweek);
    }
    
    // ========================================
    // HELPERS PRIVADOS
    // ========================================
    
    private function ensureMarketOpen(?Gameweek $gameweek): void
    {
        if (!$gameweek || $gameweek->is_closed) {
            throw ValidationException::withMessages([
                'market' => __('El mercado está cerrado.')
            ]);
        }
    }
    
    private function ensureBudget(FantasyTeam $team, float $amount): void
    {
        if (!$team->hasBudget($amount)) {
            throw ValidationException::withMessages([
                'budget' => __('Presupuesto insuficiente.')
            ]);
        }
    }
    
    private function ensureIsFreeAgent(Player $player, $league, ?Gameweek $gameweek): void
    {
        if (!$gameweek) return;
        
        $isInTeam = $player->fantasyRosters()
            ->whereHas('fantasyTeam', fn($q) => $q->where('league_id', $league->id))
            ->where('gameweek_id', $gameweek->id)
            ->exists();
        
        if ($isInTeam) {
            throw ValidationException::withMessages([
                'player' => __('El jugador no es agente libre.')
            ]);
        }
    }
    
    private function ensureSquadLimit(FantasyTeam $team, ?Gameweek $gameweek): void
    {
        if (!$gameweek) return;
        
        $count = $team->rosters()
            ->where('gameweek_id', $gameweek->id)
            ->distinct('player_id')
            ->count();
        
        $limit = config('market.squad_limits.total', 23);
        
        if ($count >= $limit) {
            throw ValidationException::withMessages([
                'squad' => __('Plantilla completa (:limit jugadores).', ['limit' => $limit])
            ]);
        }
    }
    
    private function ensurePositionLimit(FantasyTeam $team, int $position, ?Gameweek $gameweek): void
    {
        if (!$gameweek) return;
        
        $count = $team->rosters()
            ->where('gameweek_id', $gameweek->id)
            ->whereHas('player', fn($q) => $q->where('position', $position))
            ->count();
        
        $limits = config('market.squad_limits.positions', []);
        $max = $limits[$position]['max'] ?? 99;
        
        if ($count >= $max) {
            throw ValidationException::withMessages([
                'position' => __('Límite de posición alcanzado.')
            ]);
        }
    }
    
    private function ensurePlayerOwnership(FantasyTeam $team, Player $player, ?Gameweek $gameweek): void
    {
        if (!$gameweek) return;
        
        $owned = $team->rosters()
            ->where('player_id', $player->id)
            ->where('gameweek_id', $gameweek->id)
            ->exists();
        
        if (!$owned) {
            throw ValidationException::withMessages([
                'player' => __('El jugador no pertenece a tu equipo.')
            ]);
        }
    }
    
    private function ensureNotInActiveRoster(FantasyTeam $team, Player $player, ?Gameweek $gameweek): void
    {
        if (!$gameweek) return;
        
        $inRoster = $team->rosters()
            ->where('player_id', $player->id)
            ->where('gameweek_id', $gameweek->id)
            ->where('is_starter', true)
            ->exists();
        
        if ($inRoster) {
            throw ValidationException::withMessages([
                'player' => __('El jugador está en el roster activo.')
            ]);
        }
    }
    
    private function ensureNoActiveListing($league, Player $player): void
    {
        $exists = Listing::where('league_id', $league->id)
            ->where('player_id', $player->id)
            ->where('status', Listing::STATUS_ACTIVE)
            ->exists();
        
        if ($exists) {
            throw ValidationException::withMessages([
                'listing' => __('Ya existe un listing activo para este jugador.')
            ]);
        }
    }
    
    private function ensureValidPrice(float $price, float $marketValue, $league): void
    {
        $settings = $league->marketSettings;
        
        if (!$settings) return;
        
        if (!$settings->isValidPrice($price, $marketValue)) {
            throw ValidationException::withMessages([
                'price' => __('Precio fuera de rango permitido.')
            ]);
        }
    }
    
    private function ensureOfferCooldown(FantasyTeam $team, Listing $listing): void
    {
        $settings = $listing->league->marketSettings;
        $cooldown = $settings ? $settings->min_offer_cooldown_h : 2;
        
        $lastOffer = Offer::where('listing_id', $listing->id)
            ->where('buyer_fantasy_team_id', $team->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastOffer) {
            $canOfferAgain = $lastOffer->created_at->addHours($cooldown);
            
            if (now()->lt($canOfferAgain)) {
                throw ValidationException::withMessages([
                    'offer' => __('Debes esperar antes de hacer otra oferta.')
                ]);
            }
        }
    }
    
    private function ensureTransferLimit(FantasyTeam $team, ?Gameweek $gameweek): void
    {
        if (!$gameweek) return;
        
        $limit = config('market.max_transfers_per_gameweek', 3);
        $cacheKey = "transfers_count_{$team->id}_{$gameweek->id}";
        $used = Cache::get($cacheKey, 0);
        
        if ($used >= $limit) {
            throw ValidationException::withMessages([
                'transfers' => __('Has alcanzado el límite de transferencias.')
            ]);
        }
    }
    
    private function getCurrentGameweek(Season $season): ?Gameweek
    {
        return Gameweek::where('season_id', $season->id)
            ->where('is_closed', false)
            ->orderBy('number', 'asc')
            ->first();
    }
}