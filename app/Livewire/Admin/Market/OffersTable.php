<?php

namespace App\Livewire\Admin\Market;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Offer;
use App\Models\League;

class OffersTable extends Component
{
    use WithPagination;

    public $leagueId = '';
    public $status = '';
    public $search = '';

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
        $query = Offer::with(['listing.player', 'listing.fantasyTeam', 'listing.league', 'buyerTeam']);

        if ($this->leagueId) {
            $query->whereHas('listing', fn($q) => $q->where('league_id', $this->leagueId));
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->whereHas('listing.player', function($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            });
        }

        $offers = $query->orderBy('created_at', 'desc')->paginate(50);
        $leagues = League::select('id', 'name')->get();

        return view('livewire.admin.market.offers-table', [
            'offers' => $offers,
            'leagues' => $leagues,
        ]);
    }
}