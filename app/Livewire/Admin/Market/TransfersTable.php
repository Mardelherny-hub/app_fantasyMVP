<?php

namespace App\Livewire\Admin\Market;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transfer;
use App\Models\League;

class TransfersTable extends Component
{
    use WithPagination;

    public $leagueId = '';
    public $type = '';
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = ['leagueId', 'type', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLeagueId()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Transfer::with(['player', 'fromTeam', 'toTeam', 'league']);

        if ($this->leagueId) {
            $query->where('league_id', $this->leagueId);
        }

        if ($this->type !== '') {
            $query->where('type', $this->type);
        }

        if ($this->search) {
            $query->whereHas('player', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('effective_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('effective_at', '<=', $this->dateTo);
        }

        $transfers = $query->orderBy('effective_at', 'desc')->paginate(50);
        $leagues = League::select('id', 'name')->get();

        return view('livewire.admin.market.transfers-table', [
            'transfers' => $transfers,
            'leagues' => $leagues,
        ]);
    }
}