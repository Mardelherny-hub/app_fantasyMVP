<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealTeam;
use App\Models\RealPlayer;
use App\Models\RealTeamMembership;
use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\Season;
use Illuminate\Support\Str;

class CanadianPlayersDemoSeeder extends Seeder
{
    /**
     * Canadian first names
     */
    private array $firstNames = [
        'Liam', 'Noah', 'Oliver', 'Elijah', 'James', 'William', 'Benjamin', 'Lucas', 'Henry', 'Alexander',
        'Mason', 'Michael', 'Ethan', 'Daniel', 'Matthew', 'Aiden', 'Jackson', 'Logan', 'David', 'Joseph',
        'Samuel', 'Sebastian', 'Carter', 'Wyatt', 'Jayden', 'John', 'Jack', 'Luke', 'Dylan', 'Jacob',
        'Owen', 'Nathan', 'Caleb', 'Ryan', 'Nicholas', 'Andrew', 'Joshua', 'Christopher', 'Grayson', 'Max'
    ];

    /**
     * Canadian last names
     */
    private array $lastNames = [
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
        'Hernandez', 'Lopez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee',
        'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker', 'Young',
        'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores', 'Green', 'Adams'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Generando jugadores ficticios para equipos canadienses...');

        // Get all real teams
        $teams = RealTeam::all();
        
        if ($teams->isEmpty()) {
            $this->command->error('❌ No hay equipos cargados.');
            $this->command->warn('⚠️  Ejecuta primero: php artisan db:seed --class=CanadianTeamsSeeder');
            return;
        }

        // Get seasons
        $season2024 = Season::where('name', '2024')->first();
        $season2025 = Season::where('name', '2025')->first();

        if (!$season2024 || !$season2025) {
            $this->command->error('❌ No se encontraron las temporadas 2024 y 2025.');
            return;
        }

        $this->command->info("✅ Equipos encontrados: " . $teams->count());
        $this->command->info("✅ Generando ~20 jugadores por equipo");
        $this->command->newLine();

        $totalRealPlayers = 0;
        $totalFantasyPlayers = 0;
        $totalMemberships = 0;

        foreach ($teams as $team) {
            $this->command->info("⚽ {$team->name}");

            // Players per position for each team
            $roster = [
                'GK' => 3,  // 3 Goalkeepers
                'DF' => 7,  // 7 Defenders
                'MF' => 7,  // 7 Midfielders
                'FW' => 4,  // 4 Forwards
            ];

            $teamPlayers = 0;
            $shirtNumber = 1;

            foreach ($roster as $position => $count) {
                for ($i = 0; $i < $count; $i++) {
                    // Generate unique player
                    $firstName = $this->firstNames[array_rand($this->firstNames)];
                    $lastName = $this->lastNames[array_rand($this->lastNames)];
                    $fullName = "{$firstName} {$lastName}";
                    
                    // Generate unique external_id (team_id * 1000 + player_number)
                    $externalId = ($team->external_id * 1000) + $shirtNumber;

                    // Create RealPlayer
                    $realPlayer = RealPlayer::create([
                        'external_id' => $externalId,
                        'full_name' => $fullName,
                        'position' => $position,
                        'birthdate' => now()->subYears(rand(18, 35))->subDays(rand(0, 364)),
                        'nationality' => 'CA',
                        'photo_url' => null,
                        'meta' => [
                            'generated' => true,
                            'team_name' => $team->name,
                            'created_at' => now()->toDateTimeString(),
                        ],
                    ]);

                    $totalRealPlayers++;
                    $teamPlayers++;

                    // Create fantasy player (anonymized)
                    $fantasyPlayer = $this->createFantasyPlayer($realPlayer, $season2025, $position);
                    
                    if ($fantasyPlayer) {
                        $totalFantasyPlayers++;
                    }

                    // Create team membership for both seasons
                    foreach ([$season2024, $season2025] as $season) {
                        RealTeamMembership::create([
                            'real_team_id' => $team->id,
                            'real_player_id' => $realPlayer->id,
                            'season_id' => $season->id,
                            'shirt_number' => $shirtNumber,
                            'from_date' => $season->starts_at,
                            'to_date' => null, // Current player
                        ]);

                        $totalMemberships++;
                    }

                    $shirtNumber++;
                }
            }

            $this->command->line("  ✓ Generados: {$teamPlayers} jugadores");
        }

        $this->command->newLine();
        $this->command->info("✅ Resumen general:");
        $this->command->line("   - Jugadores reales creados: {$totalRealPlayers}");
        $this->command->line("   - Jugadores fantasy creados: {$totalFantasyPlayers}");
        $this->command->line("   - Membresías creadas: {$totalMemberships}");

        // Show statistics by position
        $this->showPositionStats();
    }

    /**
     * Create anonymized fantasy player from real player.
     */
    private function createFantasyPlayer(RealPlayer $realPlayer, Season $season, string $position): ?Player
    {
        // Anonymize name (use initials + last name)
        $nameParts = explode(' ', $realPlayer->full_name);
        $lastName = array_pop($nameParts);
        $initials = '';
        
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1)) . '. ';
            }
        }
        
        $anonymizedName = trim($initials . $lastName);

        // Map position from string (GK/DF/MF/FW) to integer (1/2/3/4)
        $positionMap = [
            'GK' => Player::POSITION_GK,
            'DF' => Player::POSITION_DF,
            'MF' => Player::POSITION_MF,
            'FW' => Player::POSITION_FW,
        ];

        $positionInt = $positionMap[$position] ?? Player::POSITION_MF;

        // Create fantasy player
        $fantasyPlayer = Player::create([
            'real_player_id' => $realPlayer->id,
            'full_name' => $anonymizedName,
            'known_as' => null,
            'position' => $positionInt,
            'nationality' => $realPlayer->nationality,
            'birthdate' => $realPlayer->birthdate,
            'height_cm' => rand(165, 195),
            'weight_kg' => rand(65, 95),
            'photo_url' => null, // Use generic avatar
            'is_active' => true,
        ]);

        // Create initial valuation
        $this->createInitialValuation($fantasyPlayer, $season, $positionInt);

        return $fantasyPlayer;
    }

    /**
     * Create initial market valuation for fantasy player.
     */
    private function createInitialValuation(Player $player, Season $season, int $position): void
    {
        // Base value by position
        $baseValues = [
            Player::POSITION_GK => 5.0,  // Goalkeepers
            Player::POSITION_DF => 6.0,  // Defenders
            Player::POSITION_MF => 7.0,  // Midfielders
            Player::POSITION_FW => 8.0,  // Forwards
        ];

        $baseValue = $baseValues[$position] ?? 6.0;
        
        // Add random variation ±20%
        $variation = rand(80, 120) / 100;
        $marketValue = round($baseValue * $variation, 2);

        PlayerValuation::create([
            'player_id' => $player->id,
            'season_id' => $season->id,
            'market_value' => $marketValue,
            'updated_at' => now(),
        ]);
    }

    /**
     * Show statistics by position.
     */
    private function showPositionStats(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Jugadores por posición:');

        $stats = Player::selectRaw('position, COUNT(*) as total')
            ->groupBy('position')
            ->orderBy('position')
            ->get();

        $positionNames = [
            Player::POSITION_GK => 'Goalkeepers',
            Player::POSITION_DF => 'Defenders',
            Player::POSITION_MF => 'Midfielders',
            Player::POSITION_FW => 'Forwards',
        ];

        foreach ($stats as $stat) {
            $name = $positionNames[$stat->position] ?? 'Unknown';
            $this->command->line("   {$name}: {$stat->total}");
        }
    }
}