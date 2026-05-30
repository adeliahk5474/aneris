<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Chat;
use App\Models\Order;
use App\Models\User;
use App\Events\MessageSent;

class ChatController extends Controller
{
    /* ===============================
       HELPER — ambil semua conversations
    =============================== */
    private function getConversations(string $userId)
    {
        $chats = Chat::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->get();

        return $chats->groupBy(function ($chat) use ($userId) {
            return $chat->order_id
                ? 'order_' . $chat->order_id
                : 'dm_' . ($chat->sender_id == $userId ? $chat->receiver_id : $chat->sender_id);
        })->map(fn($group) => $group->first());
    }

    private function enrichConversationsForThread($conversations, string $userId, User $otherUser, ?Order $order = null)
    {
        return $conversations->map(function ($conv) use ($userId, $otherUser, $order) {
            $other = $conv->sender_id === $userId ? $conv->receiver : $conv->sender;
            $conv->other = $other;
            $conv->isActive = ($otherUser->user_id ?? '') === ($other->user_id ?? '')
                || ($order && $order->order_id === $conv->order_id);
            return $conv;
        });
    }

    private function enrichChats($chats, string $userId)
    {
        return $chats->map(function ($chat) use ($userId) {
            $chat->isMine = $chat->sender_id === $userId;
            return $chat;
        });
    }

    /* ===============================
       CHAT LIST
    =============================== */
    public function list()
    {
        $userId       = Auth::user()->user_id;
        $focusUserId  = request('user_id');
        $focusOrderId = request('order_id');

        $conversations = $this->getConversations($userId)->map(function ($conv) use ($userId, $focusUserId, $focusOrderId) {
            $other = $conv->sender_id === $userId ? $conv->receiver : $conv->sender;
            $conv->other    = $other;
            $conv->isActive = $focusUserId === ($other->user_id ?? '') ||
                $focusOrderId === ($conv->order_id ?? '');
            return $conv;
        });

        return view('pages.chat_list', compact('conversations'));
    }

    /* ===============================
       CHAT THREAD
    =============================== */
    public function index(Request $request)
    {
        $userId   = Auth::user()->user_id;
        $order_id = $request->order_id;
        $user_id  = $request->user_id;

        $rawConversations = $this->getConversations($userId);

        /* ORDER CHAT */
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

            $conversations = $this->enrichConversationsForThread($rawConversations, $userId, $otherUser, $order);
            $chats         = $this->enrichChats($chats, $userId);

            return view('pages.chat_thread', compact(
                'chats',
                'order',
                'otherUser',
                'conversations'
            ));
        }

        /* DM CHAT */
        if ($user_id) {
            $otherUser = User::where('user_id', $user_id)->firstOrFail();

            $chats = Chat::with(['sender'])
                ->whereNull('order_id')
                ->where(function ($q) use ($userId, $user_id) {
                    $q->where(function ($q2) use ($userId, $user_id) {
                        $q2->where('sender_id', $userId)->where('receiver_id', $user_id);
                    })->orWhere(function ($q2) use ($userId, $user_id) {
                        $q2->where('sender_id', $user_id)->where('receiver_id', $userId);
                    });
                })
                ->orderBy('created_at', 'asc')
                ->get();

            Chat::whereNull('order_id')
                ->where('receiver_id', $userId)
                ->where('sender_id', $user_id)
                ->update(['is_read' => true]);

            $conversations = $this->enrichConversationsForThread($rawConversations, $userId, $otherUser);
            $chats         = $this->enrichChats($chats, $userId);

            return view('pages.chat_thread', compact(
                'chats',
                'otherUser',
                'conversations'
            ));
        }

        abort(404);
    }

    /* ===============================
       SEND CHAT
    =============================== */
    public function send(Request $request)
    {
        $request->validate([
            'order_id'    => 'nullable|exists:orders,order_id',
            'receiver_id' => 'required|exists:users,user_id',
            'message'     => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = \App\Services\CloudinaryService::upload(
                $request->file('image'),
                'chats'
            );
        }

        $chat = Chat::create([
            'chat_id'     => Str::uuid(),
            'order_id'    => $request->order_id,
            'sender_id'   => Auth::user()->user_id,
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'image'       => $imageUrl,
            'is_read'     => false,
        ]);

        $chat->load('sender');
        broadcast(new MessageSent($chat))->toOthers();

        return back();
    }

    /* ===============================
       FETCH CHAT (REALTIME SUPPORT / FALLBACK)
    =============================== */
    public function fetch(Request $request)
    {
        $userId   = Auth::user()->user_id;
        $order_id = $request->order_id;
        $user_id  = $request->user_id;

        if ($order_id) {
            $chats = Chat::with(['sender'])
                ->where('order_id', $order_id)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json(['type' => 'order', 'data' => $chats]);
        }

        $chats = Chat::with(['sender'])
            ->whereNull('order_id')
            ->where(function ($q) use ($userId, $user_id) {
                $q->where(fn($q2) => $q2->where('sender_id', $userId)->where('receiver_id', $user_id))
                    ->orWhere(fn($q2) => $q2->where('sender_id', $user_id)->where('receiver_id', $userId));
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['type' => 'dm', 'data' => $chats]);
    }

    public function activeChannels()
    {
        $userId = Auth::user()->user_id;
        $conversations = $this->getConversations($userId);

        $channels = $conversations->map(function ($conv) use ($userId) {
            if ($conv->order_id) {
                return [
                    'channel' => 'order.' . $conv->order_id,
                    'href'    => '/chat/thread?order_id=' . $conv->order_id,
                ];
            }
            $otherId = $conv->sender_id === $userId ? $conv->receiver_id : $conv->sender_id;
            $ids = [$userId, $otherId];
            sort($ids);
            return [
                'channel' => 'dm.' . implode('.', $ids),
                'href'    => '/chat/thread?user_id=' . $otherId,
            ];
        })->values();

        return response()->json($channels);
    }

    public function unreadCount()
    {
        $userId = Auth::user()->user_id;
        $count = Chat::where('receiver_id', $userId)
            ->where('is_read', false)
            ->distinct('sender_id')
            ->count('sender_id');

        return response()->json(['count' => $count]);
    }
}
