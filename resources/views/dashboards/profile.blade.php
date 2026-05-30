{{--
    resources/views/dashboards/profile.blade.php
    Digunakan untuk SEMUA role (artist & client).
    Artist  → menampilkan profil publik + tombol ke Artist Dashboard
    Client  → menampilkan profil + mini-dashboard (orders, review, wishlist)
    Toggle antara "Profile view" dan "Dashboard view" bisa dilakukan via tab.
--}}
@extends('layouts.app')

@section('title', ($user->name ?? 'Profile') . ' — Aneris')

@section('content')
@vite('resources/css/dashboards/profile.css')

{{-- ══════════════════════════════════════════
     PROFILE HEADER
════════════════════════════════════════════ --}}
<div class="profile-wrap">
    <div class="profile-top">

        {{-- Avatar --}}
        <div class="profile-avatar-wrap">
            <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
                class="profile-avatar" alt="{{ $user->name }}">
            @if($user->isVerifiedArtist())
            <span class="verified-badge">
                <i class="bi bi-patch-check-fill"></i> Verified Non-AI
            </span>
            @endif
        </div>

        {{-- Info --}}
        <div class="profile-info">

            <div class="profile-name-row">
                <span class="profile-username">{{ $user->name }}</span>
                @if($isArtist)
                <span class="profile-badge artist">Digital Artist</span>
                @else
                <span class="profile-badge">Client</span>
                @endif
                @if($user->country)
                <span style="font-size:13px; color:var(--muted);">📍 {{ $user->country }}</span>
                @endif
            </div>

            {{-- STATS --}}
            <div class="profile-stats">
                @if($isArtist)
                <div class="stat-item">
                    <span class="stat-num">{{ $postCount }}</span>
                    <span class="stat-label">Posts</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">{{ number_format($followerCount) }}</span>
                    <span class="stat-label">Followers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">{{ number_format($followingCount) }}</span>
                    <span class="stat-label">Following</span>
                </div>
                @else
                <div class="stat-item">
                    <span class="stat-num">{{ $user->ordersAsClient()->count() }}</span>
                    <span class="stat-label">Commissions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">{{ number_format($followingCount) }}</span>
                    <span class="stat-label">Following</span>
                </div>
                @endif
            </div>

            {{-- BIO --}}
            @if($user->bio)
            <div class="profile-bio">{{ $user->bio }}</div>
            @endif

            {{-- CLIENT BADGES --}}
            @if(!$isArtist && count($clientBadges))
            <div class="client-badges">
                @foreach($clientBadges as $badge)
                <span class="client-badge" style="color:{{ $badge['color'] }}; border-color:{{ $badge['color'] }}22;">
                    <i class="bi {{ $badge['icon'] }}"></i>
                    {{ $badge['label'] }}
                </span>
                @endforeach
            </div>
            @endif

            {{-- ARTIST RATING SUMMARY --}}
            @if($isArtist && $reviewStats && $reviewStats['count'] > 0)
            <div class="rating-summary">
                <div class="rating-summary-title">
                    ⭐ {{ number_format($reviewStats['avg_overall'],1) }} — {{ $reviewStats['count'] }} reviews
                </div>
                @php
                $bars = [
                'Quality' => $reviewStats['avg_quality'] ?? 0,
                'Timeliness' => $reviewStats['avg_timeliness'] ?? 0,
                'Communication' => $reviewStats['avg_communication'] ?? 0,
                ];
                @endphp
                @foreach($bars as $label => $val)
                <div class="rating-row">
                    <span class="rating-row-label">{{ $label }}</span>
                    <div class="rating-bar-wrap">
                        <div class="rating-bar-fill" style="width:{{ ($val/5)*100 }}%"></div>
                    </div>
                    <span class="rating-row-val">{{ number_format($val,1) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ACTION BUTTONS --}}
            @if($isOwner)
            <div class="profile-actions">
                <button class="btn-edit-profile"
                    onclick="document.getElementById('editProfileModal').classList.add('open')">
                    Edit Profile
                </button>
                <button class="btn-edit-profile">Share Profile</button>
            </div>
            @if($isArtist)
            <a href="{{ route('artist.dashboard') }}" class="btn-dashboard">
                <i class="bi bi-grid-1x2"></i> Artist Dashboard
            </a>
            @endif
            @else
            <div class="profile-actions">
                <form action="{{ route('follow.toggle', $user->user_id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-follow {{ $isFollowing ? 'following' : '' }}">
                        {{ $isFollowing ? 'Following' : 'Follow' }}
                    </button>
                </form>
                <a href="{{ route('chat.index', ['user_id' => $user->user_id]) }}" class="btn-message-profile">
                    <i class="bi bi-chat"></i> Message
                </a>
            </div>
            @endif

        </div>{{-- /profile-info --}}
    </div>{{-- /profile-top --}}
