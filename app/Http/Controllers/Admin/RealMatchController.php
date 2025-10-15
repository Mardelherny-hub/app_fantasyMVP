<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RealMatch;
use App\Models\RealCompetition;
use App\Models\Season;
use Illuminate\Http\Request;

class RealMatchController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealMatch::with(['fixture.competition', 'fixture.season', 'fixture.homeTeam', 'fixture.awayTeam']);

        // Filtro por competición
        if ($request->filled('competition')) {
            $query->whereHas('fixture', function($q) use ($request) {
                $q->where('real_competition_id', $request->competition);
            });
        }

        // Filtro por temporada
        if ($request->filled('season')) {
            $query->whereHas('fixture', function($q) use ($request) {
                $q->where('season_id', $request->season);
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por fecha desde
        if ($request->filled('date_from')) {
            $query->whereDate('started_at_utc', '>=', $request->date_from);
        }

        // Filtro por fecha hasta
        if ($request->filled('date_to')) {
            $query->whereDate('started_at_utc', '<=', $request->date_to);
        }

        // Búsqueda por equipo
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('fixture', function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->whereHas('homeTeam', function($teamQ) use ($search) {
                        $teamQ->where('name', 'like', "%{$search}%");
                    })->orWhereHas('awayTeam', function($teamQ) use ($search) {
                        $teamQ->where('name', 'like', "%{$search}%");
                    });
                });
            });
        }

        $matches = $query->orderBy('started_at_utc', 'desc')
                        ->paginate(20)
                        ->withQueryString();

        // Datos para filtros
        $competitions = RealCompetition::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();
        $statuses = ['live', 'ht', 'ft', 'finished', 'postponed', 'cancelled'];

        return view('admin.real-matches.index', compact('matches', 'competitions', 'seasons', 'statuses'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $fixtures = \App\Models\RealFixture::with(['competition', 'homeTeam', 'awayTeam'])
            ->whereDoesntHave('match')
            ->orderBy('match_date_utc', 'desc')
            ->get();
        
        $statuses = ['live', 'ht', 'ft', 'finished', 'postponed', 'cancelled'];

        return view('admin.real-matches.create', compact('fixtures', 'statuses'));
    }

    public function store(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'real_fixture_id' => 'required|exists:real_fixtures,id',
            'status' => 'required|in:live,ht,ft,finished,postponed,cancelled',
            'minute' => 'nullable|integer|min:0|max:120',
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'started_at_utc' => 'nullable|date',
            'finished_at_utc' => 'nullable|date',
        ]);

        RealMatch::create($data);

        return redirect()->route('admin.real-matches.index', $locale)
            ->with('success', __('Match creado exitosamente'));
    }

    public function show(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $realMatch->load([
            'fixture.competition',
            'fixture.season',
            'fixture.homeTeam',
            'fixture.awayTeam',
            'lineups.player',
            'lineups.team',
            'events.player',
            'events.team',
            'stats.player',
            'stats.team'
        ]);

        return view('admin.real-matches.show', compact('realMatch'));
    }

    public function edit(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $fixtures = \App\Models\RealFixture::with(['competition', 'homeTeam', 'awayTeam'])
            ->orderBy('match_date_utc', 'desc')
            ->get();
        
        $statuses = ['live', 'ht', 'ft', 'finished', 'postponed', 'cancelled'];

        return view('admin.real-matches.edit', compact('realMatch', 'fixtures', 'statuses'));
    }

    public function update(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'real_fixture_id' => 'required|exists:real_fixtures,id',
            'status' => 'required|in:live,ht,ft,finished,postponed,cancelled',
            'minute' => 'nullable|integer|min:0|max:120',
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'started_at_utc' => 'nullable|date',
            'finished_at_utc' => 'nullable|date',
        ]);

        $realMatch->update($data);

        return redirect()->route('admin.real-matches.index', $locale)
            ->with('success', __('Match actualizado exitosamente'));
    }

    public function destroy(Request $request, string $locale, RealMatch $realMatch)
    {
        app()->setLocale($locale);

        $realMatch->delete();

        return redirect()->route('admin.real-matches.index', $locale)
            ->with('success', __('Match eliminado exitosamente'));
    }
}