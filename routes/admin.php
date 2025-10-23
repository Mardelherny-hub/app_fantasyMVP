<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\RealTeamController;
use App\Http\Controllers\Admin\RealTeamPlayerController;
use App\Http\Controllers\Admin\RealPlayerController;
use App\Http\Controllers\Admin\FootballMatchController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\Imports\PlayersImportController;
use App\Http\Controllers\Admin\RealCompetitionController;
use App\Http\Controllers\Admin\RealFixtureController;
use App\Http\Controllers\Admin\RealMatchController;
use App\Http\Controllers\Admin\Fantasy\LeagueController;
use App\Http\Controllers\Admin\Fantasy\GameweekController;
use App\Http\Controllers\Admin\Fantasy\TeamsController;
use App\Http\Controllers\Admin\Fantasy\GameweeksController;
use App\Http\Controllers\Admin\Market\MarketDashboardController;
use App\Http\Controllers\Admin\Market\ListingsManagementController;
use App\Http\Controllers\Admin\Market\OffersManagementController;
use App\Http\Controllers\Admin\Market\TransfersController;
use App\Http\Controllers\Admin\Market\MarketSettingsController;
use App\Http\Controllers\Admin\Market\PricesManagementController;      // FASE 6
use App\Http\Controllers\Admin\Market\ModerationController;            // FASE 7
use App\Http\Controllers\Admin\Market\AnalyticsController;             // FASE 8
use App\Http\Controllers\Admin\ScoringController;


/* ========================================
 * ADMIN ROUTES
 * ======================================== */

/* 
* Admin Routes - Dashboard
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

/* 
* Admin Routes - Usuarios (CRUD)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Usuarios (CRUD)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Activar/Desactivar (soft delete / restore opcional)
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });

/* 
* Admin Routes - Roles (CRUD)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

/* 
* Admin Routes - Ligas (CRUD)
*/
Route::middleware(['web','auth','verified','role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Ligas (CRUD)
        Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
        Route::get('/leagues/create', [LeagueController::class, 'create'])->name('leagues.create');
        Route::get('/leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');
        Route::post('/leagues', [LeagueController::class, 'store'])->name('leagues.store');
        Route::get('/leagues/{league}/edit', [LeagueController::class, 'edit'])->name('leagues.edit');
        Route::put('/leagues/{league}', [LeagueController::class, 'update'])->name('leagues.update');
        Route::delete('/leagues/{league}', [LeagueController::class, 'destroy'])->name('leagues.destroy');
        // Toggle lock status
        Route::patch('/leagues/{league}/toggle-lock', [LeagueController::class, 'toggleLock'])->name('leagues.toggle-lock');
        // Fill with bots
        Route::post('/leagues/{league}/fill-bots', [LeagueController::class, 'fillWithBots'])->name('leagues.fill-bots');
        // Team management (add/remove)
        Route::post('/leagues/{league}/teams', [LeagueController::class, 'addTeam'])->name('leagues.add-team');
        Route::delete('/leagues/{league}/teams/{team}', [LeagueController::class, 'removeTeam'])->name('leagues.remove-team');
    });

