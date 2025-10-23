<?php

namespace App\Http\Controllers\Admin\Fantasy;

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

   /**
     * Display the specified league details.
     */
    public function show(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        // Cargar relaciones necesarias
        $league->load([
            'owner',
            'season',
            'fantasyTeams.user',
            'fixtures',
            'standings'
        ]);

        // Estadísticas básicas
        $stats = [
            'total_teams' => $league->fantasyTeams()->count(),
            'bot_teams' => $league->fantasyTeams()->where('is_bot', true)->count(),
            'user_teams' => $league->fantasyTeams()->where('is_bot', false)->count(),
            'total_fixtures' => $league->fixtures()->count(),
            'finished_fixtures' => $league->fixtures()->where('status', 1)->count(),
            'available_slots' => $league->max_participants - $league->fantasyTeams()->count(),
        ];

        // Equipos disponibles (sin liga asignada o de esta liga)
        $availableTeams = \App\Models\FantasyTeam::whereNull('league_id')
            ->orWhere('league_id', $league->id)
            ->with('user')
            ->orderBy('name')
            ->get();

        return view('admin.leagues.show', compact('league', 'stats', 'availableTeams'));
    }

    public function create(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $seasons = Season::orderByDesc('starts_at')->get(['id','name','code','starts_at','ends_at','is_active']);
        $defaultSeasonId = Season::where('is_active', 1)->value('id')
            ?? Season::orderByDesc('starts_at')->value('id');

        return view('admin.leagues.create', compact('seasons','defaultSeasonId'));
    }

    public function store(\App\Http\Requests\Admin\StoreLeagueRequest $request, string $locale)
    {
        app()->setLocale($locale);
        
        $data = $request->validated();
        
        // Generar código si no viene
        if (empty($data['code'])) {
            do {
                $code = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));
            } while (League::where('code', $code)->exists());
            $data['code'] = $code;
        }
        
        // Asignar owner_user_id si no viene (admin actual)
        if (empty($data['owner_user_id'])) {
            $data['owner_user_id'] = auth()->id();
        }
        
        // Establecer status APPROVED por defecto cuando admin crea liga
        if (!isset($data['status'])) {
            $data['status'] = League::STATUS_APPROVED; // 1
        }
        
        // Si no viene season_id, usar la activa
        if (empty($data['season_id'])) {
            $data['season_id'] = Season::where('is_active', 1)->value('id')
                ?? Season::orderByDesc('starts_at')->value('id');
        }
        
        // Crear la liga
        $league = League::create($data);
        
        return redirect()->route('admin.fantasy.leagues.index', $locale)
            ->with('success', __('Liga creada correctamente.'));
    }

    // EDIT: pasar también $seasons y $defaultSeasonId
    public function edit(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        // Cargar owners (managers o todos si no hay managers)
        $owners = User::role('manager')->orderBy('name')->get(['id','name','email']);
        if ($owners->isEmpty()) {
            $owners = User::orderBy('name')->get(['id','name','email']);
        }

        // Cargar seasons y definir por defecto la activa
        $seasons = Season::orderByDesc('starts_at')->get(['id','name','code','is_active']);
        $defaultSeasonId = Season::where('is_active', 1)->value('id')
                ?? Season::orderByDesc('starts_at')->value('id');

        // Pasar como 'lg' para que el formulario lo reconozca
        $lg = $league;

        return view('admin.leagues.edit', compact('lg', 'league', 'owners', 'seasons', 'defaultSeasonId'));
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

    /**
     * Remove the specified league.
     */
    public function destroy(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        // Validar que no tenga fantasy teams asociados
        if ($league->fantasyTeams()->exists()) {
            return back()->with('error', __('No se puede eliminar una liga con equipos fantasy asociados.'));
        }

        // Validar que no tenga fixtures
        if ($league->fixtures()->exists()) {
            return back()->with('error', __('No se puede eliminar una liga con partidos asociados.'));
        }

        // Validar que no tenga standings
        if ($league->standings()->exists()) {
            return back()->with('error', __('No se puede eliminar una liga con tabla de posiciones.'));
        }

        $league->delete();

        return redirect()->route('admin.leagues.index', $locale)
            ->with('success', __('Liga eliminada correctamente.'));
    }

    /**
     * Toggle lock/unlock league (cerrar/abrir inscripciones).
     */
    public function toggleLock(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        $league->is_locked = !$league->is_locked;
        $league->save();

        $message = $league->is_locked 
            ? __('Liga cerrada. No se pueden agregar más participantes.')
            : __('Liga abierta. Se pueden agregar participantes.');

        return back()->with('success', $message);
    }


    /**
     * Fill league with bot teams.
     */
    public function fillWithBots(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        $service = new \App\Services\Admin\LeagueService;

        // Validar que se puede llenar con bots
        if (!$service->canFillWithBots($league)) {
            return back()->with('error', __('Esta liga no permite auto-completar con bots o ya está llena.'));
        }

        // Crear bots
        $botsCreated = $service->fillWithBots($league);

        if ($botsCreated === 0) {
            return back()->with('error', __('No hay cupos disponibles para agregar bots.'));
        }

        return back()->with('success', __(':count equipos bot creados correctamente.', ['count' => $botsCreated]));
    }

   /**
     * Add fantasy team to league.
     */
    public function addTeam(Request $request, string $locale, League $league)
    {
        app()->setLocale($locale);

        $request->validate([
            'fantasy_team_id' => ['required', 'exists:fantasy_teams,id'],
        ]);

        $team = \App\Models\FantasyTeam::findOrFail($request->fantasy_team_id);

        // Validar que el equipo esté disponible
        if ($team->league_id && $team->league_id !== $league->id) {
            return back()->with('error', __('Este equipo ya está en otra liga.'));
        }

        // Validar que la liga no esté llena
        if ($league->fantasyTeams()->count() >= $league->max_participants) {
            return back()->with('error', __('La liga está completa. No hay cupos disponibles.'));
        }

        // Asignar equipo a la liga
        $team->league_id = $league->id;
        $team->save();

        return back()->with('success', __('Equipo agregado a la liga correctamente.'));
    }

    /**
     * Remove fantasy team from league.
     */
    public function removeTeam(Request $request, string $locale, League $league, $teamId)
    {
        app()->setLocale($locale);

        $team = $league->fantasyTeams()->findOrFail($teamId);

        // No permitir remover si es bot
        if ($team->is_bot) {
            return back()->with('error', __('No se puede remover un equipo bot. Use "Completar con bots" para ajustar.'));
        }

        // Validar que no tenga fixtures jugados
        $hasPlayedFixtures = $league->fixtures()
            ->where(function($q) use ($team) {
                $q->where('home_fantasy_team_id', $team->id)
                  ->orWhere('away_fantasy_team_id', $team->id);
            })
            ->where('status', 1) // finished
            ->exists();

        if ($hasPlayedFixtures) {
            return back()->with('error', __('No se puede remover un equipo con partidos jugados.'));
        }

        // Liberar el equipo (quitar de la liga)
        $team->league_id = null;
        $team->total_points = 0; // Reset puntos
        $team->budget = 100.00; // Reset presupuesto
        $team->save();

        return back()->with('success', __('Equipo removido de la liga correctamente.'));
    }

    /**
     * Update member role.
     */
    public function updateMemberRole(Request $request, string $locale, League $league, $memberId)
    {
        app()->setLocale($locale);

        $request->validate([
            'role' => ['required', 'integer', 'in:1,2,3'],
        ]);

        $member = $league->members()->findOrFail($memberId);

        // No permitir cambiar rol del owner
        if ($member->user_id === $league->owner_user_id) {
            return back()->with('error', __('No se puede cambiar el rol del propietario de la liga.'));
        }

        $member->update(['role' => $request->role]);

        return back()->with('success', __('Rol actualizado correctamente.'));
    }

    /**
     * Remove member from league.
     */
    public function removeMember(Request $request, string $locale, League $league, $memberId)
    {
        app()->setLocale($locale);

        $member = $league->members()->findOrFail($memberId);

        // No permitir remover al owner
        if ($member->user_id === $league->owner_user_id) {
            return back()->with('error', __('No se puede remover al propietario de la liga.'));
        }

        // Validar que no tenga fantasy team activo
        if ($league->fantasyTeams()->where('user_id', $member->user_id)->exists()) {
            return back()->with('error', __('No se puede remover un miembro con equipo fantasy activo.'));
        }

        $member->delete();

        return back()->with('success', __('Miembro removido correctamente.'));
    }

    /**
     * Toggle member active status.
     */
    public function toggleMember(Request $request, string $locale, League $league, $memberId)
    {
        app()->setLocale($locale);

        $member = $league->members()->findOrFail($memberId);

        // No permitir desactivar al owner
        if ($member->user_id === $league->owner_user_id) {
            return back()->with('error', __('No se puede desactivar al propietario de la liga.'));
        }

        $member->is_active = !$member->is_active;
        $member->save();

        $message = $member->is_active 
            ? __('Miembro activado correctamente.')
            : __('Miembro desactivado correctamente.');

        return back()->with('success', $message);
    }
}
