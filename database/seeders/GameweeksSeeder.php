<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\Gameweek;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GameweeksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la temporada activa
        $season = Season::where('is_active', true)->first();

        if (!$season) {
            $this->command->warn('⚠️  No hay temporada activa. Ejecuta SeasonsSeeder primero.');
            return;
        }

        $this->command->info("📅 Generando gameweeks para temporada {$season->name}...");

        $startDate = Carbon::parse($season->starts_at);
        
        // ========================================
        // FASE REGULAR: 27 GAMEWEEKS
        // ========================================
        for ($i = 1; $i <= 27; $i++) {
            $gwStartsAt = $startDate->copy()->addWeeks($i - 1);
            $gwEndsAt = $gwStartsAt->copy()->addDays(6)->endOfDay();

            Gameweek::firstOrCreate(
                [
                    'season_id' => $season->id,
                    'number' => $i,
                ],
                [
                    'starts_at' => $gwStartsAt,
                    'ends_at' => $gwEndsAt,
                    'is_closed' => false,
                    'is_playoff' => false,
                    'playoff_round' => null,
                ]
            );
        }

        $this->command->info('✅ Fase regular: 27 gameweeks creadas');

        // ========================================
        // PLAYOFFS: 3 GAMEWEEKS
        // ========================================
        
        // GW28: CUARTOS DE FINAL (4° vs 5°)
        $gw28StartsAt = $startDate->copy()->addWeeks(27);
        $gw28EndsAt = $gw28StartsAt->copy()->addDays(6)->endOfDay();
        
        Gameweek::firstOrCreate(
            [
                'season_id' => $season->id,
                'number' => 28,
            ],
            [
                'starts_at' => $gw28StartsAt,
                'ends_at' => $gw28EndsAt,
                'is_closed' => false,
                'is_playoff' => true,
                'playoff_round' => Gameweek::PLAYOFF_QUARTERS,
            ]
        );

        // GW29: SEMIFINALES (2° vs 3° y Winner(Q) vs 1°)
        $gw29StartsAt = $startDate->copy()->addWeeks(28);
        $gw29EndsAt = $gw29StartsAt->copy()->addDays(6)->endOfDay();
        
        Gameweek::firstOrCreate(
            [
                'season_id' => $season->id,
                'number' => 29,
            ],
            [
                'starts_at' => $gw29StartsAt,
                'ends_at' => $gw29EndsAt,
                'is_closed' => false,
                'is_playoff' => true,
                'playoff_round' => Gameweek::PLAYOFF_SEMIS,
            ]
        );

        // GW30: FINAL
        $gw30StartsAt = $startDate->copy()->addWeeks(29);
        $gw30EndsAt = $gw30StartsAt->copy()->addDays(6)->endOfDay();
        
        Gameweek::firstOrCreate(
            [
                'season_id' => $season->id,
                'number' => 30,
            ],
            [
                'starts_at' => $gw30StartsAt,
                'ends_at' => $gw30EndsAt,
                'is_closed' => false,
                'is_playoff' => true,
                'playoff_round' => Gameweek::PLAYOFF_FINAL,
            ]
        );

        $this->command->info('✅ Playoffs: 3 gameweeks creadas (GW28: Cuartos, GW29: Semis, GW30: Final)');
        $this->command->info("🎯 Total: 30 gameweeks para la temporada {$season->name}");
    }
}