/* 
* Admin Routes - Seasons (CRUD)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Seasons (CRUD)
        Route::get('/seasons', [SeasonController::class, 'index'])->name('seasons.index');
        Route::get('/seasons/create', [SeasonController::class, 'create'])->name('seasons.create');
        Route::post('/seasons', [SeasonController::class, 'store'])->name('seasons.store');
        Route::get('/seasons/{season}/edit', [SeasonController::class, 'edit'])->name('seasons.edit');
        Route::put('/seasons/{season}', [SeasonController::class, 'update'])->name('seasons.update');
        Route::delete('/seasons/{season}', [SeasonController::class, 'destroy'])->name('seasons.destroy');
        
        // Toggle active status
        Route::patch('/seasons/{season}/toggle', [SeasonController::class, 'toggle'])->name('seasons.toggle');
    });

/* 
* Admin Routes - Gameweeks (CRUD)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Gameweeks (CRUD)
        Route::get('/gameweeks', [GameweekController::class, 'index'])->name('gameweeks.index');
        Route::get('/gameweeks/create', [GameweekController::class, 'create'])->name('gameweeks.create');
        Route::post('/gameweeks', [GameweekController::class, 'store'])->name('gameweeks.store');
        Route::get('/gameweeks/{gameweek}/edit', [GameweekController::class, 'edit'])->name('gameweeks.edit');
        Route::put('/gameweeks/{gameweek}', [GameweekController::class, 'update'])->name('gameweeks.update');
        Route::delete('/gameweeks/{gameweek}', [GameweekController::class, 'destroy'])->name('gameweeks.destroy');
        
        // Toggle closed status
        Route::patch('/gameweeks/{gameweek}/toggle', [GameweekController::class, 'toggle'])->name('gameweeks.toggle');
    });

/* 
* Admin Routes - Football Matches (CRUD)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Football Matches (CRUD)
        Route::get('/football-matches', [FootballMatchController::class, 'index'])->name('football-matches.index');
        Route::get('/football-matches/create', [FootballMatchController::class, 'create'])->name('football-matches.create');
        Route::post('/football-matches', [FootballMatchController::class, 'store'])->name('football-matches.store');
        Route::get('/football-matches/{footballMatch}/edit', [FootballMatchController::class, 'edit'])->name('football-matches.edit');
        Route::put('/football-matches/{footballMatch}', [FootballMatchController::class, 'update'])->name('football-matches.update');
        Route::delete('/football-matches/{footballMatch}', [FootballMatchController::class, 'destroy'])->name('football-matches.destroy');
        
        // Quick status update
        Route::patch('/football-matches/{footballMatch}/update-status', [FootballMatchController::class, 'updateStatus'])->name('football-matches.update-status');
    });

/* 
* Admin Routes - Players (CRUD + Import)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Prefix /players para evitar conflictos
        Route::prefix('players')->as('players.')->group(function () {
            // CRUD
            Route::get('/', [PlayerController::class, 'index'])->name('index');
            Route::get('/create', [PlayerController::class, 'create'])->name('create');
            Route::post('/', [PlayerController::class, 'store'])->name('store');
            Route::get('/{player}/edit', [PlayerController::class, 'edit'])->name('edit');
            Route::put('/{player}', [PlayerController::class, 'update'])->name('update');
            Route::delete('/{player}', [PlayerController::class, 'destroy'])->name('destroy');

            // Toggle activo
            Route::patch('/{player}/toggle', [PlayerController::class, 'toggle'])->name('toggle');

            // Import (form + submit)
            Route::get('/import', [PlayersImportController::class, 'index'])->name('import');
            Route::post('/import', [PlayersImportController::class, 'store'])->name('import.store');

            // Plantillas (opcional)
            Route::get('/import/template', [PlayersImportController::class, 'template'])->name('import.template');
            Route::get('/import/template-csv', [PlayersImportController::class, 'templateCsv'])->name('import.template_csv');
        });
    });

/* ========================================
 * REAL GAME - Datos Reales
 * ======================================== */

