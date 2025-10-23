<?php

namespace App\Jobs\Admin\Fixtures;

use App\Models\League;
use App\Services\Admin\Fixtures\FixtureGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateFixturesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    public function __construct(
        public int $leagueId,
        public string $type,
        public int $startGameweek = 1
    ) {}

    public function handle(FixtureGeneratorService $generator): void
    {
        try {
            $league = League::findOrFail($this->leagueId);

            if ($this->type === 'regular') {
                $fixtures = $generator->generateRegularSeasonFixtures($league, $this->startGameweek);
            } else {
                $fixtures = $generator->generatePlayoffFixtures($league);
            }

            Log::info("Job completado: {$fixtures->count()} fixtures generados para liga {$this->leagueId}");

        } catch (\Exception $e) {
            Log::error("Error en GenerateFixturesJob: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateFixturesJob falló para liga {$this->leagueId}: {$exception->getMessage()}");
    }
}