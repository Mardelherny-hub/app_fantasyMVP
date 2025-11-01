<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RealCompetition;
use App\Models\RealCompetitionTeamSeason;
use App\Models\RealTeam;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealCompetitionTeamController extends Controller
{
    /**
     * Display teams for the competition (integrated in show view).
     * This method can be used for AJAX requests if needed.
     */
    public function index(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        $seasonId = $request->input('season_id');
        
        $query = $realCompetition->teamSeasons()
            ->with(['team', 'season'])
            ->orderBy('created_at', 'desc');

        if ($seasonId) {
            $query->where('season_id', $seasonId);
        }

        $teamSeasons = $query->get();
        $seasons = Season::orderBy('starts_at', 'desc')->get();

        return view('admin.real-competitions.teams.index', compact(
            'realCompetition',
            'teamSeasons',
            'seasons',
            'seasonId'
        ));
    }

    /**
     * Show form to add teams to competition.
     */
    public function create(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        // Get selected season or most recent active season
        $selectedSeasonId = $request->input('season_id');
        
        if ($selectedSeasonId) {
            $selectedSeason = Season::find($selectedSeasonId);
        } else {
            $selectedSeason = Season::where('is_active', true)->first() 
                ?? Season::orderBy('starts_at', 'desc')->first();
        }

        // Get all seasons for dropdown
        $seasons = Season::orderBy('starts_at', 'desc')->get();

        // Get teams already assigned to this competition + season
        $assignedTeamIds = [];
        if ($selectedSeason) {
            $assignedTeamIds = RealCompetitionTeamSeason::where('real_competition_id', $realCompetition->id)
                ->where('season_id', $selectedSeason->id)
                ->pluck('real_team_id')
                ->toArray();
        }

        // Get available teams (not assigned to this competition + season)
        $availableTeams = RealTeam::whereNotIn('id', $assignedTeamIds)
            ->orderBy('name')
            ->get();

        return view('admin.real-competitions.teams.create', compact(
            'realCompetition',
            'seasons',
            'selectedSeason',
            'availableTeams'
        ));
    }

    /**
     * Store selected teams to competition.
     */
    public function store(Request $request, string $locale, RealCompetition $realCompetition)
    {
        app()->setLocale($locale);

        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'team_ids' => ['required', 'array', 'min:1'],
            'team_ids.*' => ['exists:real_teams,id'],
        ], [
            'season_id.required' => __('Debe seleccionar una temporada.'),
            'season_id.exists' => __('La temporada seleccionada no existe.'),
            'team_ids.required' => __('Debe seleccionar al menos un equipo.'),
            'team_ids.min' => __('Debe seleccionar al menos un equipo.'),
            'team_ids.*.exists' => __('Uno o más equipos seleccionados no existen.'),
        ]);

        $seasonId = $data['season_id'];
        $teamIds = $data['team_ids'];
        $addedCount = 0;
        $duplicates = [];

        DB::beginTransaction();
        try {
            foreach ($teamIds as $teamId) {
                // Check if already exists
                $exists = RealCompetitionTeamSeason::where('real_competition_id', $realCompetition->id)
                    ->where('season_id', $seasonId)
                    ->where('real_team_id', $teamId)
                    ->exists();

                if ($exists) {
                    $team = RealTeam::find($teamId);
                    $duplicates[] = $team->name;
                    continue;
                }

                // Create assignment
                RealCompetitionTeamSeason::create([
                    'real_competition_id' => $realCompetition->id,
                    'season_id' => $seasonId,
                    'real_team_id' => $teamId,
                ]);

                $addedCount++;
            }

            DB::commit();

            // Build success message
            $message = '';
            if ($addedCount > 0) {
                $message .= __(':count equipos agregados correctamente.', ['count' => $addedCount]);
            }
            if (count($duplicates) > 0) {
                $message .= ' ' . __('Equipos ya existentes omitidos: :teams', [
                    'teams' => implode(', ', $duplicates)
                ]);
            }

            return redirect()
                ->route('admin.real-competitions.show', [
                    'locale' => $locale,
                    'realCompetition' => $realCompetition
                ])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', __('Error al agregar equipos: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Remove team from competition.
     */
    public function destroy(Request $request, string $locale, RealCompetition $realCompetition, RealTeam $realTeam)
    {
        app()->setLocale($locale);

        // Get season_id from request
        $seasonId = $request->input('season_id');

        if (!$seasonId) {
            return back()->with('error', __('Debe especificar la temporada.'));
        }

        // Find the assignment
        $teamSeason = RealCompetitionTeamSeason::where('real_competition_id', $realCompetition->id)
            ->where('season_id', $seasonId)
            ->where('real_team_id', $realTeam->id)
            ->first();

        if (!$teamSeason) {
            return back()->with('error', __('El equipo no está asignado a esta competición y temporada.'));
        }

        // Check if team has fixtures in this competition + season
        $hasFixtures = $realCompetition->fixtures()
            ->where('season_id', $seasonId)
            ->where(function($query) use ($realTeam) {
                $query->where('home_team_id', $realTeam->id)
                    ->orWhere('away_team_id', $realTeam->id);
            })
            ->exists();

        if ($hasFixtures) {
            return back()->with('error', __('No se puede eliminar el equipo porque tiene fixtures asociados en esta competición y temporada.'));
        }

        // Delete assignment
        $teamSeason->delete();

        return back()->with('success', __('Equipo removido correctamente de la competición.'));
    }
}