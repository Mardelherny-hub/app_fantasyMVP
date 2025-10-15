<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealCompetition;
use App\Models\RealTeam;
use App\Models\RealCompetitionStanding;
use App\Models\Season;
use App\Services\LiveScoreApiService;

class CanadianStandingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏆 Cargando tablas de posiciones...');

        // Verify data exists
        $competitions = RealCompetition::all();
        $teams = RealTeam::all();

        if ($competitions->isEmpty() || $teams->isEmpty()) {
            $this->command->error('❌ Faltan datos previos.');
            $this->command->warn('⚠️  Asegúrate de ejecutar primero los seeders anteriores.');
            return;
        }

        // Get seasons
        $season2024 = Season::where('name', '2024')->first();
        $season2025 = Season::where('name', '2025')->first();

        if (!$season2024 || !$season2025) {
            $this->command->error('❌ No se encontraron las temporadas 2024 y 2025.');
            return;
        }

        $this->command->info("✅ Competiciones: " . $competitions->count());
        $this->command->newLine();

        $apiService = new LiveScoreApiService();
        
        $totalStandings = 0;
        $competitionsWithStandings = 0;

        foreach ($competitions as $competition) {
            $this->command->info("📋 Procesando: {$competition->name}");

            // Get standings from API
            $standingsData = $apiService->getStandings($competition->external_id);

            if (empty($standingsData)) {
                $this->command->warn("  ⚠️  Sin tabla de posiciones disponible");
                continue;
            }

            $competitionsWithStandings++;
            $compStandings = 0;

            // Process each standing entry
            foreach ($standingsData as $standingData) {
                // Find team
                $team = RealTeam::where('external_id', $standingData['team_id'] ?? null)->first();

                if (!$team) {
                    continue;
                }

                // Determine season (use 2025 for current standings)
                $season = $season2025;

                // Extract stage and group if available
                $stage = $standingData['stage'] ?? 'Regular Season';
                $group = $standingData['group'] ?? null;

                // Parse form (recent results)
                $form = $this->parseForm($standingData['form'] ?? null);

                // Create or update standing
                $standing = RealCompetitionStanding::updateOrCreate(
                    [
                        'real_competition_id' => $competition->id,
                        'season_id' => $season->id,
                        'stage' => $stage,
                        'group' => $group,
                        'real_team_id' => $team->id,
                    ],
                    [
                        'rank' => $standingData['rank'] ?? $standingData['position'] ?? null,
                        'played' => $standingData['played'] ?? $standingData['matches'] ?? 0,
                        'won' => $standingData['won'] ?? $standingData['wins'] ?? 0,
                        'drawn' => $standingData['drawn'] ?? $standingData['draws'] ?? 0,
                        'lost' => $standingData['lost'] ?? $standingData['losses'] ?? 0,
                        'goals_for' => $standingData['goals_for'] ?? $standingData['gf'] ?? 0,
                        'goals_against' => $standingData['goals_against'] ?? $standingData['ga'] ?? 0,
                        'goal_diff' => $standingData['goal_diff'] ?? $standingData['gd'] ?? 0,
                        'points' => $standingData['points'] ?? $standingData['pts'] ?? 0,
                        'form' => $form,
                        'meta' => [
                            'loaded_at' => now()->toDateTimeString(),
                            'source' => 'LiveScore API',
                        ],
                    ]
                );

                if ($standing->wasRecentlyCreated) {
                    $compStandings++;
                    $totalStandings++;
                }
            }

            $this->command->line("  📊 Posiciones creadas: {$compStandings}");

            // Show top 5 teams
            if ($compStandings > 0) {
                $this->showTopTeams($competition, $season);
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Resumen general:");
        $this->command->line("   - Competiciones con tabla: {$competitionsWithStandings}");
        $this->command->line("   - Total posiciones: {$totalStandings}");

        // Show overall statistics
        $this->showStandingsStats();
    }

    /**
     * Parse form string from API.
     */
    private function parseForm(?string $formString): ?array
    {
        if (!$formString) {
            return null;
        }

        // Form might come as "WWDLW" or comma separated
        $formString = strtoupper(str_replace([',', ' ', '-'], '', $formString));
        
        if (empty($formString)) {
            return null;
        }

        // Convert to array of individual results
        return str_split($formString);
    }

    /**
     * Show top 5 teams for a competition.
     */
    private function showTopTeams(RealCompetition $competition, Season $season): void
    {
        $standings = RealCompetitionStanding::with('team')
            ->where('real_competition_id', $competition->id)
            ->where('season_id', $season->id)
            ->whereNull('group') // Main table only
            ->orderBy('rank')
            ->limit(5)
            ->get();

        if ($standings->isEmpty()) {
            return;
        }

        $this->command->line("  🏆 Top 5:");
        foreach ($standings as $standing) {
            $formDisplay = $standing->form_display ?? '';
            $this->command->line(
                sprintf(
                    "     %d. %-25s %2d pts (%d-%d-%d) %s",
                    $standing->rank,
                    $standing->team->name,
                    $standing->points,
                    $standing->won,
                    $standing->drawn,
                    $standing->lost,
                    $formDisplay
                )
            );
        }
    }

    /**
     * Show standings statistics.
     */
    private function showStandingsStats(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Estadísticas generales:');

        // Leaders by competition
        $competitions = RealCompetition::with(['standings' => function($query) {
            $query->where('rank', 1)
                  ->whereNull('group')
                  ->with('team');
        }])->get();

        foreach ($competitions as $competition) {
            $leader = $competition->standings->first();
            
            if ($leader) {
                $this->command->line(
                    sprintf(
                        "   🥇 %s: %s (%d pts)",
                        $competition->name,
                        $leader->team->name,
                        $leader->points
                    )
                );
            }
        }

        // Overall stats
        $totalTeams = RealCompetitionStanding::distinct('real_team_id')->count();
        $undefeatedTeams = RealCompetitionStanding::where('lost', 0)
            ->where('played', '>', 0)
            ->count();

        $this->command->newLine();
        $this->command->line("   📊 Equipos en tablas: {$totalTeams}");
        $this->command->line("   🛡️  Equipos invictos: {$undefeatedTeams}");
    }
}