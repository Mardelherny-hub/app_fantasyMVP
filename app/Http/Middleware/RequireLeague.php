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

        if (!$user || !$user->hasRole('manager')) {
            return $next($request);
        }

        $hasActiveLeagues = $user->leagueMembers()
            ->where('is_active', true)
            ->exists();

        if (!$hasActiveLeagues) {
            if ($request->routeIs('manager.onboarding.*')) {
                return $next($request);
            }
            return redirect()->route('manager.onboarding.welcome', ['locale' => app()->getLocale()]);
        }

        return $next($request);
    }
}