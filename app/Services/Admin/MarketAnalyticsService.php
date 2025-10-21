<?php

namespace App\Services\Admin\Market;

use App\Models\League;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Transfer;
use App\Models\Player;
use App\Models\FantasyTeam;
use Illuminate\Support\Facades\DB;

class MarketAnalyticsService
{
    /**
     * Get total transactions count
     */
    public function getTotalTransactions(?int $leagueId = null): int
    {
        $query = Transfer::query();
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->count();
    }

    /**
     * Get total value moved in market
     */
    public function getTotalValueMoved(?int $leagueId = null): float
    {
        $query = Transfer::query();
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->sum('price') ?? 0;
    }

    /**
     * Get active listings count
     */
    public function getActiveListingsCount(?int $leagueId = null): int
    {
        $query = Listing::where('status', Listing::STATUS_ACTIVE);
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->count();
    }

    /**
     * Get pending offers count
     */
    public function getPendingOffersCount(?int $leagueId = null): int
    {
        $query = Offer::where('status', Offer::STATUS_PENDING);
        
        if ($leagueId) {
            $query->whereHas('listing', fn($q) => $q->where('league_id', $leagueId));
        }
        
        return $query->count();
    }

    /**
     * Get revenue from commissions (5%)
     */
    public function getRevenue(?int $leagueId = null): float
    {
        $totalMoved = $this->getTotalValueMoved($leagueId);
        return $totalMoved * 0.05;
    }

    /**
     * Get top 5 most sold players
     */
    public function getTopPlayers(?int $leagueId = null, int $limit = 5): array
    {
        $query = Transfer::select('player_id', DB::raw('COUNT(*) as sales_count'))
            ->with('player')
            ->groupBy('player_id')
            ->orderBy('sales_count', 'desc')
            ->limit($limit);
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->get()->map(fn($t) => [
            'player' => $t->player->full_name,
            'sales' => $t->sales_count,
        ])->toArray();
    }

    /**
     * Get top 5 most active teams
     */
    public function getTopTeams(?int $leagueId = null, int $limit = 5): array
    {
        $query = Transfer::select('to_fantasy_team_id', DB::raw('COUNT(*) as purchases'))
            ->with('toTeam')
            ->whereNotNull('to_fantasy_team_id')
            ->groupBy('to_fantasy_team_id')
            ->orderBy('purchases', 'desc')
            ->limit($limit);
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->get()->map(fn($t) => [
            'team' => $t->toTeam->name,
            'purchases' => $t->purchases,
        ])->toArray();
    }

    /**
     * Get transfers by gameweek (last 10)
     */
    public function getTransfersByGameweek(?int $leagueId = null): array
    {
        $query = Transfer::select(
                DB::raw('DATE(effective_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(10);
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->get()->map(fn($t) => [
            'date' => $t->date,
            'count' => $t->count,
        ])->toArray();
    }

    /**
     * Get offer acceptance rate
     */
    public function getOfferAcceptanceRate(?int $leagueId = null): float
    {
        $query = Offer::query();
        
        if ($leagueId) {
            $query->whereHas('listing', fn($q) => $q->where('league_id', $leagueId));
        }
        
        $total = $query->whereIn('status', [
            Offer::STATUS_ACCEPTED,
            Offer::STATUS_REJECTED,
        ])->count();
        
        if ($total === 0) {
            return 0;
        }
        
        $accepted = $query->where('status', Offer::STATUS_ACCEPTED)->count();
        
        return round(($accepted / $total) * 100, 2);
    }

    /**
     * Get average transfer price
     */
    public function getAverageTransferPrice(?int $leagueId = null): float
    {
        $query = Transfer::query();
        
        if ($leagueId) {
            $query->where('league_id', $leagueId);
        }
        
        return $query->avg('price') ?? 0;
    }

    /**
     * Get market statistics summary
     */
    public function getMarketStats(?int $leagueId = null): array
    {
        return [
            'total_transactions' => $this->getTotalTransactions($leagueId),
            'total_value_moved' => $this->getTotalValueMoved($leagueId),
            'active_listings' => $this->getActiveListingsCount($leagueId),
            'pending_offers' => $this->getPendingOffersCount($leagueId),
            'revenue' => $this->getRevenue($leagueId),
            'avg_price' => $this->getAverageTransferPrice($leagueId),
            'acceptance_rate' => $this->getOfferAcceptanceRate($leagueId),
        ];
    }
}