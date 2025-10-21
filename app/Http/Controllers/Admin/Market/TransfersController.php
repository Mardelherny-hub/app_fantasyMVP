<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class TransfersController extends Controller
{
    public function index(): View
    {
        return view('admin.market.transfers.index');
    }
}