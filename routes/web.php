<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Support\DashboardRoute;

use App\Http\Controllers\DashboardRouterController;


Route::pattern('locale', 'es|en|fr');

// Redirige "/" al idioma (sesión o Accept-Language)
Route::get('/', function () {
    $supported = ['es', 'en', 'fr'];
    $preferred = request()->getPreferredLanguage($supported) ?? 'es';
    $locale = session('app_locale', $preferred);
    return redirect($locale);
});

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'es|en|fr'],
], function () {
    // Página pública
    Route::get('/', fn() => view('welcome'))->name('home');

    // Jetstream / Fortify dashboard redirect
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [DashboardRouterController::class, 'redirect'])
            ->name('dashboard');
    });

    // Socialite
    Route::get('auth/google', [SocialiteController::class, 'redirect']);
    Route::get('auth/google/callback', [SocialiteController::class, 'callback']);
});

// ========================================
// RUTAS DE UTILIDAD Y DESARROLLO
// ========================================

// Limpiar caché (producción: restringir a admin)
Route::get('/clear', function() {
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('event:clear');
    \Artisan::call('clear-compiled');
    \Artisan::call('optimize:clear');
    return "Cache is cleared";
});

// Crear symlink de storage
Route::get('/symlink', function () {
    \Artisan::call('storage:link');
    return 'The storage link has been created!';
});


