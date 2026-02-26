<?php

namespace App\Services\Admin\Market;

use App\Models\League;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Transfer;
use App\Models\Player;
use App\Models\FantasyTeam;
use App\Models\Season;
use App\Models\PlayerValuation;
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

    /**
     * Obtener jugadores más vendidos
     *
     * @param League|null $league
     * @param int $limit
     * @return array
     */
    public function getTopSoldPlayers(?League $league = null, int $limit = 10): array
    {
        $query = Transfer::with('player')
            ->selectRaw('player_id, COUNT(*) as transfers_count, AVG(price) as avg_price, MAX(price) as max_price')
            ->groupBy('player_id')
            ->orderByDesc('transfers_count')
            ->limit($limit);

        if ($league) {
            $query->where('league_id', $league->id);
        }

        return $query->get()->map(function ($item) {
            return [
                'player' => $item->player,
                'transfers_count' => $item->transfers_count,
                'avg_price' => round($item->avg_price, 2),
                'max_price' => round($item->max_price, 2),
            ];
        })->toArray();
    }

    /**
     * Obtener equipos más activos
     *
     * @param League|null $league
     * @param int $limit
     * @return array
     */
    public function getMostActiveTeams(?League $league = null, int $limit = 10): array
    {
        $query = Transfer::with('toTeam')
            ->selectRaw('to_fantasy_team_id, COUNT(*) as purchases, SUM(price) as total_spent')
            ->whereNotNull('to_fantasy_team_id')
            ->groupBy('to_fantasy_team_id')
            ->orderByDesc('purchases')
            ->limit($limit);

        if ($league) {
            $query->where('league_id', $league->id);
        }

        return $query->get()->map(function ($item) {
            return [
                'team' => $item->toTeam,
                'purchases' => $item->purchases,
                'total_spent' => round($item->total_spent, 2),
            ];
        })->toArray();
    }

    /**
     * Obtener precio promedio por posición
     *
     * @param Season $season
     * @return array
     */
    public function getAveragePriceByPosition(Season $season): array
    {
        $positions = [
            Player::POSITION_GK => 'GK',
            Player::POSITION_DF => 'DF',
            Player::POSITION_MF => 'MF',
            Player::POSITION_FW => 'FW',
        ];

        $result = [];

        foreach ($positions as $posId => $posName) {
            $avg = PlayerValuation::where('season_id', $season->id)
                ->whereHas('player', fn($q) => $q->where('position', $posId))
                ->avg('market_value');

            $result[$posName] = round($avg ?? 0, 2);
        }

        return $result;
    }

    /**
     * Obtener evolución de transfers por gameweek
     *
     * @param League $league
     * @param int $limit
     * @return array
     */
    public function getTransferTrends(League $league, int $limit = 10): array
    {
        $gameweeks = Gameweek::where('season_id', $league->season_id)
            ->orderByDesc('number')
            ->limit($limit)
            ->get();

        return $gameweeks->map(function ($gw) use ($league) {
            $count = Transfer::where('league_id', $league->id)
                ->whereBetween('effective_at', [$gw->starts_at, $gw->ends_at])
                ->count();

            return [
                'gameweek' => $gw->number,
                'transfers' => $count,
            ];
        })->reverse()->values()->toArray();
    }

    /**
     * Tasa de éxito de ofertas
     *
     * @param League|null $league
     * @return array
     */
    public function getOfferSuccessRate(?League $league = null): array
    {
        $query = Offer::query();

        if ($league) {
            $query->whereHas('listing', fn($q) => $q->where('league_id', $league->id));
        }

        $total = $query->count();
        $accepted = $query->where('status', Offer::STATUS_ACCEPTED)->count();
        $rejected = $query->where('status', Offer::STATUS_REJECTED)->count();
        $pending = $query->where('status', Offer::STATUS_PENDING)->count();

        return [
            'total' => $total,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'pending' => $pending,
            'success_rate' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
        ];
    }
}