/* 
* Real Competitions (Competiciones Reales Canadá)
*/
Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        // Real Competitions (CRUD)
        Route::get('/real-competitions', [RealCompetitionController::class, 'index'])->name('real-competitions.index');
        Route::get('/real-competitions/create', [RealCompetitionController::class, 'create'])->name('real-competitions.create');
        Route::post('/real-competitions', [RealCompetitionController::class, 'store'])->name('real-competitions.store');
        Route::get('/real-competitions/{realCompetition}', [RealCompetitionController::class, 'show'])->name('real-competitions.show');
        Route::get('/real-competitions/{realCompetition}/edit', [RealCompetitionController::class, 'edit'])->name('real-competitions.edit');
        Route::put('/real-competitions/{realCompetition}', [RealCompetitionController::class, 'update'])->name('real-competitions.update');
        Route::delete('/real-competitions/{realCompetition}', [RealCompetitionController::class, 'destroy'])->name('real-competitions.destroy');
        
        // Toggle active status
        Route::patch('/real-competitions/{realCompetition}/toggle', [RealCompetitionController::class, 'toggle'])->name('real-competitions.toggle');
    });

    /* 
    * Admin Routes - Real Teams (CRUD)
    */
    Route::middleware(['web', 'auth', 'verified', 'role:admin'])
        ->prefix('{locale}/admin')
        ->where(['locale' => 'es|en|fr'])
        ->as('admin.')
        ->group(function () {
            // Real Teams (CRUD)
            Route::get('/real-teams', [RealTeamController::class, 'index'])->name('real-teams.index');
            Route::get('/real-teams/create', [RealTeamController::class, 'create'])->name('real-teams.create');
            Route::post('/real-teams', [RealTeamController::class, 'store'])->name('real-teams.store');
            Route::get('/real-teams/{realTeam}', [RealTeamController::class, 'show'])->name('real-teams.show');
            Route::get('/real-teams/{realTeam}/edit', [RealTeamController::class, 'edit'])->name('real-teams.edit');
            Route::put('/real-teams/{realTeam}', [RealTeamController::class, 'update'])->name('real-teams.update');
            Route::delete('/real-teams/{realTeam}', [RealTeamController::class, 'destroy'])->name('real-teams.destroy');
            
            // Restore soft deleted team
            Route::patch('/real-teams/{id}/restore', [RealTeamController::class, 'restore'])->name('real-teams.restore');

            // Rutas para agregar jugadores al equipo (selector + attach)
            Route::get('/real-teams/{realTeam}/players/available', [RealTeamPlayerController::class, 'index'])->name('real-teams.players.index');
            Route::post('/real-teams/{realTeam}/players/attach', [RealTeamPlayerController::class, 'store'])->name('real-teams.players.store');
        });

    /* 
    * Admin Routes - Real Players (CRUD)
    */
    Route::middleware(['web', 'auth', 'verified', 'role:admin'])
        ->prefix('{locale}/admin')
        ->where(['locale' => 'es|en|fr'])
        ->as('admin.')
        ->group(function () {
            // Real Players (CRUD)
            Route::get('/real-players', [RealPlayerController::class, 'index'])->name('real-players.index');
            Route::get('/real-players/create', [RealPlayerController::class, 'create'])->name('real-players.create');
            Route::post('/real-players', [RealPlayerController::class, 'store'])->name('real-players.store');
            Route::get('/real-players/{realPlayer}', [RealPlayerController::class, 'show'])->name('real-players.show');
            Route::get('/real-players/{realPlayer}/edit', [RealPlayerController::class, 'edit'])->name('real-players.edit');
            Route::put('/real-players/{realPlayer}', [RealPlayerController::class, 'update'])->name('real-players.update');
            Route::delete('/real-players/{realPlayer}', [RealPlayerController::class, 'destroy'])->name('real-players.destroy');
        });

    /* 
    * Real Fixtures (Fixtures de Ligas Reales)
    */
    Route::middleware(['web', 'auth', 'verified', 'role:admin'])
        ->prefix('{locale}/admin')
        ->where(['locale' => 'es|en|fr'])
        ->as('admin.')
        ->group(function () {
            // Real Fixtures (Listado y detalle)
            Route::get('/real-fixtures', [RealFixtureController::class, 'index'])->name('real-fixtures.index');
            Route::get('/real-fixtures/create', [RealFixtureController::class, 'create'])->name('real-fixtures.create');
            Route::post('/real-fixtures', [RealFixtureController::class, 'store'])->name('real-fixtures.store');
            Route::get('/real-fixtures/{realFixture}', [RealFixtureController::class, 'show'])->name('real-fixtures.show');
            Route::get('/real-fixtures/{realFixture}/edit', [RealFixtureController::class, 'edit'])->name('real-fixtures.edit');
            Route::put('/real-fixtures/{realFixture}', [RealFixtureController::class, 'update'])->name('real-fixtures.update');
            Route::delete('/real-fixtures/{realFixture}', [RealFixtureController::class, 'destroy'])->name('real-fixtures.destroy');    
            
            // Importar fixtures desde API
            Route::get('/real-fixtures/import', [RealFixtureController::class, 'importForm'])->name('real-fixtures.import-form');
            Route::post('/real-fixtures/import', [RealFixtureController::class, 'import'])->name('real-fixtures.import');
        });

    /* 
    * Real Matches (Partidos Jugados de Ligas Reales)
    */
    Route::middleware(['web', 'auth', 'verified', 'role:admin'])
        ->prefix('{locale}/admin')
        ->where(['locale' => 'es|en|fr'])
        ->as('admin.')
        ->group(function () {
            // Real Matches (Listado y detalle con lineups/eventos)
            Route::get('/real-matches', [RealMatchController::class, 'index'])->name('real-matches.index');
            Route::get('/real-matches/create', [RealMatchController::class, 'create'])->name('real-matches.create');
            Route::post('/real-matches', [RealMatchController::class, 'store'])->name('real-matches.store');
            Route::get('/real-matches/{realMatch}', [RealMatchController::class, 'show'])->name('real-matches.show');
            Route::get('/real-matches/{realMatch}/edit', [RealMatchController::class, 'edit'])->name('real-matches.edit');
            Route::put('/real-matches/{realMatch}', [RealMatchController::class, 'update'])->name('real-matches.update');
            Route::delete('/real-matches/{realMatch}', [RealMatchController::class, 'destroy'])->name('real-matches.destroy');
            
            // Ver alineaciones y eventos
            Route::get('/real-matches/{realMatch}/lineups', [RealMatchController::class, 'lineups'])->name('real-matches.lineups');
            Route::get('/real-matches/{realMatch}/events', [RealMatchController::class, 'events'])->name('real-matches.events');
            
            // Importar datos del partido desde API
            Route::post('/real-matches/{realMatch}/import-data', [RealMatchController::class, 'importData'])->name('real-matches.import-data');
        });

    // Fantasy
     Route::middleware(['web', 'auth', 'verified', 'role:admin'])
        ->prefix('{locale}/admin')
        ->where(['locale' => 'es|en|fr'])
        ->as('admin.')
        ->group(function () {
            // Fantasy Routes
        Route::prefix('fantasy')->as('fantasy.')->group(function () {
            Route::resource('leagues', LeagueController::class);
            Route::resource('seasons', SeasonController::class);
            Route::resource('gameweeks', GameweeksController::class);
            Route::get('teams', [TeamsController::class, 'index'])->name('teams.index');
            Route::get('teams/{team}', [TeamsController::class, 'show'])->name('teams.show');
        });

        // Market Routes
        Route::prefix('market')->as('market.')->group(function () {
            // Dashboard/Overview
            Route::get('/', [MarketDashboardController::class, 'index'])->name('index');
            
            // Listings Management
            Route::get('listings', [ListingsManagementController::class, 'index'])->name('listings.index');
            Route::post('listings/{listing}/cancel', [ListingsManagementController::class, 'cancel'])->name('listings.cancel');
            
            // Offers Management
            Route::get('offers', [OffersManagementController::class, 'index'])->name('offers.index');
            
            // Transfers Management
            Route::get('transfers', [TransfersController::class, 'index'])->name('transfers.index');
            
            // Market Settings
            Route::get('settings', [MarketSettingsController::class, 'index'])->name('settings.index');
            Route::post('settings/{league}', [MarketSettingsController::class, 'update'])->name('settings.update');
            
            // Prices Management (FASE 6)
            Route::get('prices', [PricesManagementController::class, 'index'])->name('prices.index');
            
            // Moderation Panel (FASE 7)
            Route::get('moderation', [ModerationController::class, 'index'])->name('moderation.index');
            
            // Analytics Dashboard (FASE 8)
            Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        });


        // Scoring & Fixtures
        Route::prefix('scoring')->name('scoring.')->group(function () {
            Route::get('/', [ScoringController::class, 'index'])->name('index');
            Route::get('/{gameweek}', [ScoringController::class, 'show'])->name('show');
            Route::post('/{gameweek}/process', [ScoringController::class, 'process'])->name('process');
            Route::post('/{gameweek}/recalculate', [ScoringController::class, 'recalculate'])->name('recalculate');

            // ✅ NUEVAS RUTAS
            Route::post('/{gameweek}/close', [ScoringController::class, 'close'])->name('close');
            Route::post('/{gameweek}/reopen', [ScoringController::class, 'reopen'])->name('reopen');

            Route::get('/rules', [ScoringController::class, 'rules'])->name('rules');
        });

        // Fixtures
        Route::prefix('fantasy/fixtures')->as('fantasy.fixtures.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\Fantasy\FixtureController::class, 'index'])->name('index');
            Route::get('/{fixture}', [App\Http\Controllers\Admin\Fantasy\FixtureController::class, 'show'])->name('show');
            Route::post('/generate', [App\Http\Controllers\Admin\Fantasy\FixtureController::class, 'generate'])->name('generate');
            Route::post('/{fixture}/finish', [App\Http\Controllers\Admin\Fantasy\FixtureController::class, 'finish'])->name('finish');
        });


    });