<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Support\DashboardRoute;


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

