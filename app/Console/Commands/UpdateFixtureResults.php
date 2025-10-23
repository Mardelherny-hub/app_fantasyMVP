<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Models\Gameweek;
use App\Services\Admin\Fixtures\FixtureProcessingService;
use Illuminate\Console\Command;

class UpdateFixtureResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:update-results 
                            {gameweek_id? : ID del gameweek a procesar (opcional)}
                            {--all : Procesar todos los gameweeks abiertos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar resultados de fixtures finalizados basándose en puntos fantasy';

    /**
     * Execute the console command.
     */
    public function handle(FixtureProcessingService $fixtureService): int
    {
        $gameweekId = $this->argument('gameweek_id');
        $processAll = $this->option('all');

        if ($processAll) {
            return $this->processAllGameweeks($fixtureService);
        }

        if ($gameweekId) {
            return $this->processGameweek($gameweekId, $fixtureService);
        }

        // Si no se especifica nada, procesar gameweek actual
        return $this->processCurrentGameweek($fixtureService);
    }

    /**
     * Procesar todos los gameweeks abiertos.
     */
    protected function processAllGameweeks(FixtureProcessingService $fixtureService): int
    {
        $this->info('Procesando todos los gameweeks abiertos...');

        $gameweeks = Gameweek::where('is_closed', false)
            ->orderBy('starts_at')
            ->get();

        if ($gameweeks->isEmpty()) {
            $this->warn('No hay gameweeks abiertos para procesar.');
            return 0;
        }

        $totalFixtures = 0;
        $totalErrors = 0;

        foreach ($gameweeks as $gameweek) {
            $this->line("→ Procesando Gameweek {$gameweek->number}...");
            
            $result = $this->processGameweekFixtures($gameweek, $fixtureService);
            $totalFixtures += $result['processed'];
            $totalErrors += $result['errors'];
        }

        $this->newLine();
        $this->info("✓ Proceso completado:");
        $this->line("  • Gameweeks procesados: {$gameweeks->count()}");
        $this->line("  • Fixtures actualizados: {$totalFixtures}");
        
        if ($totalErrors > 0) {
            $this->warn("  • Errores: {$totalErrors}");
            return 1;
        }

        return 0;
    }

    /**
     * Procesar un gameweek específico.
     */
    protected function processGameweek(int $gameweekId, FixtureProcessingService $fixtureService): int
    {
        $gameweek = Gameweek::find($gameweekId);

        if (!$gameweek) {
            $this->error("Gameweek {$gameweekId} no encontrado.");
            return 1;
        }

        $this->info("Procesando Gameweek {$gameweek->number}...");

        $result = $this->processGameweekFixtures($gameweek, $fixtureService);

        $this->newLine();
        $this->info("✓ Fixtures actualizados: {$result['processed']}");
        
        if ($result['errors'] > 0) {
            $this->warn("✗ Errores: {$result['errors']}");
            return 1;
        }

        return 0;
    }

    /**
     * Procesar gameweek actual.
     */
    protected function processCurrentGameweek(FixtureProcessingService $fixtureService): int
    {
        $gameweek = Gameweek::where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if (!$gameweek) {
            $this->warn('No hay gameweek activo en este momento.');
            return 0;
        }

        $this->info("Procesando Gameweek actual: {$gameweek->number}...");

        $result = $this->processGameweekFixtures($gameweek, $fixtureService);

        $this->newLine();
        $this->info("✓ Fixtures actualizados: {$result['processed']}");
        
        if ($result['errors'] > 0) {
            $this->warn("✗ Errores: {$result['errors']}");
            return 1;
        }

        return 0;
    }

    /**
     * Procesar fixtures de un gameweek.
     */
    protected function processGameweekFixtures(Gameweek $gameweek, FixtureProcessingService $fixtureService): array
    {
        $fixtures = Fixture::where('gameweek_id', $gameweek->id)
            ->where('status', Fixture::STATUS_PENDING)
            ->with(['homeTeam', 'awayTeam', 'league'])
            ->get();

        if ($fixtures->isEmpty()) {
            $this->line('  • No hay fixtures pendientes.');
            return ['processed' => 0, 'errors' => 0];
        }

        $processed = 0;
        $errors = 0;

        $this->withProgressBar($fixtures, function ($fixture) use ($fixtureService, &$processed, &$errors) {
            try {
                $fixtureService->updateFixtureResult($fixture);
                $processed++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  Error en fixture {$fixture->id}: {$e->getMessage()}");
                $errors++;
            }
        });

        $this->newLine();

        return ['processed' => $processed, 'errors' => $errors];
    }
}
