<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Artist;

class ArtistDashboardController extends Controller
{
    /**
     * Tampilkan dashboard untuk artist yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Pastikan user adalah artist dan punya relasi data artist
        $artist = Artist::where('user_id', $user->id)->first();

        // Jika belum ada data artist, arahkan untuk melengkapi profil
        if (!$artist) {
            return redirect()->route('auth.page')->with('error', 'Profil artist belum dibuat.');
        }

        // Kirim data ke view dashboard
        return view('dashboard.artist', [
            'user' => $user,
            'artist' => $artist,
        ]);
    }
}
