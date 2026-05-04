@extends('layouts.app')
@section('title', 'Chat')

@section('content')

<style>
    .chat-container {
        padding-top: 12px;
        padding-bottom: 90px;
    }

    .chat-box {
        min-height: 65vh;
        max-height: 70vh;
        overflow-y: auto;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
    }

    .chat-bubble {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-size: 14px;
        word-wrap: break-word;
    }

    .chat-left {
        background: #ffffff;
    }

    .chat-right {
        background: #4f46e5;
        color: #fff;
        margin-left: auto;
    }

    .chat-image {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 5px;
    }

    .chat-input {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: #fff;
        border-top: 1px solid #ddd;
        padding: 10px;
        z-index: 1050;
    }
</style>

<div class="container chat-container">

    {{-- HEADER --}}
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('chat.list') }}" class="btn btn-link p-0 me-2">
            <i class="bi bi-arrow-left"></i>
        </a>

        @php
        $me = auth()->user()->user_id;

        if(isset($order)) {
        $otherUser = $order->client_id == $me ? $order->artist : $order->client;
        $receiver_id = $otherUser->user_id;
        } else {
        $receiver_id = $otherUser->user_id ?? null;
        }
        @endphp

        <div class="d-flex align-items-center">
            <img src="{{ $otherUser->avatar ?? 'https://via.placeholder.com/36' }}"
                class="rounded-circle me-2"
                width="36" height="36">

            <div>
                <strong>{{ $otherUser->name ?? 'User' }}</strong><br>
                @if(isset($order))
                <small class="text-muted">Order Chat</small>
                @else
                <small class="text-muted">Direct Message</small>
                @endif
            </div>
        </div>
    </div>

    {{-- CHAT BOX --}}
    <div class="chat-box" id="chatBox">

        @foreach($chats as $chat)
        @php
        $isMe = $chat->sender_id == $me;
        @endphp

        <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">

            <div class="chat-bubble {{ $isMe ? 'chat-right' : 'chat-left' }}">

                {{-- TEXT --}}
                @if($chat->message)
                <div>{{ $chat->message }}</div>
                @endif

                {{-- IMAGE --}}
                @if($chat->image)
                <img src="{{ asset('storage/'.$chat->image) }}" class="chat-image">
                @endif

                {{-- TIME --}}
                <div class="text-end small mt-1" style="opacity:0.6;">
                    {{ \Carbon\Carbon::parse($chat->created_at)->format('H:i') }}
                </div>

            </div>

        </div>
        @endforeach

    </div>

</div>

{{-- INPUT --}}
<form action="{{ route('chat.send') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="chat-input d-flex gap-2">

        {{-- hidden --}}
        <input type="hidden" name="order_id" value="{{ $order->order_id ?? '' }}">
        <input type="hidden" name="receiver_id" value="{{ $receiver_id }}">

        <input type="text" name="message" class="form-control" placeholder="Message...">

        <input type="file" name="image" class="form-control" style="max-width:120px;">

        <button class="btn btn-primary">Send</button>
    </div>

</form>

{{-- AUTO SCROLL --}}
<script id="f7y8qk">
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
</script>

@endsection