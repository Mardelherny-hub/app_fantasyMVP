<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlayerRequest;
use App\Http\Requests\Admin\UpdatePlayerRequest;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Display a listing of players.
     */
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = Player::query()->orderBy('full_name');

        // Filtro: búsqueda por nombre
        if ($q = $request->get('q')) {
            $query->where(function($x) use ($q) {
                $x->where('full_name', 'like', "%{$q}%")
                  ->orWhere('known_as', 'like', "%{$q}%");
            });
        }

        // Filtro: por posición
        if (($position = $request->get('position')) !== null && $position !== '') {
            $query->where('position', (int)$position);
        }

        // Filtro: por nacionalidad
        if ($nationality = $request->get('nationality')) {
            $query->where('nationality', $nationality);
        }

        // Filtro: solo activos
        if ($request->get('active') === 'yes') {
            $query->where('is_active', true);
        }

        // Filtro: solo inactivos
        if ($request->get('active') === 'no') {
            $query->where('is_active', false);
        }

        // Incluir eliminados (soft deleted)
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        $players = $query->paginate(20)->withQueryString();

        // Obtener nacionalidades únicas para el filtro
        $nationalities = Player::select('nationality')
                                ->distinct()
                                ->whereNotNull('nationality')
                                ->orderBy('nationality')
                                ->pluck('nationality');

        return view('admin.players.index', compact('players', 'nationalities'));
    }

    /**
     * Show the form for creating a new player.
     */
    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        return view('admin.players.create');
    }

    /**
     * Store a newly created player.
     */
    public function store(StorePlayerRequest $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        Player::create($data);

        return redirect()->route('admin.players.index', $locale)
            ->with('success', __('Jugador creado correctamente.'));
    }

    /**
     * Show the form for editing the specified player.
     */
    public function edit(Request $request, string $locale, Player $player)
    {
        app()->setLocale($locale);

        return view('admin.players.edit', compact('player'));
    }

    /**
     * Update the specified player.
     */
    public function update(UpdatePlayerRequest $request, string $locale, Player $player)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();

        $player->update($data);

        return redirect()->route('admin.players.index', $locale)
            ->with('success', __('Jugador actualizado correctamente.'));
    }

    /**
     * Remove the specified player (soft delete).
     */
    public function destroy(Request $request, string $locale, Player $player)
    {
        app()->setLocale($locale);

        // Verificar que no tenga estadísticas
        if ($player->matchStats()->exists()) {
            return back()->with('error', __('No se puede eliminar un jugador con estadísticas de partidos.'));
        }

        $player->delete();

        return redirect()->route('admin.players.index', $locale)
            ->with('success', __('Jugador eliminado correctamente.'));
    }

    /**
     * Toggle player active status.
     */
    public function toggle(Request $request, string $locale, Player $player)
    {
        app()->setLocale($locale);

        $player->update(['is_active' => !$player->is_active]);

        $status = $player->is_active ? __('activado') : __('desactivado');
        
        return back()->with('success', __('Jugador ') . $status . '.');
    }

    /**
     * Restore a soft deleted player.
     */
    public function restore(Request $request, string $locale, int $id)
    {
        app()->setLocale($locale);

        $player = Player::onlyTrashed()->findOrFail($id);
        $player->restore();

        return back()->with('success', __('Jugador restaurado correctamente.'));
    }
}