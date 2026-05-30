<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::where('order_id', $orderId)->first();

    if (!$order) return false;

    return (string) $user->user_id === (string) $order->client_id
        || (string) $user->user_id === (string) $order->artist_id;
});

// ── DM CHANNEL ──
Broadcast::channel('dm.{userA}.{userB}', function ($user, $userA, $userB) {
    return (string) $user->user_id === (string) $userA
        || (string) $user->user_id === (string) $userB;
});
