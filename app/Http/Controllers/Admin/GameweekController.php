<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGameweekRequest;
use App\Http\Requests\Admin\UpdateGameweekRequest;
use App\Models\Gameweek;
use App\Models\Season;
use Illuminate\Http\Request;

class GameweekController extends Controller
{
    /**
     * Display a listing of gameweeks.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = Gameweek::query()->with('season')->orderBy('season_id', 'desc')->orderBy('number');

        // Filtro: por temporada
        if ($seasonId = $request->get('season_id')) {
            $query->where('season_id', $seasonId);
        }

        // Filtro: solo cerradas
        if ($request->get('closed') === 'yes') {
            $query->where('is_closed', true);
        }

        // Filtro: solo abiertas
        if ($request->get('closed') === 'no') {
            $query->where('is_closed', false);
        }

        // Filtro: solo playoffs
        if ($request->get('playoff') === 'yes') {
            $query->where('is_playoff', true);
        }

        // Filtro: solo temporada regular
        if ($request->get('playoff') === 'no') {
            $query->where('is_playoff', false);
        }

        $gameweeks = $query->paginate(20)->withQueryString();
        
        // Obtener temporadas para el filtro
        $seasons = Season::orderByDesc('starts_at')->get();

        return view('admin.gameweeks.index', compact('gameweeks', 'seasons'));
    }

    /**
     * Show the form for creating a new gameweek.
     */
    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $seasons = Season::orderByDesc('starts_at')->get();
        
        // Obtener temporada activa por defecto
        $defaultSeasonId = Season::where('is_active', true)->value('id')
            ?? Season::orderByDesc('starts_at')->value('id');

        return view('admin.gameweeks.create', compact('seasons', 'defaultSeasonId'));
    }

    /**
     * Store a newly created gameweek.
     */
    public function store(StoreGameweekRequest $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        Gameweek::create($data);

        return redirect()->route('admin.gameweeks.index', $locale)
            ->with('success', __('Jornada creada correctamente.'));
    }

    /**
     * Show the form for editing the specified gameweek.
     */
    public function edit(Request $request, string $locale, Gameweek $gameweek)
    {
        app()->setLocale($locale);

        $seasons = Season::orderByDesc('starts_at')->get();

        return view('admin.gameweeks.edit', compact('gameweek', 'seasons'));
    }

    /**
     * Update the specified gameweek.
     */
    public function update(UpdateGameweekRequest $request, string $locale, Gameweek $gameweek)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        $gameweek->update($data);

        return redirect()->route('admin.gameweeks.index', $locale)
            ->with('success', __('Jornada actualizada correctamente.'));
    }

    /**
     * Remove the specified gameweek.
     */
    public function destroy(Request $request, string $locale, Gameweek $gameweek)
    {
        app()->setLocale($locale);

        // Verificar que no tenga datos relacionados
        if ($gameweek->fixtures()->exists()) {
            return back()->with('error', __('No se puede eliminar una jornada con partidos asociados.'));
        }

        if ($gameweek->rosters()->exists()) {
            return back()->with('error', __('No se puede eliminar una jornada con alineaciones asociadas.'));
        }

        $gameweek->delete();

        return redirect()->route('admin.gameweeks.index', $locale)
            ->with('success', __('Jornada eliminada correctamente.'));
    }

    /**
     * Toggle gameweek closed status.
     */
    public function toggle(Request $request, string $locale, Gameweek $gameweek)
    {
        app()->setLocale($locale);

        if ($gameweek->is_closed) {
            $gameweek->open();
            return back()->with('success', __('Jornada abierta. Las alineaciones y transferencias están habilitadas.'));
        } else {
            $gameweek->close();
            return back()->with('success', __('Jornada cerrada. Las alineaciones y transferencias están bloqueadas.'));
        }
    }
}