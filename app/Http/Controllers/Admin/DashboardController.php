<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale); // opcional si no tenés un middleware de locale
        return view('admin.dashboard');
    }
}
