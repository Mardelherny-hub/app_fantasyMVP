<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LeagueController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\RealTeamController;
use App\Http\Controllers\Admin\RealTeamPlayerController;
use App\Http\Controllers\Admin\GameweekController;
use App\Http\Controllers\Admin\FootballMatchController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\Imports\PlayersImportController;
use App\Http\Controllers\Admin\RealCompetitionController;
use App\Http\Controllers\Admin\RealFixtureController;


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
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

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

        // ⬇️ NUEVAS rutas para agregar jugadores al equipo (selector + attach)
        Route::get('/real-teams/{realTeam}/players/available', [RealTeamPlayerController::class, 'index'])
            ->name('real-teams.players.index');   // <— index
        Route::post('/real-teams/{realTeam}/players/attach', [RealTeamPlayerController::class, 'store'])
            ->name('real-teams.players.store');   // <— store

        Route::get('/real-teams/{realTeam}', [RealTeamController::class, 'show'])->name('real-teams.show');
        Route::post('/real-teams', [RealTeamController::class, 'store'])->name('real-teams.store');
        Route::get('/real-teams/{realTeam}/edit', [RealTeamController::class, 'edit'])->name('real-teams.edit');
        Route::put('/real-teams/{realTeam}', [RealTeamController::class, 'update'])->name('real-teams.update');
        Route::delete('/real-teams/{realTeam}', [RealTeamController::class, 'destroy'])->name('real-teams.destroy');
        
        // Restore soft deleted team
        Route::patch('/real-teams/{id}/restore', [RealTeamController::class, 'restore'])->name('real-teams.restore');
    });

/* 
* Admin Routes - Agregar jujador a real team
*/
    

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

    /* Admin Routes - Players (CRUD + Import) */
    Route::middleware(['web', 'auth', 'verified', 'role:admin'])
        ->prefix('{locale}/admin')
        ->where(['locale' => 'es|en|fr'])
        ->as('admin.')
        ->group(function () {

            // Prefix /players para evitar conflictos
            Route::prefix('players')->as('players.')->group(function () {

                // CRUD
                Route::get('/', [\App\Http\Controllers\Admin\PlayerController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\PlayerController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\PlayerController::class, 'store'])->name('store');
                Route::get('/{player}/edit', [\App\Http\Controllers\Admin\PlayerController::class, 'edit'])->name('edit');
                Route::put('/{player}', [\App\Http\Controllers\Admin\PlayerController::class, 'update'])->name('update');
                Route::delete('/{player}', [\App\Http\Controllers\Admin\PlayerController::class, 'destroy'])->name('destroy');

                // Toggle activo
                Route::patch('/{player}/toggle', [\App\Http\Controllers\Admin\PlayerController::class, 'toggle'])->name('toggle');

                // Import (form + submit)
                Route::get('/import', [\App\Http\Controllers\Admin\Imports\PlayersImportController::class, 'index'])->name('import');
                Route::post('/import', [\App\Http\Controllers\Admin\Imports\PlayersImportController::class, 'store'])->name('import.store');

                // Plantillas (opcional)
                Route::get('/import/template', [\App\Http\Controllers\Admin\Imports\PlayersImportController::class, 'template'])->name('import.template');
                Route::get('/import/template-csv', [\App\Http\Controllers\Admin\Imports\PlayersImportController::class, 'templateCsv'])->name('import.template_csv');
            });
    });

     /* 
            * ⭐ NUEVAS RUTAS - Real Competitions (Competiciones Reales Canadá)
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
            * ⭐ NUEVAS RUTAS - Real Fixtures (Fixtures de Ligas Reales)
            */
            Route::middleware(['web', 'auth', 'verified', 'role:admin'])
                ->prefix('{locale}/admin')
                ->where(['locale' => 'es|en|fr'])
                ->as('admin.')
                ->group(function () {
                    // Real Fixtures (Listado y detalle)
                    Route::get('/real-fixtures', [RealFixtureController::class, 'index'])->name('real-fixtures.index');
                    Route::get('/real-fixtures/{realFixture}', [RealFixtureController::class, 'show'])->name('real-fixtures.show');
                    Route::get('/real-fixtures/create', [RealFixtureController::class, 'create'])->name('real-fixtures.create');
                    Route::post('/real-fixtures', [RealFixtureController::class, 'store'])->name('real-fixtures.store');
                    Route::get('/real-fixtures/{realFixture}/edit', [RealFixtureController::class, 'edit'])->name('real-fixtures.edit');
                    Route::put('/real-fixtures/{realFixture}', [RealFixtureController::class, 'update'])->name('real-fixtures.update');
                    Route::delete('/real-fixtures/{realFixture}', [RealFixtureController::class, 'destroy'])->name('real-fixtures.destroy');    
                    
                    // Importar fixtures desde API
                    Route::get('/real-fixtures/import', [RealFixtureController::class, 'importForm'])->name('real-fixtures.import-form');
                    Route::post('/real-fixtures/import', [RealFixtureController::class, 'import'])->name('real-fixtures.import');
                });

            /* 
            * ⭐ NUEVAS RUTAS - Real Matches (Partidos Jugados de Ligas Reales)
            */
            Route::middleware(['web', 'auth', 'verified', 'role:admin'])
                ->prefix('{locale}/admin')
                ->where(['locale' => 'es|en|fr'])
                ->as('admin.')
                ->group(function () {
                    // Real Matches (Listado y detalle con lineups/eventos)
                    Route::get('/real-matches', [RealMatchController::class, 'index'])->name('real-matches.index');
                    Route::get('/real-matches/{realMatch}', [RealMatchController::class, 'show'])->name('real-matches.show');
                    
                    // Ver alineaciones y eventos
                    Route::get('/real-matches/{realMatch}/lineups', [RealMatchController::class, 'lineups'])->name('real-matches.lineups');
                    Route::get('/real-matches/{realMatch}/events', [RealMatchController::class, 'events'])->name('real-matches.events');
                    
                    // Importar datos del partido desde API
                    Route::post('/real-matches/{realMatch}/import-data', [RealMatchController::class, 'importData'])->name('real-matches.import-data');
                });

