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
            'banner1_title'    => 'required|string|max:100',
            'banner1_subtitle' => 'nullable|string|max:300',
            'banner1_color'    => 'required|string|max:20',
            'banner2_title'    => 'required|string|max:100',
            'banner2_subtitle' => 'nullable|string|max:300',
            'banner2_color'    => 'required|string|max:20',
        ]);

        $keys = [
            'hero_title',
            'hero_subtitle',
            'banner1_title',
            'banner1_subtitle',
            'banner1_color',
            'banner2_title',
            'banner2_subtitle',
            'banner2_color',
        ];

        foreach ($keys as $key) {
            HomeSetting::set($key, $request->input($key, ''));
        }

        return back()->with('success', 'Tampilan homepage berhasil diperbarui.');
    }
}
