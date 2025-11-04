<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar Carbon locale según el idioma de la app
        $locale = App::getLocale();
        
        // Mapeo de locales Laravel a Carbon
        $carbonLocales = [
            'es' => 'es',
            'en' => 'en',
            'fr' => 'fr',
        ];
        
        $carbonLocale = $carbonLocales[$locale] ?? 'en';
        Carbon::setLocale($carbonLocale);
        
        // También configurar timezone si es necesario
        date_default_timezone_set(config('app.timezone'));
    }
}