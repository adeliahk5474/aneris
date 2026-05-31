<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\Artwork;
use App\Models\CommissionService;
use App\Models\Category;

class UploadController extends Controller
{

    public function popup()
    {
        $categories   = Category::orderBy('name')->get();
        $isArtist     = Auth::user()->role === 'artist';
        $isVerified   = Auth::user()->canUploadCommission(); // true = boleh buka commission
        $switchToCommissionTab = (bool) (old('title') || old('description'));
        $titleCharCount = strlen(old('title', ''));
        $descCharCount  = strlen(old('description', ''));

        return view('page.popup', compact(
            'categories',
            'isArtist',
            'isVerified',
            'switchToCommissionTab',
            'titleCharCount',
            'descCharCount'
        ));
    }

    /* ===============================
       UPLOAD ARTWORK
    =============================== */
    public function uploadArtwork(Request $request)
    {
        $request->validate([
            'caption'     => 'nullable|string|max:500',
            'image'       => 'required|image|mimes:png,jpg,jpeg,webp|max:8192',
            'category_id' => 'nullable|exists:categories,category_id',
        ]);

        $url = \App\Services\CloudinaryService::upload(
            $request->file('image'),
            'artworks'
        );

        Artwork::create([
            'artwork_id'  => Str::uuid(),
            'user_id'     => Auth::user()->user_id,
            'category_id' => $request->category_id ?: null,
            'caption'     => $request->caption,
            'image_url'   => $url,
            'status'      => 'published',
        ]);

        return redirect()->route('home')->with('success', 'Artwork berhasil diupload!');
    }

    /* ===============================
       UPLOAD COMMISSION SERVICE
    =============================== */
    public function uploadCommission(Request $request)
    {
        if (!Auth::user()->canUploadCommission()) {
            return redirect()->route('artist.dashboard', ['tab' => 'portfolio'])
                ->with('info', 'Kamu perlu terverifikasi dulu sebelum bisa membuka commission.');
        }

        $request->validate([
            'title'          => 'required|string|max:100',
            'description'    => 'required|string|max:2000',
            'category_id'    => 'required|exists:categories,category_id',
            'image'          => 'required|image|mimes:png,jpg,jpeg,webp|max:10240',
            'gallery.*'      => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
            'base_price'     => 'required|numeric|min:1000',
            'turnaround'     => 'nullable|string|max:100',
            'estimated_days' => 'nullable|integer|min:1|max:365',
            'queue_slots'    => 'nullable|integer|min:1|max:50',
            'revision_limit' => 'nullable|integer|min:0|max:20',
            'will_do'        => 'nullable|string|max:2000',
            'wont_do'        => 'nullable|string|max:2000',
            'status'         => 'nullable|in:active,inactive',
        ]);

        $coverUrl = \App\Services\CloudinaryService::upload(
            $request->file('image'),
            'commissions'
        );

        $galleryUrls = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gFile) {
                if (count($galleryUrls) >= 3) break;
                $galleryUrls[] = \App\Services\CloudinaryService::upload(
                    $gFile,
                    'commissions/gallery'
                );
            }
        }

        $addons = [];
        if ($request->has('addons') && is_array($request->addons)) {
            foreach ($request->addons as $addon) {
                if (!empty($addon['name'])) {
                    $addons[] = [
                        'name'        => trim($addon['name']),
                        'description' => trim($addon['description'] ?? ''),
                        'price'       => (int) ($addon['price'] ?? 0),
                    ];
                }
            }
        }

        CommissionService::create([
            'artist_id'      => Auth::user()->user_id,
            'category_id'    => $request->category_id,
            'title'          => $request->title,
            'description'    => $request->description,
            'will_do'        => $request->will_do,
            'wont_do'        => $request->wont_do,
            'image_url'      => $coverUrl,
            'gallery_images' => !empty($galleryUrls) ? $galleryUrls : null,
            'base_price'     => $request->base_price,
            'status'         => $request->status ?? 'active',
            'queue_slots'    => $request->queue_slots ?? 5,
            'estimated_days' => $request->estimated_days ?? 7,
            'max_revisions'  => $request->revision_limit ?? 2,
            'addons'         => !empty($addons) ? $addons : null,
            'avg_rating'     => 0,
            'review_count'   => 0,
            'order_count'    => 0,
            'like_count'     => 0,
        ]);

        return redirect()->route('profile.show', Auth::user()->user_id)
            ->with('success', 'Commission service berhasil dipublish!');
    }
}
