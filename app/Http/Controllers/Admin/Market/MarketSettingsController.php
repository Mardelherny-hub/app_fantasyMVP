<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\MarketSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketSettingsController extends Controller
{
    public function index(): View
    {
        $leagues = League::with('marketSettings')->get();
        
        return view('admin.market.settings.index', compact('leagues'));
    }

    public function update(Request $request, League $league)
    {
        $validated = $request->validate([
            'max_multiplier' => 'required|numeric|min:1|max:10',
            'trade_window_open' => 'required|boolean',
            'loan_allowed' => 'required|boolean',
            'min_offer_cooldown_h' => 'required|integer|min:1|max:48',
        ]);

        $settings = $league->marketSettings ?? new MarketSettings(['league_id' => $league->id]);
        $settings->fill($validated);
        $settings->save();

        return back()->with('success', __('Market settings updated successfully.'));
    }
}