<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gameweek;
use App\Services\Admin\Scoring\ScoringCalculationService;
use App\Services\Admin\Fixtures\FixtureProcessingService;
use Illuminate\Http\Request;

class ScoringController extends Controller
{
    protected $scoringService;
    protected $fixtureService;

    public function __construct(
        ScoringCalculationService $scoringService,
        FixtureProcessingService $fixtureService
    ) {
        $this->scoringService = $scoringService;
        $this->fixtureService = $fixtureService;
    }

    public function index(Request $request)
{
    // Obtener datos para filtros
    $leagues = \App\Models\League::orderBy('name')->get();
    $seasons = \App\Models\Season::orderBy('starts_at', 'desc')->get();
    
    // Query base - CORREGIDO: no cargar 'league' porque no existe
    $query = Gameweek::with(['season', 'fixtures']);
    
    // Aplicar filtros
    if ($request->filled('season_id')) {
        $query->where('season_id', $request->season_id);
    }
    
    if ($request->filled('league_id')) {
        // Filtrar por liga a través de los fixtures
        $query->whereHas('fixtures', function($q) use ($request) {
            $q->where('league_id', $request->league_id);
        });
    }
    
    if ($request->filled('status')) {
        if ($request->status === 'open') {
            $query->where('is_closed', false);
        } elseif ($request->status === 'closed') {
            $query->where('is_closed', true);
        }
    }
    
    $gameweeks = $query->orderBy('starts_at', 'desc')->paginate(20);
    
    // Calcular estadísticas
    $stats = [
        'total_gameweeks' => Gameweek::count(),
        'calculated' => Gameweek::where('is_closed', true)
            ->whereHas('fixtures', function($q) {
                $q->where('status', 1);
            })->count(),
        'pending' => Gameweek::where('is_closed', false)->count(),
        'fixtures_finished' => \App\Models\Fixture::where('status', 1)->count(),
    ];
    
    return view('admin.scoring.index', compact('gameweeks', 'leagues', 'seasons', 'stats'));
}


    /**
     * Ver detalle de scoring de gameweek.
     */
    public function show($locale, $gameweek)
    {
        $gameweek = Gameweek::with(['season', 'fixtures.homeTeam', 'fixtures.awayTeam'])
            ->findOrFail($gameweek);
        
        return view('admin.scoring.show', compact('gameweek'));
    }

    /**
     * Procesar scoring de gameweek.
     */
    public function process(Request $request, $locale, $gameweek)
    {
        $gameweek = Gameweek::findOrFail($gameweek);
        
        try {
            $scoringResults = $this->scoringService->processGameweekScoring($gameweek);
            $fixtureResults = $this->fixtureService->processCompletedGameweek($gameweek);
            
            return redirect()
                ->route('admin.scoring.show', ['locale' => $locale, 'gameweek' => $gameweek->id])
                ->with('success', "Gameweek {$gameweek->number} procesado exitosamente. {$scoringResults['teams_processed']} equipos, {$fixtureResults['fixtures_processed']} fixtures.");
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', "Error procesando gameweek: {$e->getMessage()}");
        }
    }

    public function rules(string $locale)
    {
        app()->setLocale($locale);
        
        $seasons = \App\Models\Season::with('scoringRules')->get();
        
        return view('admin.scoring.rules', compact('seasons'));
    }

    /**
     * Recalcular puntos de gameweek.
     */
    public function recalculate($locale, $gameweek)
    {
        $gameweek = Gameweek::findOrFail($gameweek);
        
        try {
            \DB::table('fantasy_roster_scores')
                ->where('gameweek_id', $gameweek->id)
                ->delete();
            
            $results = $this->scoringService->processGameweekScoring($gameweek);
            
            return redirect()
                ->route('admin.scoring.show', ['locale' => $locale, 'gameweek' => $gameweek->id])
                ->with('success', "Puntos recalculados: {$results['teams_processed']} equipos, {$results['total_points_calculated']} puntos.");
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', "Error recalculando: {$e->getMessage()}");
        }
    }

    /**
     * Cerrar gameweek y disparar cálculos.
     */
    public function close($locale, $gameweek)
    {
        $gameweek = Gameweek::findOrFail($gameweek);

        // Verificar si ya está cerrado
        if ($gameweek->is_closed) {
            return redirect()
                ->back()
                ->with('warning', __('Gameweek already closed.'));
        }

        // Verificar si ha terminado
        if ($gameweek->ends_at > now()) {
            return redirect()
                ->back()
                ->with('error', __('Cannot close gameweek before it ends.'));
        }

        try {
            DB::transaction(function () use ($gameweek) {
                // 1. Actualizar fixtures pendientes
                $this->fixtureService->processCompletedGameweek($gameweek);
                
                // 2. Marcar como cerrado
                $gameweek->update(['is_closed' => true]);
                
                // 3. Disparar job de cálculo
                \App\Jobs\Admin\Scoring\CalculateGameweekScoresJob::dispatch($gameweek);
            });

            Log::info("Gameweek closed from admin panel", [
                'gameweek_id' => $gameweek->id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.scoring.show', ['locale' => $locale, 'gameweek' => $gameweek->id])
                ->with('success', __('Gameweek closed successfully. Points will be calculated in background.'));

        } catch (\Exception $e) {
            Log::error("Failed to close gameweek from admin", [
                'gameweek_id' => $gameweek->id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', __('Error closing gameweek: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Reabrir gameweek cerrado.
     */
    public function reopen($locale, $gameweek)
    {
        $gameweek = Gameweek::findOrFail($gameweek);

        if (!$gameweek->is_closed) {
            return redirect()
                ->back()
                ->with('warning', __('Gameweek is already open.'));
        }

        try {
            $gameweek->update(['is_closed' => false]);

            Log::info("Gameweek reopened from admin panel", [
                'gameweek_id' => $gameweek->id,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->with('success', __('Gameweek reopened successfully.'));

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', __('Error reopening gameweek: :message', ['message' => $e->getMessage()]));
        }
    }
}
