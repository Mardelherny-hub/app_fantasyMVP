<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RealPlayer;
use App\Models\RealTeam;
use App\Models\Season;
use Illuminate\Http\Request;

class RealPlayerController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = RealPlayer::with(['memberships.team', 'memberships.season']);

        // Filtro por nombre
        if ($request->filled('search')) {
            $query->where('full_name', 'like', "%{$request->search}%");
        }

        // Filtro por posición
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Filtro por nacionalidad
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        // Filtro por equipo actual
        if ($request->filled('team')) {
            if ($request->team === 'free') {
                $query->whereDoesntHave('memberships', function($q) {
                    $q->whereNull('to_date');
                });
            } else {
                $query->whereHas('memberships', function($q) use ($request) {
                    $q->where('real_team_id', $request->team)
                      ->whereNull('to_date');
                });
            }
        }

        $players = $query->orderBy('full_name')
                        ->paginate(20)
                        ->withQueryString();

        // Datos para filtros
        $positions = ['GK', 'DF', 'MF', 'FW'];
        $teams = RealTeam::orderBy('name')->get();
        $nationalities = RealPlayer::select('nationality')
                                   ->distinct()
                                   ->orderBy('nationality')
                                   ->pluck('nationality');

        return view('admin.real-players.index', compact('players', 'positions', 'teams', 'nationalities'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $positions = ['GK', 'DF', 'MF', 'FW'];
        $teams = RealTeam::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();

        return view('admin.real-players.create', compact('positions', 'teams', 'seasons'));
    }

    public function store(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'external_id' => 'nullable|integer|unique:real_players,external_id',
            'full_name' => 'required|string|max:255',
            'position' => 'required|in:GK,DF,MF,FW',
            'birthdate' => 'nullable|date',
            'nationality' => 'nullable|string|max:2',
            'photo_url' => 'nullable|url|max:500',
            'real_team_id' => 'nullable|exists:real_teams,id',
            'season_id' => 'nullable|exists:seasons,id',
            'shirt_number' => 'nullable|integer|min:1|max:99',
        ]);

        $player = RealPlayer::create([
            'external_id' => $data['external_id'] ?? null,
            'full_name' => $data['full_name'],
            'position' => $data['position'],
            'birthdate' => $data['birthdate'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'photo_url' => $data['photo_url'] ?? null,
        ]);

        // Crear membresía si se especificó equipo
        if ($request->filled('real_team_id') && $request->filled('season_id')) {
            \App\Models\RealTeamMembership::create([
                'real_team_id' => $data['real_team_id'],
                'real_player_id' => $player->id,
                'season_id' => $data['season_id'],
                'shirt_number' => $data['shirt_number'] ?? null,
                'from_date' => now(),
                'to_date' => null,
            ]);
        }

        return redirect()->route('admin.real-players.index', $locale)
            ->with('success', __('Jugador creado exitosamente'));
    }

    public function show(Request $request, string $locale, RealPlayer $realPlayer)
    {
        app()->setLocale($locale);

        $realPlayer->load([
            'memberships.team',
            'memberships.season',
            'lineups.match.fixture',
            'events.match.fixture',
            'stats.match.fixture'
        ]);

        return view('admin.real-players.show', compact('realPlayer'));
    }

    public function edit(Request $request, string $locale, RealPlayer $realPlayer)
    {
        app()->setLocale($locale);

        $positions = ['GK', 'DF', 'MF', 'FW'];
        $teams = RealTeam::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();
        
        // Membresía actual
        $currentMembership = $realPlayer->memberships()->whereNull('to_date')->first();

        return view('admin.real-players.edit', compact('realPlayer', 'positions', 'teams', 'seasons', 'currentMembership'));
    }

    public function update(Request $request, string $locale, RealPlayer $realPlayer)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'external_id' => 'nullable|integer|unique:real_players,external_id,' . $realPlayer->id,
            'full_name' => 'required|string|max:255',
            'position' => 'required|in:GK,DF,MF,FW',
            'birthdate' => 'nullable|date',
            'nationality' => 'nullable|string|max:2',
            'photo_url' => 'nullable|url|max:500',
        ]);

        $realPlayer->update($data);

        return redirect()->route('admin.real-players.index', $locale)
            ->with('success', __('Jugador actualizado exitosamente'));
    }

    public function destroy(Request $request, string $locale, RealPlayer $realPlayer)
    {
        app()->setLocale($locale);

        $realPlayer->delete();

        return redirect()->route('admin.real-players.index', $locale)
            ->with('success', __('Jugador eliminado exitosamente'));
    }

    /**
     * Show bulk create form.
     */
    public function bulkCreate(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $teams = RealTeam::orderBy('name')->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();

        return view('admin.real-players.bulk-create', compact('teams', 'seasons'));
    }

    /**
     * Process bulk CSV upload.
     */
    public function bulkStore(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'real_team_id' => 'nullable|exists:real_teams,id',
            'season_id' => 'nullable|exists:seasons,id',
        ]);

        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map('trim', array_shift($rows));

        // Validar que el header tenga las columnas requeridas
        $required = ['full_name', 'position'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                return back()->with('error', __("La columna ':col' es obligatoria en el CSV.", ['col' => $col]));
            }
        }

        $created = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2; // +2 porque header es línea 1

            // Saltar filas vacías
            if (empty(array_filter($row))) {
                continue;
            }

            // Mapear columnas
            $data = array_combine($header, array_pad($row, count($header), null));

            // Validar campos requeridos
            if (empty(trim($data['full_name'] ?? ''))) {
                $errors[] = __("Línea :line: full_name es obligatorio.", ['line' => $lineNumber]);
                continue;
            }

            $position = strtoupper(trim($data['position'] ?? ''));
            if (!in_array($position, ['GK', 'DF', 'MF', 'FW'])) {
                $errors[] = __("Línea :line: posición ':pos' no válida (usar GK, DF, MF, FW).", ['line' => $lineNumber, 'pos' => $position]);
                continue;
            }

            // Crear jugador
            $player = RealPlayer::create([
                'full_name' => trim($data['full_name']),
                'position' => $position,
                'nationality' => !empty($data['nationality']) ? strtoupper(trim($data['nationality'])) : null,
                'birthdate' => !empty($data['birthdate']) ? $data['birthdate'] : null,
                'photo_url' => !empty($data['photo_url']) ? trim($data['photo_url']) : null,
            ]);

            // Crear membresía solo si se seleccionó equipo y temporada
            if ($request->filled('real_team_id') && $request->filled('season_id')) {
                \App\Models\RealTeamMembership::create([
                    'real_team_id' => $request->real_team_id,
                    'real_player_id' => $player->id,
                    'season_id' => $request->season_id,
                    'shirt_number' => !empty($data['shirt_number']) ? (int) $data['shirt_number'] : null,
                    'from_date' => now(),
                    'to_date' => null,
                ]);
            }

            $created++;
        }

        $message = __(':count jugadores creados exitosamente.', ['count' => $created]);
        if (!empty($errors)) {
            $message .= ' ' . __(':errors errores encontrados.', ['errors' => count($errors)]);
            return redirect()->route('admin.real-players.index', $locale)
                ->with('success', $message)
                ->with('bulk_errors', $errors);
        }

        return redirect()->route('admin.real-players.index', $locale)
            ->with('success', $message);
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="players_template.csv"',
        ];

        $columns = ['full_name', 'position', 'nationality', 'birthdate', 'shirt_number', 'photo_url'];
        $example = ['John Doe', 'MF', 'CA', '1995-06-15', '10', ''];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}