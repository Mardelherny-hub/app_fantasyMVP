<?php

namespace App\Listeners;

use App\Events\LeagueApproved;
use Illuminate\Support\Facades\Log;

class SetMembersDeadline
{
    /**
     * Handle the event.
     */
    public function handle(LeagueApproved $event): void
    {
        $league = $event->league;
        
        // Actualizar deadline solo para miembros que no lo tienen establecido
        // y que no son bots
        $updated = $league->members()
            ->whereNull('squad_deadline_at')
            ->whereNotNull('user_id')
            ->update([
                'squad_deadline_at' => now()->addHours(72)
            ]);
        
        Log::info("Squad deadlines set for league approval", [
            'league_id' => $league->id,
            'league_name' => $league->name,
            'members_updated' => $updated,
        ]);
    }
}