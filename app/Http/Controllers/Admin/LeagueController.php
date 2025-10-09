<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeagueRequest;
use App\Http\Requests\Admin\UpdateLeagueRequest;
use App\Models\League;
use App\Models\User;
use App\Models\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeagueController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = League::query()->with(['owner'])->orderByDesc('id');

        if ($q = $request->get('q')) {
            $query->where(function($x) use ($q) {
                $x->where('name','like',"%{$q}%")
                  ->orWhere('code','like',"%{$q}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', (int) $type);
        }

        if (($locked = $request->get('locked')) !== null && $locked !== '') {
            $query->where('is_locked', (bool) $locked);
        }

        if ($lg = $request->get('locale')) {
            $query->where('locale', $lg);
        }

        $leagues = $query->paginate(15)->withQueryString();

        return view('admin.leagues.index', compact('leagues'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $owners = \App\Models\User::role('manager')->orderBy('name')->get(['id','name','email']);
        if ($owners->isEmpty()) {
            $owners = \App\Models\User::orderBy('name')->get(['id','name','email']);
        }

        $seasons = Season::orderByDesc('starts_at')->get(['id','name','code','starts_at','ends_at','is_active']);
        $defaultSeasonId = Season::where('is_active', 1)->value('id')
            ?? Season::orderByDesc('starts_at')->value('id');

        return view('admin.leagues.create', compact('owners','seasons','defaultSeasonId'));
    }

    // STORE: si no viene season_id, tomar la activa
    public function store(\App\Http\Requests\Admin\StoreLeagueRequest $request, string $locale)
    {
        app()->setLocale($locale);
        $data = $request->validated();

        if (empty($data['code'])) {
            do { $code = Str::upper(Str::random(6)); }
            while (\App\Models\League::where('code', $code)->exists());
            $data['code'] = $code;
        }

        unset($data['is_locked']);

        if (empty($data['season_id'])) {
            $data['season_id'] = Season::where('is_active', 1)->value('id')
                ?? Season::orderByDesc('starts_at')->value('id');
        }

        \App\Models\League::create($data);

        return redirect()->route('admin.leagues.index', $locale)
            ->with('success', __('Liga creada correctamente.'));
    }

    // EDIT: pasar también $seasons y $defaultSeasonId
    public function edit(Request $request, string $locale, League $league)
    {
        // Validar locale permitido (según tus idiomas activos)
        if (!in_array($locale, ['es','en','fr'])) {
            abort(404, 'Idioma no válido');
        }

        // Forzar idioma de la app
        app()->setLocale($locale);

        // 🔒 Validación de acceso: solo admin
        if (!$request->user()->hasRole('admin')) {
            abort(403, __('No tienes permiso para editar ligas.'));
        }

        // Cargar owners (managers o todos si no hay managers)
        $owners = User::role('manager')->orderBy('name')->get(['id','name','email']);
        if ($owners->isEmpty()) {
            $owners = User::orderBy('name')->get(['id','name','email']);
        }

        // Cargar seasons y definir por defecto la activa
        $seasons = Season::orderByDesc('starts_at')->get(['id','name','code','is_active']);
        $defaultSeasonId = Season::where('is_active', 1)->value('id')
            ?? Season::orderByDesc('starts_at')->value('id');

        // Validar que la liga exista y tenga una season asociada válida
        if (!$league) {
            abort(404, __('Liga no encontrada.'));
        }
        if (!$league->season && !$defaultSeasonId) {
            return back()->with('error', __('No hay ninguna temporada válida para asociar.'));
        }

        return view('admin.leagues.edit', compact('league', 'owners', 'seasons', 'defaultSeasonId'));
    }

    // UPDATE: mantener existente o aplicar fallback si viene vacío
    public function update(\App\Http\Requests\Admin\UpdateLeagueRequest $request, string $locale, \App\Models\League $league)
    {
        //dd($request->all());
        app()->setLocale($locale);
        $data = $request->validated();

        if (empty($data['code'])) {
            $data['code'] = $league->code ?: (function(){
                do { $c = Str::upper(Str::random(6)); }
                while (\App\Models\League::where('code', $c)->exists());
                return $c;
            })();
        }

        unset($data['is_locked']);

        if (empty($data['season_id'])) {
            // si no se envía, mantener la actual del registro; si no hay, fallback a activa
            $data['season_id'] = $league->season_id ?: (
                Season::where('is_active', 1)->value('id')
                ?? Season::orderByDesc('starts_at')->value('id')
            );
        }

        $league->update($data);

        return redirect()->route('admin.leagues.index', $locale)
            ->with('success', __('Liga actualizada correctamente.'));
    }


    public function destroy(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        if ($league->is_locked) {
            return back()->with('error', __('No se puede eliminar una liga con inscripciones cerradas.'));
        }

        $league->delete();

        return redirect()
            ->route('admin.leagues.index', $locale)
            ->with('success', __('Liga eliminada.'));
    }




}
