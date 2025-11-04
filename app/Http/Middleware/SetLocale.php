<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon; // 👈 Agregar

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $supported = ['es','en','fr'];

        $segment = $request->segment(1);
        $locale = in_array($segment, $supported)
            ? $segment
            : (session('app_locale') ?? config('app.locale', 'es'));

        if (in_array($segment, $supported) && session('app_locale') !== $segment) {
            session(['app_locale' => $segment]);
        }

        App::setLocale($locale);
        
        // 👇 Configurar Carbon con el mismo locale
        Carbon::setLocale($locale);

        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}