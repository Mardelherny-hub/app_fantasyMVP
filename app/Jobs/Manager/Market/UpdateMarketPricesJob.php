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
    public function handle(PriceCalculationService $priceService): void
    {
        Log::info('UpdateMarketPricesJob: Starting price update', [
            'season_id' => $this->season->id,
            'season_name' => $this->season->name,
        ]);

        try {
            // Usar el servicio para actualizar precios
            $summary = $priceService->updateAllPrices($this->season);

            Log::info('UpdateMarketPricesJob: Completed successfully', array_merge(
                ['season_id' => $this->season->id],
                $summary
            ));

        } catch (\Exception $e) {
            Log::error('UpdateMarketPricesJob: Failed to complete', [
                'season_id' => $this->season->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
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