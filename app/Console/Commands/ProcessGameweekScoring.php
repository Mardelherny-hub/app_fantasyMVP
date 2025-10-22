<?php

namespace App\Console\Commands;

use App\Models\Gameweek;
use App\Services\Admin\Scoring\ScoringCalculationService;
use App\Services\Admin\Fixtures\FixtureProcessingService;
use Illuminate\Console\Command;

class ProcessGameweekScoring extends Command
{
    protected $signature = 'scoring:process-gameweek {gameweek_id}';
    protected $description = 'Procesar puntuación completa de un gameweek';

    public function handle(ScoringCalculationService $scoringService, FixtureProcessingService $fixtureService)
    {
        $gameweekId = $this->argument('gameweek_id');
        $gameweek = Gameweek::find($gameweekId);
        
        if (!$gameweek) {
            $this->error("Gameweek {$gameweekId} no encontrado.");
            return 1;
        }
        
        $this->info("Procesando Gameweek {$gameweek->number}...");
        
        // Paso 1: Calcular puntos de jugadores
        $this->line('→ Calculando puntos...');
        $scoringResults = $scoringService->processGameweekScoring($gameweek);
        $this->info("✓ {$scoringResults['teams_processed']} equipos procesados");
        $this->info("✓ {$scoringResults['total_points_calculated']} puntos totales");
        
        // Paso 2: Actualizar fixtures y standings
        $this->line('→ Actualizando fixtures y standings...');
        $fixtureResults = $fixtureService->processCompletedGameweek($gameweek);
        $this->info("✓ {$fixtureResults['fixtures_processed']} fixtures procesados");
        $this->info("✓ {$fixtureResults['standings_updated']} standings actualizados");
        
        if (!empty($scoringResults['errors']) || !empty($fixtureResults['errors'])) {
            $this->warn("Errores detectados:");
            foreach (array_merge($scoringResults['errors'], $fixtureResults['errors']) as $error) {
                $this->error(json_encode($error));
            }
        }
        
        $this->info('✓ Gameweek procesado exitosamente!');
        return 0;
    }
}
