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

@foreach($feed as $item)
<div class="post">

    {{-- HEADER --}}
    <div class="post-header">
        <div class="user">
            <div class="avatar">
                @if($item->type === 'artwork')
                    <img src="{{ $item->user->avatar ?? '/default-avatar.png' }}">
                @else
                    <img src="{{ $item->artist->avatar ?? '/default-avatar.png' }}">
                @endif
            </div>

            @if($item->type === 'artwork')
                {{ $item->user->name ?? 'Unknown Artist' }}
            @else
                {{ $item->artist->name ?? 'Unknown Artist' }}
            @endif
        </div>
        <div>⋯</div>
    </div>

    {{-- IMAGE --}}
    <div class="post-image">
        @if($item->type === 'artwork')
            <img src="{{ $item->preview_url ?? $item->file_url }}">
        @else
            <img src="{{ $item->image_url }}">
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="post-footer">

        <div class="buttons">

            <div class="icon-group">
                @svg('heroicon-o-heart')
                <span class="counter">
                    {{ $item->likes ?? 0 }}
                </span>
            </div>

            <div class="icon-group">
                @svg('heroicon-o-chat-bubble-left')
                <span class="counter">
                    {{ $item->comments ?? 0 }}
                </span>
            </div>

            <div class="icon-group">
                @svg('heroicon-o-paper-airplane')
                <span class="counter">
                    {{ $item->shares ?? 0 }}
                </span>
            </div>

        </div>

        {{-- CAPTION / TITLE --}}
        <div class="caption">
            @if($item->type === 'artwork')
                {{ $item->caption ?? '' }}
            @else
                <b>{{ $item->title }}</b><br>
                Rp {{ number_format($item->price, 0, ',', '.') }}
            @endif
        </div>

    </div>

</div>
@endforeach

    </div>
</div>

@include('layouts.botnav')

@endsection
