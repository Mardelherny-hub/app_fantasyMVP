<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;

Route::middleware(['web', 'auth', 'verified', 'role:manager'])
    ->prefix('{locale}/manager')
    ->where(['locale' => 'es|en|fr'])
    ->as('manager.')
    ->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    });
