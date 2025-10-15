<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealCompetition;
use App\Models\RealTeam;
use App\Models\RealCompetitionTeamSeason;
use App\Models\Season;
use App\Services\LiveScoreApiService;

class CanadianTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('⚽ Cargando equipos canadienses...');

        // Verify competitions exist
        $competitions = RealCompetition::all();
        
        if ($competitions->isEmpty()) {
            $this->command->error('❌ No hay competiciones cargadas.');
            $this->command->warn('⚠️  Ejecuta primero: php artisan db:seed --class=CanadianCompetitionsSeeder');
            return;
        }

        // Get or create seasons
        $season2024 = Season::firstOrCreate(
            ['name' => '2024'],
            [
                'code' => '2024',
                'starts_at' => '2024-04-01',
                'ends_at' => '2024-11-30',
                'is_active' => false,
            ]
        );

        $season2025 = Season::firstOrCreate(
            ['name' => '2025'],
            [
                'code' => '2025',
                'starts_at' => '2025-04-01',
                'ends_at' => '2025-11-30',
                'is_active' => true,
            ]
        );

        $seasons = [$season2024, $season2025];

        $this->command->info("✅ Temporadas: 2024, 2025");
        $this->command->newLine();

        $apiService = new LiveScoreApiService();
        $allTeamsById = [];
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalRegistrations = 0;

        foreach ($competitions as $competition) {
            $this->command->info("📋 Procesando: {$competition->name} (ID: {$competition->external_id})");

            // Get fixtures to extract teams
            $fixtures = $apiService->getFixtures($competition->external_id);
            
            if (empty($fixtures)) {
                $this->command->warn("  ⚠️  Sin fixtures disponibles");
                
                // Try standings as fallback
                $standings = $apiService->getStandings($competition->external_id);
                
                if (empty($standings)) {
                    $this->command->warn("  ⚠️  Sin standings disponibles. Saltando competición.");
                    continue;
                }
                
                $teams = $apiService->extractTeamsFromStandings($standings);
                $this->command->info("  ✓ Equipos extraídos de standings: " . count($teams));
            } else {
                $teams = $apiService->extractTeamsFromFixtures($fixtures);
                $this->command->info("  ✓ Equipos extraídos de fixtures: " . count($teams));
            }

            $compCreated = 0;
            $compUpdated = 0;

            foreach ($teams as $teamData) {
                // Skip if already processed
                if (isset($allTeamsById[$teamData['external_id']])) {
                    $team = $allTeamsById[$teamData['external_id']];
                } else {
                    // Create or update team
                    $team = RealTeam::updateOrCreate(
                        ['external_id' => $teamData['external_id']],
                        [
                            'name' => $teamData['name'],
                            'short_name' => $this->generateShortName($teamData['name']),
                            'country' => $teamData['country'],
                            'logo_url' => $teamData['logo_url'],
                            'stadium' => $teamData['stadium'],
                            'meta' => [
                                'loaded_at' => now()->toDateTimeString(),
                                'source' => 'LiveScore API',
                            ],
                        ]
                    );

                    if ($team->wasRecentlyCreated) {
                        $compCreated++;
                        $totalCreated++;
                    } else {
                        $compUpdated++;
                        $totalUpdated++;
                    }

                    $allTeamsById[$teamData['external_id']] = $team;
                }

                // Register team in competition for both seasons
                foreach ($seasons as $season) {
                    $registration = RealCompetitionTeamSeason::firstOrCreate([
                        'real_competition_id' => $competition->id,
                        'season_id' => $season->id,
                        'real_team_id' => $team->id,
                    ]);

                    if ($registration->wasRecentlyCreated) {
                        $totalRegistrations++;
                    }
                }
            }

            $this->command->line("  📊 Creados: {$compCreated} | Actualizados: {$compUpdated}");
        }

        $this->command->newLine();
        $this->command->info("✅ Resumen general:");
        $this->command->line("   - Equipos creados: {$totalCreated}");
        $this->command->line("   - Equipos actualizados: {$totalUpdated}");
        $this->command->line("   - Total equipos: " . count($allTeamsById));
        $this->command->line("   - Registros competición-temporada: {$totalRegistrations}");

        // Show team list
        $this->command->newLine();
        $this->command->info('📋 Equipos cargados:');
        
        $allTeams = RealTeam::orderBy('name')->get();
        
        foreach ($allTeams as $team) {
            $shortName = $team->short_name ? " ({$team->short_name})" : '';
            $this->command->line("   ⚽ {$team->name}{$shortName}");
        }
    }

    /**
     * Generate short name from full name.
     */
    private function generateShortName(string $fullName): string
    {
        // Remove common suffixes
        $name = str_replace(['FC', 'CF', 'United', 'City', 'Town'], '', $fullName);
        $name = trim($name);

        // Get first word (usually city name)
        $words = explode(' ', $name);
        $shortName = $words[0];

        // Limit to 15 characters
        return substr($shortName, 0, 15);
    }
}