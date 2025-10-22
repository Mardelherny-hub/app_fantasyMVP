<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Services\Admin\Market\MarketAnalyticsService;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketDashboardController extends Controller
{
    public function __construct(
        private MarketAnalyticsService $analyticsService
    ) {}

    /**
     * Display market dashboard
     */
    public function index(Request $request): View
    {
        $leagueId = $request->input('league_id');
        $leagues = League::select('id', 'name')->get();
        
        $stats = $this->analyticsService->getMarketStats($leagueId);
        $topPlayers = $this->analyticsService->getTopPlayers($leagueId);
        $topTeams = $this->analyticsService->getTopTeams($leagueId);
        $chartData = $this->analyticsService->getTransfersByGameweek($leagueId);
        
        return view('admin.market.dashboard', compact(
            'stats',
            'topPlayers',
            'topTeams',
            'chartData',
            'leagues',
            'leagueId'
        ));
    }
}