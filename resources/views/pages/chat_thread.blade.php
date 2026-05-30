{{-- resources/views/pages/chat_thread.blade.php --}}
@extends('layouts.app')

@section('title', 'Chat — Aneris')

@section('content')

@vite('resources/css/pages/chat-thread.css')

<div class="chat-layout">

    {{-- ── SIDEBAR ── --}}
    <div class="chat-sidebar">
        <div class="sidebar-head">
            <span class="sidebar-title">Messages</span>
            <a href="#" class="sidebar-compose"><i class="bi bi-pencil-square"></i></a>
        </div>

        <div class="conv-list">
            @php $myId = Auth::user()->user_id; @endphp

            @forelse($conversations ?? [] as $conv)
            @php
            $convHref = $conv->order_id
            ? route('chat.index', ['order_id' => $conv->order_id])
            : route('chat.index', ['user_id' => $conv->other->user_id ?? '']);

            $isMine = $conv->sender_id === $myId;
            $isUnread = !$isMine && !$conv->is_read;
            $isRead = $isMine && $conv->is_read;
            @endphp

            <a href="{{ $convHref }}"
                class="conv-item {{ $conv->isActive ? 'active' : '' }} {{ $isUnread ? 'unread' : '' }}"
                data-href="{{ $convHref }}">

                <div class="conv-avatar-wrap">
                    <img src="{{ $conv->other->avatar ?? asset('images/default-avatar.png') }}"
                        class="conv-avatar" alt="{{ $conv->other->name ?? '' }}">
                    <span class="conv-online"></span>
                </div>

                <div class="conv-body">
                    <div class="conv-top">
                        <span class="conv-name">{{ $conv->other->name ?? 'Unknown' }}</span>
                        @if($conv->order_id)
                        <span class="conv-order-tag">Order</span>
                        @endif
                        <span class="conv-time">{{ $conv->created_at?->diffForHumans(null, true) ?? '' }}</span>
                        @if($isUnread)
                        <span class="conv-unread-badge">1</span>
                        @endif
                    </div>
                    <div class="conv-bottom">
                        @if($isMine)
                        <i class="bi bi-check2-all conv-check {{ $isRead ? 'read' : '' }}"></i>
                        @endif
                        <span class="conv-preview">
                            @if($conv->image) 📎 Attachment
                            @else {{ Str::limit($conv->message, 36) }}
                            @endif
                        </span>
                    </div>
                </div>

            </a>
            @empty
            <div style="padding:40px 20px;text-align:center;color:var(--muted);font-size:13px;">
                No conversations yet.
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── THREAD PANEL ── --}}
    <div class="chat-thread">

        {{-- Header --}}
        <div class="thread-head">
            <div class="thread-user">
                <img src="{{ $otherUser->avatar ?? asset('images/default-avatar.png') }}"
                    class="thread-avatar" alt="{{ $otherUser->name ?? '' }}">
                <div class="thread-user-info">
                    <span class="thread-name">{{ $otherUser->name ?? 'Unknown' }}</span>
                    <span class="thread-status">
                        <span class="status-dot"></span> Online
                    </span>
                </div>
            </div>
            <div class="thread-actions">
                <div class="thread-action-btn" title="Call"><i class="bi bi-telephone"></i></div>
                <div class="thread-action-btn" title="Video"><i class="bi bi-camera-video"></i></div>
                <div class="thread-action-btn" title="Info"><i class="bi bi-info-circle"></i></div>
            </div>
        </div>

        {{-- Order banner (hanya jika order chat) --}}
        @if(isset($order))
        <div class="thread-order-banner">
            <i class="bi bi-box-seam"></i>
            Order #{{ strtoupper(substr($order->order_id, 0, 8)) }}
            &nbsp;·&nbsp;
            {{ $order->service->title ?? 'Commission' }}
        </div>
        @endif

        {{-- Messages --}}
        <div class="thread-messages"
            id="messages-area"
            data-order-id="{{ $order->order_id ?? '' }}"
            data-current-user-id="{{ Auth::user()->user_id }}">

            <div class="date-divider">Today</div>

            @foreach($chats as $chat)
            <div class="bubble-row {{ $chat->isMine ? 'mine' : '' }}">

                @if(!$chat->isMine)
                <img src="{{ $chat->sender->avatar ?? asset('images/default-avatar.png') }}"
                    class="bubble-avatar" alt="">
                @endif

                <div class="bubble-wrap">
                    @if($chat->image)
                    <div class="bubble-img">
                        <img src="{{ asset('storage/' . $chat->image) }}" alt="attachment">
                    </div>
                    @endif

                    @if($chat->message)
                    <div class="bubble">{{ $chat->message }}</div>
                    @endif

                    <div class="bubble-time">
                        {{ $chat->created_at?->format('g:i A') ?? '' }}
                        @if($chat->isMine)
                        <i class="bi bi-check2-all read-check {{ $chat->is_read ? 'read' : '' }}"></i>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach

        </div>

        {{-- Input --}}
        <div class="thread-input">
            <form action="{{ route('chat.send') }}" method="POST"
                enctype="multipart/form-data" id="chat-form">
                @csrf
                @if(isset($order))
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                @endif
                <input type="hidden" name="receiver_id" value="{{ $otherUser->user_id ?? '' }}">
                <input type="file" name="image" id="image-input"
                    style="display:none;" accept="image/*">

                <div class="input-row">
                    <button type="button" class="input-attach"
                        onclick="document.getElementById('image-input').click()">
                        <i class="bi bi-plus-circle"></i>
                    </button>

                    <textarea
                        name="message"
                        class="input-field"
                        placeholder="Message {{ $otherUser->name ?? '...' }}"
                        rows="1"
                        id="message-input"
                        onkeydown="handleEnter(event)"></textarea>

                    <div class="input-actions">
                        <button type="button" class="input-icon-btn" title="Emoji">
                            <i class="bi bi-emoji-smile"></i>
                        </button>
                        <button type="button" class="input-icon-btn"
                            onclick="document.getElementById('image-input').click()"
                            title="Image">
                            <i class="bi bi-image"></i>
                        </button>
                        <button type="submit" class="input-send" title="Send">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@vite('resources/js/pages/chat-thread.js')

@endsection