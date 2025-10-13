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
        $q          = trim((string)$request->get('q'));
        $position   = $request->get('position');      // tinyint
        $nationality= $request->get('nationality');   // ISO-2
        $perPage    = (int)($request->get('per_page', 20));

        // Subquery: jugadores con membresía activa (to_date IS NULL)
        $activeMembership = DB::table('player_team_history')
            ->select('player_id')
            ->whereNull('to_date');

        $players = DB::table('players')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            // disponibles = NO están en activeMembership
            ->whereNotIn('id', $activeMembership)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('full_name', 'like', "%{$q}%")
                       ->orWhere('known_as', 'like', "%{$q}%");
                });
            })
            ->when(strlen((string)$position), fn($query) => $query->where('position', (int)$position))
            ->when($nationality, fn($query) => $query->where('nationality', strtoupper($nationality)))
            ->orderBy('full_name')
            ->paginate($perPage)
            ->appends($request->query());

        return view('admin.real-teams.players.available', [
            'team'      => $realTeam,
            'players'   => $players,
            'filters'   => [
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
            'player_ids.*' => ['integer', 'distinct', Rule::exists('players', 'id')],
            'from_date'    => ['nullable', 'date_format:Y-m-d'],
            'shirt_number' => ['nullable', 'integer', 'between:1,99'],
        ], [
            'player_ids.required'   => 'Seleccioná al menos un jugador.',
            'player_ids.array'      => 'Formato inválido.',
            'player_ids.*.distinct' => 'Hay jugadores repetidos en la selección.',
            'player_ids.*.exists'   => 'Algún jugador no existe.',
            'from_date.date_format' => 'La fecha debe tener formato YYYY-MM-DD.',
        ]);

        // Regla extra: NO permitir jugadores con membresía activa (to_date NULL) en ningún equipo
        $validator->after(function ($v) use ($request) {
            $ids = collect((array) $request->input('player_ids'))
                ->filter()->unique()->values();

            if ($ids->isEmpty()) {
                return;
            }

            $countActive = DB::table('player_team_history')
                ->whereIn('player_id', $ids)
                ->whereNull('to_date')
                ->count();

            if ($countActive > 0) {
                $v->errors()->add('player_ids', 'Uno o más jugadores ya tienen una membresía activa.');
            }
        });

        $validator->validate();

        // Parse de fecha correcto
        $fromDateInput = $request->input('from_date');
        $fromDate = $fromDateInput ? Carbon::parse($fromDateInput) : Carbon::today();

        DB::transaction(function () use ($playerIds, $realTeam, $fromDate, $request) {
            foreach ($playerIds as $pid) {
                // Doble check dentro de la transacción (race condition)
                $alreadyActive = DB::table('player_team_history')
                    ->where('player_id', (int) $pid)
                    ->whereNull('to_date')
                    ->exists();

                if ($alreadyActive) {
                    // Podés optar por saltar o lanzar excepción; acá saltamos silenciosamente
                    continue;
                }

                DB::table('player_team_history')->insert([
                    'player_id'    => (int) $pid,
                    'real_team_id' => (int) $realTeam->id,
                    'from_date'    => $fromDate->toDateString(),
                    'to_date'      => null,
                    'shirt_number' => $request->input('shirt_number'), // opcional si agregás de a uno
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        });

        return redirect()->route('admin.real-teams.show', [
            'locale'   => $locale,
            'realTeam' => $realTeam->id,
        ])->with('success', 'Jugador(es) agregados al equipo correctamente.');
    }

}
