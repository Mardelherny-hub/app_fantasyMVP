<?php

namespace App\Http\Controllers\Admin\CPL;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CPL\StoreRealMatchRequest;
use App\Http\Requests\Admin\CPL\UpdateRealMatchRequest;
use App\Models\RealMatch;
use App\Models\RealFixture;
use Illuminate\Http\Request;

class RealMatchController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealMatch::with(['fixture.homeTeam', 'fixture.awayTeam', 'fixture.season']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($seasonId = $request->get('season_id')) {
            $query->whereHas('fixture', fn($q) => $q->where('season_id', $seasonId));
        }

        $matches = $query->orderBy('started_at_utc', 'desc')->paginate(20);

        return view('admin.cpl.matches.index', compact('matches'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $fixtures = RealFixture::with(['homeTeam', 'awayTeam', 'season'])
            ->doesntHave('match')
            ->orderBy('match_date_utc', 'desc')
            ->get();

        return view('admin.cpl.matches.create', compact('fixtures'));
    }

    public function store(StoreRealMatchRequest $request, string $locale)
    {
        app()->setLocale($locale);

        RealMatch::create($request->validated());

        return redirect()
            ->route('admin.cpl.matches.index', $locale)
            ->with('success', __('Partido creado exitosamente'));
    }

    public function edit(Request $request, string $locale, RealMatch $match)
    {
        app()->setLocale($locale);

        $match->load('fixture.homeTeam', 'fixture.awayTeam');

        $fixtures = RealFixture::with(['homeTeam', 'awayTeam', 'season'])
            ->orderBy('match_date_utc', 'desc')
            ->get();

        return view('admin.cpl.matches.edit', compact('match', 'fixtures'));
    }

    public function update(UpdateRealMatchRequest $request, string $locale, RealMatch $match)
    {
        app()->setLocale($locale);

        $match->update($request->validated());

        return redirect()
            ->route('admin.cpl.matches.index', $locale)
            ->with('success', __('Partido actualizado exitosamente'));
    }

    public function destroy(Request $request, string $locale, RealMatch $match)
    {
        app()->setLocale($locale);

        $match->delete();

        return redirect()
            ->route('admin.cpl.matches.index', $locale)
            ->with('success', __('Partido eliminado exitosamente'));
    }
}