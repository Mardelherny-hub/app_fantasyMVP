<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\RealTeam; 

class RealTeamPlayerController extends Controller
{
    /**
     * Listado de jugadores disponibles (sin membresía activa en ningún equipo).
     * Filtros: q (nombre), position, nationality.
     */
    public function index(Request $request, string $locale, RealTeam $realTeam)
    {
        $q           = trim((string)$request->get('q'));
        $position    = $request->get('position');
        $nationality = $request->get('nationality');
        $perPage     = (int)($request->get('per_page', 20));

        // Subquery: jugadores con membresía activa (to_date IS NULL)
        $activeMembership = DB::table('real_team_memberships')
            ->select('real_player_id')
            ->whereNull('to_date');

        $players = DB::table('real_players')
            ->whereNotIn('id', $activeMembership)
            ->when($q, function ($query) use ($q) {
                $query->where('full_name', 'like', "%{$q}%");
            })
            ->when($position, fn($query) => $query->where('position', $position))
            ->when($nationality, fn($query) => $query->where('nationality', strtoupper($nationality)))
            ->orderBy('full_name')
            ->paginate($perPage)
            ->appends($request->query());

        return view('admin.real-teams.players.available', [
            'team'    => $realTeam,
            'players' => $players,
            'filters' => [
                'q' => $q,
                'position' => $position,
                'nationality' => $nationality,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Adjunta jugador/es al equipo generando membresía activa (to_date NULL).
     * Acepta player_id (int) o player_ids (array de ints).
     */
    public function store(Request $request, string $locale, \App\Models\RealTeam $realTeam)
    {
        // Normalizar: admitir player_id o player_ids[]
        $playerIds = $request->input('player_ids');
        if (!$playerIds && $request->filled('player_id')) {
            $playerIds = [(int) $request->input('player_id')];
        }
        $request->merge(['player_ids' => $playerIds]);

        // Validación base
        $validator = Validator::make($request->all(), [
            'player_ids'   => ['required', 'array', 'min:1'],
            'player_ids.*' => ['integer', 'distinct', Rule::exists('real_players', 'id')],
            'from_date'    => ['nullable', 'date_format:Y-m-d'],
        ], [
            'player_ids.required'   => 'Seleccioná al menos un jugador.',
            'player_ids.array'      => 'Formato inválido.',
            'player_ids.*.distinct' => 'Hay jugadores repetidos en la selección.',
            'player_ids.*.exists'   => 'Algún jugador no existe.',
            'from_date.date_format' => 'La fecha debe tener formato YYYY-MM-DD.',
        ]);

        // Regla extra: NO permitir jugadores con membresía activa
        $validator->after(function ($v) use ($request) {
            $ids = collect((array) $request->input('player_ids'))
                ->filter()->unique()->values();

            if ($ids->isEmpty()) {
                return;
            }

            $countActive = DB::table('real_team_memberships')
                ->whereIn('real_player_id', $ids)
                ->whereNull('to_date')
                ->count();

            if ($countActive > 0) {
                $v->errors()->add('player_ids', 'Uno o más jugadores ya tienen una membresía activa.');
            }
        });

        $validator->validate();

        $fromDateInput = $request->input('from_date');
        $fromDate = $fromDateInput ? Carbon::parse($fromDateInput) : Carbon::today();

        // Obtener temporada activa
        $activeSeason = DB::table('seasons')->where('is_active', true)->first();

        DB::transaction(function () use ($playerIds, $realTeam, $fromDate, $activeSeason) {
            foreach ($playerIds as $pid) {
                $alreadyActive = DB::table('real_team_memberships')
                    ->where('real_player_id', (int) $pid)
                    ->whereNull('to_date')
                    ->exists();

                if ($alreadyActive) {
                    continue;
                }

                DB::table('real_team_memberships')->insert([
                    'real_player_id' => (int) $pid,
                    'real_team_id'   => (int) $realTeam->id,
                    'season_id'      => $activeSeason ? $activeSeason->id : null,
                    'from_date'      => $fromDate->toDateString(),
                    'to_date'        => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        });

        return redirect()->route('admin.real-teams.show', [
            'locale'   => $locale,
            'realTeam' => $realTeam->id,
        ])->with('success', 'Jugador(es) agregados al equipo correctamente.');
    }

}
