<?php

namespace App\Jobs\Manager\Market;

use App\Models\Season;
use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\FantasyPoint;
use App\Models\Offer;
use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdateMarketPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The season to update prices for.
     *
     * @var Season
     */
    public Season $season;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 900; // 15 minutos

    /**
     * Create a new job instance.
     */
    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('UpdateMarketPricesJob: Starting price update', [
            'season_id' => $this->season->id,
            'season_name' => $this->season->name,
        ]);

        $totalProcessed = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        try {
            // Obtener todos los jugadores activos con sus valuaciones para la temporada
            Player::where('is_active', true)
                ->whereHas('valuations', function ($query) {
                    $query->where('season_id', $this->season->id);
                })
                ->with(['valuations' => function ($query) {
                    $query->where('season_id', $this->season->id);
                }])
                ->chunk(50, function ($players) use (&$totalProcessed, &$totalUpdated, &$totalSkipped) {
                    foreach ($players as $player) {
                        try {
                            $updated = $this->updatePlayerPrice($player);
                            
                            if ($updated) {
                                $totalUpdated++;
                            } else {
                                $totalSkipped++;
                            }

                        } catch (\Exception $e) {
                            Log::error('Failed to update price for player', [
                                'player_id' => $player->id,
                                'player_name' => $player->full_name,
                                'error' => $e->getMessage(),
                            ]);
                            $totalSkipped++;
                        }

                        $totalProcessed++;
                    }
                });

            Log::info('UpdateMarketPricesJob: Completed successfully', [
                'season_id' => $this->season->id,
                'total_processed' => $totalProcessed,
                'total_updated' => $totalUpdated,
                'total_skipped' => $totalSkipped,
            ]);

        } catch (\Exception $e) {
            Log::error('UpdateMarketPricesJob: Failed to complete', [
                'season_id' => $this->season->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lanzar la excepción para que el job se reintente
            throw $e;
        }
    }

    /**
     * Update price for a single player.
     *
     * @param Player $player
     * @return bool True if price was updated, false if skipped
     */
    private function updatePlayerPrice(Player $player): bool
    {
        // Obtener valuación actual
        $valuation = $player->valuations()
            ->where('season_id', $this->season->id)
            ->first();

        if (!$valuation) {
            Log::debug('No valuation found for player', [
                'player_id' => $player->id,
                'season_id' => $this->season->id,
            ]);
            return false;
        }

        $currentValue = (float) $valuation->market_value;

        // Calcular factores
        $performanceFactor = $this->calculatePerformanceFactor($player);
        $demandFactor = $this->calculateDemandFactor($player);

        // Calcular cambio porcentual
        // 70% peso en performance, 30% peso en demanda
        $changePercentage = ($performanceFactor * 0.7) + ($demandFactor * 0.3);

        // Calcular nuevo valor
        $newValue = $currentValue * (1 + $changePercentage);

        // Aplicar límites
        $newValue = max($newValue, 0.50); // Mínimo absoluto $0.50
        $newValue = min($newValue, $currentValue * 2); // Máximo 2x en una actualización
        $newValue = round($newValue, 2);

        // Si no hay cambio significativo, skip
        if (abs($newValue - $currentValue) < 0.01) {
            return false;
        }

        // Actualizar valuación
        $valuation->update(['market_value' => $newValue]);

        Log::debug('Price updated for player', [
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
     * @return float
     */
    private function calculatePerformanceFactor(Player $player): float
    {
        // Obtener puntos de los últimos 5 gameweeks
        $recentPoints = FantasyPoint::where('player_id', $player->id)
            ->whereHas('gameweek', function ($query) {
                $query->where('season_id', $this->season->id)
                    ->where('is_closed', true);
            })
            ->orderBy('gameweek_id', 'desc')
            ->limit(5)
            ->pluck('total_points');

        // Si no hay datos, factor neutro
        if ($recentPoints->isEmpty()) {
            return 0.0;
        }

        // Calcular promedio
        $avgPoints = $recentPoints->avg();

        // Fórmula: (avgPoints - 5) / 50
        // Escala: 0 puntos = -10%, 5 puntos = 0%, 10 puntos = +10%
        $factor = ($avgPoints - 5) / 50;

        // Limitar entre -10% y +10%
        return max(-0.10, min(0.10, $factor));
    }

    /**
     * Calculate demand factor based on recent market activity.
     * Returns value between 0 and +0.05.
     *
     * @param Player $player
     * @return float
     */
    private function calculateDemandFactor(Player $player): float
    {
        $cutoffDate = now()->subDays(7);

        // Contar ofertas recientes
        $offersCount = Offer::where('created_at', '>=', $cutoffDate)
            ->whereHas('listing', function ($query) use ($player) {
                $query->where('player_id', $player->id);
            })
            ->count();

        // Contar transferencias recientes (pesan el doble)
        $transfersCount = Transfer::where('player_id', $player->id)
            ->where('effective_at', '>=', $cutoffDate)
            ->count();

        // Actividad total (transferencias pesan más)
        $totalActivity = $offersCount + ($transfersCount * 2);

        // Fórmula: min(activity / 200, 0.05)
        // Escala: 0 actividad = 0%, 10+ actividad = +5%
        $factor = min($totalActivity / 200, 0.05);

        return $factor;
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('UpdateMarketPricesJob: Job failed permanently', [
            'season_id' => $this->season->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}