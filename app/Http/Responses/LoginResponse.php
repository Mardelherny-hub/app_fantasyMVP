<?php

namespace App\Http\Responses;

use App\Support\DashboardRoute;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Si el usuario intentaba acceder a una URL protegida, respetar "intended"
        $intended = redirect()->intended()->getTargetUrl();
        $home = DashboardRoute::for($request->user());

        // Si "intended" apunta al login o raíz, mejor usá el home por rol
        if (!$intended || str_contains($intended, route('login'))) {
            return redirect()->to($home);
        } 

        return redirect()->intended($home);
    }
}
