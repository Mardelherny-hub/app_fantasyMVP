<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealCompetition;
use App\Models\RealTeam;
use App\Models\RealFixture;
use App\Models\RealMatch;
use App\Models\Season;
use App\Services\LiveScoreApiService;
use Carbon\Carbon;

class CanadianFixturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📅 Cargando fixtures canadienses...');

        // Verify data exists
        $competitions = RealCompetition::all();
        $teams = RealTeam::all();

        if ($competitions->isEmpty() || $teams->isEmpty()) {
            $this->command->error('❌ Faltan datos previos.');
            $this->command->warn('⚠️  Asegúrate de ejecutar primero:');
            $this->command->line('   1. CanadianCompetitionsSeeder');
            $this->command->line('   2. CanadianTeamsSeeder');
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
        $this->command->info("✅ Equipos: " . $teams->count());
        $this->command->newLine();

        $apiService = new LiveScoreApiService();
        
        $totalFixtures = 0;
        $totalMatches = 0;

        foreach ($competitions as $competition) {
            $this->command->info("📋 Procesando fixtures: {$competition->name}");

            // Get fixtures (scheduled matches)
            $fixturesData = $apiService->getFixtures($competition->external_id);
            
            // Get history (finished matches)
            $historyData = $apiService->getHistory($competition->external_id);

            $compFixtures = 0;
            $compMatches = 0;

            // Process fixtures (future/scheduled)
            foreach ($fixturesData as $fixtureData) {
                $result = $this->processFixture($fixtureData, $competition, $season2025);
                
                if ($result['fixture']) {
                    $compFixtures++;
                    $totalFixtures++;
                }
            }

            // Process history (past matches)
            foreach ($historyData as $matchData) {
                $result = $this->processMatch($matchData, $competition, $season2024);
                
                if ($result['fixture']) {
                    $compFixtures++;
                    $totalFixtures++;
                }
                
                if ($result['match']) {
                    $compMatches++;
                    $totalMatches++;
                }
            }

            $this->command->line("  📊 Fixtures: {$compFixtures} | Matches: {$compMatches}");
        }

        $this->command->newLine();
        $this->command->info("✅ Resumen general:");
        $this->command->line("   - Fixtures creados: {$totalFixtures}");
        $this->command->line("   - Matches creados: {$totalMatches}");

        // Show statistics
        $this->showFixtureStats();
    }

    /**
     * Process fixture (scheduled match).
     */
    private function processFixture(array $data, RealCompetition $competition, Season $season): array
    {
        // Find teams
        $homeTeam = RealTeam::where('external_id', $data['home_id'] ?? null)->first();
        $awayTeam = RealTeam::where('external_id', $data['away_id'] ?? null)->first();

        if (!$homeTeam || !$awayTeam) {
            return ['fixture' => null, 'match' => null];
        }

        // Parse date and time
        $matchDate = isset($data['date']) ? Carbon::parse($data['date'])->toDateString() : null;
        // Parse time - skip if it's a status like 'FT', 'HT', etc.
        $matchTime = null;
        if (isset($data['time']) && !in_array(strtoupper($data['time']), ['FT', 'HT', 'LIVE', 'SCHEDULED', 'POSTPONED', 'CANCELED'])) {
            try {
                $matchTime = Carbon::parse($data['time']);
            } catch (\Exception $e) {
                // Invalid time format, skip
            }
        }

        // Determine status
        $status = $this->normalizeStatus($data['status'] ?? 'scheduled');

        // Create or update fixture
        $fixture = RealFixture::updateOrCreate(
            ['external_id' => $data['id'] ?? null],
            [
                'real_competition_id' => $competition->id,
                'season_id' => $season->id,
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'round' => $data['round'] ?? null,
                'venue' => $data['venue'] ?? $homeTeam->stadium,
                'status' => $status,
                'match_date_utc' => $matchDate,
                'match_time_utc' => $matchTime,
                'meta' => [
                    'loaded_at' => now()->toDateTimeString(),
                    'source' => 'LiveScore API',
                    'raw_status' => $data['status'] ?? null,
                ],
            ]
        );

        return ['fixture' => $fixture, 'match' => null];
    }

    /**
     * Process match (finished match with result).
     */
    private function processMatch(array $data, RealCompetition $competition, Season $season): array
    {
        // First create/get the fixture
        $fixtureResult = $this->processFixture($data, $competition, $season);
        $fixture = $fixtureResult['fixture'];

        if (!$fixture) {
            return ['fixture' => null, 'match' => null];
        }

        // Only create match if status is finished or live
        if (!in_array($fixture->status, ['finished', 'live', 'ft', 'ht'])) {
            return ['fixture' => $fixture, 'match' => null];
        }

        // Create or update match
        $match = RealMatch::updateOrCreate(
            ['external_id' => $data['id'] ?? null],
            [
                'real_fixture_id' => $fixture->id,
                'status' => $fixture->status,
                'minute' => $data['minute'] ?? ($fixture->status === 'finished' ? 90 : null),
                'home_score' => $data['home_score'] ?? 0,
                'away_score' => $data['away_score'] ?? 0,
                'started_at_utc' => $this->parseDateTime($data),
                'finished_at_utc' => $fixture->status === 'finished' 
                    ? (isset($data['date']) ? Carbon::parse($data['date'])->addHours(2) : null)
                    : null,
                'meta' => [
                    'loaded_at' => now()->toDateTimeString(),
                    'source' => 'LiveScore API',
                ],
            ]
        );

        return ['fixture' => $fixture, 'match' => $match];
    }

    /**
     * Normalize status from API.
     */
    private function normalizeStatus(?string $status): string
    {
        if (!$status) {
            return 'scheduled';
        }

        $status = strtolower(trim($status));

        return match($status) {
            'scheduled', 'upcoming', 'not started' => 'scheduled',
            'live', 'in play', 'playing' => 'live',
            'finished', 'ft', 'full time', 'ended' => 'finished',
            'ht', 'half time' => 'ht',
            'postponed', 'delayed' => 'postponed',
            'canceled', 'cancelled', 'abandoned' => 'canceled',
            default => 'scheduled',
        };
    }

    /**
     * Show fixture statistics.
     */
    private function showFixtureStats(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Fixtures por estado:');

        $stats = RealFixture::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $statusIcons = [
            'scheduled' => '📅',
            'live' => '🔴',
            'finished' => '✅',
            'ht' => '⏸️',
            'postponed' => '⏰',
            'canceled' => '❌',
        ];

        foreach ($stats as $stat) {
            $icon = $statusIcons[$stat->status] ?? '📌';
            $this->command->line("   {$icon} " . ucfirst($stat->status) . ": {$stat->total}");
        }

        // Show matches stats
        $totalMatches = RealMatch::count();
        $finishedMatches = RealMatch::where('status', 'finished')->count();
        $liveMatches = RealMatch::where('status', 'live')->count();

        $this->command->newLine();
        $this->command->info('📊 Matches con datos:');
        $this->command->line("   ✅ Finalizados: {$finishedMatches}");
        $this->command->line("   🔴 En vivo: {$liveMatches}");
        $this->command->line("   📊 Total: {$totalMatches}");
    }

    /**
     * Parse date and time from API data safely.
     */
    private function parseDateTime(array $data): ?Carbon
    {
        if (!isset($data['date'])) {
            return null;
        }

        // If no time or time is a status, just use date
        if (!isset($data['time']) || in_array(strtoupper($data['time']), ['FT', 'HT', 'LIVE', 'SCHEDULED', 'POSTPONED', 'CANCELED'])) {
            try {
                return Carbon::parse($data['date']);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try to parse date + time
        try {
            return Carbon::parse($data['date'] . ' ' . $data['time']);
        } catch (\Exception $e) {
            // If fails, just use date
            try {
                return Carbon::parse($data['date']);
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}