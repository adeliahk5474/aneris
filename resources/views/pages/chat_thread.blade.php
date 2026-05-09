@extends('layouts.app')
@section('title', 'Chat')

@section('content')

<style>
    .chat-page {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background: #f5f5f5;
    }

    html,
    body {
        height: 100%;
        overflow: hidden;
    }

    /* ================= HEADER ================= */
    .chat-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: #fff;
        border-bottom: 1px solid #ddd;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .chat-header img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-header .back-btn {
        text-decoration: none;
        font-size: 22px;
        color: #000;
    }

    /* ================= CHAT BOX ================= */
    .chat-box {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        padding-bottom: 200px;
        scroll-behavior: smooth;
    }

    .chat-row {
        display: flex;
        margin-bottom: 12px;
    }

    .chat-row.me {
        justify-content: flex-end;
    }

    .chat-row.other {
        justify-content: flex-start;
    }

    .bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 18px;
        word-break: break-word;
    }

    .bubble.me {
        background: #111827;
        color: white;
        border-bottom-right-radius: 5px;
    }

    .bubble.other {
        background: #fff;
        color: #000;
        border-bottom-left-radius: 5px;
    }

    .chat-time {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 4px;
        text-align: right;
    }

    .chat-image {
        max-width: 240px;
        border-radius: 12px;
        margin-top: 8px;
        cursor: pointer;
    }

    /* ================= INPUT ================= */
    .chat-input-area {
        position: fixed;
        bottom: 80px;
        left: 50%;
        transform: translateX(-50%);
        width: 92%;
        max-width: 700px;

        background: white;
        border: 1px solid #ddd;
        border-radius: 25px;
        padding: 10px 12px;

        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        z-index: 999;
    }

    .chat-form {
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }

    .chat-textarea {
        flex: 1;
        border: none;
        outline: none;
        resize: none;
        background: #f3f4f6;
        border-radius: 18px;
        padding: 10px 12px;
        font-size: 14px;
        max-height: 120px;
        overflow-y: auto;
    }

    /* IMAGE BUTTON */
    .image-upload-btn {
        width: 42px;
        height: 42px;
        min-width: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #f1f1f1;
        border-radius: 50%;

        cursor: pointer;
        font-size: 18px;
    }

    .send-btn {
        border: none;
        background: #111827;
        color: white;
        padding: 10px 16px;
        border-radius: 20px;
    }

    .preview-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        display: none;
        margin-bottom: 6px;
    }
</style>

<div class="chat-page">

    {{-- HEADER --}}
    <div class="chat-header">
        <a href="{{ route('chat.list') }}" class="back-btn">←</a>

        <img src="{{ $otherUser->avatar ?? 'https://via.placeholder.com/100' }}">

        <div>
            <strong>{{ $otherUser->name }}</strong>
            @isset($order)
            <div style="font-size:12px;color:gray;">Order Chat</div>
            @endisset
        </div>
    </div>

    {{-- CHAT BOX --}}
    <div class="chat-box" id="chatBox"></div>

    {{-- INPUT --}}
    <div class="chat-input-area">

        <form id="chatForm" action="{{ route('chat.send') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="order_id" value="{{ $order->order_id ?? '' }}">
            <input type="hidden" name="receiver_id" value="{{ $otherUser->user_id }}">

            <img id="preview" class="preview-image">

            <div class="chat-form">

                {{-- TEXT --}}
                <textarea name="message"
                    class="chat-textarea"
                    placeholder="Message..."
                    rows="1"></textarea>

                {{-- IMAGE --}}
                <label class="image-upload-btn">
                    +
                    <input type="file" name="image" id="imageInput" hidden>
                </label>

                {{-- SEND --}}
                <button type="submit" class="send-btn">Send</button>

            </div>

        </form>

    </div>

</div>

@include('layouts.botnav')

<script>
    const chatBox = document.getElementById('chatBox');
    const form = document.getElementById('chatForm');
    const textarea = document.querySelector('.chat-textarea');
    const imageInput = document.getElementById('imageInput');
    const preview = document.getElementById('preview');

    /* ================= CHAT RENDER ================= */
    function renderChat(data) {

        chatBox.innerHTML = '';

        data.forEach(chat => {

            const isMe = chat.sender_id == "{{ auth()->user()->user_id }}";

            chatBox.innerHTML += `
        <div class="chat-row ${isMe ? 'me' : 'other'}">
            <div class="bubble ${isMe ? 'me' : 'other'}">

                ${chat.message ? `<div>${chat.message}</div>` : ''}

                ${chat.image ? `<img src="/storage/${chat.image}" class="chat-image">` : ''}

                <div class="chat-time">
                    ${new Date(chat.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}
                </div>

            </div>
        </div>`;
        });

        chatBox.scrollTop = chatBox.scrollHeight;
    }

    /* ================= FETCH CHAT ================= */
    function loadChat() {
        fetch("/chat/fetch?user_id={{ $otherUser->user_id }}")
            .then(res => res.json())
            .then(res => renderChat(res.data));
    }

    loadChat();
    setInterval(loadChat, 2000);

    /* ================= ENTER TO SEND ================= */
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.submit();
        }
    });

    /* ================= AUTO RESIZE TEXTAREA ================= */
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        const max = 120;

        if (this.scrollHeight > max) {
            this.style.height = max + 'px';
            this.style.overflowY = 'auto';
        } else {
            this.style.height = this.scrollHeight + 'px';
            this.style.overflowY = 'hidden';
        }
    });

    /* ================= IMAGE PREVIEW ================= */
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    });
</script>

@endsection
