@extends('layouts.app')

@section('content')

<style>
    .cart-container {
        max-width: 700px;
        margin: auto;
        padding: 10px;
    }

    .cart-item {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .cart-title {
        font-weight: 600;
        font-size: 15px;
    }

    .cart-meta {
        font-size: 13px;
        color: #555;
    }

    .cart-status {
        font-size: 12px;
        margin-top: 5px;
        font-weight: 600;
    }

    .status-pending {
        color: orange;
    }

    .status-progress {
        color: blue;
    }

    .status-done {
        color: green;
    }

    .status-cancel {
        color: red;
    }
</style>

<div class="cart-container">

    <h5 style="margin-bottom:10px;">My Cart</h5>

    @forelse($orders as $order)

    <div class="cart-item">

        <div class="cart-title">
            {{ $order->service->title ?? 'Service' }}
        </div>

        <div class="cart-meta">
            by {{ $order->artist->name ?? 'Unknown Artist' }}
        </div>

        <div class="cart-meta">
            Rp {{ number_format($order->total_price, 0, ',', '.') }}
        </div>

        <div class="cart-status
                @if($order->status == 'pending') status-pending
                @elseif($order->status == 'in_progress') status-progress
                @elseif($order->status == 'completed') status-done
                @elseif($order->status == 'canceled') status-cancel
                @endif
            ">
            {{ strtoupper($order->status) }}
        </div>

        @if($order->result_file)
        <a href="{{ asset('storage/'.$order->result_file) }}" target="_blank">
            View Result
        </a>
        @endif

    </div>

    @empty

    <p>No orders yet</p>

    @endforelse

</div>

@endsection