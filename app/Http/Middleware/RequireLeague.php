<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireLeague
{
    /**
     * Handle an incoming request.
     *
     * Redirect to onboarding if manager has no leagues.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Solo aplica a usuarios autenticados con rol manager
        if (!$user || !$user->hasRole('manager')) {
            return $next($request);
        }

        // Si el usuario no tiene ligas activas, redirigir a onboarding
        if ($user->leagues()->count() === 0) {
            // Excepciones: rutas del onboarding mismo
            $allowedRoutes = [
                'manager.onboarding.*',
            ];

            foreach ($allowedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $next($request);
                }
            }

            return redirect()->route('manager.onboarding.welcome', ['locale' => app()->getLocale()]);
        }

        return $next($request);
    }
}