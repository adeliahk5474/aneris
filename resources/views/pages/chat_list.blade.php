{{-- resources/views/pages/chat_list.blade.php --}}
@extends('layouts.app')

@section('title', 'Messages — Aneris')

@section('content')

@vite('resources/css/pages/chat-list.css')

<div class="chat-layout">

    {{-- SIDEBAR --}}
    <div class="chat-sidebar">
        <div class="sidebar-head">
            <span class="sidebar-title">Messages</span>
            <a href="#" class="sidebar-compose" aria-label="New message">
                <i class="bi bi-pencil-square"></i>
            </a>
        </div>

        <div class="conv-list">
            @php $myId = Auth::user()->user_id; @endphp

            @forelse($conversations as $conv)

            @php
            $href = $conv->order_id
            ? route('chat.index', ['order_id' => $conv->order_id])
            : route('chat.index', ['user_id' => $conv->other->user_id ?? '']);

            $isMine = $conv->sender_id === $myId;
            $isUnread = !$isMine && !$conv->is_read;
            $isRead = $isMine && $conv->is_read;
            @endphp

            <a href="{{ $href }}"
                class="conv-item {{ $conv->isActive ? 'active' : '' }} {{ $isUnread ? 'unread' : '' }}"
                data-href="{{ $href }}">

                <div class="conv-avatar-wrap">
                    <img
                        src="{{ $conv->other->avatar ?? asset('images/default-avatar.png') }}"
                        class="conv-avatar"
                        alt="{{ $conv->other->name ?? '' }}">
                    <span class="conv-online"></span>
                </div>

                <div class="conv-body">
                    {{-- TOP: nama + waktu + unread badge --}}
                    <div class="conv-top">
                        <span class="conv-name">{{ $conv->other->name ?? 'Unknown' }}</span>

                        @if($conv->order_id)
                        <span class="conv-order-tag">Order</span>
                        @endif

                        <span class="conv-time">
                            {{ $conv->created_at?->diffForHumans(null, true) ?? '' }}
                        </span>

                        @if($isUnread)
                        <span class="conv-unread-badge">1</span>
                        @endif
                    </div>

                    {{-- BOTTOM: checkmark + preview --}}
                    <div class="conv-bottom">
                        @if($isMine)
                        <i class="bi bi-check2-all conv-check {{ $isRead ? 'read' : '' }}"></i>
                        @endif

                        <span class="conv-preview">
                            @if($conv->image)
                            📎 Attachment
                            @else
                            {{ Str::limit($conv->message, 36) }}
                            @endif
                        </span>
                    </div>
                </div>

            </a>

            @empty
            <div style="padding: 40px 20px; text-align: center; color: var(--muted); font-size: 13px;">
                No conversations yet.
            </div>
            @endforelse
        </div>
    </div>

    {{-- PLACEHOLDER saat tidak ada thread terpilih --}}
    <div class="chat-empty">
        <i class="bi bi-chat-dots"></i>
        <span>Select a conversation to start chatting</span>
    </div>

</div>

@endsection