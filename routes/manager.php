<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;

// ========================================
// ONBOARDING ROUTES (Livewire - sin require_league)
// ========================================
Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('{locale}/manager/onboarding')
    ->where(['locale' => 'es|en|fr'])
    ->as('manager.onboarding.')
    ->group(function () {
        Route::get('/welcome', function() {
            return view('manager.onboarding.welcome');
        })->name('welcome');
        
        Route::get('/public-leagues', function() {
            return view('manager.onboarding.public-leagues');
        })->name('public-leagues');
        
        Route::get('/join-with-code', function() {
            return view('manager.onboarding.join-with-code');
        })->name('join-with-code');
        
        Route::get('/create-private', function() {
            return view('manager.onboarding.create-private');
        })->name('create-private');
        
        Route::get('/pending-approval', function() {
            return view('manager.onboarding.pending-approval');
        })->name('pending-approval');
    });

// ========================================
// MANAGER DASHBOARD (requiere league)
// ========================================
Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('{locale}/manager')
    ->where(['locale' => 'es|en|fr'])
    ->as('manager.')
    ->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    });


// ========================================
// SQUAD BUILDER ROUTES (Armado de Plantilla Inicial)
// ========================================
Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('{locale}/manager/squad-builder')
    ->where(['locale' => 'es|en|fr'])
    ->as('manager.squad-builder.')
    ->group(function () {
        
        // Vista principal del wizard (Livewire)
        Route::get('/', function() {
            return view('manager.squad-builder.index');
        })->name('index');
        
        // Endpoints AJAX para el wizard (manejados por Livewire actions)
        // Nota: Los componentes Livewire manejan las acciones automáticamente
    });

    // ========================================
    // LINEUP MANAGEMENT ROUTES
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/lineup')
        ->where(['locale' => 'es|en|fr'])
        ->as('manager.lineup.')
        ->group(function () {
            Route::get('/', function() {
                return view('manager.lineup.index');
            })->name('index');
        });

    // ========================================
    // MARKET ROUTES (Transfer Market)
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/market')
        ->where(['locale' => 'es|en|fr'])
        ->name('manager.market.')
        ->group(function () {
            Route::get('/', function() {
                return view('manager.market.index');
            })->name('index');
        });

    // ========================================
    // FIXTURES ROUTES (Calendario de Partidos)
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/fixtures')
        ->where(['locale' => 'es|en|fr'])
        ->as('manager.fixtures.')
        ->group(function () {
            Route::get('/', function() {
                return view('manager.fixtures.index');
            })->name('index');
        });

    // ========================================
    // SCORES ROUTES (Puntos Detallados)
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/scores')
        ->where(['locale' => 'es|en|fr'])
        ->as('manager.scores.')
        ->group(function () {
            Route::get('/', function() {
                return view('manager.scores.index');
            })->name('index');
        });

    // ========================================
    // LEAGUE ROUTES (Clasificación)
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/league')
        ->where(['locale' => 'es|en|fr'])
        ->as('manager.league.')
        ->group(function () {
            Route::get('/standings', function() {
                return view('manager.league.standings');
            })->name('standings');
        });

    // ========================================
    // CALENDAR ROUTES (Calendario de Jornadas)
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/calendar')
        ->where(['locale' => 'es|en|fr'])
        ->as('manager.calendar.')
        ->group(function () {
            Route::get('/', function() {
                return view('manager.calendar.index');
            })->name('index');
        });

    // ========================================
    // STATS ROUTES (Estadísticas)
    // ========================================
    Route::middleware(['web', 'auth', 'verified', 'role:manager'])
        ->prefix('{locale}/manager/stats')
        ->where(['locale' => 'es|en|fr'])
        ->as('manager.stats.')
        ->group(function () {
            Route::get('/', function() {
                return view('manager.stats.index');
            })->name('index');
        });

