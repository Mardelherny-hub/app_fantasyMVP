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