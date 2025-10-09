<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSeasonRequest;
use App\Http\Requests\Admin\UpdateSeasonRequest;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    /**
     * Display a listing of seasons.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = Season::query()->orderByDesc('starts_at');

        // Filtro: búsqueda por nombre o código
        if ($q = $request->get('q')) {
            $query->where(function($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%");
            });
        }

        // Filtro: solo activas
        if ($request->get('active') === 'yes') {
            $query->where('is_active', true);
        }

        // Filtro: solo inactivas
        if ($request->get('active') === 'no') {
            $query->where('is_active', false);
        }

        $seasons = $query->paginate(15)->withQueryString();

        return view('admin.seasons.index', compact('seasons'));
    }

    /**
     * Show the form for creating a new season.
     */
    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        return view('admin.seasons.create');
    }

    /**
     * Store a newly created season.
     */
    public function store(StoreSeasonRequest $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        $season = Season::create($data);

        // Si se marca como activa, desactivar las demás
        if ($data['is_active'] ?? false) {
            $season->activate();
        }

        return redirect()->route('admin.seasons.index', $locale)
            ->with('success', __('Temporada creada correctamente.'));
    }

    /**
     * Show the form for editing the specified season.
     */
    public function edit(Request $request, string $locale, Season $season)
    {
        app()->setLocale($locale);

        return view('admin.seasons.edit', compact('season'));
    }

    /**
     * Update the specified season.
     */
    public function update(UpdateSeasonRequest $request, string $locale, Season $season)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        $season->update($data);

        // Si se marca como activa, desactivar las demás
        if ($data['is_active'] ?? false) {
            $season->activate();
        }

        return redirect()->route('admin.seasons.index', $locale)
            ->with('success', __('Temporada actualizada correctamente.'));
    }

    /**
     * Remove the specified season.
     */
    public function destroy(Request $request, string $locale, Season $season)
    {
        app()->setLocale($locale);

        // Verificar que no tenga datos relacionados
        if ($season->leagues()->exists()) {
            return back()->with('error', __('No se puede eliminar una temporada con ligas asociadas.'));
        }

        if ($season->gameweeks()->exists()) {
            return back()->with('error', __('No se puede eliminar una temporada con jornadas asociadas.'));
        }

        if ($season->matches()->exists()) {
            return back()->with('error', __('No se puede eliminar una temporada con partidos asociados.'));
        }

        $season->delete();

        return redirect()->route('admin.seasons.index', $locale)
            ->with('success', __('Temporada eliminada correctamente.'));
    }

    /**
     * Toggle season active status.
     */
    public function toggle(Request $request, string $locale, Season $season)
    {
        app()->setLocale($locale);

        if ($season->is_active) {
            // Desactivar esta temporada
            $season->update(['is_active' => false]);
            return back()->with('success', __('Temporada desactivada.'));
        } else {
            // Activar esta temporada (desactiva todas las demás automáticamente)
            $season->activate();
            return back()->with('success', __('Temporada activada.'));
        }
    }
}