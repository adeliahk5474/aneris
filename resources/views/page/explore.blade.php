{{-- resources/views/page/explore.blade.php --}}
@extends('layouts.app')

@section('title', 'Explore — Aneris')

@section('content')

@vite('resources/css/page/explore.css')

{{-- ── FILTER BAR ── --}}
<div class="filter-bar">

    {{-- ROW 1: Kategori --}}
    <div class="filter-categories">
        <div class="filter-pills">
            <a href="{{ route('explore', array_merge(request()->except('category'), ['sort' => $sort])) }}"
                class="pill {{ !$category ? 'active' : '' }}">
                All
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('explore', array_merge(request()->except('category'), ['category' => $cat->name, 'sort' => $sort])) }}"
                class="pill {{ $category === $cat->name ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- ROW 2: Sort + Search --}}
    <div class="filter-sort-row">
        <span style="font-size:11px;color:var(--muted);white-space:nowrap;flex-shrink:0;">Urutkan:</span>

        @foreach([
            'popular'   => ['🔥', 'Populer'],
            'newest'    => ['✨', 'Terbaru'],
            'top_rated' => ['⭐', 'Top Rating'],
        ] as $key => [$icon, $label])
        <a href="{{ route('explore', array_merge(request()->except('sort'), ['sort' => $key])) }}"
            class="pill sort-pill {{ $sort === $key ? 'active' : '' }}">
            {{ $icon }} {{ $label }}
        </a>
        @endforeach

        <div class="filter-divider"></div>

        {{-- Search --}}
        <form action="{{ route('explore') }}" method="GET" class="explore-search-form">
            @if($category)
            <input type="hidden" name="category" value="{{ $category }}">
            @endif
            <input type="hidden" name="sort" value="{{ $sort }}">
            <i class="bi bi-search"></i>
            <input type="text" name="search" placeholder="Search..." value="{{ $search }}" autocomplete="off">
        </form>
    </div>

</div>

<main class="explore-main">

    {{-- USER RESULTS --}}
    @if($users->count())
    <div class="user-section">
        <div class="section-label">Users</div>
        <div class="user-scroll">
            @foreach($users as $user)
            <a href="{{ route('profile.show', $user->user_id) }}" class="user-card">
                <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
                    alt="{{ $user->name }}" class="user-avatar" loading="lazy">
                <span class="user-name">{{ $user->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- MASONRY GRID --}}
    @if($explore->count())
    <div class="masonry-grid">
        @foreach($explore as $item)

        @if($item->type === 'artwork')
        {{-- ── ARTWORK ── --}}
        <div class="masonry-item">
            <a href="{{ $item->image_url }}" target="_blank">
                <img src="{{ $item->image_url }}"
                    alt="{{ $item->caption ?? 'artwork' }}" loading="lazy">
            </a>

            <span class="item-badge badge-artwork">
                {{ $item->category->name ?? 'Artwork' }}
            </span>

            <div class="masonry-overlay">
                <div class="overlay-row">
                    <a href="{{ route('profile.show', $item->user->user_id ?? '#') }}" class="overlay-artist">
                        <img src="{{ $item->user->avatar ?? asset('images/default-avatar.png') }}"
                            class="overlay-avatar" alt="">
                        <span class="overlay-name">{{ $item->user->name ?? '' }}</span>
                    </a>
                    {{-- Like artwork --}}
                    @auth
                    <button class="overlay-like"
                        onclick="event.preventDefault();event.stopPropagation();toggleLike(this,'{{ $item->artwork_id ?? '' }}','artwork')"
                        aria-label="Like artwork">
                        <i class="bi bi-heart"></i>
                    </button>
                    @else
                    <a href="{{ route('auth.form') }}" class="overlay-like" aria-label="Like">
                        <i class="bi bi-heart"></i>
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        @else
        {{-- ── COMMISSION SERVICE ── --}}
        <div class="masonry-item"
            data-service-id="{{ $item->service_id }}"
            data-liked="{{ $item->is_liked ? 'true' : 'false' }}"
            data-like-count="{{ $item->lc }}">

            <a href="{{ route('commission.show', $item->service_id) }}">
                <img src="{{ $item->image_url ?? asset('images/default-thumb.png') }}"
                    alt="{{ $item->title }}" loading="lazy">
            </a>

            <span class="item-badge {{ $item->isLive2D ? 'badge-live2d' : 'badge-commission' }}">
                {{ $item->catName }}
            </span>

            {{-- Price badge --}}
            <div class="price-badge">
                Rp {{ number_format($item->base_price ?? 0, 0, ',', '.') }}
            </div>

            {{-- Like count --}}
            <div class="like-count-badge {{ $item->lc === 0 ? 'hidden' : '' }}"
                id="lc-{{ $item->service_id }}">
                <i class="bi bi-heart-fill" style="font-size:8px;"></i>
                <span>{{ $item->lc }}</span>
            </div>

            {{-- Rating badge --}}
            @if($item->avgR > 0)
            <div class="rating-badge">
                <i class="bi bi-star-fill" style="font-size:9px;"></i>
                {{ number_format($item->avgR, 1) }}
            </div>
            @endif

            <div class="masonry-overlay">
                <div class="overlay-row">
                    <a href="{{ route('profile.show', $item->artist->user_id ?? '#') }}" class="overlay-artist">
                        <img src="{{ $item->artist->avatar ?? asset('images/default-avatar.png') }}"
                            class="overlay-avatar" alt="">
                        <span class="overlay-name">{{ $item->artist->name ?? '' }}</span>
                    </a>

                    @auth
                    <button class="overlay-like {{ $item->is_liked ? 'liked' : '' }}"
                        onclick="event.preventDefault();event.stopPropagation();toggleLike(this,'{{ $item->service_id }}','commission_service')"
                        aria-label="Like">
                        <i class="bi bi-heart{{ $item->is_liked ? '-fill' : '' }}"></i>
                    </button>
                    @else
                    <a href="{{ route('auth.form') }}" class="overlay-like"
                        onclick="event.stopPropagation()"
                        aria-label="Like">
                        <i class="bi bi-heart"></i>
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        @endif

        @endforeach
    </div>

    @else
    <div class="empty-state">
        <i class="bi bi-search empty-icon"></i>
        <span class="empty-title">
            @if($search) No results for "{{ $search }}"
            @elseif($category) No content in "{{ $category }}"
            @else Nothing here yet
            @endif
        </span>
        <span class="empty-sub">Try a different keyword or category</span>
    </div>
    @endif

</main>

@auth
<a href="{{ route('upload.popup') }}" class="fab" aria-label="Upload">
    <i class="bi bi-plus-lg"></i>
</a>
@endauth

@php
    $explorePageConfig = [
        'isAuth'        => auth()->check(),
        'likeToggleUrl' => route('like.toggle'),
        'authFormUrl'   => route('auth.form'),
    ];
@endphp

<script>
window.explorePage = @json($explorePageConfig);
</script>
@vite('resources/js/page/explore.js')

@endsection
