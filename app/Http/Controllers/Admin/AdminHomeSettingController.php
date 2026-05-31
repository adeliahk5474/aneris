<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSetting;
use Illuminate\Http\Request;

class AdminHomeSettingController extends Controller
{
    public function edit()
    {
        $settings = HomeSetting::getAllKeyed();
        return view('admin.home-setting', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_title'       => 'required|string|max:200',
            'hero_subtitle'    => 'nullable|string|max:300',
            'hero_image'       => 'nullable|image|max:5120',

            'banner1_title'    => 'required|string|max:100',
            'banner1_subtitle' => 'nullable|string|max:300',
            'banner1_color'    => 'required|string|max:20',
            'banner1_image'    => 'nullable|image|max:5120',

            'banner2_title'    => 'required|string|max:100',
            'banner2_subtitle' => 'nullable|string|max:300',
            'banner2_color'    => 'required|string|max:20',
            'banner2_image'    => 'nullable|image|max:5120',
        ]);

        // Simpan field teks
        $textKeys = [
            'hero_title', 'hero_subtitle',
            'banner1_title', 'banner1_subtitle', 'banner1_color',
            'banner2_title', 'banner2_subtitle', 'banner2_color',
        ];
        foreach ($textKeys as $key) {
            HomeSetting::set($key, $request->input($key, ''));
        }

        // Upload gambar ke Cloudinary
        $imageKeys = ['hero_image', 'banner1_image', 'banner2_image'];
        foreach ($imageKeys as $key) {
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                // Hapus gambar lama dari Cloudinary jika ada
                $oldPublicId = HomeSetting::get("{$key}_public_id");
                if ($oldPublicId) {
                    try {
                        cloudinary()->destroy($oldPublicId);
                    } catch (\Throwable) {
                        // Lanjut meski gagal hapus
                    }
                }

                // Upload ke Cloudinary folder home-settings
                $result = cloudinary()->upload(
                    $request->file($key)->getRealPath(),
                    [
                        'folder'         => 'aneris/home-settings',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto',
                        ],
                    ]
                );

                HomeSetting::set($key, $result->getSecurePath());
                HomeSetting::set("{$key}_public_id", $result->getPublicId());
            }
        }

        return back()->with('success', 'Tampilan homepage berhasil diperbarui.');
    }

    public function removeImage(Request $request)
    {
        $request->validate([
            'key' => 'required|in:hero_image,banner1_image,banner2_image',
        ]);

        $key      = $request->key;
        $publicId = HomeSetting::get("{$key}_public_id");

        if ($publicId) {
            try {
                cloudinary()->destroy($publicId);
            } catch (\Throwable) {}
        }

        HomeSetting::set($key, '');
        HomeSetting::set("{$key}_public_id", '');

        return back()->with('success', 'Gambar berhasil dihapus.');
    }
}
