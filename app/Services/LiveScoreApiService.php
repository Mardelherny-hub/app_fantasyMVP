<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LiveScoreApiService
{
    /**
     * API credentials
     */
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl = 'https://livescore-api.com/api-client';

    /**
     * Competition IDs for Canada
     */
    const CANADIAN_COMPETITIONS = [
        257 => 'Canadian Championship',
        448 => 'Canadian Soccer League',
        76  => 'Major League Soccer',
        258 => 'Canadian Premier League',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.livescore.key', 'FPPjkKhSsMiqLQ9W');
        $this->apiSecret = config('services.livescore.secret', 'R8LsjGHfqpknfRZUGPd1zuU1hcs23BZU');
    }

    // ========================================
    // COMPETITIONS
    // ========================================

    /**
     * Get all Canadian competitions.
     */
    public function getCanadianCompetitions(): array
    {
        $competitions = [];

        foreach (self::CANADIAN_COMPETITIONS as $id => $name) {
            $competitions[] = [
                'external_id' => $id,
                'name' => $name,
                'country' => 'CA',
                'type' => $this->inferCompetitionType($name),
                'active' => true,
                'external_source' => 'livescore',
            ];
        }

        return $competitions;
    }

    /**
     * Infer competition type from name.
     */
    private function inferCompetitionType(string $name): string
    {
        return str_contains(strtolower($name), 'championship') ? 'cup' : 'league';
    }

    // ========================================
    // FIXTURES
    // ========================================

    /**
     * Get fixtures for a competition.
     */
    public function getFixtures(int $competitionId): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/fixtures/matches.json", [
                'competition_id' => $competitionId,
                'key' => $this->apiKey,
                'secret' => $this->apiSecret,
            ]);

            if ($response->successful()) {
                return $response->json('data.fixtures', []);
            }

            Log::warning("LiveScore API - Fixtures failed for competition {$competitionId}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error("LiveScore API - Fixtures exception for competition {$competitionId}", [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    // ========================================
    // LIVE SCORES
    // ========================================

    /**
     * Get live matches for a competition.
     */
    public function getLiveScores(int $competitionId): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/matches/live.json", [
                'competition_id' => $competitionId,
                'key' => $this->apiKey,
                'secret' => $this->apiSecret,
            ]);

            if ($response->successful()) {
                return $response->json('data.match', []);
            }

            Log::warning("LiveScore API - Live scores failed for competition {$competitionId}", [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error("LiveScore API - Live scores exception for competition {$competitionId}", [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    // ========================================
    // HISTORY (PAST RESULTS)
    // ========================================

    /**
     * Get match history for a competition.
     */
    public function getHistory(int $competitionId): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/scores/history.json", [
                'competition_id' => $competitionId,
                'key' => $this->apiKey,
                'secret' => $this->apiSecret,
            ]);

            if ($response->successful()) {
                return $response->json('data.match', []);
            }

            Log::warning("LiveScore API - History failed for competition {$competitionId}", [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error("LiveScore API - History exception for competition {$competitionId}", [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    // ========================================
    // STANDINGS
    // ========================================

    /**
     * Get standings table for a competition.
     */
    public function getStandings(int $competitionId): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/leagues/table.json", [
                'competition_id' => $competitionId,
                'key' => $this->apiKey,
                'secret' => $this->apiSecret,
            ]);

            if ($response->successful()) {
                return $response->json('data.table', []);
            }

            Log::warning("LiveScore API - Standings failed for competition {$competitionId}", [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error("LiveScore API - Standings exception for competition {$competitionId}", [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    // ========================================
    // TEAMS (Extracted from fixtures/standings)
    // ========================================

    /**
     * Extract unique teams from fixtures data.
     */
    public function extractTeamsFromFixtures(array $fixtures): array
    {
        $teams = [];
        $teamsById = [];

        foreach ($fixtures as $fixture) {
            // Home team
            if (isset($fixture['home_id']) && !isset($teamsById[$fixture['home_id']])) {
                $teamsById[$fixture['home_id']] = [
                    'external_id' => $fixture['home_id'],
                    'name' => $fixture['home_name'] ?? 'Unknown',
                    'country' => 'CA',
                    'logo_url' => $fixture['home_logo'] ?? null,
                    'stadium' => $fixture['venue'] ?? null,
                ];
            }

            // Away team
            if (isset($fixture['away_id']) && !isset($teamsById[$fixture['away_id']])) {
                $teamsById[$fixture['away_id']] = [
                    'external_id' => $fixture['away_id'],
                    'name' => $fixture['away_name'] ?? 'Unknown',
                    'country' => 'CA',
                    'logo_url' => $fixture['away_logo'] ?? null,
                    'stadium' => null,
                ];
            }
        }

        return array_values($teamsById);
    }

    /**
     * Extract unique teams from standings data.
     */
    public function extractTeamsFromStandings(array $standings): array
    {
        $teams = [];
        $teamsById = [];

        foreach ($standings as $standing) {
            if (isset($standing['team_id']) && !isset($teamsById[$standing['team_id']])) {
                $teamsById[$standing['team_id']] = [
                    'external_id' => $standing['team_id'],
                    'name' => $standing['team_name'] ?? 'Unknown',
                    'country' => 'CA',
                    'logo_url' => $standing['team_logo'] ?? null,
                    'stadium' => null,
                ];
            }
        }

        return array_values($teamsById);
    }

    // ========================================
    // PLAYERS (Would need additional endpoint)
    // ========================================

    /**
     * Note: LiveScore API might not provide player roster endpoint.
     * This is a placeholder for when/if we find the correct endpoint.
     * 
     * For now, we'll extract players from match lineups when available.
     */
    public function extractPlayersFromMatch(array $match): array
    {
        $players = [];

        // Check if lineup data exists
        if (isset($match['lineup']['home'])) {
            foreach ($match['lineup']['home'] as $player) {
                $players[] = $this->normalizePlayer($player, $match['home_id']);
            }
        }

        if (isset($match['lineup']['away'])) {
            foreach ($match['lineup']['away'] as $player) {
                $players[] = $this->normalizePlayer($player, $match['away_id']);
            }
        }

        return $players;
    }

    /**
     * Normalize player data from API.
     */
    private function normalizePlayer(array $playerData, int $teamId): array
    {
        return [
            'external_id' => $playerData['id'] ?? null,
            'full_name' => $playerData['name'] ?? 'Unknown Player',
            'position' => $this->normalizePosition($playerData['position'] ?? null),
            'birthdate' => $playerData['birthdate'] ?? null,
            'nationality' => $playerData['nationality'] ?? 'CA',
            'photo_url' => $playerData['photo'] ?? null,
            'team_id' => $teamId,
        ];
    }

    /**
     * Normalize position from API to our format (GK, DF, MF, FW).
     */
    private function normalizePosition(?string $position): ?string
    {
        if (!$position) {
            return null;
        }

        $position = strtoupper(trim($position));

        // Map various position formats to our standard
        return match(true) {
            in_array($position, ['G', 'GK', 'GOALKEEPER']) => 'GK',
            in_array($position, ['D', 'DF', 'DEF', 'DEFENDER']) => 'DF',
            in_array($position, ['M', 'MF', 'MID', 'MIDFIELDER']) => 'MF',
            in_array($position, ['F', 'FW', 'FOR', 'FORWARD', 'ST', 'STRIKER']) => 'FW',
            default => null,
        };
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Test API connection.
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/leagues/table.json", [
                'competition_id' => 257, // Canadian Championship
                'key' => $this->apiKey,
                'secret' => $this->apiSecret,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('LiveScore API - Connection test failed', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get API credentials info (for debugging).
     */
    public function getCredentialsInfo(): array
    {
        return [
            'key' => substr($this->apiKey, 0, 8) . '...',
            'secret' => substr($this->apiSecret, 0, 8) . '...',
            'base_url' => $this->baseUrl,
        ];
    }
}