<?php

namespace App\Support;

use App\Models\User;

class DashboardRoute
{
    public static function for(User $user): string
    {
        $locale = request()->route('locale') ?? app()->getLocale();

        if ($user->hasRole('admin')) {
            return route('admin.dashboard', ['locale' => $locale]);
        }

        if ($user->hasRole('manager')) {
            // Verificar si tiene ligas activas
            $hasActiveLeagues = $user->leagueMembers()
                ->where('is_active', true)
                ->exists();
            
            if ($hasActiveLeagues) {
                return route('manager.dashboard', ['locale' => $locale]);
            }
            
            return route('manager.onboarding.welcome', ['locale' => $locale]);
        }

        return route('manager.onboarding.welcome', ['locale' => $locale]);
    }
}