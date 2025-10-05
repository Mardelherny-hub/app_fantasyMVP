<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::middleware(['web', 'auth', 'verified', 'role:admin'])
    ->prefix('{locale}/admin')
    ->where(['locale' => 'es|en|fr'])
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });
