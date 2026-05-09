<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Follow;

class FollowController extends Controller
{
    // ===============================
    // FOLLOW / UNFOLLOW
    // ===============================
    public function toggle($id)
    {
        $user = Auth::user();

        // cegah follow diri sendiri
        if ($user->user_id == $id) {
            return back();
        }

        // cek sudah follow belum
        $existing = Follow::where('follower_id', $user->user_id)
            ->where('following_id', $id)
            ->first();

        // ===============================
        // UNFOLLOW
        // ===============================
        if ($existing) {

            $existing->delete();

        } else {

            // ===============================
            // FOLLOW
            // ===============================
            Follow::create([
                'follow_id' => Str::uuid(),

                'follower_id' => $user->user_id,

                'following_id' => $id
            ]);
        }

        return back();
    }
}
