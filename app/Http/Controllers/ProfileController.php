<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // ===========================
    // TAMPILKAN PROFILE
    // ===========================
    public function show($id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        $isOwner = auth()->check() && auth()->user()->user_id === $user->user_id;

        $artworks = $user->artworks()->latest()->get();

        return view('dashboards.profile', compact('user', 'isOwner', 'artworks'));
    }
    // ===========================
    // UPDATE PROFILE
    // ===========================
    public function update(Request $request, $id)
{
    $user = User::where('user_id', $id)->firstOrFail();

    // Update name
    $user->name = $request->name;

    // Update avatar jika ada file baru
    if($request->hasFile('profile_picture')){
        // Hapus file lama jika ada
        if($user->avatar && file_exists(public_path($user->avatar))){
            unlink(public_path($user->avatar));
        }

        // Simpan file baru ke storage/app/public/profile_pictures
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $user->avatar = '/storage/'.$path; // path yang bisa diakses di browser
    }

    $user->save();

    return redirect()->route('profile.show', $user->user_id)
                     ->with('success', 'Profile updated successfully.');
}


}
