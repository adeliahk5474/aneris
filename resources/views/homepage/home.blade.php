{{-- resources/views/homepage/home.blade.php --}}
@extends('layouts.app')
@section('title', 'Aneris — For the love of human creativity')
@section('content')

@vite('resources/css/homepage/home.css')

@php
$hs = \App\Models\HomeSetting::getAllKeyed();
@endphp

{{-- ── HERO ── --}}
@php
$heroStyle = !empty($hs['hero_image'])
? 'background-image:url(' . e($hs['hero_image']) . ');background-size:cover;background-position:center;'
: '';
@endphp
<div class="home-hero" style="{{ $heroStyle }}">
    <h1>{{ $hs['hero_title'] ?? 'For the love of human creativity' }}</h1>
    @if(!empty($hs['hero_subtitle']))
    <p class="hero-subtitle">{{ $hs['hero_subtitle'] }}</p>
    @endif
    <form action="{{ route('explore') }}" method="GET">
        <div class="hero-search-wrap">
            <span class="hero-search-icon"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="hero-search-input"
                placeholder="Search commissions, artists..." autocomplete="off">
        </div>
    </form>
</div>

{{-- ── BANNER ── --}}
@php
$b1Style = !empty($hs['banner1_image'])
? 'background-image:url(' . e($hs['banner1_image']) . ');background-size:cover;background-position:center;'
: 'background:' . ($hs['banner1_color'] ?? '#1a1a2e') . ';';

$b2Style = !empty($hs['banner2_image'])
? 'background-image:url(' . e($hs['banner2_image']) . ');background-size:cover;background-position:center;'
: 'background:' . ($hs['banner2_color'] ?? '#0a1a10') . ';';
@endphp
<div class="banner-section">
    <div class="banner-grid">
        <a href="{{ route('explore', ['category' => 'Illustrations']) }}" class="banner-card"
            style="{{ $b1Style }}">
            <div class="banner-text">
                <div class="banner-title">{{ $hs['banner1_title'] ?? 'Made for creators' }}</div>
                <div class="banner-sub">{{ $hs['banner1_subtitle'] ?? 'Illustrations, avatars, emotes, live2d — made by humans who love what they do.' }}</div>
            </div>
        </a>
        <a href="{{ route('explore') }}" class="banner-card"
            style="{{ $b2Style }}">
            <div class="banner-text">
                <div class="banner-title">{{ $hs['banner2_title'] ?? 'No Generative AI' }}</div>
                <div class="banner-sub">{{ $hs['banner2_subtitle'] ?? 'Until generative AI is made with Consent, Credit, and Compensation, it is not welcome here.' }}</div>
            </div>
        </a>
    </div>
</div>

