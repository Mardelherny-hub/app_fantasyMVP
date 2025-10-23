<?php

namespace App\Http\Controllers\Admin\Fantasy;

use App\Http\Controllers\Controller;
use App\Models\FantasyTeam;
use App\Models\League;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamsController extends Controller
{
    /**
     * Display a listing of fantasy teams.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = FantasyTeam::query()
            ->with(['user', 'league'])
            ->orderByDesc('total_points');

        // Filtro: por nombre
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filtro: por liga
        if ($leagueId = $request->get('league_id')) {
            $query->where('league_id', $leagueId);
        }

        // Filtro: solo bots
        if ($request->get('bots') === 'yes') {
            $query->where('is_bot', true);
        }

        // Filtro: solo usuarios
        if ($request->get('bots') === 'no') {
            $query->where('is_bot', false);
        }

        // Filtro: plantilla completa
        if ($request->get('squad_complete') === 'yes') {
            $query->where('is_squad_complete', true);
        }

        // Filtro: plantilla incompleta
        if ($request->get('squad_complete') === 'no') {
            $query->where('is_squad_complete', false);
        }

        $teams = $query->paginate(20)->withQueryString();

        // Datos para filtros
        $leagues = League::orderBy('name')->get(['id', 'name']);

        // Estadísticas
        $stats = [
            'total' => FantasyTeam::count(),
            'with_league' => FantasyTeam::whereNotNull('league_id')->count(),
            'bots' => FantasyTeam::where('is_bot', true)->count(),
            'complete' => FantasyTeam::where('is_squad_complete', true)->count(),
        ];

        return view('admin.fantasy.teams.index', compact('teams', 'leagues', 'stats'));
    }

    /**
     * Display the specified fantasy team.
     */
    public function show(Request $request, string $locale, FantasyTeam $team)
    {
        app()->setLocale($locale);

        // Cargar relaciones necesarias
        $team->load([
            'user',
            'league.season',
            'rosters.player',
            'homeFixtures.awayTeam',
            'awayFixtures.homeTeam',
        ]);

        // Obtener roster actual (última gameweek)
        $currentRoster = $team->rosters()
            ->with('player')
            ->whereHas('gameweek', function($q) {
                $q->orderBy('number', 'desc');
            })
            ->get()
            ->groupBy('is_starter');

        $starters = $currentRoster->get(1, collect());
        $bench = $currentRoster->get(0, collect());

        // Estadísticas del equipo
        $stats = [
            'total_points' => $team->total_points,
            'budget' => $team->budget,
            'fixtures_played' => $team->homeFixtures()->where('status', 1)->count() + 
                                 $team->awayFixtures()->where('status', 1)->count(),
            'roster_size' => $team->rosters()->distinct('player_id')->count('player_id'),
            'squad_complete' => $team->is_squad_complete,
        ];

        return view('admin.fantasy.teams.show', compact('team', 'starters', 'bench', 'stats'));
    }

    /**
     * Show the form for editing the specified team.
     */
    public function edit(Request $request, string $locale, FantasyTeam $team)
    {
        app()->setLocale($locale);

        $leagues = League::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.fantasy.teams.edit', compact('team', 'leagues', 'users'));
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, string $locale, FantasyTeam $team)
    {
        app()->setLocale($locale);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'league_id' => 'nullable|exists:leagues,id',
            'user_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'emblem_url' => 'nullable|url|max:500',
            'is_bot' => 'boolean',
            'is_squad_complete' => 'boolean',
        ]);

        // Regenerar slug si cambió el nombre
        if ($validated['name'] !== $team->name) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Asegurar unicidad del slug
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (FantasyTeam::where('slug', $validated['slug'])
                              ->where('id', '!=', $team->id)
                              ->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $team->update($validated);

        return redirect()->route('admin.fantasy.teams.show', [$locale, $team->id])
            ->with('success', __('Equipo actualizado correctamente.'));
    }
}