<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::pattern('locale', 'es|en|fr');

// Redirige "/" al idioma (sesión o Accept-Language)
Route::get('/', function () {
    $supported = ['es','en','fr'];
    $preferred = request()->getPreferredLanguage($supported) ?? 'es';
    $locale = session('app_locale', $preferred);
    return redirect($locale);
});

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'es|en|fr'],
], function () {
    Route::get('/', fn () => view('welcome'))->name('home');

    // Jetstream / dashboard
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    });

    // tus rutas de app...
});