</div>{{-- /profile-wrap --}}

{{-- ══════════════════════════════════════════
     TABS
════════════════════════════════════════════ --}}
<div class="profile-tabs">
    @if($isArtist)
    <div class="profile-tab active" onclick="switchTab('artwork',this)">
        <i class="bi bi-grid-3x3"></i> Artwork
    </div>
    <div class="profile-tab" onclick="switchTab('commission',this)">
        <i class="bi bi-star"></i> Commission
    </div>
    <div class="profile-tab" onclick="switchTab('review',this)">
        <i class="bi bi-person-badge"></i> Reviews
    </div>
    @else
    <div class="profile-tab active" onclick="switchTab('artwork',this)">
        <i class="bi bi-image"></i> Artwork
    </div>
    @if($isOwner)
    <div class="profile-tab" onclick="switchTab('dashboard',this)">
        <i class="bi bi-grid-1x2"></i> My Orders
    </div>
    @endif
    <div class="profile-tab" onclick="switchTab('review',this)">
        <i class="bi bi-star"></i> Reviews Given
    </div>
    <div class="profile-tab" onclick="switchTab('wishlist',this)">
        <i class="bi bi-bookmark"></i> Wishlist
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════
     TAB: ARTWORK
════════════════════════════════════════════ --}}
<div class="tab-panel active" id="tab-artwork">
    @if($artworks->count())
    <div class="artwork-grid">
        @foreach($artworks as $art)
        <div class="artwork-item">
            <img src="{{ $art->image_url ?? asset('images/default-thumb.png') }}"
                alt="{{ $art->caption ?? 'artwork' }}" loading="lazy">
            <div class="artwork-overlay">
                <span class="artwork-stat"><i class="bi bi-heart-fill"></i> 0</span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state"><i class="bi bi-image"></i>
        <p>Belum ada artwork.</p>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════
     TAB: COMMISSION (artist only)
════════════════════════════════════════════ --}}
@if($isArtist)
<div class="tab-panel" id="tab-commission">
    @if($commissionServices->count())
    <div class="commission-grid">
        @foreach($commissionServices as $svc)
        <a href="{{ route('commission.show', $svc->service_id) }}" class="commission-card">
            <div class="commission-thumb">
                <img src="{{ $svc->image_url ?? asset('images/default-thumb.png') }}"
                    alt="{{ $svc->title }}" loading="lazy">
            </div>
            <div class="commission-info">
                <div class="commission-title">{{ $svc->title }}</div>
                <div class="commission-cat">{{ $svc->category->name ?? '' }}</div>
                <div class="commission-price">Rp {{ number_format($svc->base_price ?? 0, 0, ',', '.') }}</div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-state"><i class="bi bi-star"></i>
        <p>Belum ada jasa commission.</p>
    </div>
    @endif
</div>
@endif

