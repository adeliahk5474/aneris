@extends('layouts.app')
@section('title', 'Chats')

@section('content')
<div class="container" style="padding-top:12px; padding-bottom:80px;">

    <div class="d-flex align-items-center mb-3">
        <h5 class="mb-0">Messages</h5>
    </div>

    <div class="list-group">

        @forelse($conversations as $chat)

        @php
        $me = auth()->user()->user_id;

        // tentukan lawan chat
        $otherUser = $chat->sender_id == $me
        ? $chat->receiver
        : $chat->sender;

        // waktu
        $time = \Carbon\Carbon::parse($chat->created_at)->diffForHumans();

        // last message
        $lastMessage = $chat->message
        ? $chat->message
        : ($chat->image ? '📷 Image' : '-');

        // unread
        $isUnread = !$chat->is_read && $chat->receiver_id == $me;
        @endphp

        {{-- ===============================
                ORDER CHAT
            =============================== --}}
        @if($chat->order_id)

        <a href="{{ route('chat.index', ['order_id' => $chat->order_id]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center">

            <img src="{{ $otherUser->avatar ?? 'https://via.placeholder.com/48' }}"
                class="rounded-circle me-3"
                width="48" height="48"
                style="object-fit:cover;">

            <div class="flex-fill">

                <div class="d-flex justify-content-between">
                    <strong>
                        {{ $otherUser->name }}
                        <span class="text-primary small">(Order)</span>
                    </strong>

                    <small class="text-muted">{{ $time }}</small>
                </div>

                <div class="small {{ $isUnread ? 'fw-bold text-dark' : 'text-muted' }}">
                    {{ $lastMessage }}
                </div>

            </div>

        </a>

        @else

        {{-- ===============================
                DM CHAT
            =============================== --}}
        <a href="{{ route('chat.index', ['user_id' => $otherUser->user_id]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center">

            <img src="{{ $otherUser->avatar ?? 'https://via.placeholder.com/48' }}"
                class="rounded-circle me-3"
                width="48" height="48"
                style="object-fit:cover;">

            <div class="flex-fill">

                <div class="d-flex justify-content-between">
                    <strong>{{ $otherUser->name }}</strong>
                    <small class="text-muted">{{ $time }}</small>
                </div>

                <div class="small {{ $isUnread ? 'fw-bold text-dark' : 'text-muted' }}">
                    {{ $lastMessage }}
                </div>

            </div>

        </a>

        @endif

        @empty

        <div class="text-center text-muted py-5">
            No messages yet
        </div>

        @endforelse

    </div>
</div>
@endsection