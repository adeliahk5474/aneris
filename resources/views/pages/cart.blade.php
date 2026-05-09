@extends('layouts.app')

@section('content')

<style>
    .cart-container {
        max-width: 750px;
        margin: auto;
        padding: 12px;
    }

    .cart-title {
        font-weight: 600;
        font-size: 15px;
    }

    .cart-item {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
    }

    .cart-meta {
        font-size: 13px;
        color: #555;
        margin-top: 2px;
    }

    .status {
        font-size: 12px;
        font-weight: 600;
        margin-top: 6px;
    }

    .status-pending {
        color: orange;
    }

    .status-progress {
        color: blue;
    }

    .status-wait {
        color: #a855f7;
    }

    .status-rev {
        color: #ef4444;
    }

    .status-done {
        color: green;
    }

    .btn {
        border: none;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        cursor: pointer;
        color: #fff;
    }

    .btn-chat {
        background: #7c3aed;
    }

    .btn-view {
        background: #2563eb;
    }

    .cart-actions {
        margin-top: 10px;
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
</style>

<div class="cart-container">

    <h5 style="margin-bottom:12px;">My Orders</h5>

    @forelse($orders as $order)

    <div class="cart-item">

        <div class="cart-title">
            {{ $order->service->title ?? 'Service' }}
        </div>

        <div class="cart-meta">
            Artist: {{ $order->artist->name ?? '-' }}
        </div>

        <div class="cart-meta">
            Rp {{ number_format($order->total_price, 0, ',', '.') }}
        </div>

        {{-- STATUS --}}
        <div class="status
            @if($order->status=='pending') status-pending
            @elseif($order->status=='in_progress') status-progress
            @elseif($order->status=='waiting_client') status-wait
            @elseif($order->status=='revision') status-rev
            @elseif($order->status=='completed') status-done
            @endif
        ">
            {{ strtoupper($order->status) }}
        </div>

        {{-- ACTION --}}
        <div class="cart-actions">

            {{-- VIEW RESULT --}}
            @if($order->result_file)
            <a href="{{ asset('storage/'.$order->result_file) }}"
                target="_blank"
                class="btn btn-view">
                View File
            </a>
            @endif

            {{-- CHAT BUTTON (WAITING REVIEW) --}}
            @if($order->status == 'waiting_client' || $order->status == 'revision')

            <form method="GET" action="{{ route('chat.index') }}">
                <input type="hidden" name="user_id" value="{{ $order->artist_id }}">
                <button class="btn btn-chat">
                    Chat Artist
                </button>
            </form>

            @endif

            {{-- INFO --}}
            @if($order->status == 'pending')
            <span style="font-size:12px;color:orange;">
                Waiting acceptance
            </span>
            @endif

            @if($order->status == 'in_progress')
            <span style="font-size:12px;color:blue;">
                Work in progress
            </span>
            @endif

            @if($order->status == 'completed')
            <span style="font-size:12px;color:green;">
                Completed
            </span>
            @endif

        </div>

    </div>

    @empty
    <p>No orders yet</p>
    @endforelse

</div>

@endsection
