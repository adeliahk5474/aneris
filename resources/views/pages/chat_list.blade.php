@extends('layouts.app')
@section('title', 'Chats')

@section('content')

<div class="container py-3">

    <h5 class="mb-3">Messages</h5>

    <div class="list-group">

        @forelse($conversations as $chat)

        @php

        $me = auth()->user()->user_id;

        $otherUser = $chat->sender_id == $me
        ? $chat->receiver
        : $chat->sender;

        $lastMessage = $chat->message
        ? $chat->message
        : ($chat->image ? '📷 Image' : '-');

        // ================= UNREAD =================
        $unreadMessages = \App\Models\Chat::where('sender_id', $otherUser->user_id)
        ->where('receiver_id', $me)
        ->where('is_read', false)
        ->count();

        @endphp

        {{-- ORDER CHAT --}}
        @if($chat->order_id)

        <a href="{{ route('chat.index', ['order_id' => $chat->order_id]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center">

            <img src="{{ $otherUser->avatar ?? 'https://via.placeholder.com/48' }}"
                width="48"
                height="48"
                class="rounded-circle me-3">

            <div class="flex-fill">

                <div class="d-flex justify-content-between">

                    <strong>
                        {{ $otherUser->name }}
                        <span class="text-primary small">
                            (Order)
                        </span>
                    </strong>

                    <div class="d-flex align-items-center gap-2">

                        <small class="text-muted">
                            {{ $chat->created_at->diffForHumans() }}
                        </small>

                        @if($unreadMessages > 0)

                        <span class="badge rounded-pill bg-primary"
                            style="font-size:10px;">

                            {{ $unreadMessages }}

                        </span>

                        @endif

                    </div>

                </div>

                <div class="small text-muted">
                    {{ $lastMessage }}
                </div>

            </div>

        </a>

        @else

        {{-- DM CHAT --}}
        <a href="{{ route('chat.index', ['user_id' => $otherUser->user_id]) }}"
            class="list-group-item list-group-item-action d-flex align-items-center">

            <img src="{{ $otherUser->avatar ?? 'https://via.placeholder.com/48' }}"
                width="48"
                height="48"
                class="rounded-circle me-3">

            <div class="flex-fill">

                <div class="d-flex justify-content-between align-items-center">

                    <strong>
                        {{ $otherUser->name }}
                    </strong>

                    <div class="d-flex align-items-center gap-2">

                        <small class="text-muted">
                            {{ $chat->created_at->diffForHumans() }}
                        </small>

                        @if($unreadMessages > 0)

                        <span class="badge rounded-pill bg-primary"
                            style="
                        font-size:10px;
                        min-width:20px;
                        height:20px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    ">

                            {{ $unreadMessages }}

                        </span>

                        @endif

                    </div>

                </div>

                <div class="small text-muted">
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
