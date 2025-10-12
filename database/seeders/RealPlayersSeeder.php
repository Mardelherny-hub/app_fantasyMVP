<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\RealTeam;
use App\Models\PlayerTeamHistory;
use App\Models\PlayerValuation;
use App\Models\Season;

class RealPlayersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚽ Creando jugadores reales...');

        // Obtener equipos argentinos
        $teams = RealTeam::where('country', 'AR')->get();
        
        if ($teams->isEmpty()) {
            $this->command->warn('⚠️  No hay equipos argentinos. Ejecuta RealTeamsSeeder primero.');
            return;
        }

        // Obtener temporada activa para valuaciones
        $season = Season::where('is_active', true)->first() 
            ?? Season::orderByDesc('starts_at')->first();

        if (!$season) {
            $this->command->warn('⚠️  No hay temporadas. Crea una temporada primero.');
            return;
        }

        $created = 0;
        $playersPerTeam = 6; // 6 jugadores por equipo

        foreach ($teams as $team) {
            // Crear jugadores por equipo (1 GK, 2 DF, 2 MF, 1 FW)
            $positions = [1, 2, 2, 3, 3, 4]; // GK, DF, DF, MF, MF, FW

            foreach ($positions as $index => $position) {
                $playerData = $this->generatePlayerData($team, $position, $index + 1);
                
                // Crear jugador
                $player = Player::create($playerData);

                // Asignar a equipo (PlayerTeamHistory)
                PlayerTeamHistory::create([
                    'player_id' => $player->id,
                    'real_team_id' => $team->id,
                    'from_date' => now()->subYears(2),
                    'to_date' => null, // Jugador actual
                    'shirt_number' => $this->getShirtNumber($position, $index),
                ]);

                // Asignar valor de mercado
                PlayerValuation::create([
                    'player_id' => $player->id,
                    'season_id' => $season->id,
                    'market_value' => $this->getMarketValue($position),
                ]);

                $created++;
            }
        }

        $this->command->info("✅ {$created} jugadores creados y asignados a equipos");
    }

    /**
     * Generar datos de jugador
     */
    private function generatePlayerData(RealTeam $team, int $position, int $index): array
    {
        $surnames = [
            'González', 'Rodríguez', 'Fernández', 'López', 'Martínez',
            'Sánchez', 'Pérez', 'Gómez', 'Martín', 'Jiménez',
            'Ruiz', 'Hernández', 'Díaz', 'Moreno', 'Álvarez',
            'Romero', 'Alonso', 'Gutiérrez', 'Navarro', 'Torres',
            'Domínguez', 'Vázquez', 'Ramos', 'Gil', 'Ramírez',
            'Serrano', 'Blanco', 'Molina', 'Morales', 'Ortega',
            'Delgado', 'Castro', 'Ortiz', 'Rubio', 'Marín',
            'Soto', 'Núñez', 'Medina', 'Iglesias', 'Garrido'
        ];

        $names = [
            'Juan', 'Carlos', 'Luis', 'Miguel', 'José',
            'Antonio', 'Francisco', 'Javier', 'Daniel', 'David',
            'Manuel', 'Pedro', 'Alejandro', 'Pablo', 'Diego',
            'Martín', 'Fernando', 'Santiago', 'Matías', 'Nicolás',
            'Sebastián', 'Gonzalo', 'Facundo', 'Luciano', 'Maximiliano',
            'Agustín', 'Rodrigo', 'Ignacio', 'Tomás', 'Emiliano'
        ];

        $name = $names[array_rand($names)];
        $surname = $surnames[array_rand($surnames)];
        $fullName = "{$name} {$surname}";

        // Edad aleatoria 18-35 años
        $age = rand(18, 35);
        $birthdate = now()->subYears($age)->subDays(rand(0, 364));

        return [
            'full_name' => $fullName,
            'known_as' => rand(0, 10) > 7 ? $name : $surname,
            'position' => $position,
            'nationality' => 'AR',
            'birthdate' => $birthdate,
            'height_cm' => $this->getHeight($position),
            'weight_kg' => rand(65, 85),
            'photo_url' => null,
            'is_active' => true,
        ];
    }

    /**
     * Obtener altura según posición
     */
    private function getHeight(int $position): int
    {
        return match($position) {
            1 => rand(180, 195), // GK: más altos
            2 => rand(175, 190), // DF: altos
            3 => rand(170, 185), // MF: medianos
            4 => rand(168, 185), // FW: variados
            default => rand(170, 185),
        };
    }

    /**
     * Obtener número de camiseta según posición
     */
    private function getShirtNumber(int $position, int $index): int
    {
        return match($position) {
            1 => rand(1, 13),     // GK: 1-13
            2 => rand(2, 6),      // DF: 2-6
            3 => rand(5, 20),     // MF: 5-20
            4 => rand(7, 11),     // FW: 7-11
            default => rand(14, 99),
        };
    }

    /**
     * Obtener valor de mercado según posición (en millones)
     */
    private function getMarketValue(int $position): float
    {
        $baseValues = [
            1 => [2.0, 8.0],   // GK: 2M - 8M
            2 => [1.5, 12.0],  // DF: 1.5M - 12M
            3 => [2.0, 15.0],  // MF: 2M - 15M
            4 => [3.0, 20.0],  // FW: 3M - 20M
        ];

        [$min, $max] = $baseValues[$position] ?? [1.0, 10.0];
        
        return round(mt_rand($min * 100, $max * 100) / 100, 2);
    }
}