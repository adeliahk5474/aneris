<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function tabs(): array
    {
        return [
            ''           => ['label' => 'Semua',     'icon' => 'bi-bell'],
            'order'      => ['label' => 'Order',      'icon' => 'bi-bag-check'],
            'commission' => ['label' => 'Commission', 'icon' => 'bi-palette'],
            'review'     => ['label' => 'Review',     'icon' => 'bi-star'],
            'like'       => ['label' => 'Like',       'icon' => 'bi-heart'],
            'system'     => ['label' => 'System',     'icon' => 'bi-gear'],
        ];
    }

    public function index(Request $request)
    {
        $user  = Auth::user();
        $type  = $request->get('type', '');

        $query = Notification::where('user_id', $user->user_id)
            ->latest('created_at');

        if (!empty($type) && $type !== 'all') {
            $query->where('type', $type);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $tabs          = $this->tabs();

        return view('pages.notifications', compact('notifications', 'type', 'tabs'));
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::user()->user_id)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    public function markRead($notifId)
    {
        Notification::where('notif_id', $notifId)
            ->where('user_id', Auth::user()->user_id)
            ->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::user()->user_id)
            ->where('status', 'unread')
            ->count();

        return response()->json(['count' => $count]);
    }
}
