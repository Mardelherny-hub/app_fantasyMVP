<?php

namespace App\Livewire\Admin\Market;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Listing;
use App\Models\League;

class ListingsTable extends Component
{
    use WithPagination;

    public $leagueId = '';
    public $status = '';
    public $search = '';
    public $minPrice = '';
    public $maxPrice = '';

    protected $queryString = ['leagueId', 'status', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLeagueId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Listing::with(['player', 'fantasyTeam', 'league', 'offers']);

        if ($this->leagueId) {
            $query->where('league_id', $this->leagueId);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->whereHas('player', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(50);
        $leagues = League::select('id', 'name')->get();

        return view('livewire.admin.market.listings-table', [
            'listings' => $listings,
            'leagues' => $leagues,
        ]);
    }
}