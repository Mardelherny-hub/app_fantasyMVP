<?php

namespace App\Console\Commands;

use App\Jobs\Manager\Market\UpdateMarketPricesJob;
use App\Models\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMarketPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:update-prices 
                            {--season= : Specific season ID to update (optional)}
                            {--force : Force update even if no active seasons}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update player market prices based on performance and demand';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting market price update...');

        try {
            // Si se especifica una temporada
            if ($seasonId = $this->option('season')) {
                $season = Season::find($seasonId);
                
                if (!$season) {
                    $this->error("Season with ID {$seasonId} not found.");
                    return Command::FAILURE;
                }
                
                $this->updateSeason($season);
                return Command::SUCCESS;
            }

            // Buscar temporadas activas
            $activeSeasons = Season::where('is_active', true)->get();

            if ($activeSeasons->isEmpty()) {
                if ($this->option('force')) {
                    $this->warn('No active seasons found, but --force flag is set.');
                    $this->info('Skipping price update.');
                    return Command::SUCCESS;
                }
                
                $this->warn('No active seasons found.');
                $this->line('Use --force flag to suppress this warning or specify --season=ID');
                return Command::SUCCESS;
            }

            $this->info("Found {$activeSeasons->count()} active season(s)");
            $this->newLine();

            // Procesar cada temporada
            foreach ($activeSeasons as $season) {
                $this->updateSeason($season);
            }

            $this->newLine();
            $this->info('✅ Market price update completed successfully!');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to update market prices: ' . $e->getMessage());
            
            Log::error('Market price update command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Update prices for a specific season.
     *
     * @param Season $season
     * @return void
     */
    private function updateSeason(Season $season): void
    {
        $this->line("Processing season: {$season->name} (ID: {$season->id})");
        
        Log::info('Market: Dispatching price update job', [
            'season_id' => $season->id,
            'season_name' => $season->name,
        ]);

        // Dispatch job
        dispatch(new UpdateMarketPricesJob($season));
        
        $this->info("  ✓ Price update job dispatched for season {$season->id}");
    }
}