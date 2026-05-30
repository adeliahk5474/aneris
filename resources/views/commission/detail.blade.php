{{-- resources/views/commission/detail.blade.php --}}
@extends('layouts.app')

@section('title', $service->title . ' — Aneris')

@section('content')

@include('layouts.ordernav')
@vite('resources/css/commission/detail.css')
{{-- ORBS --}}
<div class="page-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
</div>

{{-- LIGHTBOX --}}
<div class="lightbox" id="lightbox">
    <button class="lb-nav prev" id="lbPrev" onclick="lbNav(-1)"><i class="bi bi-chevron-left"></i></button>
    <div class="lightbox-inner">
        <button class="lb-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
        <img src="" id="lightboxImg" alt="">
    </div>
    <button class="lb-nav next" id="lbNext" onclick="lbNav(1)"><i class="bi bi-chevron-right"></i></button>
</div>

<div class="detail-outer">
    <div class="detail-wrap">

        {{-- ════ LEFT ════ --}}
        <div>

            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('explore') }}">Explore</a>
                <i class="bi bi-chevron-right"></i>
                <span style="color:var(--text);">{{ Str::limit($service->title, 40) }}</span>
            </div>

            <div class="artist-row">
                <img src="{{ $service->artist->avatar ?? asset('images/default-avatar.png') }}"
                    class="artist-avatar" alt="{{ $service->artist->name ?? '' }}">
                <div>
                    <div class="artist-name">{{ $service->artist->name ?? 'Unknown Artist' }}</div>
                    <div class="artist-sub">
                        <span>{{ $service->artist->bio ?? 'Digital Artist' }}</span>
                        @if(($service->artist->role ?? '') === 'artist')
                            <span class="artist-badge">Verified</span>
                        @endif
                    </div>
                </div>
                <div class="artist-actions">
                    <a href="{{ route('profile.show', $service->artist_id) }}" class="btn-follow">
                        <i class="bi bi-person-fill"></i> Profil
                    </a>
                </div>
            </div>

            {{-- Title + like button --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px;">
                <h1 class="service-title" style="margin-bottom:0;">{{ $service->title }}</h1>

                {{-- ── LIKE BUTTON ── --}}
                @auth
                <button
                    class="like-btn {{ $isLiked ? 'liked' : '' }}"
                    id="like-btn"
                    onclick="toggleLike()"
                    title="{{ $isLiked ? 'Unlike' : 'Like' }}"
                    style="flex-shrink:0;margin-top:6px;">
                    <i class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }}" id="like-icon"></i>
                    <span id="like-count">{{ $likeCount }}</span>
                </button>
                @else
                <a href="{{ route('auth.form') }}"
                    class="like-btn"
                    title="Login untuk like"
                    style="flex-shrink:0;margin-top:6px;">
                    <i class="bi bi-heart"></i>
                    <span>{{ $likeCount }}</span>
                </a>
                @endauth
            </div>

            <div class="stat-chips">
                @if($service->category)
                    <span class="stat-chip accent"><i class="bi bi-tag-fill"></i> {{ $service->category->name }}</span>
                @endif
                @if($avgRating > 0)
                    <span class="stat-chip yellow">
                        <i class="bi bi-star-fill"></i>
                        {{ number_format($avgRating, 1) }}
                        <span style="color:var(--muted);font-weight:400;">({{ $reviewCount }})</span>
                    </span>
                @endif
                <span class="stat-chip green">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ $service->order_count ?? 0 }} selesai
                </span>
                @if($service->estimated_days || $service->turnaround)
                    <span class="stat-chip">
                        <i class="bi bi-clock" style="color:var(--accent)"></i>
                        {{ $service->estimated_days ? $service->estimated_days . ' hari' : $service->turnaround }}
                    </span>
                @endif
                @if($service->max_revisions !== null)
                    <span class="stat-chip">
                        <i class="bi bi-arrow-counterclockwise" style="color:var(--muted2)"></i>
                        {{ $service->max_revisions }}x revisi
                    </span>
                @endif
                @if($likeCount > 0)
                    <span class="stat-chip pink">
                        <i class="bi bi-heart-fill"></i>
                        {{ $likeCount }} likes
                    </span>
                @endif
            </div>

            {{-- ════ GALLERY ════ --}}
            <div class="gallery-wrap" id="galleryWrap">
                <div class="gallery-main" id="galleryMain">
                    <div class="gallery-main-frame">
                        <img
                            src="{{ $allImages[0] }}"
                            class="gallery-main-img"
                            id="mainImg"
                            alt="{{ $service->title }}"
                            onclick="openLightbox(currentIndex)">
                    </div>

                    {{-- Nav arrows: hanya tampil jika >1 gambar --}}
                    @if(count($allImages) > 1)
                    <button class="gallery-nav prev" id="galleryPrev" onclick="galleryNav(-1)">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="gallery-nav next" id="galleryNext" onclick="galleryNav(1)">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <div class="gallery-counter" id="galleryCounter">
                        1 / {{ count($allImages) }}
                    </div>
                    @endif
                </div>

                {{-- Thumbnail strip --}}
                @if(count($allImages) > 1)
                <div class="gallery-strip" id="galleryStrip">
                    @foreach($allImages as $gi => $img)
                    <div class="gallery-thumb {{ $gi === 0 ? 'active' : '' }}"
                        id="thumb-{{ $gi }}"
                        onclick="goToImage({{ $gi }})">
                        <img src="{{ $img }}" alt="Thumbnail {{ $gi + 1 }}" loading="lazy">
                    </div>
                    @endforeach
                </div>
                <div class="swipe-hint"><i class="bi bi-arrow-left-right"></i> Geser untuk melihat gambar lain</div>
                @endif
            </div>

            {{-- Description --}}
            @if($service->description)
            <div class="sec-head"><i class="bi bi-file-text-fill"></i> Deskripsi</div>
            <div class="desc-card">
                <div class="desc-text" id="descText">{{ $service->description }}</div>
                <button class="btn-readmore" onclick="toggleDesc(event)">
                    <i class="bi bi-chevron-down"></i> Selengkapnya
                </button>
            </div>
            @endif

            {{-- Add-ons --}}
            @if(!empty($addons))
            <div class="sec-head">
                <i class="bi bi-plus-circle-fill"></i> Layanan Tambahan
                <span class="sec-count">{{ count($addons) }} opsi</span>
            </div>
            <div class="addon-grid">
                @foreach($addons as $addon)
                @php $addonPrice = (float) ($addon['price'] ?? 0); @endphp
                <div class="addon-item" onclick="toggleAddon(this, {{ $addonPrice }})">
                    <div class="addon-check"><i class="bi bi-check"></i></div>
                    <div class="addon-info">
                        <div class="addon-name-text">{{ $addon['name'] ?? '' }}</div>
                        @if(!empty($addon['description']))
                            <div class="addon-desc-text">{{ $addon['description'] }}</div>
                        @endif
                    </div>
                    <div class="addon-price-text">+Rp {{ number_format($addonPrice, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Do & Don't --}}
            @if(!empty($willList) || !empty($wontList))
            <div class="sec-head"><i class="bi bi-list-check"></i> Do & Don't</div>
            <div class="do-dont-grid">
                @if(!empty($willList))
                <div class="do-box">
                    <div class="do-dont-head"><i class="bi bi-check-circle-fill"></i> I Will Do</div>
                    @foreach($willList as $item)
                    <div class="do-dont-item">
                        <div class="do-icon will"><i class="bi bi-check-lg"></i></div>
                        <span>{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                @if(!empty($wontList))
                <div class="dont-box">
                    <div class="do-dont-head"><i class="bi bi-x-circle-fill"></i> I Won't Do</div>
                    @foreach($wontList as $item)
                    <div class="do-dont-item">
                        <div class="do-icon wont"><i class="bi bi-x-lg"></i></div>
                        <span>{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- Queue --}}
            <div class="sec-head">
                <i class="bi bi-people-fill"></i> Artist Queue
                <div style="display:flex;gap:6px;margin-left:auto;">
                    <span class="badge-pill green"><span class="dot"></span> {{ $activeCount }} Aktif</span>
                    @if($waitlistCount > 0)
                        <span class="badge-pill muted">{{ $waitlistCount }} Menunggu</span>
                    @endif
                </div>
            </div>
            <div class="queue-card">
                <div class="queue-slot-bar-wrap">
                    <div class="queue-slot-label">
                        <span>Slot Terisi</span>
                        <span class="slot-val">{{ $slotsUsed }} / {{ $slotsTotal }}</span>
                    </div>
                    <div class="queue-bar-track">
                        <div class="queue-bar-fill" style="width:{{ $slotPct }}%;"></div>
                    </div>
                </div>
                <div class="queue-table-head">
                    <span>Client</span><span>Status</span><span>Progress</span>
                </div>
                @forelse($activeOrders as $qOrder)
                @php
                $prog = match($qOrder->status) {
                    'paid'           => 10,
                    'in_progress'    => $qOrder->phase === 'coloring' ? 55 : 30,
                    'revision'       => 40,
                    'waiting_client' => 70,
                    'completed'      => 100,
                    default          => 20,
                };
                @endphp
                <div class="queue-row">
                    <span class="queue-client">{{ $qOrder->client->name ?? 'Client' }}</span>
                    <span class="q-status qs-{{ $qOrder->status }}">
                        {{ ucfirst(str_replace('_', ' ', $qOrder->status)) }}
                    </span>
                    <div class="queue-prog">
                        <div class="mini-bar">
                            <div class="mini-fill" style="width:{{ $prog }}%;"></div>
                        </div>
                        <span style="font-size:10px;color:var(--muted);min-width:28px;text-align:right;">{{ $prog }}%</span>
                    </div>
                </div>
                @empty
                <div class="queue-empty">
                    <i class="bi bi-lightning-charge" style="color:var(--green);font-size:20px;display:block;margin-bottom:6px;"></i>
                    Slot tersedia — jadilah yang pertama order!
                </div>
                @endforelse
            </div>

            {{-- Reviews --}}
            <div class="sec-head">
                <i class="bi bi-star-fill"></i> Review
                <span class="sec-count">{{ $reviewCount }} review</span>
            </div>
            @if(isset($reviews) && $reviews->count())
            <div class="review-summary">
                <div>
                    <div class="rating-big">{{ number_format($avgRating, 1) }}</div>
                    <div class="rating-stars-row">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <div class="rating-count">{{ $reviewCount }} review</div>
                </div>
            </div>
            <div class="review-list">
                @foreach($reviews->take(5) as $review)
                <div class="review-item">
                    <div class="review-top">
                        <img src="{{ $review->reviewer->avatar ?? asset('images/default-avatar.png') }}"
                            class="r-avatar" alt="">
                        <div>
                            <div class="r-name">{{ $review->reviewer->name ?? 'Anonymous' }}</div>
                            <div class="r-date">{{ $review->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="r-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <div class="r-comment">"{{ $review->comment }}"</div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="review-empty">
                <div style="font-size:32px;color:var(--muted);opacity:.3;margin-bottom:10px;"><i class="bi bi-star"></i></div>
                <div style="font-size:13px;color:var(--muted);">Belum ada review. Jadilah yang pertama!</div>
            </div>
            @endif

        </div>{{-- /LEFT --}}

        {{-- ════ RIGHT ════ --}}
        <div>
            <div class="purchase-card">

                <div class="pc-service-preview">
                    <img src="{{ $service->image_url ?? asset('images/default-thumb.png') }}"
                        class="pc-thumb" alt="">
                    <div>
                        <div class="pc-title">{{ Str::limit($service->title, 48) }}</div>
                        @if($service->category)
                            <div class="pc-cat"><i class="bi bi-tag-fill"></i> {{ $service->category->name }}</div>
                        @endif
                    </div>
                </div>

                <div class="price-breakdown">
                    <div class="pb-row">
                        <span>Harga</span>
                        <span class="pb-val" id="basePriceDisp">Rp {{ number_format($defaultPrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="pb-row" id="addonRow" style="display:none;">
                        <span>Add-ons</span>
                        <span class="pb-val" id="addonDisp" style="color:var(--green);">+Rp 0</span>
                    </div>
                </div>

                <div class="total-section">
                    <div class="total-label">Starting from</div>
                    <div class="total-price" id="totalPrice">Rp {{ number_format($defaultPrice, 0, ',', '.') }}</div>
                </div>

                @if($isArtist)
                <div class="artist-notice">
                    <i class="bi bi-palette-fill"></i>
                    Artist tidak bisa memesan komisi
                </div>
                @elseif($hasPendingOrder)
                <a href="{{ route('cart.index', ['tab' => 'checkout']) }}" class="btn-already-ordered">
                    <i class="bi bi-check-circle-fill"></i>
                    Sudah di Cart — Selesaikan Pembayaran
                </a>
                @elseif(auth()->check())
                <form action="{{ route('order.store') }}" method="POST" id="order-form">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->service_id }}">
                    <input type="hidden" name="payment_method" value="midtrans">
                    <input type="hidden" name="note" value="">
                    <input type="hidden" name="selected_addons" id="selectedAddonsInput" value="">
                    <button type="submit" class="btn-order" id="btn-order" onclick="handleOrder(event)">
                        <i class="bi bi-bag-check-fill"></i> Order Sekarang
                    </button>
                </form>
                @else
                <a href="{{ route('auth.form') }}" class="btn-order">
                    <i class="bi bi-bag-check-fill"></i> Order Sekarang
                </a>
                @endif

                @if(!$isOwner)
                @auth
                <a href="{{ route('chat.index', ['user_id' => $service->artist_id]) }}" class="btn-msg">
                    <i class="bi bi-chat-dots-fill"></i> Chat Artist
                </a>
                @else
                <a href="{{ route('auth.form') }}" class="btn-msg">
                    <i class="bi bi-chat-dots-fill"></i> Chat Artist
                </a>
                @endauth
                @endif

                <div class="protect-box">
                    <i class="bi bi-shield-check protect-icon"></i>
                    <span class="protect-text">
                        <strong style="color:var(--green);">Aneris Protected</strong> —
                        Pembayaranmu ditahan aman sampai komisi selesai dan kamu setujui hasilnya.
                    </span>
                </div>

                <div class="meta-info-grid">
                    <div class="meta-info-box">
                        <div class="mib-val accent">{{ $service->estimated_days ?? 7 }}</div>
                        <div class="mib-lbl">Est. Hari</div>
                    </div>
                    <div class="meta-info-box">
                        <div class="mib-val" style="color:var(--yellow);">{{ $service->max_revisions ?? 2 }}</div>
                        <div class="mib-lbl">Revisi</div>
                    </div>
                    <div class="meta-info-box">
                        <div class="mib-val green">{{ $slotsLeft }}</div>
                        <div class="mib-lbl">Slot Sisa</div>
                    </div>
                    <div class="meta-info-box">
                        <div class="mib-val" style="color:var(--muted2);">{{ $service->order_count ?? 0 }}</div>
                        <div class="mib-lbl">Selesai</div>
                    </div>
                </div>

                <div class="slot-indicator">
                    <div class="si-label">
                        <span>Antrian</span>
                        <span>{{ $slotsLeft }} slot tersisa</span>
                    </div>
                    <div class="si-bar">
                        <div class="si-fill" style="width:{{ 100 - $slotPct }}%;"></div>
                    </div>
                </div>

            </div>
        </div>{{-- /RIGHT --}}

    </div>
</div>


<script>
window.commissionDetail = {
    images: @json($allImages),
    isLiked: @json($isLiked),
    likeCount: @json($likeCount),
    basePrice: @json($defaultPrice),
    likeToggleUrl: @json(route('like.toggle')),
    serviceId: @json($service->service_id),
};
</script>
@vite('resources/js/commission/detail.js')

@endsection
