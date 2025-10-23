<?php

namespace App\Http\Controllers\Admin\CPL;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CPL\StoreRealPlayerEventRequest;
use App\Jobs\Admin\Scoring\ProcessMatchStatsJob;
use App\Models\RealMatch;
use App\Models\RealPlayerEvent;
use App\Models\RealPlayer;
use Illuminate\Http\Request;

class RealPlayerEventController extends Controller
{
    public function index(Request $request, string $locale, RealMatch $match)
    {
        app()->setLocale($locale);

        $match->load([
            'fixture.homeTeam',
            'fixture.awayTeam',
            'events.player',
            'events.team'
        ]);

        $homeTeamId = $match->fixture->home_team_id;
        $awayTeamId = $match->fixture->away_team_id;

        $homePlayers = RealPlayer::whereHas('memberships', function($q) use ($homeTeamId) {
            $q->where('real_team_id', $homeTeamId)->whereNull('to_date');
        })->orderBy('full_name')->get();

        $awayPlayers = RealPlayer::whereHas('memberships', function($q) use ($awayTeamId) {
            $q->where('real_team_id', $awayTeamId)->whereNull('to_date');
        })->orderBy('full_name')->get();

        return view('admin.cpl.matches.events', compact('match', 'homePlayers', 'awayPlayers'));
    }

    public function store(StoreRealPlayerEventRequest $request, string $locale, RealMatch $match)
    {
        app()->setLocale($locale);

        RealPlayerEvent::create([
            'real_match_id' => $match->id,
            'real_player_id' => $request->real_player_id,
            'real_team_id' => $request->real_team_id,
            'type' => $request->type,
            'minute' => $request->minute,
        ]);

        return redirect()
            ->route('admin.cpl.matches.events.index', [$locale, $match])
            ->with('success', __('Evento agregado exitosamente'));
    }

    public function destroy(Request $request, string $locale, RealPlayerEvent $event)
    {
        app()->setLocale($locale);

        $matchId = $event->real_match_id;
        $event->delete();

        return redirect()
            ->route('admin.cpl.matches.events.index', [$locale, $matchId])
            ->with('success', __('Evento eliminado exitosamente'));
    }

    public function process(Request $request, string $locale, RealMatch $match)
    {
        app()->setLocale($locale);

        if ($match->events()->count() === 0) {
            return back()->with('warning', __('No hay eventos para procesar'));
        }

        ProcessMatchStatsJob::dispatch($match);

        return redirect()
            ->route('admin.scoring.index', $locale)
            ->with('success', __('Procesando stats del partido. Verifica en unos momentos.'));
    }
}