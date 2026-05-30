<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioVerification;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending'   => PortfolioVerification::where('status', 'pending')->count(),
            'in_review' => PortfolioVerification::where('status', 'in_review')->count(),
            'approved'  => PortfolioVerification::where('status', 'approved')->count(),
            'rejected'  => PortfolioVerification::where('status', 'rejected')->count(),
        ];

        $recent = PortfolioVerification::with('artist')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent'));
    }
}
