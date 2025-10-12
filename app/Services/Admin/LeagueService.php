<?php

namespace App\Services\Admin;

use App\Models\League;
use App\Models\FantasyTeam;
use Illuminate\Support\Str;

class LeagueService
{
    /**
     * Fill league with bot teams up to max_participants.
     */
    public function fillWithBots(League $league): int
    {
        $currentTeams = $league->fantasyTeams()->count();
        $spotsAvailable = $league->max_participants - $currentTeams;

        if ($spotsAvailable <= 0) {
            return 0;
        }

        $botsCreated = 0;

        for ($i = 1; $i <= $spotsAvailable; $i++) {
            $botName = $this->generateUniqueBotName($league);
            
            FantasyTeam::create([
                'league_id' => $league->id,
                'user_id' => null,
                'name' => $botName,
                'slug' => Str::slug($botName . '-' . $league->id . '-' . time() . '-' . $i),
                'is_bot' => true,
                'budget' => 100.00, // Presupuesto default
                'total_points' => 0,
            ]);

            $botsCreated++;
        }

        return $botsCreated;
    }

    /**
     * Generate unique bot name for league.
     */
    protected function generateUniqueBotName(League $league): string
    {
        $botNames = [
            'Robot FC', 'Cyber Team', 'AI United', 'Bot City', 'Digital FC',
            'Virtual Squad', 'Automatic FC', 'Machine Team', 'Tech United',
            'Binary FC', 'Auto Squad', 'Silicon Valley', 'Code Warriors',
            'Data Rangers', 'Pixel United', 'Byte FC', 'Logic Squad'
        ];

        do {
            $name = $botNames[array_rand($botNames)] . ' ' . rand(1, 999);
        } while (
            $league->fantasyTeams()
                   ->where('name', $name)
                   ->exists()
        );

        return $name;
    }

    /**
     * Check if league can be filled with bots.
     */
    public function canFillWithBots(League $league): bool
    {
        if (!$league->auto_fill_bots) {
            return false;
        }

        $currentTeams = $league->fantasyTeams()->count();
        
        return $currentTeams < $league->max_participants;
    }

    /**
     * Get available slots for bots.
     */
    public function getAvailableSlots(League $league): int
    {
        $currentTeams = $league->fantasyTeams()->count();
        return max(0, $league->max_participants - $currentTeams);
    }
}