<?php

namespace App\Http\Controllers\Admin\Fantasy;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\League;
use App\Services\Admin\Fixtures\FixtureGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\GenerateFixturesRequest;

class FixtureController extends Controller
{
    public function __construct(
        protected FixtureGeneratorService $fixtureGenerator
    ) {}

    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = Fixture::with(['league', 'gameweek', 'homeTeam', 'awayTeam']);

        if ($request->filled('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->filled('gameweek_id')) {
            $query->where('gameweek_id', $request->gameweek_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_playoff')) {
            $query->where('is_playoff', $request->is_playoff);
        }

        $fixtures = $query->orderBy('gameweek_id')->paginate(50);

        $leagues = League::all();

        return view('admin.fantasy.fixtures.index', compact('fixtures', 'leagues', 'locale'));
    }

    public function show(Request $request, string $locale, Fixture $fixture)
    {
        app()->setLocale($locale);

        $fixture->load(['league', 'gameweek', 'homeTeam', 'awayTeam']);

        return view('admin.fantasy.fixtures.show', compact('fixture', 'locale'));
    }

    public function generate(GenerateFixturesRequest $request, string $locale)
    {
        app()->setLocale($locale);

        $validated = $request->validated();
            
        try {
            $league = League::findOrFail($validated['league_id']);

            if ($validated['type'] === 'regular') {
                $fixtures = $this->fixtureGenerator->generateRegularSeasonFixtures(
                    $league,
                    $validated['start_gameweek'] ?? 1
                );
            } else {
                $fixtures = $this->fixtureGenerator->generatePlayoffFixtures($league);
            }

            Log::info("Fixtures generados: {$fixtures->count()} para liga {$league->id}");

            return redirect()->route('admin.fantasy.fixtures.index', $locale)
                ->with('success', __('Fixtures generados exitosamente: :count', ['count' => $fixtures->count()]));

        } catch (\Exception $e) {
            Log::error("Error generando fixtures: {$e->getMessage()}");

            return back()->with('error', $e->getMessage());
        }
    }

    public function finish(Request $request, string $locale, Fixture $fixture)
    {
        app()->setLocale($locale);

        try {
            $fixture->update(['status' => Fixture::STATUS_FINISHED]);

            return redirect()->route('admin.fantasy.fixtures.show', ['locale' => $locale, 'fixture' => $fixture])
                ->with('success', __('Fixture marcado como finalizado'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}