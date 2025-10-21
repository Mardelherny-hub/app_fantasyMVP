<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingsManagementController extends Controller
{
    /**
     * Display all listings
     */
    public function index(): View
    {
        return view('admin.market.listings.index');
    }

    /**
     * Cancel a listing (moderation)
     */
    public function cancel(Listing $listing)
    {
        $listing->update(['status' => Listing::STATUS_WITHDRAWN]);
        
        $listing->offers()
            ->where('status', \App\Models\Offer::STATUS_PENDING)
            ->update(['status' => \App\Models\Offer::STATUS_REJECTED]);

        return back()->with('success', __('Listing cancelled successfully.'));
    }
}