<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RealCompetition;
use Illuminate\Http\Request;

class RealCompetitionController extends Controller
{
    /**
     * Display a listing of real competitions.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealCompetition::query()->orderBy('name');

        // Filtro: búsqueda por nombre
        if ($q = $request->get('q')) {
            $query->where('name', 'like', "%{$q}%");
        }

        // Filtro: por país
        if ($country = $request->get('country')) {
            $query->where('country', $country);
        }

        // Filtro: por tipo
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Filtro: por estado (activo/inactivo)
        if ($request->has('active')) {
            $active = $request->get('active');
            if ($active === '1' || $active === 'yes') {
                $query->where('active', true);
            } elseif ($active === '0' || $active === 'no') {
                $query->where('active', false);
            }
        }

        // Filtro: por fuente externa
        if ($source = $request->get('source')) {
            $query->where('external_source', $source);
        }

        $competitions = $query->paginate(15)->withQueryString();

        // Obtener lista de países únicos para el filtro
        $countries = RealCompetition::select('country')
                                    ->distinct()
                                    ->whereNotNull('country')
                                    ->orderBy('country')
                                    ->pluck('country');

        // Obtener fuentes únicas
        $sources = RealCompetition::select('external_source')
                                  ->distinct()
                                  ->orderBy('external_source')
                                  ->pluck('external_source');

        return view('admin.real-competitions.index', compact('competitions', 'countries', 'sources'));
    }

    /**
     * Show the form for creating a new competition.
     */
    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        return view('admin.real-competitions.create');
    }

    /**
     * Store a newly created competition.
     */
    public function store(Request $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validate([
            'external_id' => ['required', 'integer', 'unique:real_competitions,external_id'],
            'name' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:league,cup'],
            'active' => ['boolean'],
            'external_source' => ['required', 'string', 'max:50'],
        ]);

        // Establecer valor por defecto para active si no viene
        $data['active'] = $data['active'] ?? true;

        RealCompetition::create($data);

        return redirect()->route('admin.real-competitions.index', $locale)
            ->with('success', __('Competición creada correctamente.'));
    }

    /**
     * Display the specified competition.
     */
    public function show(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        // Cargar fixtures con sus relaciones
        $realCompetition->load([
            'fixtures' => function($query) {
                $query->with(['homeTeam', 'awayTeam', 'match'])
                      ->orderBy('match_date_utc', 'desc')
                      ->limit(20);
            }
        ]);

        // Obtener estadísticas
        $stats = [
            'total_fixtures' => $realCompetition->fixtures()->count(),
            'played_matches' => $realCompetition->fixtures()->has('match')->count(),
            'scheduled_fixtures' => $realCompetition->fixtures()->scheduled()->count(),
            'teams_count' => $realCompetition->teamSeasons()->distinct('real_team_id')->count(),
        ];

        return view('admin.real-competitions.show', compact('realCompetition', 'stats'));
    }

    /**
     * Show the form for editing the specified competition.
     */
    public function edit(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        return view('admin.real-competitions.edit', compact('realCompetition'));
    }

    /**
     * Update the specified competition.
     */
    public function update(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);
        
        $data = $request->validate([
            'external_id' => ['required', 'integer', 'unique:real_competitions,external_id,' . $realCompetition->id],
            'name' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:league,cup'],
            'active' => ['boolean'],
            'external_source' => ['required', 'string', 'max:50'],
        ]);

        $realCompetition->update($data);

        return redirect()->route('admin.real-competitions.index', $locale)
            ->with('success', __('Competición actualizada correctamente.'));
    }

    /**
     * Remove the specified competition.
     */
    public function destroy(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        // Verificar que no tenga fixtures asociados
        if ($realCompetition->fixtures()->exists()) {
            return back()->with('error', __('No se puede eliminar una competición con fixtures asociados.'));
        }

        // Verificar que no tenga equipos asociados
        if ($realCompetition->teamSeasons()->exists()) {
            return back()->with('error', __('No se puede eliminar una competición con equipos asociados.'));
        }

        $realCompetition->delete();

        return redirect()->route('admin.real-competitions.index', $locale)
            ->with('success', __('Competición eliminada correctamente.'));
    }

    /**
     * Toggle active status of the competition.
     */
    public function toggle(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        $realCompetition->update([
            'active' => !$realCompetition->active
        ]);

        $status = $realCompetition->active ? __('activada') : __('desactivada');

        return back()->with('success', __('Competición') . ' ' . $status . ' ' . __('correctamente.'));
    }
}