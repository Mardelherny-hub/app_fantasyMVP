<?php

namespace App\Events;

use App\Models\League;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeagueApproved
{
    use Dispatchable, SerializesModels;

    public League $league;

    /**
     * Create a new event instance.
     */
    public function __construct(League $league)
    {
        $this->league = $league;
    }
}