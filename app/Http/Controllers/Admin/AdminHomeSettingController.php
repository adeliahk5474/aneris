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
            'hero_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'banner1_title'    => 'required|string|max:100',
            'banner1_subtitle' => 'nullable|string|max:300',
            'banner1_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'banner2_title'    => 'required|string|max:100',
            'banner2_subtitle' => 'nullable|string|max:300',
            'banner2_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Simpan field teks — cast ke string agar tidak null
        $textKeys = [
            'hero_title', 'hero_subtitle',
            'banner1_title', 'banner1_subtitle',
            'banner2_title', 'banner2_subtitle',
        ];
        foreach ($textKeys as $key) {
            HomeSetting::set($key, (string) ($request->input($key) ?? ''));
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
                    } catch (\Throwable) {}
                }

                // Upload ke Cloudinary — compatible dengan cloudinary-labs v3
                try {
                    $uploaded = cloudinary()->uploadFile(
                        $request->file($key)->getRealPath(),
                        ['folder' => 'aneris/home-settings']
                    )->getResponse();

                    HomeSetting::set($key, (string) ($uploaded['secure_url'] ?? ''));
                    HomeSetting::set("{$key}_public_id", (string) ($uploaded['public_id'] ?? ''));

                } catch (\Throwable $e) {
                    // Fallback: coba pakai upload() biasa
                    try {
                        $result = cloudinary()->upload(
                            $request->file($key)->getRealPath(),
                            ['folder' => 'aneris/home-settings']
                        );

                        $secureUrl = null;
                        $publicId  = null;

                        // Handle berbagai return type Cloudinary
                        if (is_array($result)) {
                            $secureUrl = $result['secure_url'] ?? null;
                            $publicId  = $result['public_id'] ?? null;
                        } elseif (is_object($result)) {
                            if (method_exists($result, 'getSecurePath')) {
                                $secureUrl = $result->getSecurePath();
                            } elseif (method_exists($result, 'getResponse')) {
                                $resp      = $result->getResponse();
                                $secureUrl = $resp['secure_url'] ?? null;
                                $publicId  = $resp['public_id'] ?? null;
                            }
                            if (method_exists($result, 'getPublicId')) {
                                $publicId = $result->getPublicId();
                            }
                        }

                        if ($secureUrl) {
                            HomeSetting::set($key, (string) $secureUrl);
                            HomeSetting::set("{$key}_public_id", (string) ($publicId ?? ''));
                        }

                    } catch (\Throwable) {
                        // Gagal upload — lanjut tanpa ubah gambar
                    }
                }
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
