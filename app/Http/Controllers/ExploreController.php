<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Artwork;
use App\Models\CommissionService;
use App\Models\User;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        // ===============================
        // SEARCH
        // ===============================
        $search = $request->search;

        // ===============================
        // USERS SEARCH
        // ===============================
        $users = User::when($search, function ($q) use ($search) {

                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('username', 'ILIKE', "%{$search}%");

            })
            ->latest()
            ->take(20)
            ->get();

        // ===============================
        // ARTWORKS
        // ===============================
        $artworks = Artwork::when($search, function ($q) use ($search) {

                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");

            })
            ->latest()
            ->take(30)
            ->get();

        // ===============================
        // COMMISSION SERVICES
        // ===============================
        $services = CommissionService::where('status', 'active')
            ->when($search, function ($q) use ($search) {

                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");

            })
            ->latest()
            ->take(20)
            ->get();

        // ===============================
        // EXPLORE FEED
        // ===============================
        $explore = $artworks->map(function ($item) {

            $item->type = 'artwork';

            return $item;

        })->merge(

            $services->map(function ($item) {

                $item->type = 'service';

                return $item;

            })

        )->shuffle();

        // ===============================
        // RETURN VIEW
        // ===============================
        return view('page.explore', compact(
            'explore',
            'users',
            'artworks',
            'services',
            'search'
        ));
    }
}
