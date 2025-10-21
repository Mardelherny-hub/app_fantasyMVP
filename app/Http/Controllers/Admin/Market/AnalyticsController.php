<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Season;
use App\Services\Admin\Market\MarketAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $leagues = League::where('status', League::STATUS_APPROVED)
            ->with('season')
            ->orderBy('name')
            ->get();

        $selectedLeague = $request->has('league_id')
            ? League::find($request->league_id)
            : null;

        $currentSeason = Season::where('is_active', true)->first();

        return view('admin.market.analytics.index', [
            'leagues' => $leagues,
            'selectedLeague' => $selectedLeague,
            'currentSeason' => $currentSeason,
        ]);
    }
}