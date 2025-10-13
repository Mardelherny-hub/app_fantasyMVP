<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRealTeamRequest;
use App\Http\Requests\Admin\UpdateRealTeamRequest;
use App\Models\RealTeam;
use Illuminate\Http\Request;

class RealTeamController extends Controller
{
    /**
     * Display a listing of real teams.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealTeam::query()->orderBy('name');

        // Filtro: búsqueda por nombre
        if ($q = $request->get('q')) {
            $query->where(function($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('short_name', 'like', "%{$q}%");
            });
        }

        // Filtro: por país
        if ($country = $request->get('country')) {
            $query->where('country', $country);
        }

        // Incluir eliminados (soft deleted)
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $teams = $query->paginate(15)->withQueryString();

        // Obtener lista de países únicos para el filtro
        $countries = RealTeam::select('country')
                              ->distinct()
                              ->orderBy('country')
                              ->pluck('country');

        return view('admin.real-teams.index', compact('teams', 'countries'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        return view('admin.real-teams.create');
    }

    /**
     * Display the specified team with its current players.
     */
    public function show(Request $request, string $locale, RealTeam $realTeam)
    {
        app()->setLocale($locale);

        // Cargar jugadores actuales del equipo (to_date = null)
        $realTeam->load([
            'playerHistory' => function($query) {
                $query->current()
                    ->with(['player.valuations' => function($q) {
                        $q->latest('updated_at')->limit(1);
                    }])
                    ->orderBy('shirt_number');
            }
        ]);

        // Organizar jugadores por posición
        $playersByPosition = $realTeam->playerHistory->groupBy(function($history) {
            return $history->player->position;
        });

        return view('admin.real-teams.show', compact('realTeam', 'playersByPosition'));
    }

    /**
     * Store a newly created team.
     */
    public function store(StoreRealTeamRequest $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        RealTeam::create($data);

        return redirect()->route('admin.real-teams.index', $locale)
            ->with('success', __('Equipo creado correctamente.'));
    }

    /**
     * Show the form for editing the specified team.
     */
    public function edit(Request $request, string $locale, RealTeam $realTeam)
    {
        app()->setLocale($locale);

        return view('admin.real-teams.edit', compact('realTeam'));
    }

    /**
     * Update the specified team.
     */
    public function update(UpdateRealTeamRequest $request, string $locale, RealTeam $realTeam)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        $realTeam->update($data);

        return redirect()->route('admin.real-teams.index', $locale)
            ->with('success', __('Equipo actualizado correctamente.'));
    }

    /**
     * Remove the specified team (soft delete).
     */
    public function destroy(Request $request, string $locale, RealTeam $realTeam)
    {
        app()->setLocale($locale);

        // Verificar que no tenga jugadores asignados actualmente
        if ($realTeam->playerHistory()->whereNull('to_date')->exists()) {
            return back()->with('error', __('No se puede eliminar un equipo con jugadores asignados actualmente.'));
        }

        // Verificar que no tenga partidos asociados
        if ($realTeam->homeMatches()->exists() || $realTeam->awayMatches()->exists()) {
            return back()->with('error', __('No se puede eliminar un equipo con partidos asociados.'));
        }

        $realTeam->delete();

        return redirect()->route('admin.real-teams.index', $locale)
            ->with('success', __('Equipo eliminado correctamente.'));
    }

    /**
     * Restore a soft deleted team.
     */
    public function restore(Request $request, string $locale, int $id)
    {
        app()->setLocale($locale);

        $team = RealTeam::onlyTrashed()->findOrFail($id);
        $team->restore();

        return back()->with('success', __('Equipo restaurado correctamente.'));
    }
}