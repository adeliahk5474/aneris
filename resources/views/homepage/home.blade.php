@extends('layouts.app')

@section('content')

<style>
    /* Grid 2 kolom */
    .grid-feed {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding-bottom: 80px;
    }

    .post {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
    }

    .post-header .user {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13px;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        background: #ccc;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* FIXED 4:5 */
    .post-image {
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #000;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .post-image img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* utuh tanpa crop */
        object-position: center;
    }

    .post-footer {
        padding: 10px;
    }

    .buttons {
        display: flex;
        gap: 14px;
        margin-bottom: 6px;
        align-items: center;
    }

    .buttons svg {
        width: 22px;
        height: 22px;
        stroke-width: 1.6;
        cursor: pointer;
    }

    .icon-group {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .counter {
        font-size: 12px;
        color: #555;
    }

    .likes {
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .caption {
        font-size: 13px;
        color: #333;
    }
</style>

<div class="container">
    <div class="grid-feed">

        @foreach($feed as $art)
        <div class="post">

            {{-- HEADER --}}
            <div class="post-header">
                <div class="user">
                    <div class="avatar">
                        <img src="{{ $art->user->avatar ?? '/default-avatar.png' }}">
                    </div>
                    {{ $art->user->name ?? 'Unknown Artist' }}
                </div>
                <div>⋯</div>
            </div>

            {{-- IMAGE --}}
            <div class="post-image">
                <img src="{{ $art->preview_url ?? $art->file_url }}">
            </div>

            {{-- FOOTER --}}
            <div class="post-footer">

                <div class="buttons">

                    <div class="icon-group">
                        @svg('heroicon-o-heart')
                        <span class="counter">{{ $art->likes ?? 0 }}</span>
                    </div>

                    <div class="icon-group">
                        @svg('heroicon-o-chat-bubble-left')
                        <span class="counter">{{ $art->comments ?? 0 }}</span>
                    </div>

                    <div class="icon-group">
                        @svg('heroicon-o-paper-airplane')
                        <span class="counter">{{ $art->shares ?? 0 }}</span>
                    </div>

                    <div class="icon-group">
                        @svg('heroicon-o-bookmark')
                    </div>
                </div>

                <div class="likes">
                    {{ $art->likes ?? 0 }} likes
                </div>

                <div class="caption">
                    {{ $art->caption ?? '' }}
                </div>

            </div>

        </div>
        @endforeach

    </div>
</div>

@include('layouts.botnav')

@endsection
