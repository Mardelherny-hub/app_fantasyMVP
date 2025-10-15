<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealCompetition;
use App\Models\RealTeam;
use App\Models\RealPlayer;
use App\Models\RealTeamMembership;
use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\Season;
use App\Services\LiveScoreApiService;
use Illuminate\Support\Str;

class CanadianPlayersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Cargando jugadores canadienses...');

        // Verify teams exist
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
        $this->command->info("✅ Temporadas: 2024, 2025");
        $this->command->newLine();

        $apiService = new LiveScoreApiService();
        $competitions = RealCompetition::all();

        $totalRealPlayers = 0;
        $totalFantasyPlayers = 0;
        $totalMemberships = 0;
        $allPlayersById = [];

        foreach ($competitions as $competition) {
            $this->command->info("📋 Procesando jugadores de: {$competition->name}");

            // Get match history to extract player data from lineups
            $matches = $apiService->getHistory($competition->external_id);

            if (empty($matches)) {
                $this->command->warn("  ⚠️  Sin partidos históricos disponibles");
                continue;
            }

            $compRealPlayers = 0;
            $compFantasyPlayers = 0;

            foreach ($matches as $match) {
                $players = $apiService->extractPlayersFromMatch($match);

                foreach ($players as $playerData) {
                    // Skip if no external_id
                    if (!isset($playerData['external_id'])) {
                        continue;
                    }

                    // Skip if already processed
                    if (isset($allPlayersById[$playerData['external_id']])) {
                        continue;
                    }

                    // Create or update RealPlayer
                    $realPlayer = RealPlayer::updateOrCreate(
                        ['external_id' => $playerData['external_id']],
                        [
                            'full_name' => $playerData['full_name'],
                            'position' => $playerData['position'],
                            'birthdate' => $playerData['birthdate'],
                            'nationality' => $playerData['nationality'],
                            'photo_url' => $playerData['photo_url'],
                            'meta' => [
                                'loaded_at' => now()->toDateTimeString(),
                                'source' => 'LiveScore API',
                            ],
                        ]
                    );

                    if ($realPlayer->wasRecentlyCreated) {
                        $compRealPlayers++;
                        $totalRealPlayers++;

                        // Create fantasy player (anonymized)
                        $fantasyPlayer = $this->createFantasyPlayer($realPlayer, $season2025);
                        
                        if ($fantasyPlayer) {
                            $compFantasyPlayers++;
                            $totalFantasyPlayers++;
                        }
                    }

                    $allPlayersById[$playerData['external_id']] = $realPlayer;

                    // Create team membership if team exists
                    if (isset($playerData['team_id'])) {
                        $team = RealTeam::where('external_id', $playerData['team_id'])->first();
                        
                        if ($team) {
                            foreach ([$season2024, $season2025] as $season) {
                                $membership = RealTeamMembership::firstOrCreate([
                                    'real_team_id' => $team->id,
                                    'real_player_id' => $realPlayer->id,
                                    'season_id' => $season->id,
                                ], [
                                    'from_date' => $season->starts_at,
                                    'to_date' => null, // Current player
                                ]);

                                if ($membership->wasRecentlyCreated) {
                                    $totalMemberships++;
                                }
                            }
                        }
                    }
                }
            }

            $this->command->line("  📊 Jugadores reales: {$compRealPlayers} | Fantasy: {$compFantasyPlayers}");
        }

        $this->command->newLine();
        $this->command->info("✅ Resumen general:");
        $this->command->line("   - Jugadores reales creados: {$totalRealPlayers}");
        $this->command->line("   - Jugadores fantasy creados: {$totalFantasyPlayers}");
        $this->command->line("   - Membresías creadas: {$totalMemberships}");
        $this->command->line("   - Total jugadores únicos: " . count($allPlayersById));

        // Show statistics by position
        $this->showPositionStats();
    }

    /**
     * Create anonymized fantasy player from real player.
     */
    private function createFantasyPlayer(RealPlayer $realPlayer, Season $season): ?Player
    {
        // Skip if fantasy player already exists
        if (Player::where('real_player_id', $realPlayer->id)->exists()) {
            return null;
        }

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

        $position = $positionMap[strtoupper($realPlayer->position)] ?? Player::POSITION_MF;

        // Create fantasy player
        $fantasyPlayer = Player::create([
            'real_player_id' => $realPlayer->id,
            'full_name' => $anonymizedName,
            'known_as' => null, // Could add nickname logic here
            'position' => $position,
            'nationality' => $realPlayer->nationality,
            'birthdate' => $realPlayer->birthdate,
            'height_cm' => null, // Not available from API
            'weight_kg' => null, // Not available from API
            'photo_url' => null, // Use generic avatar instead of real photo
            'is_active' => true,
        ]);

        // Create initial valuation
        $this->createInitialValuation($fantasyPlayer, $season, $position);

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