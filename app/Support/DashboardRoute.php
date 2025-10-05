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
            return route('manager.dashboard', ['locale' => $locale]);
        }

        return route('dashboard', ['locale' => $locale]);
    }
}
