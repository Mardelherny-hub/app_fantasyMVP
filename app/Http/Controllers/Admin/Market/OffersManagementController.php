<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class OffersManagementController extends Controller
{
    /**
     * Display all offers
     */
    public function index(): View
    {
        return view('admin.market.offers.index');
    }
}