@extends('layouts.app')
@section('title', 'Chat')

@section('content')
<div class="container" style="padding-top:12px; padding-bottom:80px;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('chat.index') }}" class="btn btn-link p-0 me-2"><i class="bi bi-arrow-left"></i></a>
        <div class="d-flex align-items-center">
            <img src="{{ $chat->sender->avatar ?? 'https://via.placeholder.com/36' }}" class="rounded-circle me-2" alt="avatar">
            <strong>Chat about Order #{{ $chat->order_id ?? '—' }}</strong>
        </div>
    </div>

    <div class="bg-light border rounded p-3 mb-3" style="min-height:50vh;">
        {{-- Render single chat message (historical messages require chat table expansion) --}}
        <div class="mb-2">
            <div class="small text-muted">From: {{ $chat->sender->name ?? 'User' }} — To: {{ $chat->receiver->name ?? 'Artist' }}</div>
            <div class="bg-white p-2 rounded mt-1">{{ $chat->message }}</div>
        </div>
    </div>

    <form method="POST" action="#">
        <div class="input-group fixed-bottom p-3 bg-white" style="max-width:100%; z-index:1040;">
            <input type="text" class="form-control" placeholder="Message..." name="message">
            <button class="btn btn-primary" type="button">Send</button>
        </div>
    </form>
</div>
@endsection
