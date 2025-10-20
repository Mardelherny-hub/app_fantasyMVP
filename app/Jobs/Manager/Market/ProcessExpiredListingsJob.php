<?php

namespace App\Jobs\Manager\Market;

use App\Models\Listing;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessExpiredListingsJob implements ShouldQueue
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
        Log::info('ProcessExpiredListingsJob: Starting to process expired listings');

        $totalProcessed = 0;
        $totalExpired = 0;
        $totalOffersRejected = 0;

        try {
            // Procesar listings activos con expires_at vencido
            Listing::where('status', Listing::STATUS_ACTIVE)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->chunk(100, function ($listings) use (&$totalProcessed, &$totalExpired, &$totalOffersRejected) {
                    foreach ($listings as $listing) {
                        try {
                            // Contar ofertas pendientes antes de rechazar
                            $pendingOffersCount = $listing->offers()
                                ->where('status', Offer::STATUS_PENDING)
                                ->count();

                            // Marcar listing como expirado
                            $listing->markAsExpired();
                            
                            // Rechazar todas las ofertas pendientes asociadas
                            $rejectedCount = $listing->offers()
                                ->where('status', Offer::STATUS_PENDING)
                                ->update(['status' => Offer::STATUS_EXPIRED]);

                            $totalExpired++;
                            $totalOffersRejected += $rejectedCount;
                            
                            Log::debug('Listing expired', [
                                'listing_id' => $listing->id,
                                'player_id' => $listing->player_id,
                                'fantasy_team_id' => $listing->fantasy_team_id,
                                'expires_at' => $listing->expires_at->toDateTimeString(),
                                'pending_offers_count' => $pendingOffersCount,
                                'offers_rejected' => $rejectedCount,
                            ]);

                        } catch (\Exception $e) {
                            Log::error('Failed to expire listing', [
                                'listing_id' => $listing->id,
                                'error' => $e->getMessage(),
                            ]);
                        }

                        $totalProcessed++;
                    }
                });

            Log::info('ProcessExpiredListingsJob: Completed successfully', [
                'total_processed' => $totalProcessed,
                'total_expired' => $totalExpired,
                'total_offers_rejected' => $totalOffersRejected,
                'cutoff_time' => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessExpiredListingsJob: Failed to complete', [
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
        Log::error('ProcessExpiredListingsJob: Job failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}