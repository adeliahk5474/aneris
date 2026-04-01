<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    // ===========================
    // FEED / HOMEPAGE
    // ===========================
    public function feed()
    {
        $feed = Artwork::with('artist')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($feed as $art) {
            $art->liked = session()->has("liked_{$art->id}");
        }

        return view('homepage.home', compact('feed'));
    }

    // ===========================
    // UPLOAD ARTWORK
    // ===========================
    public function store(Request $request)
    {
        $request->validate([
            'artwork' => 'required|image|max:5000',
            'caption' => 'nullable|string|max:255',
            'category_id' => 'nullable|uuid'
        ]);

        $path = $request->file('artwork')->store('artworks', 'public');

        $art = new Artwork();
        $art->user_id = auth()->id() ?? null;
        $art->caption = $request->caption;
        $art->category_id = $request->category_id;
        $art->image_url = asset('storage/' . $path);
        $art->status = 'published';
        $art->likes = 0;
        $art->save();

        return back()->with('success', 'Uploaded');
    }

    // ===========================
    // LIKE / UNLIKE
    // ===========================
    public function like($id)
    {
        $art = Artwork::findOrFail($id);

        if ($art->likes === null) {
            $art->likes = 0;
        }

        $likedSession = "liked_$id";

        if (!session()->has($likedSession)) {
            $art->likes++;
            session()->put($likedSession, true);
            $status = "liked";
        } else {
            if ($art->likes > 0) {
                $art->likes--;
            }
            session()->forget($likedSession);
            $status = "unliked";
        }

        $art->save();

        return response()->json([
            'status' => $status,
            'likes' => $art->likes
        ]);
    }

    // ===========================
    // EDIT ARTWORK (halaman edit)
    // ===========================
    public function edit($id)
    {
        $artwork = Artwork::where('artwork_id', $id)->firstOrFail();

        if ($artwork->user_id !== Auth::id()) {
            abort(403);
        }

        return view('artwork.edit', compact('artwork'));
    }

    // ===========================
    // UPDATE ARTWORK DARI MODAL
    // ===========================
    public function updateFromModal(Request $request)
    {
        $artwork = Artwork::where('artwork_id', $request->artwork_id)->firstOrFail();

        if ($artwork->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'caption' => 'nullable|string|max:255',
            'category_id' => 'nullable|uuid',
            'image' => 'nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('artworks', 'public');
            $artwork->image_url = asset('storage/' . $path);
        }

        $artwork->caption = $request->caption;
        $artwork->category_id = $request->category_id;
        $artwork->save();

        return redirect()->back()->with('success', 'Artwork updated');
    }

    // ===========================
    // DELETE ARTWORK DARI MODAL
    // ===========================
    public function destroyFromModal(Request $request)
    {
        $artwork = Artwork::where('artwork_id', $request->artwork_id)->firstOrFail();

        if ($artwork->user_id !== Auth::id()) {
            abort(403);
        }

        $artwork->delete();

        return redirect()->back()->with('success', 'Artwork deleted');
    }
}
