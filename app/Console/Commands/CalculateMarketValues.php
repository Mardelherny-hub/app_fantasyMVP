<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\PlayerValuationHistory;
use App\Models\Season;
use App\Models\Gameweek;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateMarketValues extends Command
{
    protected $signature = 'calculate:market-values
                            {--gameweek= : ID del gameweek recién procesado}
                            {--test : Modo prueba sin guardar}';

    protected $description = 'Calcula valores de mercado (mixto: 70% anterior + 30% rendimiento GW)';

    private array $baseValues = [
        'Goalkeeper' => 3.0,
        'Defender' => 4.0,
        'Midfielder' => 5.0,
        'Attacker' => 6.0,
    ];

    public function handle()
    {
        $this->info('🔁 Calculando valores de mercado...');

        $testMode = $this->option('test');
        $gameweekId = $this->option('gameweek');

        // Obtener season activa
        $season = Season::where('is_active', true)->first();
        if (!$season) {
            $this->error('No hay temporada activa.');
            return 1;
        }

        // Obtener gameweek
        $gameweek = null;
        if ($gameweekId) {
            $gameweek = Gameweek::find($gameweekId);
        } else {
            // Buscar último gameweek cerrado de la temporada
            $gameweek = Gameweek::where('season_id', $season->id)
                ->where('is_closed', true)
                ->orderBy('number', 'desc')
                ->first();
        }

        if (!$gameweek) {
            $this->error('No se encontró gameweek para procesar.');
            return 1;
        }

        $this->info("Season: {$season->name} | GW{$gameweek->number} (id={$gameweek->id})");

        // Puntos del último GW por jugador
        $gwPoints = DB::table('fantasy_roster_scores')
            ->select('player_id', DB::raw('SUM(base_points) as gw_points'))
            ->where('gameweek_id', $gameweek->id)
            ->groupBy('player_id')
            ->pluck('gw_points', 'player_id');

        if ($gwPoints->isEmpty()) {
            $this->warn('No hay puntos en este gameweek.');
            return 0;
        }

        $maxGwPoints = max($gwPoints->max(), 1);
        $this->info("Jugadores con puntos en GW: {$gwPoints->count()} | Max: {$maxGwPoints}");

        // Procesar jugadores
        $playerIds = $gwPoints->keys()->toArray();
        $players = Player::whereIn('id', $playerIds)->get();

        $updated = 0;
        $errors = 0;

        foreach ($players as $player) {
            try {
                $points = $gwPoints->get($player->id, 0);

                // Valor anterior (de player_valuations o base por posición)
                $currentValuation = PlayerValuation::where('player_id', $player->id)
                    ->where('season_id', $season->id)
                    ->first();

                $previousValue = $currentValuation
                    ? (float) $currentValuation->market_value
                    : $this->getBaseValue($player->position);

                // Rendimiento GW normalizado (0 a 1)
                $gwPerformance = $points / $maxGwPoints;

                // Valor por rendimiento: mapear a rango 1-15M
                $performanceValue = 1.0 + ($gwPerformance * 14.0);

                // Mixto: 70% anterior + 30% rendimiento
                $newValue = round(($previousValue * 0.7) + ($performanceValue * 0.3), 2);

                // Limitar entre 1.0 y 15.0
                $newValue = max(1.0, min(15.0, $newValue));

                if ($testMode) {
                    $this->line("{$player->known_as}: {$points}pts | anterior:{$previousValue}M → nuevo:{$newValue}M");
                } else {
                    DB::beginTransaction();

                    // Actualizar valor actual
                    PlayerValuation::updateOrCreate(
                        ['player_id' => $player->id, 'season_id' => $season->id],
                        ['market_value' => $newValue, 'updated_at' => now()]
                    );

                    // Guardar historial
                    PlayerValuationHistory::updateOrCreate(
                        ['player_id' => $player->id, 'season_id' => $season->id, 'gameweek_id' => $gameweek->id],
                        ['market_value' => $newValue, 'previous_value' => $previousValue, 'source' => 'auto']
                    );

                    DB::commit();
                }

                $updated++;
            } catch (\Exception $e) {
                if (!$testMode) {
                    DB::rollBack();
                }
                $errors++;
                Log::error("Error valor mercado player {$player->id}: " . $e->getMessage());
                if ($errors <= 3) {
                    $this->error("Error {$player->known_as}: " . $e->getMessage());
                }
            }
        }

        // Asignar valor base a jugadores activos sin valuación
        $playersWithoutValuation = Player::where('is_active', true)
            ->whereDoesntHave('valuations', fn($q) => $q->where('season_id', $season->id))
            ->get();

        $baseAssigned = 0;
        foreach ($playersWithoutValuation as $player) {
            try {
                $baseValue = $this->getBaseValue($player->position);
                if (!$testMode) {
                    PlayerValuation::updateOrCreate(
                        ['player_id' => $player->id, 'season_id' => $season->id],
                        ['market_value' => $baseValue, 'updated_at' => now()]
                    );
                }
                $baseAssigned++;
            } catch (\Exception $e) {
                Log::error("Error valor base player {$player->id}: " . $e->getMessage());
            }
        }

        $this->info("Valor base asignado a: {$baseAssigned} jugadores sin puntos");

        $this->info("✅ Procesados: {$updated}" . ($errors > 0 ? " | Errores: {$errors}" : ""));
        return 0;
    }

    private function getBaseValue($position): float
    {
        return match((int) $position) {
            1 => $this->baseValues['Goalkeeper'],
            2 => $this->baseValues['Defender'],
            3 => $this->baseValues['Midfielder'],
            4 => $this->baseValues['Attacker'],
            default => 4.0,
        };
    }
}