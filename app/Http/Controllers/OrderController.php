<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;

public function store(Request $request)
{
    $request->validate([
        'service_id' => 'required',
        'payment_method' => 'required',
    ]);

    Order::create([
        'order_id' => Str::uuid(),
        'user_id' => auth()->id(),
        'service_id' => $request->service_id,
        'note' => $request->note,
        'payment_method' => $request->payment_method,
        'status' => 'pending',
    ]);

    return redirect()->back()->with('success', 'Order created!');
}
