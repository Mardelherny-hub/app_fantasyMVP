<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Services\Admin\Market\ModerationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    /**
     * Mostrar panel de moderación
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $leagues = League::with('season')
            ->where('status', League::STATUS_APPROVED)
            ->orderBy('name')
            ->get();

        $selectedLeague = null;

        if ($request->has('league_id')) {
            $selectedLeague = League::find($request->league_id);
        }

        $moderationService = app(ModerationService::class);
        $suspiciousActivity = $moderationService->getSuspiciousActivity($selectedLeague);

        return view('admin.market.moderation.index', [
            'leagues' => $leagues,
            'selectedLeague' => $selectedLeague,
            'suspiciousActivity' => $suspiciousActivity,
        ]);
    }
}