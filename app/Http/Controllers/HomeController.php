<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\CommissionService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
public function index()
{
    // Ambil commission terbaru atau artwork terbaru
        // limit memory usage by paginating the feed (loads only a page at a time)
        $feed = Artwork::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(24);

    return view('homepage.home', compact('feed'));
}

}
