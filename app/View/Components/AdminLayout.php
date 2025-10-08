<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AdminLayout extends Component
{
    public function render(): View
    {
        return view('components.admin-layout');  // ← Apunta a components/
    }
}