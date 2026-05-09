<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;
use App\Models\CommissionService;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:commission_services,service_id',
            'payment_method' => 'required|string',
            'note' => 'nullable|string'
        ]);

        $service = CommissionService::where('service_id', $request->service_id)->firstOrFail();

        Order::create([
            'order_id' => Str::uuid(),

            'service_id' => $service->service_id,
            'client_id' => Auth::user()->user_id,
            'artist_id' => $service->artist_id,

            'note' => $request->note,
            'payment_method' => $request->payment_method,

            'total_price' => $service->price,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Order berhasil dibuat');
    }

    public function pay(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:commission_services,service_id',
            'payment_method' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $service = CommissionService::where('service_id', $request->service_id)->firstOrFail();

        Order::create([
            'order_id' => Str::uuid(),

            'service_id' => $service->service_id,
            'client_id' => Auth::user()->user_id,
            'artist_id' => $service->artist_id,

            'note' => $request->note,
            'payment_method' => $request->payment_method,

            'total_price' => $service->price,
            'status' => 'pending'
        ]);

        return redirect()->route('explore')
            ->with('success', 'Payment berhasil & order dibuat');
    }

    public function cart()
    {
        $orders = Order::with(['artist'])
            ->where('client_id', auth()->id())
            ->latest()
            ->get();

        return view('pages.cart', compact('orders'));
    }

    /* =========================
       ARTIST ACCEPT ORDER
    ========================= */
    public function accept(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->artist_id !== auth()->user()->user_id) {
            abort(403);
        }

        $order->status = 'in_progress';
        $order->save();

        return back()->with('success', 'Order accepted');
    }

    public function reject(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->artist_id !== auth()->user()->user_id) {
            abort(403);
        }

        $order->status = 'canceled';
        $order->save();

        return back()->with('success', 'Order rejected');
    }

    /* =========================
       ARTIST SEND RESULT
       (sketsa / coloring)
       masuk ke waiting client
    ========================= */
    public function sendToClient(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->artist_id !== auth()->user()->user_id) {
            abort(403);
        }

        if ($request->hasFile('result_file')) {
            $file = $request->file('result_file')->store('orders', 'public');
            $order->result_file = $file;
        }

        $order->status = 'waiting_client';
        $order->save();

        return back()->with('success', 'Dikirim ke client untuk review');
    }

    /* =========================
       CLIENT REVISION
       max 2x (logic nanti bisa ditambah counter)
    ========================= */
    public function revision(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->client_id !== auth()->user()->user_id) {
            abort(403);
        }

        if ($order->status !== 'waiting_client') {
            return back()->with('error', 'Tidak bisa revisi sekarang');
        }

        $order->status = 'revision';
        $order->save();

        return back()->with('success', 'Masuk tahap revisi');
    }

    /* =========================
       CLIENT ACCEPT FINAL RESULT
    ========================= */
    public function acceptFinal(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->firstOrFail();

        if ($order->client_id !== auth()->user()->user_id) {
            abort(403);
        }

        if ($order->status !== 'waiting_client') {
            return back();
        }

        $order->status = 'completed';
        $order->save();

        return back()->with('success', 'Order selesai');
    }
}
