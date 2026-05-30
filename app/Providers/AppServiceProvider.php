<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Notification;
use App\Models\Chat;
use App\Observers\ReviewObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Review::observe(ReviewObserver::class);

        // Shared helper closure
        $getAuthBase = function () {
            $authUser = auth()->user();
            return [
                'authUser' => $authUser,
                'isArtist' => $authUser && $authUser->role === 'artist',
            ];
        };

        View::composer('layouts.app', function ($view) use ($getAuthBase) {
            $base    = $getAuthBase();
            $authUser = $base['authUser'];

            $unreadNotifCount = 0;
            $unreadChatCount  = 0;

            if ($authUser) {
                $unreadNotifCount = Notification::where('user_id', $authUser->user_id)
                    ->where('status', 'unread')
                    ->count();

                $unreadChatCount = Chat::where('receiver_id', $authUser->user_id)
                    ->where('is_read', false)
                    ->distinct('sender_id')
                    ->count('sender_id');
            }

            $view->with(array_merge($base, compact('unreadNotifCount', 'unreadChatCount')));
        });

        View::composer('layouts.ordernav', function ($view) use ($getAuthBase) {
            $base = $getAuthBase();

            $unreadChatCount = 0;
            if ($base['authUser']) {
                $unreadChatCount = Chat::where('receiver_id', $base['authUser']->user_id)
                    ->where('is_read', false)
                    ->distinct('sender_id')
                    ->count('sender_id');
            }

            $view->with(array_merge($base, compact('unreadChatCount')));
        });

        View::composer('layouts.topnav', function ($view) use ($getAuthBase) {
            $base = $getAuthBase();

            $unreadChatCount = 0;
            if ($base['authUser']) {
                $unreadChatCount = Chat::where('receiver_id', $base['authUser']->user_id)
                    ->where('is_read', false)
                    ->distinct('sender_id')
                    ->count('sender_id');
            }

            $view->with(array_merge($base, compact('unreadChatCount')));
        });
    }
}
