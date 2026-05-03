@extends('layouts.app')

@section('content')

<style>
    .detail-container {
        max-width: 900px;
        margin: auto;
        padding-bottom: 90px;
    }

    /* ===== BACK ===== */
    .back-btn {
        padding: 12px;
        display: inline-block;
        font-size: 14px;
    }

    /* ===== IMAGE ===== */
    .image-wrapper {
        width: 100%;
    }

    .detail-image {
        width: 100%;
        max-height: 420px;
        object-fit: cover;
        border-radius: 10px;
    }

    /* ===== ARTIST ===== */
    .artist-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 15px 0;
    }

    .artist-box img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* ===== TITLE ===== */
    .detail-title {
        font-size: 22px;
        font-weight: 600;
    }

    /* ===== PRICE ===== */
    .detail-price {
        font-size: 20px;
        font-weight: bold;
        color: #16a34a;
        margin: 6px 0 12px;
    }

    /* ===== SECTION ===== */
    .section {
        margin-top: 20px;
    }

    .section h6 {
        font-weight: 600;
        margin-bottom: 6px;
    }

    /* ===== DESCRIPTION ===== */
    .detail-desc {
        font-size: 14px;
        color: #444;
        line-height: 1.7;
    }

    /* ===== STICKY BUTTON ===== */
    .bottom-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #fff;
        border-top: 1px solid #ddd;
        padding: 10px;
        display: flex;
        gap: 10px;
        z-index: 999;
    }

    .bottom-bar button {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
    }

    .btn-chat {
        background: #eee;
    }

    .btn-order {
        background: #4f46e5;
        color: #fff;
    }
</style>

<div class="detail-container">

    {{-- BACK --}}
    <a href="{{ url()->previous() }}" class="back-btn">← Back</a>

    {{-- IMAGE --}}
    <div class="image-wrapper">
        <img src="{{ $service->image_url }}" class="detail-image">
    </div>

    {{-- ARTIST --}}
    <div class="artist-box">
        <img src="{{ $service->user->avatar ?? '/default-avatar.png' }}">
        <div>
            <b>{{ $service->artist->name ?? 'Unknown Artist' }}</b><br>
            <small class="text-muted">Artist</small>
        </div>
    </div>

    {{-- TITLE --}}
    <div class="detail-title">
        {{ $service->title }}
    </div>

    {{-- PRICE --}}
    <div class="detail-price">
        Rp {{ number_format($service->price, 0, ',', '.') }}
    </div>

    {{-- DESCRIPTION --}}
    <div class="section">
        <h6>Description</h6>
        <div class="detail-desc">
            {!! nl2br(e($service->description)) !!}
        </div>
    </div>

</div>

{{-- BOTTOM ACTION --}}
<div class="bottom-bar">
    <button class="btn-chat">Chat</button>

    <button id="openOrderBtn"
        data-id="{{ $service->service_id }}"
        data-title="{{ $service->title }}"
        data-price="{{ $service->price }}"
        class="btn btn-dark w-100">
        Order Now
    </button>
</div>

@include('commission.order')
<script src="{{ asset('js/order-popup.js') }}"></script>

@endsection
