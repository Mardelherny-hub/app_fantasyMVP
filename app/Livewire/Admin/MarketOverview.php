<?php

namespace App\Livewire\Admin\Market;

use Livewire\Component;
use App\Services\Admin\Market\MarketAnalyticsService;

class MarketOverview extends Component
{
    public ?int $leagueId = null;
    public array $stats = [];
    
    protected $listeners = ['leagueChanged' => 'updateLeague'];

    public function mount(?int $leagueId = null)
    {
        $this->leagueId = $leagueId;
        $this->loadStats();
    }

    public function loadStats()
    {
        $analyticsService = app(MarketAnalyticsService::class);
        $this->stats = $analyticsService->getMarketStats($this->leagueId);
    }

    public function updateLeague(int $leagueId)
    {
        $this->leagueId = $leagueId;
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.admin.market.market-overview');
    }
}