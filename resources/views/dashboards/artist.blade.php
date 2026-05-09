@php
$hideBottomNavbar = true;
@endphp

@extends('layouts.app')

@section('content')

<style>
    .dashboard {
        max-width: 1000px;
        margin: auto;
        padding: 20px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .dashboard-header img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .card-box {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 16px;
    }

    .grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    @media(max-width:768px) {
        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-3 {
            grid-template-columns: 1fr;
        }
    }

    .order-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .order-title {
        font-weight: 600;
    }

    .order-meta {
        font-size: 13px;
        color: #555;
    }

    .btn-sm {
        padding: 5px 8px;
        font-size: 12px;
        border: none;
        border-radius: 6px;
        color: #fff;
        cursor: pointer;
    }

    .btn-green {
        background: #16a34a;
    }

    .btn-red {
        background: #dc2626;
    }

    .btn-blue {
        background: #2563eb;
    }

    .btn-purple {
        background: #7c3aed;
    }

    .status {
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
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
</style>

<div class="dashboard">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <a href="{{ route('profile.show', auth()->id()) }}">← Back</a>

        <h3>Artist Dashboard</h3>

        <div style="display:flex;align-items:center;gap:10px;">
            <img src="{{ $artist->avatar ?? '/default-avatar.png' }}">
            <b>{{ $artist->name }}</b>
        </div>
    </div>

    {{-- ORDER --}}
    <div class="card-box">
        <h5>Incoming Orders</h5>

        @forelse($incomingOrders as $order)

        <div class="order-card">

            <div class="order-title">
                {{ $order->service->title ?? 'Service' }}
            </div>

            <div class="order-meta">
                Client: {{ $order->client->name ?? '-' }}
            </div>

            <div class="order-meta">
                Rp {{ number_format($order->total_price,0,',','.') }}
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
            <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap;">

                {{-- PENDING --}}
                @if($order->status=='pending')

                <form method="POST" action="{{ route('order.accept') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <button class="btn-sm btn-green">Accept</button>
                </form>

                <form method="POST" action="{{ route('order.reject') }}">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <button class="btn-sm btn-red">Reject</button>
                </form>
                @endif

                {{-- IN PROGRESS (KIRIM SKETSA / COLORING) --}}
                @if($order->status=='in_progress')

                <form method="POST" action="{{ route('order.sendToClient') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                    <input type="file" name="result_file" required>

                    <button class="btn-sm btn-blue">Send</button>
                </form>

                @endif

                {{-- WAITING CLIENT --}}
                @if($order->status=='waiting_client')

                <span style="font-size:12px;color:#555;">
                    Waiting client response
                </span>

                @endif

                {{-- REVISION (BACK TO WORK) --}}
                @if($order->status=='revision')

                <span style="font-size:12px;color:#ef4444;">
                    Revision requested
                </span>

                <form method="POST" action="{{ route('chat.index') }}">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $order->client_id }}">
                    <button class="btn-sm btn-purple">Chat Client</button>
                </form>

                @endif

                {{-- COMPLETED --}}
                @if($order->status=='completed')
                <span style="font-size:12px;color:green;">
                    Completed
                </span>
                @endif

            </div>

        </div>

        @empty
        <p>No orders</p>
        @endforelse

    </div>

</div>

@endsection
