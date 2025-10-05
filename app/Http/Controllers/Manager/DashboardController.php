<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale); // opcional
        return view('manager.dashboard');
    }
}
