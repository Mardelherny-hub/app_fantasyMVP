<?php

namespace App\Http\Controllers;

use App\Support\DashboardRoute;
use Illuminate\Http\Request;

class DashboardRouterController extends Controller
{
    public function redirect(Request $request, string $locale)
    {
        app()->setLocale($locale);
        return redirect()->to(DashboardRoute::for($request->user()));
    }
}
