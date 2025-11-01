<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\RealTeamController;
use App\Http\Controllers\Admin\RealPlayerController;
use App\Http\Controllers\Admin\RealCompetitionController;
use App\Http\Controllers\Admin\RealFixtureController;
use App\Http\Controllers\Admin\RealMatchController;
use App\Http\Controllers\Admin\Fantasy\LeagueController;
use App\Http\Controllers\Admin\Fantasy\GameweekController;
use App\Http\Controllers\Admin\Fantasy\TeamsController;
use App\Http\Controllers\Admin\Fantasy\FixtureController;
use App\Http\Controllers\Admin\Market\MarketDashboardController;
use App\Http\Controllers\Admin\Market\ListingsManagementController;
use App\Http\Controllers\Admin\Market\OffersManagementController;
use App\Http\Controllers\Admin\Market\TransfersController;
use App\Http\Controllers\Admin\Market\MarketSettingsController;
use App\Http\Controllers\Admin\Market\PricesManagementController;
use App\Http\Controllers\Admin\Market\ModerationController;
use App\Http\Controllers\Admin\Market\AnalyticsController;
use App\Http\Controllers\Admin\ScoringController;
use App\Http\Controllers\Admin\CPL\RealMatchController as CPLMatchController;
use App\Http\Controllers\Admin\CPL\RealPlayerEventController;
use App\Http\Controllers\Admin\Quiz\QuestionController;
use App\Http\Controllers\Admin\Quiz\CategoryController;
use App\Http\Controllers\Admin\Quiz\QuizController;
use App\Http\Controllers\Admin\RealCompetitionTeamController;