{{-- ══════════════════════════════════════════
     TAB: CLIENT DASHBOARD / MY ORDERS
════════════════════════════════════════════ --}}
@if(!$isArtist && $isOwner)
<div class="tab-panel" id="tab-dashboard">
    <div class="client-dashboard">

        {{-- Stat cards --}}
        <div class="cl-stat-grid">
            <div class="cl-stat-card">
                <div class="cl-stat-icon orders"><i class="bi bi-bag-check"></i></div>
                <div>
                    <div class="cl-stat-label">Total Orders</div>
                    <div class="cl-stat-value">{{ $clientOrders->count() }}</div>
                </div>
            </div>
            <div class="cl-stat-card">
                <div class="cl-stat-icon spent"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <div class="cl-stat-label">Total Spent</div>
                    <div class="cl-stat-value">Rp {{ number_format($totalSpent/1000,0,'.','.') }}k</div>
                </div>
            </div>
            <div class="cl-stat-card">
                <div class="cl-stat-icon artists"><i class="bi bi-people"></i></div>
                <div>
                    <div class="cl-stat-label">Artists Hired</div>
                    <div class="cl-stat-value">{{ $artistsHired }}</div>
                </div>
            </div>
        </div>

        {{-- Order list --}}
        <div>
            <div class="cl-section-title">Semua Order</div>

            {{-- Filter chips --}}
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;">
                @foreach(['all'=>'Semua','pending'=>'Pending','in_progress'=>'In Progress','revision'=>'Revision','waiting_client'=>'Waiting','completed'=>'Selesai','canceled'=>'Dibatalkan'] as $st => $label)
                <span class="chip {{ $st === 'all' ? 'active' : '' }}"
                    onclick="filterClientOrders('{{ $st }}', this)"
                    style="padding:5px 14px; border-radius:999px; font-size:12px; font-weight:500;
                                 border:1px solid var(--border2); background:var(--surface2); color:var(--muted);
                                 cursor:pointer; user-select:none;">
                    {{ $label }}
                </span>
                @endforeach
            </div>

            <div class="cl-order-list" id="cl-orders-list">
                @forelse($clientOrders as $order)
                <a href="{{ route('order.detail', $order->order_id) }}"
                    class="cl-order-item" data-status="{{ $order->status }}">
                    @if($order->service && $order->service->image_url)
                    <img src="{{ $order->service->image_url }}" class="cl-order-thumb" alt="">
                    @else
                    <div class="cl-order-thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:22px;opacity:.3;">
                        <i class="bi bi-image"></i>
                    </div>
                    @endif
                    <div class="cl-order-info">
                        <div class="cl-order-title">{{ $order->service->title ?? 'Commission' }}</div>
                        <div class="cl-order-meta">
                            <span>{{ $order->artist->name ?? '—' }}</span>
                            <span class="status-badge s-{{ $order->status }}">
                                @php
                                $statusLabel = [
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'in_progress' => 'In Progress',
                                'revision' => 'Revising',
                                'revision_requested' => 'Revision Requested',
                                'waiting_client' => 'Waiting Review',
                                'completed' => 'Completed',
                                'canceled' => 'Canceled',
                                ];
                                @endphp
                                {{ $statusLabel[$order->status] ?? ucfirst($order->status) }}
                            </span>
                            @if($order->revision_count > 0)
                            <span style="font-size:10px; color:var(--red);">
                                Revisi {{ $order->revision_count }}/{{ $order->service->max_revisions ?? 3 }}
                            </span>
                            @endif
                            <span>{{ $order->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="cl-order-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                </a>

                {{-- Review form jika sudah completed dan belum review --}}
                @if($order->status === 'completed' && $isOwner)
                @php
                $alreadyReviewed = $order->reviews()
                ->where('reviewer_id', auth()->user()->user_id)
                ->exists();
                @endphp
                @if(!$alreadyReviewed)
                <div style="margin-top:-6px; margin-bottom:4px; padding:10px 14px;
                                        background:var(--accent-dim); border:1px dashed var(--accent);
                                        border-radius:0 0 10px 10px; font-size:12px; color:var(--accent);
                                        display:flex; align-items:center; justify-content:space-between;">
                    <span><i class="bi bi-star"></i> Beri rating untuk order ini</span>
                    <button class="btn-submit"
                        style="padding:6px 14px; font-size:12px; border-radius:6px;"
                        onclick="openReviewModal('{{ $order->order_id }}', '{{ $order->artist->name ?? 'Artist' }}')">
                        Review
                    </button>
                </div>
                @else
                <div class="review-blind-notice" style="margin-top:-6px; margin-bottom:4px; border-radius:0 0 10px 10px;">
                    <i class="bi bi-eye-slash"></i>
                    Review terkirim. Akan terlihat setelah artist juga submit atau 14 hari berlalu.
                </div>
                @endif
                @endif

                @empty
                <div class="empty-state"><i class="bi bi-bag"></i>
                    <p>Belum ada order.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     TAB: REVIEW
════════════════════════════════════════════ --}}
<div class="tab-panel" id="tab-review">
    @if($reviews->count())
    <div class="review-list">
        @foreach($reviews as $review)
        <div class="review-item">
            <div class="review-header">
                <img src="{{ $review->reviewer->avatar ?? asset('images/default-avatar.png') }}"
                    class="review-avatar" alt="">
                <div class="review-meta">
                    <div class="review-name">{{ $review->reviewer->name ?? 'Anonymous' }}</div>
                    <div class="review-date">
                        {{ $review->created_at->format('d M Y') }}
                        @if($review->order && $review->order->service)
                        · {{ $review->order->service->title }}
                        @endif
                    </div>
                </div>
                <div class="review-stars">
                    @for($i=1;$i<=5;$i++)
                        <i class="bi {{ $i <= ($review->rating ?? 0) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                </div>
            </div>

            {{-- Sub-ratings --}}
            @if($review->rating_quality || $review->rating_timeliness || $review->rating_communication)
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:8px;">
                @if($review->rating_quality)
                <span style="font-size:11px; color:var(--muted); background:var(--surface2); padding:3px 8px; border-radius:6px;">
                    Quality {{ $review->rating_quality }}/5
                </span>
                @endif
                @if($review->rating_timeliness)
                <span style="font-size:11px; color:var(--muted); background:var(--surface2); padding:3px 8px; border-radius:6px;">
                    Timeliness {{ $review->rating_timeliness }}/5
                </span>
                @endif
                @if($review->rating_communication)
                <span style="font-size:11px; color:var(--muted); background:var(--surface2); padding:3px 8px; border-radius:6px;">
                    Communication {{ $review->rating_communication }}/5
                </span>
                @endif
            </div>
            @endif

            @if($review->comment)
            <div class="review-text">{{ $review->comment }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state"><i class="bi bi-star"></i>
        <p>Belum ada review.</p>
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════
     TAB: WISHLIST (client)
════════════════════════════════════════════ --}}
@if(!$isArtist)
<div class="tab-panel" id="tab-wishlist">
    <div class="empty-state"><i class="bi bi-bookmark"></i>
        <p>Wishlist kosong.</p>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     FAB
