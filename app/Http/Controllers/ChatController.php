<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Chat;
use App\Models\Order;
use App\Models\User;

class ChatController extends Controller
{
    /**
     * ===============================
     * CHAT LIST (HYBRID)
     * ===============================
     */
    public function list()
    {
        $userId = Auth::user()->user_id;

        $chats = Chat::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->get();

        $conversations = $chats->groupBy(function ($chat) use ($userId) {

            if ($chat->order_id) {
                return 'order_' . $chat->order_id;
            }

            $otherUser = $chat->sender_id == $userId
                ? $chat->receiver_id
                : $chat->sender_id;

            return 'dm_' . $otherUser;

        })->map(function ($group) {
            return $group->first();
        });

        return view('pages.chat_list', compact('conversations'));
    }

    /**
     * ===============================
     * CHAT THREAD
     * ===============================
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->user_id;

        $order_id = $request->order_id;
        $user_id  = $request->user_id;

        /**
         * ORDER CHAT
         */
        if ($order_id) {

            $order = Order::with(['client', 'artist'])
                ->where('order_id', $order_id)
                ->firstOrFail();

            $otherUser = $order->client_id == $userId
                ? $order->artist
                : $order->client;

            $chats = Chat::with(['sender'])
                ->where('order_id', $order_id)
                ->orderBy('created_at', 'asc')
                ->get();

            Chat::where('order_id', $order_id)
                ->where('receiver_id', $userId)
                ->update(['is_read' => true]);

            return view('pages.chat_thread', compact(
                'chats',
                'order',
                'otherUser'
            ));
        }

        /**
         * DM CHAT
         */
        if ($user_id) {

            $otherUser = User::where('user_id', $user_id)
                ->firstOrFail();

            $chats = Chat::with(['sender'])
                ->whereNull('order_id')
                ->where(function ($q) use ($userId, $user_id) {

                    $q->where(function ($q2) use ($userId, $user_id) {
                        $q2->where('sender_id', $userId)
                            ->where('receiver_id', $user_id);
                    })->orWhere(function ($q2) use ($userId, $user_id) {
                        $q2->where('sender_id', $user_id)
                            ->where('receiver_id', $userId);
                    });

                })
                ->orderBy('created_at', 'asc')
                ->get();

            Chat::whereNull('order_id')
                ->where('receiver_id', $userId)
                ->where('sender_id', $user_id)
                ->update(['is_read' => true]);

            return view('pages.chat_thread', compact(
                'chats',
                'otherUser'
            ));
        }

        abort(404);
    }

    /**
     * ===============================
     * SEND CHAT
     * ===============================
     */
    public function send(Request $request)
    {
        $request->validate([
            'order_id' => 'nullable|exists:orders,order_id',
            'receiver_id' => 'required|exists:users,user_id',
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $path = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')
                ->store('chat_images', 'public');
        }

        Chat::create([
            'chat_id' => Str::uuid(),
            'order_id' => $request->order_id,
            'sender_id' => Auth::user()->user_id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'image' => $path,
            'is_read' => false
        ]);

        return back();
    }

    /**
     * ===============================
     * FETCH CHAT (REALTIME SUPPORT)
     * ===============================
     */
    public function fetch(Request $request)
    {
        $userId = Auth::user()->user_id;

        $order_id = $request->order_id;
        $user_id  = $request->user_id;

        /**
         * ORDER CHAT
         */
        if ($order_id) {

            $chats = Chat::with(['sender'])
                ->where('order_id', $order_id)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'type' => 'order',
                'data' => $chats
            ]);
        }

        /**
         * DM CHAT
         */
        $chats = Chat::with(['sender'])
            ->whereNull('order_id')
            ->where(function ($q) use ($userId, $user_id) {

                $q->where(function ($q2) use ($userId, $user_id) {
                    $q2->where('sender_id', $userId)
                        ->where('receiver_id', $user_id);
                })->orWhere(function ($q2) use ($userId, $user_id) {
                    $q2->where('sender_id', $user_id)
                        ->where('receiver_id', $userId);
                });

            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'type' => 'dm',
            'data' => $chats
        ]);
    }
}
