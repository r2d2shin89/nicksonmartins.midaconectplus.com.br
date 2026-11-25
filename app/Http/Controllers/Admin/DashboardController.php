<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Property;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard overview.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'propertyCount' => Property::count(),
            'messageCount' => Message::count(),
            'recentProperties' => Property::latest()->limit(5)->get(),
            'recentMessages' => Message::latest()->limit(5)->get(),
        ]);
    }
}
