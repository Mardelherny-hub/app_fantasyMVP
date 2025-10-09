<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFootballMatchRequest;
use App\Http\Requests\Admin\UpdateFootballMatchRequest;
use App\Models\FootballMatch;
use App\Models\Season;
use App\Models\RealTeam;
use Illuminate\Http\Request;

class FootballMatchController extends Controller
{
    /**
     * Display a listing of football matches.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = FootballMatch::query()
                    ->with(['season', 'homeTeam', 'awayTeam'])
                    ->orderBy('starts_at', 'desc');

        // Filtro: por temporada
        if ($seasonId = $request->get('season_id')) {
            $query->where('season_id', $seasonId);
        }

        // Filtro: por jornada
        if ($matchday = $request->get('matchday')) {
            $query->where('matchday', $matchday);
        }

        // Filtro: por estado
        if (($status = $request->get('status')) !== null && $status !== '') {
            $query->where('status', (int)$status);
        }

        // Filtro: por equipo
        if ($teamId = $request->get('team_id')) {
            $query->where(function($q) use ($teamId) {
                $q->where('home_team_id', $teamId)
                  ->orWhere('away_team_id', $teamId);
            });
        }

        $matches = $query->paginate(20)->withQueryString();
        
        // Obtener datos para filtros
        $seasons = Season::orderByDesc('starts_at')->get();
        $teams = RealTeam::orderBy('name')->get();

        return view('admin.football-matches.index', compact('matches', 'seasons', 'teams'));
    }

    /**
     * Show the form for creating a new match.
     */
    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $seasons = Season::orderByDesc('starts_at')->get();
        $teams = RealTeam::orderBy('name')->get();
        
        // Temporada activa por defecto
        $defaultSeasonId = Season::where('is_active', true)->value('id')
            ?? Season::orderByDesc('starts_at')->value('id');

        return view('admin.football-matches.create', compact('seasons', 'teams', 'defaultSeasonId'));
    }

    /**
     * Store a newly created match.
     */
    public function store(StoreFootballMatchRequest $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        FootballMatch::create($data);

        return redirect()->route('admin.football-matches.index', $locale)
            ->with('success', __('Partido creado correctamente.'));
    }

    /**
     * Show the form for editing the specified match.
     */
    public function edit(Request $request, string $locale, FootballMatch $footballMatch)
    {
        app()->setLocale($locale);

        $seasons = Season::orderByDesc('starts_at')->get();
        $teams = RealTeam::orderBy('name')->get();

        return view('admin.football-matches.edit', compact('footballMatch', 'seasons', 'teams'));
    }

    /**
     * Update the specified match.
     */
    public function update(UpdateFootballMatchRequest $request, string $locale, FootballMatch $footballMatch)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        $footballMatch->update($data);

        return redirect()->route('admin.football-matches.index', $locale)
            ->with('success', __('Partido actualizado correctamente.'));
    }

    /**
     * Remove the specified match.
     */
    public function destroy(Request $request, string $locale, FootballMatch $footballMatch)
    {
        app()->setLocale($locale);

        // Verificar que no tenga estadísticas de jugadores
        if ($footballMatch->playerStats()->exists()) {
            return back()->with('error', __('No se puede eliminar un partido con estadísticas de jugadores asociadas.'));
        }

        $footballMatch->delete();

        return redirect()->route('admin.football-matches.index', $locale)
            ->with('success', __('Partido eliminado correctamente.'));
    }

    /**
     * Update match status (quick action).
     */
    public function updateStatus(Request $request, string $locale, FootballMatch $footballMatch)
    {
        app()->setLocale($locale);

        $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2,3'],
        ]);

        $footballMatch->update(['status' => $request->status]);

        $statusName = FootballMatch::STATUSES[$request->status] ?? 'Unknown';
        
        return back()->with('success', __('Estado actualizado a: ') . __($statusName));
    }
}