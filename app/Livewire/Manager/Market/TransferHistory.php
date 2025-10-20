<?php

namespace App\Livewire\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Transfer;
use App\Models\Gameweek;
use Livewire\Component;

class TransferHistory extends Component
{
    public FantasyTeam $team;
    public ?Gameweek $currentGameweek = null;
    public bool $marketOpen = false;
    public int $limit = 20;

    public function mount(FantasyTeam $team, ?Gameweek $gameweek, bool $marketOpen)
    {
        $this->team = $team;
        $this->currentGameweek = $gameweek;
        $this->marketOpen = $marketOpen;
    }

    public function loadMore()
    {
        $this->limit += 20;
    }

    public function render()
    {
        $transfers = Transfer::where('league_id', $this->team->league_id)
            ->where(function($q) {
                $q->where('to_fantasy_team_id', $this->team->id)
                  ->orWhere('from_fantasy_team_id', $this->team->id);
            })
            ->with(['player', 'fromTeam', 'toTeam'])
            ->orderBy('effective_at', 'desc')
            ->limit($this->limit)
            ->get();

        return view('livewire.manager.market.transfer-history', [
            'transfers' => $transfers,
        ]);
    }
}