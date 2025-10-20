<?php

namespace App\Jobs\Manager\Market;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessExpiredOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('ProcessExpiredOffersJob: Starting to process expired offers');

        // Calcular tiempo de expiración (48 horas atrás)
        $expirationTime = now()->subHours(48);

        $totalProcessed = 0;
        $totalExpired = 0;

        try {
            // Procesar ofertas pendientes creadas hace más de 48 horas
            Offer::where('status', Offer::STATUS_PENDING)
                ->where('created_at', '<', $expirationTime)
                ->chunk(100, function ($offers) use (&$totalProcessed, &$totalExpired) {
                    foreach ($offers as $offer) {
                        try {
                            // Marcar como expirada usando método del modelo
                            $offer->markAsExpired();
                            
                            $totalExpired++;
                            
                            Log::debug('Offer expired', [
                                'offer_id' => $offer->id,
                                'listing_id' => $offer->listing_id,
                                'buyer_team_id' => $offer->buyer_fantasy_team_id,
                                'created_at' => $offer->created_at->toDateTimeString(),
                            ]);

                        } catch (\Exception $e) {
                            Log::error('Failed to expire offer', [
                                'offer_id' => $offer->id,
                                'error' => $e->getMessage(),
                            ]);
                        }

                        $totalProcessed++;
                    }
                });

            Log::info('ProcessExpiredOffersJob: Completed successfully', [
                'total_processed' => $totalProcessed,
                'total_expired' => $totalExpired,
                'expiration_cutoff' => $expirationTime->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessExpiredOffersJob: Failed to complete', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lanzar la excepción para que el job se reintente
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
        Log::error('ProcessExpiredOffersJob: Job failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}