════════════════════════════════════════════ --}}
@auth
<a href="{{ route('upload.popup') }}" class="fab" aria-label="Upload">
    <i class="bi bi-plus-lg"></i>
</a>
@endauth

{{-- ══════════════════════════════════════════
     MODAL: EDIT PROFILE
════════════════════════════════════════════ --}}
<div id="editProfileModal" class="modal-overlay">
    <div class="modal-card">
        <button class="modal-close"
            onclick="document.getElementById('editProfileModal').classList.remove('open')">
            <i class="bi bi-x"></i>
        </button>
        <div class="modal-title">Edit Profile</div>
        <form action="{{ route('profile.update-popup', $user->user_id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <label class="form-label">Nama</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-input">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-textarea">{{ $user->bio }}</textarea>
            <label class="form-label">Avatar</label>
            <input type="file" name="profile_picture" accept="image/*"
                style="font-size:13px; color:var(--muted); margin-bottom:20px; display:block;">
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: REVIEW (client → artist)
════════════════════════════════════════════ --}}
<div id="reviewModal" class="modal-overlay">
    <div class="modal-card" style="max-width:500px;">
        <button class="modal-close"
            onclick="document.getElementById('reviewModal').classList.remove('open')">
            <i class="bi bi-x"></i>
        </button>
        <div class="modal-title">Review untuk <span id="reviewArtistName"></span></div>

        <form action="{{ route('review.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" id="reviewOrderId">

            {{-- Overall rating --}}
            <label class="form-label">Overall Rating *</label>
            <div class="star-group" style="margin-bottom:14px;">
                @for($i=5;$i>=1;$i--)
                <input type="radio" name="overall_rating" id="or{{ $i }}" value="{{ $i }}" {{ $i===5?'required':'' }}>
                <label for="or{{ $i }}" title="{{ $i }} bintang">★</label>
                @endfor
            </div>

            {{-- Sub ratings --}}
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px;">
                @foreach(['quality'=>'Kualitas','timeliness'=>'Ketepatan Waktu','communication'=>'Komunikasi'] as $key => $lbl)
                <div>
                    <label class="form-label">{{ $lbl }}</label>
                    <select name="rating_{{ $key }}"
                        style="width:100%; background:var(--surface2); border:1px solid rgba(255,255,255,.1);
                                       border-radius:8px; padding:8px 10px; color:var(--text);
                                       font-family:'Outfit',sans-serif; font-size:13px; outline:none;">
                        <option value="">—</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
                @endforeach
            </div>

            <label class="form-label">Komentar (opsional)</label>
            <textarea name="comment" class="form-textarea" placeholder="Ceritakan pengalamanmu..."></textarea>

            <div class="review-blind-notice" style="margin-bottom:14px;">
                <i class="bi bi-eye-slash"></i>
                Review kamu tersembunyi dulu. Akan tampil setelah artist juga submit atau 14 hari berlalu.
            </div>

            <button type="submit" class="btn-submit">Kirim Review</button>
        </form>
    </div>
</div>

@vite('resources/js/dashboards/profile.js')
@endsection
