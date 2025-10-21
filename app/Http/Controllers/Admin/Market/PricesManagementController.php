<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricesManagementController extends Controller
{
    /**
     * Mostrar página de gestión de precios
     *
     * @return View
     */
    public function index(): View
    {
        $currentSeason = Season::where('is_active', true)->first();

        if (!$currentSeason) {
            $currentSeason = Season::orderBy('starts_at', 'desc')->first();
        }

        $seasons = Season::orderBy('starts_at', 'desc')->get();

        return view('admin.market.prices.index', [
            'currentSeason' => $currentSeason,
            'seasons' => $seasons,
        ]);
    }
}