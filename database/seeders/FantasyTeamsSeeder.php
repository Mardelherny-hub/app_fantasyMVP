<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\FantasyTeam;
use Illuminate\Support\Str;

class FantasyTeamsSeeder extends Seeder
{
    public function run(): void
    {
        $teamNames = [
            'Los Invencibles', 'Dragones FC', 'Titanes United', 'Halcones Dorados',
            'Leones del Sur', 'Águilas Reales', 'Lobos Salvajes', 'Tigres FC',
            'Pumas Negros', 'Cóndores Elite', 'Búfalos FC', 'Panteras Azules',
            'Vikingos FC', 'Gladiadores', 'Espartanos', 'Centuriones',
            'Samurais FC', 'Ninjas United', 'Ronin FC', 'Shogun Team',
        ];

        $colors = [
            ['primary' => '#FF0000', 'secondary' => '#FFFFFF'], // Rojo/Blanco
            ['primary' => '#0000FF', 'secondary' => '#FFD700'], // Azul/Dorado
            ['primary' => '#000000', 'secondary' => '#00FF00'], // Negro/Verde
            ['primary' => '#800080', 'secondary' => '#FFFFFF'], // Púrpura/Blanco
            ['primary' => '#FFA500', 'secondary' => '#000000'], // Naranja/Negro
            ['primary' => '#008000', 'secondary' => '#FFFFFF'], // Verde/Blanco
            ['primary' => '#FFD700', 'secondary' => '#0000FF'], // Dorado/Azul
            ['primary' => '#FF1493', 'secondary' => '#000000'], // Rosa/Negro
        ];

        // Obtener managers (excluyendo admin y operator)
        $managers = User::role('manager')->get();

        if ($managers->isEmpty()) {
            $this->command->warn('⚠️  No hay managers. Ejecuta AdminSeeder primero.');
            return;
        }

        $created = 0;

        // Crear 1-2 equipos por manager (primeros 15 managers)
        foreach ($managers->take(15) as $index => $manager) {
            // Primer equipo
            $teamName = $teamNames[$index % count($teamNames)];
            $color = $colors[$index % count($colors)];
            
            FantasyTeam::create([
                'league_id' => null, // Sin liga asignada
                'user_id' => $manager->id,
                'name' => $teamName,
                'slug' => Str::slug($teamName . '-' . $manager->id),
                'emblem_url' => null,
                'total_points' => 0,
                'budget' => 100.00,
                'is_bot' => false,
                'colors' => json_encode($color),
            ]);
            $created++;

            // Algunos managers tienen segundo equipo
            if ($index < 5) {
                $secondName = $teamNames[(count($teamNames) - 1 - $index) % count($teamNames)] . ' II';
                $secondColor = $colors[(count($colors) - 1 - $index) % count($colors)];
                
                FantasyTeam::create([
                    'league_id' => null,
                    'user_id' => $manager->id,
                    'name' => $secondName,
                    'slug' => Str::slug($secondName . '-' . $manager->id . '-2'),
                    'emblem_url' => null,
                    'total_points' => 0,
                    'budget' => 100.00,
                    'is_bot' => false,
                    'colors' => json_encode($secondColor),
                ]);
                $created++;
            }
        }

        $this->command->info("✅ {$created} equipos fantasy creados (sin liga asignada)");
    }
}