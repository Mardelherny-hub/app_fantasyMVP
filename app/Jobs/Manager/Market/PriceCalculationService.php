<?php

namespace App\Services\Manager\Market;

use App\Models\Season;
use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\FantasyPoint;
use App\Models\Offer;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;

class PriceCalculationService
{
    /**
     * Update prices for all players in a season.
     *
     * @param Season $season
     * @return array Summary of updates
     */
    public function updateAllPrices(Season $season): array
    {
        $totalProcessed = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        Player::where('is_active', true)
            ->whereHas('valuations', function ($query) use ($season) {
                $query->where('season_id', $season->id);
            })
            ->with(['valuations' => function ($query) use ($season) {
                $query->where('season_id', $season->id);
            }])
            ->chunk(50, function ($players) use ($season, &$totalProcessed, &$totalUpdated, &$totalSkipped) {
                foreach ($players as $player) {
                    try {
                        $updated = $this->updatePlayerPrice($player, $season);
                        
                        if ($updated) {
                            $totalUpdated++;
                        } else {
                            $totalSkipped++;
                        }

                    } catch (\Exception $e) {
                        Log::error('PriceCalculationService: Failed to update price for player', [
                            'player_id' => $player->id,
                            'player_name' => $player->full_name,
                            'error' => $e->getMessage(),
                        ]);
                        $totalSkipped++;
                    }

                    $totalProcessed++;
                }
            });

        return [
            'total_processed' => $totalProcessed,
            'total_updated' => $totalUpdated,
            'total_skipped' => $totalSkipped,
        ];
    }

    /**
     * Update price for a single player.
     *
     * @param Player $player
     * @param Season $season
     * @return bool True if price was updated, false if skipped
     */
    public function updatePlayerPrice(Player $player, Season $season): bool
    {
        $valuation = $player->valuations()
            ->where('season_id', $season->id)
            ->first();

        if (!$valuation) {
            return false;
        }

        $currentValue = (float) $valuation->market_value;

        // Calculate factors
        $performanceFactor = $this->calculatePerformanceFactor($player, $season);
        $demandFactor = $this->calculateDemandFactor($player);

        // Calculate change percentage (70% performance, 30% demand)
        $changePercentage = ($performanceFactor * 0.7) + ($demandFactor * 0.3);

        // Calculate new value
        $newValue = $currentValue * (1 + $changePercentage);

        // Apply limits
        $newValue = $this->applyPriceLimits($newValue, $currentValue);

        // Skip if no significant change
        if (abs($newValue - $currentValue) < 0.01) {
            return false;
        }

        // Update valuation
        $valuation->update(['market_value' => $newValue]);

        Log::debug('PriceCalculationService: Price updated', [
            'player_id' => $player->id,
            'player_name' => $player->full_name,
            'old_price' => $currentValue,
            'new_price' => $newValue,
            'change_percentage' => round($changePercentage * 100, 2) . '%',
            'performance_factor' => round($performanceFactor * 100, 2) . '%',
            'demand_factor' => round($demandFactor * 100, 2) . '%',
        ]);

        return true;
    }

    /**
     * Calculate performance factor based on last 5 gameweeks.
     * Returns value between -0.10 and +0.10.
     *
     * @param Player $player
     * @param Season $season
     * @return float
     */
    public function calculatePerformanceFactor(Player $player, Season $season): float
    {
        $recentPoints = FantasyPoint::where('player_id', $player->id)
            ->whereHas('gameweek', function ($query) use ($season) {
                $query->where('season_id', $season->id)
                    ->where('is_closed', true);
            })
            ->orderBy('gameweek_id', 'desc')
            ->limit(5)
            ->pluck('total_points');

        if ($recentPoints->isEmpty()) {
            return 0.0;
        }

        $avgPoints = $recentPoints->avg();

        // Formula: (avgPoints - 5) / 50
        // Scale: 0 points = -10%, 5 points = 0%, 10 points = +10%
        $factor = ($avgPoints - 5) / 50;

        return max(-0.10, min(0.10, $factor));
    }

    /**
     * Calculate demand factor based on recent market activity.
     * Returns value between 0 and +0.05.
     *
     * @param Player $player
     * @return float
     */
    public function calculateDemandFactor(Player $player): float
    {
        $cutoffDate = now()->subDays(7);

        // Count recent offers
        $offersCount = Offer::where('created_at', '>=', $cutoffDate)
            ->whereHas('listing', function ($query) use ($player) {
                $query->where('player_id', $player->id);
            })
            ->count();

        // Count recent transfers (weighted double)
        $transfersCount = Transfer::where('player_id', $player->id)
            ->where('effective_at', '>=', $cutoffDate)
            ->count();

        // Total activity (transfers weighted more)
        $totalActivity = $offersCount + ($transfersCount * 2);

        // Formula: min(activity / 200, 0.05)
        // Scale: 0 activity = 0%, 10+ activity = +5%
        return min($totalActivity / 200, 0.05);
    }

    /**
     * Apply price limits to calculated value.
     *
     * @param float $newValue
     * @param float $currentValue
     * @return float
     */
    protected function applyPriceLimits(float $newValue, float $currentValue): float
    {
        // Minimum absolute value: $0.50
        $newValue = max($newValue, 0.50);

        // Maximum change: 2x current price in one update
        $newValue = min($newValue, $currentValue * 2);

        return round($newValue, 2);
    }

    /**
     * Get suggested listing price for a player (market value + 5%).
     *
     * @param Player $player
     * @param Season $season
     * @return float
     */
    public function getSuggestedListingPrice(Player $player, Season $season): float
    {
        $valuation = $player->valuations()
            ->where('season_id', $season->id)
            ->first();

        if (!$valuation) {
            return 0.0;
        }

        return round($valuation->market_value * 1.05, 2);
    }

    /**
     * Calculate expected price change for a player (without saving).
     *
     * @param Player $player
     * @param Season $season
     * @return array
     */
    public function calculateExpectedChange(Player $player, Season $season): array
    {
        $valuation = $player->valuations()
            ->where('season_id', $season->id)
            ->first();

        if (!$valuation) {
            return [
                'current_price' => 0.0,
                'expected_price' => 0.0,
                'change_percentage' => 0.0,
                'performance_factor' => 0.0,
                'demand_factor' => 0.0,
            ];
        }

        $currentValue = (float) $valuation->market_value;
        $performanceFactor = $this->calculatePerformanceFactor($player, $season);
        $demandFactor = $this->calculateDemandFactor($player);
        $changePercentage = ($performanceFactor * 0.7) + ($demandFactor * 0.3);
        $expectedValue = $currentValue * (1 + $changePercentage);
        $expectedValue = $this->applyPriceLimits($expectedValue, $currentValue);

        return [
            'current_price' => $currentValue,
            'expected_price' => $expectedValue,
            'change_percentage' => $changePercentage,
            'performance_factor' => $performanceFactor,
            'demand_factor' => $demandFactor,
        ];
    }
}