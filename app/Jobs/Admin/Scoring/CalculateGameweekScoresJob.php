<?php

namespace App\Jobs\Admin\Scoring;

use App\Models\Gameweek;
use App\Services\Admin\Scoring\ScoringCalculationService;
use App\Services\Admin\Fixtures\FixtureProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateGameweekScoresJob implements ShouldQueue
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
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Gameweek $gameweek
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        ScoringCalculationService $scoringService,
        FixtureProcessingService $fixtureService
    ): void {
        Log::info("Starting gameweek scoring calculation", [
            'gameweek_id' => $this->gameweek->id,
            'gameweek_number' => $this->gameweek->number,
            'job_id' => $this->job->getJobId()
        ]);

        try {
            // Paso 1: Calcular puntos de jugadores
            Log::info("Step 1: Calculating player scores");
            $scoringResults = $scoringService->processGameweekScoring($this->gameweek);
            
            Log::info("Player scores calculated", [
                'teams_processed' => $scoringResults['teams_processed'],
                'total_points' => $scoringResults['total_points_calculated']
            ]);

            // Paso 2: Actualizar fixtures y standings
            Log::info("Step 2: Processing fixtures and standings");
            $fixtureResults = $fixtureService->processCompletedGameweek($this->gameweek);
            
            Log::info("Fixtures and standings updated", [
                'fixtures_processed' => $fixtureResults['fixtures_processed'],
                'standings_updated' => $fixtureResults['standings_updated']
            ]);

            // Log de errores si existen
            if (!empty($scoringResults['errors']) || !empty($fixtureResults['errors'])) {
                $allErrors = array_merge(
                    $scoringResults['errors'] ?? [],
                    $fixtureResults['errors'] ?? []
                );
                
                Log::warning("Gameweek processed with errors", [
                    'gameweek_id' => $this->gameweek->id,
                    'errors' => $allErrors
                ]);
            }

            Log::info("Gameweek scoring calculation completed successfully", [
                'gameweek_id' => $this->gameweek->id,
                'gameweek_number' => $this->gameweek->number
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to calculate gameweek scores", [
                'gameweek_id' => $this->gameweek->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("CalculateGameweekScoresJob failed permanently", [
            'gameweek_id' => $this->gameweek->id,
            'gameweek_number' => $this->gameweek->number,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Aquí se podría enviar notificación a admin o registrar en tabla de errores
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'scoring',
            'gameweek:' . $this->gameweek->id,
            'season:' . $this->gameweek->season_id
        ];
    }
}
