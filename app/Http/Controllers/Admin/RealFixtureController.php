<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RealFixture;
use App\Models\RealCompetition;
use App\Models\Season;
use Illuminate\Http\Request;

class RealFixtureController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealFixture::with(['competition', 'season', 'homeTeam', 'awayTeam', 'match']);

        // Filtro por competición
        if ($request->filled('competition')) {
            $query->where('real_competition_id', $request->competition);
        }

        // Filtro por temporada
        if ($request->filled('season')) {
            $query->where('season_id', $request->season);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por ronda
        if ($request->filled('round')) {
            $query->where('round', $request->round);
        }

        // Filtro por fecha desde
        if ($request->filled('date_from')) {
            $query->whereDate('match_date_utc', '>=', $request->date_from);
        }

        // Filtro por fecha hasta
        if ($request->filled('date_to')) {
            $query->whereDate('match_date_utc', '<=', $request->date_to);
        }

        // Búsqueda por equipo (home o away)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('homeTeam', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })->orWhereHas('awayTeam', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        $fixtures = $query->orderBy('match_date_utc', 'desc')
                         ->orderBy('match_time_utc', 'desc')
                         ->paginate(20)
                         ->withQueryString();

        // Datos para filtros
        $competitions = RealCompetition::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();
        $statuses = ['scheduled', 'postponed', 'cancelled'];

        return view('admin.real-fixtures.index', compact('fixtures', 'competitions', 'seasons', 'statuses'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $competitions = RealCompetition::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();
        $teams = \App\Models\RealTeam::orderBy('name')->get();
        $statuses = ['scheduled', 'postponed', 'cancelled'];

        return view('admin.real-fixtures.create', compact('competitions', 'seasons', 'teams', 'statuses'));
    }

    public function store(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'real_competition_id' => 'required|exists:real_competitions,id',
            'season_id' => 'required|exists:seasons,id',
            'home_team_id' => 'required|exists:real_teams,id',
            'away_team_id' => 'required|exists:real_teams,id|different:home_team_id',
            'round' => 'nullable|string|max:50',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,postponed,cancelled',
            'match_date_utc' => 'required|date',
            'match_time_utc' => 'nullable|date_format:H:i',
        ]);

        // Combinar fecha y hora si existe
        if ($request->filled('match_time_utc')) {
            $data['match_time_utc'] = $data['match_date_utc'] . ' ' . $data['match_time_utc'];
        }

        RealFixture::create($data);

        return redirect()->route('admin.real-fixtures.index', $locale)
            ->with('success', __('Fixture creado exitosamente'));
    }

    public function show(Request $request, string $locale, RealFixture $realFixture)
    {
        app()->setLocale($locale);

        $realFixture->load(['competition', 'season', 'homeTeam', 'awayTeam', 'match']);

        return view('admin.real-fixtures.show', compact('realFixture'));
    }

    public function edit(Request $request, string $locale, RealFixture $realFixture)
    {
        app()->setLocale($locale);

        $competitions = RealCompetition::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();
        $teams = \App\Models\RealTeam::orderBy('name')->get();
        $statuses = ['scheduled', 'postponed', 'cancelled'];

        return view('admin.real-fixtures.edit', compact('realFixture', 'competitions', 'seasons', 'teams', 'statuses'));
    }

    public function update(Request $request, string $locale, RealFixture $realFixture)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'external_id' => 'nullable|integer',
            'real_competition_id' => 'required|exists:real_competitions,id',
            'season_id' => 'required|exists:seasons,id',
            'home_team_id' => 'required|exists:real_teams,id',
            'away_team_id' => 'required|exists:real_teams,id|different:home_team_id',
            'round' => 'nullable|string|max:50',
            'venue' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,postponed,cancelled',
            'match_date_utc' => 'required|date',
            'match_time_utc' => 'nullable|date_format:H:i',
        ]);

        // Combinar fecha y hora si existe
        if ($request->filled('match_time_utc')) {
            $data['match_time_utc'] = $data['match_date_utc'] . ' ' . $data['match_time_utc'];
        }

        $realFixture->update($data);

        return redirect()->route('admin.real-fixtures.index', $locale)
            ->with('success', __('Fixture actualizado exitosamente'));
    }

    public function destroy(Request $request, string $locale, RealFixture $realFixture)
    {
        app()->setLocale($locale);

        $realFixture->delete();

        return redirect()->route('admin.real-fixtures.index', $locale)
            ->with('success', __('Fixture eliminado exitosamente'));
    }
    
}