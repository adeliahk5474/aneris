<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


use App\Models\Artwork;
use App\Models\CommissionService;
use App\Models\Category;

class UploadController extends Controller
{

    public function popup()
    {
        $categories = Category::all(); // ambil semua kategori dari DB
        return view('page.popup', compact('categories'));
    }

    /* ===============================
        UPLOAD ARTWORK
    ================================ */
    public function uploadArtwork(Request $request)
    {
        $request->validate([
            'caption' => 'nullable|string',
            'image'   => 'required|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        $file      = $request->file('image');
        $filename  = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path      = $file->storeAs('artworks', $filename, 'public');
        $url       = asset('storage/' . $path);

        Artwork::create([
            'artwork_id' => Str::uuid(),
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'caption'   => $request->caption,
            'image_url' => $url,
            'status'    => 'published',
        ]);

        return redirect()->route('home')->with('success', 'Artwork uploaded!');
    }


    /* ===============================
        UPLOAD COMMISSION SERVICE
    ================================ */
    public function uploadCommission(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric',
            'image'       => 'required|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        $file      = $request->file('image');
        $filename  = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path      = $file->storeAs('commission_services', $filename, 'public');
        $url       = asset('storage/' . $path);

        CommissionService::create([
            'artist_id'   => Auth::id(),
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'image_url'   => $url,
            'status'      => 'active',
        ]);

        return back()->with('success', 'Commission service uploaded!');
    }
}