{{-- ── TOP RATED ── --}}
<div class="home-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-star-fill" style="color:var(--yellow);font-size:16px;"></i>
            Top Rated
            <span class="section-badge">{{ number_format($totalServices) }}+ services</span>
        </div>
        <a href="{{ route('explore', ['sort' => 'top_rated']) }}" class="section-link">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="category-pills">
        <a href="{{ route('explore') }}" class="cat-pill"><span>🔥</span> All</a>
        @foreach($categories as $cat)
        <a href="{{ route('explore', ['category' => $cat->name]) }}" class="cat-pill">
            <span>
                @switch(strtolower($cat->name))
                @case('illustrations') 🎨 @break
                @case('2d avatars') 🧑 @break
                @case('3d models') 🗿 @break
                @case('emotes') 😀 @break
                @case('live2d') 🎭 @break
                @default 🖼️
                @endswitch
            </span>
            {{ $cat->name }}
        </a>
        @endforeach
    </div>

    @if($services->count())
    <div class="service-grid">
        @foreach($services as $i => $service)
        <a href="{{ route('commission.show', $service->service_id) }}" class="service-card">
            <div class="service-thumb">
                <img src="{{ $service->image_url ?? asset('images/default-thumb.png') }}"
                    alt="{{ $service->title }}" loading="lazy">

                @if($i < 3 && $service->review_count > 0)
                    <div class="service-rank">
                        {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : '🥉') }}
                    </div>
                    @else
                    @if($service->status === 'active')
                    <span class="service-badge">OPEN</span>
                    @endif
                    @endif

                    @if($service->like_count > 0)
                    <div class="service-like-badge">
                        <i class="bi bi-heart-fill" style="font-size:9px;"></i>
                        {{ $service->like_count }}
                    </div>
                    @endif
            </div>
            <div class="service-info">
                <div class="service-name">{{ $service->title }}</div>
                <div class="service-artist">
                    <div class="artist-dot">
                        <img src="{{ $service->artist->avatar ?? asset('images/default-avatar.png') }}"
                            alt="{{ $service->artist->name ?? '' }}">
                    </div>
                    <span class="artist-name-sm">{{ $service->artist->name ?? 'Unknown' }}</span>
                </div>
                <div class="service-meta">
                    <div class="service-rating">
                        @if($service->review_count > 0)
                        <i class="bi bi-star-fill"></i>
                        <span>{{ number_format($service->avg_rating, 1) }}</span>
                        <span style="opacity:.6;">({{ $service->review_count }})</span>
                        @else
                        <span class="no-review"><i class="bi bi-star" style="font-size:11px;"></i> Baru</span>
                        @endif
                    </div>
                    <div class="service-price">Rp {{ number_format($service->base_price ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="home-empty">
        <i class="bi bi-grid-3x3-gap" style="font-size:36px;opacity:.2;display:block;margin-bottom:12px;"></i>
        Belum ada commission service.
    </div>
    @endif
</div>

{{-- ── MOST LIKED ── --}}
@if(isset($mostLiked) && $mostLiked->count())
<div class="home-divider"></div>
<div class="home-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-heart-fill" style="color:var(--pink);font-size:15px;"></i>
            Most Liked
            <span class="section-badge" style="background:rgba(244,114,182,.1);color:var(--pink);border-color:rgba(244,114,182,.25);">
                Community Favorites
            </span>
        </div>
        <a href="{{ route('explore', ['sort' => 'popular']) }}" class="section-link">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="service-grid">
        @foreach($mostLiked as $i => $service)
        <a href="{{ route('commission.show', $service->service_id) }}" class="service-card">
            <div class="service-thumb">
                <img src="{{ $service->image_url ?? asset('images/default-thumb.png') }}"
                    alt="{{ $service->title }}" loading="lazy">
                <div class="service-like-badge">
                    <i class="bi bi-heart-fill" style="font-size:9px;"></i>
                    {{ $service->like_count }}
                </div>
                @if($i === 0)
                <div class="service-rank" style="color:var(--pink);">❤️</div>
                @endif
            </div>
            <div class="service-info">
                <div class="service-name">{{ $service->title }}</div>
                <div class="service-artist">
                    <div class="artist-dot">
                        <img src="{{ $service->artist->avatar ?? asset('images/default-avatar.png') }}"
                            alt="{{ $service->artist->name ?? '' }}">
                    </div>
                    <span class="artist-name-sm">{{ $service->artist->name ?? 'Unknown' }}</span>
                </div>
                <div class="service-meta">
                    <div class="service-rating">
                        @if($service->review_count > 0)
                        <i class="bi bi-star-fill"></i>
                        <span>{{ number_format($service->avg_rating, 1) }}</span>
                        <span style="opacity:.6;">({{ $service->review_count }})</span>
                        @else
                        <span class="no-review"><i class="bi bi-heart" style="font-size:11px;"></i> Disukai</span>
                        @endif
                    </div>
                    <div class="service-price">Rp {{ number_format($service->base_price ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ── RISING ARTISTS ── --}}
@if(isset($newServices) && $newServices->count())
<div class="home-divider"></div>
<div class="home-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-lightning-fill" style="color:var(--accent);font-size:15px;"></i>
            Rising Artists
            <span class="section-badge" style="background:rgba(34,197,94,.12);color:var(--green);border-color:rgba(34,197,94,.25);">New</span>
        </div>
        <a href="{{ route('explore', ['sort' => 'newest']) }}" class="section-link">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="service-grid">
        @foreach($newServices as $service)
        <a href="{{ route('commission.show', $service->service_id) }}" class="service-card">
            <div class="service-thumb">
                <img src="{{ $service->image_url ?? asset('images/default-thumb.png') }}"
                    alt="{{ $service->title }}" loading="lazy">
                <span class="service-badge new">NEW</span>
                @if($service->like_count > 0)
                <div class="service-like-badge">
                    <i class="bi bi-heart-fill" style="font-size:9px;"></i>
                    {{ $service->like_count }}
                </div>
                @endif
            </div>
            <div class="service-info">
                <div class="service-name">{{ $service->title }}</div>
                <div class="service-artist">
                    <div class="artist-dot">
                        <img src="{{ $service->artist->avatar ?? asset('images/default-avatar.png') }}"
                            alt="{{ $service->artist->name ?? '' }}">
                    </div>
                    <span class="artist-name-sm">{{ $service->artist->name ?? 'Unknown' }}</span>
                </div>
                <div class="service-meta">
                    <div class="service-rating">
                        <span class="no-review"><i class="bi bi-star" style="font-size:11px;"></i> Belum ada review</span>
                    </div>
                    <div class="service-price">Rp {{ number_format($service->base_price ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection
