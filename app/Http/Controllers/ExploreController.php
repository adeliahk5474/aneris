<?php

namespace App\Http\Controllers;

use App\Models\Artwork; // pastikan model ada

class ExploreController extends Controller
{
    public function index()
    {
        $explore = Artwork::latest()->take(30)->get(); // sementara ambil 30 artwork acak

        return view('page.explore', compact('explore'));
    }
}
