<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Report;
use App\Models\Sighting;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'petCount' => Pet::count(),
            'activeReportCount' => Report::where('status', 'active')->count(),
            'sightingCount' => Sighting::count(),
            'recentReports' => Report::with('pet')->latest()->limit(5)->get(),
        ]);
    }
}
