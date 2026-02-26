<?php

namespace App\Livewire\Admin\Market;

use App\Models\League;
use App\Models\Season;
use App\Services\Admin\Market\MarketAnalyticsService;
use Illuminate\View\View;
use Livewire\Component;

class AnalyticsPanel extends Component
{
    public $selectedLeague = null;
    public Season $currentSeason;
    public array $topPlayers = [];
    public array $topTeams = [];
    public array $pricesByPosition = [];
    public array $transferTrends = [];
    public array $offerStats = [];

    public function mount($selectedLeague = null, Season $currentSeason): void
    {
        $this->selectedLeague = $selectedLeague ? League::find($selectedLeague->id ?? $selectedLeague) : null;
        $this->currentSeason = $currentSeason;
        $this->loadAnalytics();
    }

    public function render(): View
    {
        return view('livewire.admin.market.analytics-panel');
    }

    public function loadAnalytics(): void
    {
        $service = app(MarketAnalyticsService::class);

        $this->topPlayers = $service->getTopSoldPlayers($this->selectedLeague, 10);
        $this->topTeams = $service->getMostActiveTeams($this->selectedLeague, 10);
        $this->pricesByPosition = $service->getAveragePriceByPosition($this->currentSeason);
        $this->offerStats = $service->getOfferSuccessRate($this->selectedLeague);

        if ($this->selectedLeague) {
            $this->transferTrends = $service->getTransferTrends($this->selectedLeague, 10);
        }
    }

    public function updatedSelectedLeague($value): void
    {
        $this->selectedLeague = $value ? League::find($value) : null;
        $this->loadAnalytics();
    }
}