/* ========================================
 * ADMIN ROUTES - GRUPO ÚNICO
 * ======================================== */

Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        
        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        
        // Seasons
        Route::get('/seasons', [SeasonController::class, 'index'])->name('seasons.index');
        Route::get('/seasons/create', [SeasonController::class, 'create'])->name('seasons.create');
        Route::post('/seasons', [SeasonController::class, 'store'])->name('seasons.store');
        Route::get('/seasons/{season}/edit', [SeasonController::class, 'edit'])->name('seasons.edit');
        Route::put('/seasons/{season}', [SeasonController::class, 'update'])->name('seasons.update');
        Route::delete('/seasons/{season}', [SeasonController::class, 'destroy'])->name('seasons.destroy');
        Route::patch('/seasons/{season}/toggle', [SeasonController::class, 'toggle'])->name('seasons.toggle');
        
        // Real Competitions
        Route::get('/real-competitions', [RealCompetitionController::class, 'index'])->name('real-competitions.index');
        Route::get('/real-competitions/create', [RealCompetitionController::class, 'create'])->name('real-competitions.create');
        Route::post('/real-competitions', [RealCompetitionController::class, 'store'])->name('real-competitions.store');
        Route::get('/real-competitions/{realCompetition}', [RealCompetitionController::class, 'show'])->name('real-competitions.show');
        Route::get('/real-competitions/{realCompetition}/edit', [RealCompetitionController::class, 'edit'])->name('real-competitions.edit');
        Route::put('/real-competitions/{realCompetition}', [RealCompetitionController::class, 'update'])->name('real-competitions.update');
        Route::delete('/real-competitions/{realCompetition}', [RealCompetitionController::class, 'destroy'])->name('real-competitions.destroy');
        //admin.real-competitions.toggle
        Route::patch('/real-competitions/{realCompetition}/toggle', [RealCompetitionController::class, 'toggle'])->name('real-competitions.toggle');
        
        // Real Competition Teams Management
        Route::prefix('/real-competitions/{realCompetition}/teams')->as('real-competitions.teams.')->group(function () {
            Route::get('/', [RealCompetitionTeamController::class, 'index'])->name('index');
            Route::get('/create', [RealCompetitionTeamController::class, 'create'])->name('create');
            Route::post('/', [RealCompetitionTeamController::class, 'store'])->name('store');
            Route::delete('/{realTeam}', [RealCompetitionTeamController::class, 'destroy'])->name('destroy');
        });

        // Real Teams
        Route::get('/real-teams', [RealTeamController::class, 'index'])->name('real-teams.index');
        Route::get('/real-teams/create', [RealTeamController::class, 'create'])->name('real-teams.create');
        Route::post('/real-teams', [RealTeamController::class, 'store'])->name('real-teams.store');
        Route::get('/real-teams/{realTeam}', [RealTeamController::class, 'show'])->name('real-teams.show');
        Route::get('/real-teams/{realTeam}/edit', [RealTeamController::class, 'edit'])->name('real-teams.edit');
        Route::put('/real-teams/{realTeam}', [RealTeamController::class, 'update'])->name('real-teams.update');
        Route::delete('/real-teams/{realTeam}', [RealTeamController::class, 'destroy'])->name('real-teams.destroy');
        // admin.real-teams.players.index
        Route::get('/real-teams/{realTeam}/players', [RealTeamController::class, 'playersIndex'])->name('real-teams.players.index');

        // Real Players
        Route::get('/real-players', [RealPlayerController::class, 'index'])->name('real-players.index');
        Route::get('/real-players/create', [RealPlayerController::class, 'create'])->name('real-players.create');
        Route::post('/real-players', [RealPlayerController::class, 'store'])->name('real-players.store');
        Route::get('/real-players/{realPlayer}', [RealPlayerController::class, 'show'])->name('real-players.show');
        Route::get('/real-players/{realPlayer}/edit', [RealPlayerController::class, 'edit'])->name('real-players.edit');
        Route::put('/real-players/{realPlayer}', [RealPlayerController::class, 'update'])->name('real-players.update');
        Route::delete('/real-players/{realPlayer}', [RealPlayerController::class, 'destroy'])->name('real-players.destroy');
        
        // Real Fixtures
        Route::get('/real-fixtures', [RealFixtureController::class, 'index'])->name('real-fixtures.index');
        Route::get('/real-fixtures/create', [RealFixtureController::class, 'create'])->name('real-fixtures.create');
        Route::post('/real-fixtures', [RealFixtureController::class, 'store'])->name('real-fixtures.store');
        Route::get('/real-fixtures/{realFixture}', [RealFixtureController::class, 'show'])->name('real-fixtures.show');
        Route::get('/real-fixtures/{realFixture}/edit', [RealFixtureController::class, 'edit'])->name('real-fixtures.edit');
        Route::put('/real-fixtures/{realFixture}', [RealFixtureController::class, 'update'])->name('real-fixtures.update');
        Route::delete('/real-fixtures/{realFixture}', [RealFixtureController::class, 'destroy'])->name('real-fixtures.destroy');
        
        // Real Matches
        Route::get('/real-matches', [RealMatchController::class, 'index'])->name('real-matches.index');
        Route::get('/real-matches/create', [RealMatchController::class, 'create'])->name('real-matches.create');
        Route::post('/real-matches', [RealMatchController::class, 'store'])->name('real-matches.store');
        Route::get('/real-matches/{realMatch}', [RealMatchController::class, 'show'])->name('real-matches.show');
        Route::get('/real-matches/{realMatch}/edit', [RealMatchController::class, 'edit'])->name('real-matches.edit');
        Route::put('/real-matches/{realMatch}', [RealMatchController::class, 'update'])->name('real-matches.update');
        Route::delete('/real-matches/{realMatch}', [RealMatchController::class, 'destroy'])->name('real-matches.destroy');
        
        // Fantasy
        Route::prefix('fantasy')->as('fantasy.')->group(function () {            
            Route::resource('leagues', LeagueController::class); 
            //admin.fantasy.leagues.toggle-lock
            Route::patch('leagues/{league}/toggle-lock', [LeagueController::class, 'toggleLock'])->name('leagues.toggle-lock');
            Route::resource('seasons', SeasonController::class);
            Route::resource('gameweeks', GameweekController::class);
            //admin.fantasy.gameweeks.toggle
            Route::patch('gameweeks/{gameweek}/toggle', [GameweekController::class, 'toggle'])->name('gameweeks.toggle');
            Route::get('teams', [TeamsController::class, 'index'])->name('teams.index');
            Route::get('teams/{team}', [TeamsController::class, 'show'])->name('teams.show');
            Route::get('teams/{team}/edit', [TeamsController::class, 'edit'])->name('teams.edit');
            Route::put('teams/{team}', [TeamsController::class, 'update'])->name('teams.update');
            
            // Fixtures
            Route::prefix('fixtures')->as('fixtures.')->group(function () {
                Route::get('/', [FixtureController::class, 'index'])->name('index');
                Route::get('/{fixture}', [FixtureController::class, 'show'])->name('show');
                Route::post('/generate', [FixtureController::class, 'generate'])->name('generate');
                Route::post('/{fixture}/finish', [FixtureController::class, 'finish'])->name('finish');
            });
        });
        
        // Market
        Route::prefix('market')->as('market.')->group(function () {
            Route::get('/', [MarketDashboardController::class, 'index'])->name('index');
            Route::get('listings', [ListingsManagementController::class, 'index'])->name('listings.index');
            Route::post('listings/{listing}/cancel', [ListingsManagementController::class, 'cancel'])->name('listings.cancel');
            Route::get('offers', [OffersManagementController::class, 'index'])->name('offers.index');
            Route::get('transfers', [TransfersController::class, 'index'])->name('transfers.index');
            Route::get('settings', [MarketSettingsController::class, 'index'])->name('settings.index');
            Route::post('settings/{league}', [MarketSettingsController::class, 'update'])->name('settings.update');
            Route::get('prices', [PricesManagementController::class, 'index'])->name('prices.index');
            Route::get('moderation', [ModerationController::class, 'index'])->name('moderation.index');
            Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        });
        
        // Scoring
        Route::prefix('scoring')->as('scoring.')->group(function () {
            Route::get('/', [ScoringController::class, 'index'])->name('index');
            Route::get('/rules', [ScoringController::class, 'rules'])->name('rules');
            Route::get('/{gameweek}', [ScoringController::class, 'show'])->name('show');
            Route::post('/{gameweek}/process', [ScoringController::class, 'process'])->name('process');
            Route::post('/{gameweek}/recalculate', [ScoringController::class, 'recalculate'])->name('recalculate');
            Route::post('/{gameweek}/close', [ScoringController::class, 'close'])->name('close');
            Route::post('/{gameweek}/reopen', [ScoringController::class, 'reopen'])->name('reopen');
            
        });
        
        // CPL Management
        Route::prefix('cpl')->as('cpl.')->group(function () {
            Route::prefix('matches')->as('matches.')->group(function () {
                Route::get('/', [CPLMatchController::class, 'index'])->name('index');
                Route::get('/create', [CPLMatchController::class, 'create'])->name('create');
                Route::post('/', [CPLMatchController::class, 'store'])->name('store');
                Route::get('/{match}/edit', [CPLMatchController::class, 'edit'])->name('edit');
                Route::put('/{match}', [CPLMatchController::class, 'update'])->name('update');
                Route::delete('/{match}', [CPLMatchController::class, 'destroy'])->name('destroy');
                
                // Player Events
                Route::get('/{match}/events', [RealPlayerEventController::class, 'index'])->name('events.index');
                Route::post('/{match}/events', [RealPlayerEventController::class, 'store'])->name('events.store');
                Route::delete('/events/{event}', [RealPlayerEventController::class, 'destroy'])->name('events.destroy');
                Route::post('/{match}/events/process', [RealPlayerEventController::class, 'process'])->name('events.process');
            });
        });
        
        // QUIZ MODULE - VERSIÓN SIMPLE CON IDs
        Route::prefix('quiz')->as('quiz.')->group(function () {
            
            // Questions
            Route::get('questions', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'index'])
                ->name('questions.index');
            Route::get('questions/{id}', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'show'])
                ->name('questions.show')
                ->where('id', '[0-9]+');
            Route::get('questions/create', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'create'])
                ->name('questions.create');
            Route::post('questions', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'store'])
                ->name('questions.store');
            Route::get('questions/{id}/edit', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'edit'])
                ->name('questions.edit')
                ->where('id', '[0-9]+');
            Route::put('questions/{id}', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'update'])
                ->name('questions.update')
                ->where('id', '[0-9]+');
            Route::delete('questions/{id}', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'destroy'])
                ->name('questions.destroy')
                ->where('id', '[0-9]+');
            Route::patch('questions/{id}/toggle', [App\Http\Controllers\Admin\Quiz\QuestionController::class, 'toggleActive'])
                ->name('questions.toggle')
                ->where('id', '[0-9]+');
            
            // Categories - orden correcto: create ANTES de {id}
            Route::get('categories', [App\Http\Controllers\Admin\Quiz\CategoryController::class, 'index'])
                ->name('categories.index');
            Route::get('categories/create', [App\Http\Controllers\Admin\Quiz\CategoryController::class, 'create'])
                ->name('categories.create');
            Route::post('categories', [App\Http\Controllers\Admin\Quiz\CategoryController::class, 'store'])
                ->name('categories.store');
            Route::get('categories/{id}/edit', [App\Http\Controllers\Admin\Quiz\CategoryController::class, 'edit'])
                ->name('categories.edit')
                ->where('id', '[0-9]+');
            Route::put('categories/{id}', [App\Http\Controllers\Admin\Quiz\CategoryController::class, 'update'])
                ->name('categories.update')
                ->where('id', '[0-9]+');
            Route::delete('categories/{id}', [App\Http\Controllers\Admin\Quiz\CategoryController::class, 'destroy'])
                ->name('categories.destroy')
                ->where('id', '[0-9]+');
            
            // Quizzes
            Route::get('quizzes', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'index'])
                ->name('quizzes.index');
            // Show
            Route::get('quizzes/{id}', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'show'])
                ->name('quizzes.show')
                ->where('id', '[0-9]+');
            Route::get('quizzes/create', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'create'])
                ->name('quizzes.create');
            Route::post('quizzes', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'store'])
                ->name('quizzes.store');
            Route::get('quizzes/{id}/edit', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'edit'])
                ->name('quizzes.edit')
                ->where('id', '[0-9]+');
            Route::put('quizzes/{id}', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'update'])
                ->name('quizzes.update')
                ->where('id', '[0-9]+');
            Route::delete('quizzes/{id}', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'destroy'])
                ->name('quizzes.destroy')
                ->where('id', '[0-9]+');
            Route::patch('quizzes/{id}/toggle', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'toggleActive'])
                ->name('quizzes.toggle')
                ->where('id', '[0-9]+');
            Route::post('quizzes/{id}/assign-questions', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'assignQuestions'])
                ->name('quizzes.assign-questions')
                ->where('id', '[0-9]+');
            Route::post('quizzes/{id}/auto-assign-questions', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'autoAssignQuestions'])
                ->name('quizzes.auto-assign-questions')
                ->where('id', '[0-9]+');
        });
    });