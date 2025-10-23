<?php

namespace App\Console\Commands;

use App\Models\Gameweek;
use App\Jobs\Admin\Scoring\CalculateGameweekScoresJob;
use App\Services\Admin\Fixtures\FixtureProcessingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseGameweek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gameweek:close 
                            {gameweek_id : ID del gameweek a cerrar}
                            {--force : Forzar cierre incluso si no ha terminado}
                            {--no-calculation : No calcular puntos automáticamente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cerrar un gameweek y disparar cálculos de puntos y standings';

    /**
     * Execute the console command.
     */
    public function handle(FixtureProcessingService $fixtureService): int
    {
        $gameweekId = $this->argument('gameweek_id');
        $force = $this->option('force');
        $noCalculation = $this->option('no-calculation');

        $gameweek = Gameweek::find($gameweekId);

        if (!$gameweek) {
            $this->error("Gameweek {$gameweekId} no encontrado.");
            return 1;
        }

        // Verificar si ya está cerrado
        if ($gameweek->is_closed) {
            $this->warn("Gameweek {$gameweek->number} ya está cerrado.");
            
            if (!$this->confirm('¿Desea recalcular puntos de todos modos?', false)) {
                return 0;
            }
        }

        // Verificar si ha terminado (excepto con --force)
        if (!$force && $gameweek->ends_at > now()) {
            $this->error("Gameweek {$gameweek->number} aún no ha terminado.");
            $this->line("Termina: {$gameweek->ends_at->format('Y-m-d H:i:s')}");
            $this->line("Use --force para cerrar de todos modos.");
            return 1;
        }

        $this->info("Cerrando Gameweek {$gameweek->number}...");

        try {
            DB::transaction(function () use ($gameweek, $noCalculation, $fixtureService) {
                // 1. Marcar como cerrado
                $gameweek->update(['is_closed' => true]);
                $this->line("→ Gameweek marcado como cerrado");

                if (!$noCalculation) {
                    // 2. Actualizar fixtures pendientes
                    $this->line("→ Actualizando fixtures pendientes...");
                    $fixtureResult = $fixtureService->processCompletedGameweek($gameweek);
                    $this->info("  ✓ {$fixtureResult['fixtures_processed']} fixtures procesados");
                    $this->info("  ✓ {$fixtureResult['standings_updated']} standings actualizados");

                    // 3. Disparar job de cálculo de puntos
                    $this->line("→ Enviando job de cálculo de puntos a la cola...");
                    CalculateGameweekScoresJob::dispatch($gameweek);
                    $this->info("  ✓ Job enviado a la cola");
                }
            });

            Log::info("Gameweek closed successfully", [
                'gameweek_id' => $gameweek->id,
                'gameweek_number' => $gameweek->number,
                'closed_by' => 'command',
                'force' => $force,
                'no_calculation' => $noCalculation
            ]);

            $this->newLine();
            $this->info("✓ Gameweek {$gameweek->number} cerrado exitosamente!");
            
            if (!$noCalculation) {
                $this->line("Los puntos se calcularán en segundo plano.");
                $this->line("Monitorea el progreso en Horizon o los logs.");
            }

            return 0;

        } catch (\Exception $e) {
            Log::error("Failed to close gameweek", [
                'gameweek_id' => $gameweek->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error("Error al cerrar gameweek: {$e->getMessage()}");
            return 1;
        }
    }
}