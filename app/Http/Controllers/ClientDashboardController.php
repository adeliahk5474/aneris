<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\Chat;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Active Orders
        $activeOrders = Order::with(['artist', 'transaction'])
            ->where('client_id', $user->user_id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();

        // history Orders
        $orderHistory = Order::with(['artist', 'transaction'])
            ->where('client_id', $user->user_id)
            ->where('status', 'completed')
            ->latest()
            ->get();
        // Hasil Order / Artwork (diambil dari order yang selesai)
        $results = Order::with(['artist', 'transaction'])
            ->where('client_id', $user->user_id)
            ->where('status', 'completed')
            ->latest()
            ->get();

        // Transactions terkait semua order client
        $transactions = Transaction::whereIn(
            'order_id',
            Order::where('client_id', $user->user_id)->pluck('order_id')
        )->get();

        // Reviews terkait order client
        $reviews = Review::whereIn(
            'order_id',
            Order::where('client_id', $user->user_id)->pluck('order_id')
        )->with('artist')->get();

        // Chats terkait client (sender atau receiver)
        $chats = Chat::with(['sender', 'receiver'])
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->user_id)
                  ->orWhere('receiver_id', $user->user_id);
            })
            ->orderBy('sent_at', 'desc')
            ->get();

        return view('dashboards.client', compact(
            'user',
            'activeOrders',
            'orderHistory',
            'results',
            'transactions',
            'reviews',
            'chats'
        ));
    }
}
