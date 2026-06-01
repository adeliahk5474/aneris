{{-- resources/views/dashboards/artist.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Artist — Aneris')

@section('content')
@vite('resources/css/dashboards/artist.css')

{{-- ══════════════════════════════════════════
     SEND FILE MODAL
════════════════════════════════════════════ --}}
<div class="send-modal" id="sendModal">
    <div class="send-modal-card">
        <div class="send-modal-title">Kirim Hasil Kerja</div>
        <div class="send-modal-sub" id="sendModalSub">Upload file hasil untuk dikirim ke client</div>
        <form action="{{ route('order.send') }}" method="POST" enctype="multipart/form-data" id="sendForm">
            @csrf
            <input type="hidden" name="order_id" id="sendOrderId">
            <div class="send-upload-area" onclick="document.getElementById('resultFile').click()">
                <input type="file" name="result_file" id="resultFile"
                    accept="image/*,.pdf,.zip,.rar" required
                    onchange="showFileName(this)">
                <i class="bi bi-cloud-arrow-up"></i>
                <p id="uploadText">Klik untuk upload hasil kerja</p>
                <p style="font-size:11px; color:var(--muted); margin-top:4px;">JPG, PNG, PDF, ZIP — Maks 20MB</p>
            </div>
            <button type="submit" class="btn-send-submit"><i class="bi bi-send"></i> Kirim ke Client</button>
        </form>
        <button class="btn-cancel-modal" onclick="closeSendModal()">Batal</button>
    </div>
</div>

{{-- ══════════════════════════════════════════
     REVIEW MODAL (artist → client)
════════════════════════════════════════════ --}}
<div class="review-modal" id="reviewModal">
    <div class="review-modal-card">
        <button class="modal-close" onclick="closeReviewModal()"><i class="bi bi-x"></i></button>
        <div class="review-modal-title">Review Client</div>
        <div class="review-modal-sub" id="reviewModalSub">Beri penilaian untuk client ini</div>

        <form action="{{ route('review.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" id="reviewOrderId">

            <label class="form-label">Overall Rating *</label>
            <div class="star-group" style="margin-bottom:14px;">
                @for($i=5;$i>=1;$i--)
                <input type="radio" name="overall_rating" id="aor{{ $i }}" value="{{ $i }}" {{ $i===5?'required':'' }}>
                <label for="aor{{ $i }}" title="{{ $i }} bintang">★</label>
                @endfor
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px;">
                @foreach(['brief'=>'Kejelasan Brief','attitude'=>'Sikap','revision'=>'Revisi'] as $key => $lbl)
                <div>
                    <label class="form-label">{{ $lbl }}</label>
                    <select name="rating_{{ $key }}"
                        style="width:100%; background:var(--surface2); border:1px solid rgba(255,255,255,.1); border-radius:8px; padding:8px 10px; color:var(--text); font-family:'Outfit',sans-serif; font-size:13px; outline:none;">
                        <option value="">—</option>
                        @for($i=1;$i<=5;$i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
                    </select>
                </div>
                @endforeach
            </div>

            <label class="form-label">Komentar (opsional)</label>
            <textarea name="comment" class="form-textarea" placeholder="Pengalaman bekerja dengan client ini..."></textarea>

            <div class="blind-notice">
                <i class="bi bi-eye-slash"></i>
                Review tersembunyi sampai client juga submit atau 14 hari berlalu.
            </div>

            <button type="submit" class="btn-submit">Kirim Review</button>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     DASHBOARD LAYOUT
════════════════════════════════════════════ --}}
@php
$verif = $verification ?? null;
$verifStatus = $verif?->status;
$hasSubmission = $verif && in_array($verifStatus, ['pending','in_review','approved','rejected']);
$canResubmit = $verifStatus === 'rejected'
&& ($verif->next_eligible_at === null || now()->gte($verif->next_eligible_at));
$daysLeft = $verif?->next_eligible_at
? (int) now()->diffInDays($verif->next_eligible_at, false)
: 0;
@endphp

<div class="dashboard-layout">

    {{-- SIDEBAR --}}
    <aside class="dash-sidebar">
        <div class="sidebar-artist-row">
            <img src="{{ $artist->avatar ?? asset('images/default-avatar.png') }}"
                class="sidebar-avatar" alt="{{ $artist->name }}">
            <div>
                @if($artist->isVerifiedArtist())
                <span class="verified-badge">
                    <i class="bi bi-patch-check-fill"></i> Verified Non-AI
                </span>
                @endif
                <div class="sidebar-artist-name">Artist Studio</div>
                <div class="sidebar-artist-role">Creative Pro</div>
            </div>
        </div>

        {{-- Tombol New Service --}}
        @if($artist->isVerifiedArtist())
        <a href="{{ route('upload.popup') }}" class="btn-new-service">
            <i class="bi bi-plus"></i> New Service
        </a>
        @else
        <a href="{{ route('verification.create') }}" class="btn-new-service"
            style="background:var(--surface2);color:var(--muted);border:1px solid var(--border);"
            title="Verifikasi portfolio dulu untuk membuka commission">
            <i class="bi bi-lock-fill" style="font-size:11px;"></i> New Service
        </a>
        @endif

        <nav class="sidebar-nav">
            <div class="nav-group-label">Studio</div>
            <a class="sidebar-nav-item active" onclick="switchDashTab('overview',this)" href="javascript:void(0)">
                <i class="bi bi-grid-1x2"></i> Overview
            </a>
            <a class="sidebar-nav-item" onclick="switchDashTab('orders',this)" href="javascript:void(0)">
                <i class="bi bi-bag-check"></i> Orders
                @if($pendingCommissions > 0)
                <span style="margin-left:auto; background:var(--accent); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    {{ $pendingCommissions }}
                </span>
                @endif
            </a>
            <a class="sidebar-nav-item" onclick="switchDashTab('listings',this)" href="javascript:void(0)">
                <i class="bi bi-grid-3x3-gap"></i> Listings
            </a>

            {{-- TAB PORTFOLIO VERIFIKASI --}}
            {{-- Kalau sudah ada submission → ke halaman status --}}
            {{-- Kalau belum → buka tab di dashboard --}}
            @if($hasSubmission)
            <a class="sidebar-nav-item" href="{{ route('verification.status') }}">
                <i class="bi bi-patch-check"></i> Portfolio Verif
                @if($verifStatus === 'pending' || $verifStatus === 'in_review')
                <span style="margin-left:auto; background:var(--yellow); color:#000; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    Review
                </span>
                @elseif($verifStatus === 'rejected')
                <span style="margin-left:auto; background:var(--red); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    Gagal
                </span>
                @elseif($verifStatus === 'approved')
                <span style="margin-left:auto; background:var(--green); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    ✓
                </span>
                @endif
            </a>
            @else
            <a class="sidebar-nav-item" onclick="switchDashTab('portfolio',this)" href="javascript:void(0)">
                <i class="bi bi-patch-check"></i> Portfolio Verif
            </a>
            @endif

            <div class="nav-group-label">Finance</div>
            <a class="sidebar-nav-item" href="javascript:void(0)">
                <i class="bi bi-wallet2"></i> Wallet
            </a>
        </nav>
    </aside>

    <main class="dash-main">

        {{-- ============ OVERVIEW ============ --}}
        <div id="dash-overview" class="dash-tab-panel">

            <div class="dash-page-header">
                <div>
                    <div class="dash-page-title">Dashboard Artist</div>
                    <div class="dash-page-sub">Welcome back, <span>{{ $artist->name }}</span>. Studio is live.</div>
                </div>
                @if($artist->isVerifiedArtist())
                <a href="{{ route('upload.popup') }}" class="btn-add-service">
                    <i class="bi bi-plus"></i> Tambah Jasa
                </a>
                @else
                <a href="{{ route('verification.create') }}" class="btn-add-service"
                    style="background:var(--surface2);color:var(--muted);border:1px solid var(--border);opacity:.8;">
                    <i class="bi bi-lock-fill" style="font-size:11px;"></i> Tambah Jasa
                </a>
                @endif
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon income"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <div class="stat-label">Total Pendapatan</div>
                        <div class="stat-value">Rp {{ number_format($totalEarnings,0,',','.') }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orders"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="stat-label">Pesanan Aktif</div>
                        <div class="stat-value">{{ $activeCommissions }}</div>
                        @if($pendingCommissions > 0)
                        <div class="stat-sub">{{ $pendingCommissions }} menunggu konfirmasi</div>
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rating"><i class="bi bi-star-fill"></i></div>
                    <div>
                        <div class="stat-label">Rating Rata-rata</div>
                        <div class="stat-value">
                            {{ number_format($averageRating ?? 0,1) }}
                            <span style="font-size:14px; color:var(--muted);">/5.0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-row">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <div class="chart-title">Pertumbuhan Pendapatan</div>
                            <div class="chart-sub">Last 6 months</div>
                        </div>
                        <div class="chart-year">{{ now()->year }}</div>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="revenueChart"></canvas></div>
                    <div class="chart-labels">
                        @foreach(array_slice($monthlyLabels,-6) as $label)
                        <span class="chart-label">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="actions-card">
                    <div class="actions-title">Quick Actions</div>
                    <a href="javascript:void(0)" class="action-item">
                        <div class="action-item-left"><i class="bi bi-wallet2"></i> Tarik Saldo</div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    @if($recentNotifications > 0)
                    <div class="tip-card">
                        <div class="tip-title">Perlu Perhatian</div>
                        <div class="tip-text">Ada {{ $recentNotifications }} order yang butuh tindakan. Cek segera!</div>
                    </div>
                    @endif

                    {{-- Verif CTA di Overview --}}
                    @if(!$artist->isVerifiedArtist())
                    <div class="tip-card" style="border-color:rgba(139,92,246,.3); background:var(--accent-dim); margin-top:4px;">
                        <div class="tip-title" style="color:var(--accent);">
                            <i class="bi bi-patch-exclamation" style="margin-right:4px;"></i> Belum Terverifikasi
                        </div>
                        <div class="tip-text">Submit portofoliomu untuk mendapat badge Verified Non-AI dan bisa membuka commission.</div>
                        <a href="{{ $hasSubmission ? route('verification.status') : route('verification.create') }}"
                            style="display:inline-block; margin-top:8px; font-size:12px; color:var(--accent); font-weight:600;">
                            {{ $hasSubmission ? 'Lihat Status →' : 'Ajukan Verifikasi →' }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="queue-header">
                <div class="queue-title">Active Queue</div>
                <span class="queue-view-all" onclick="switchDashTab('orders',document.querySelectorAll('.sidebar-nav-item')[1])">
                    View All Orders
                </span>
            </div>

            <div class="queue-list">
                @forelse($activeForOverview as $order)
                @php
                $maxRevisions = $order->service->max_revisions ?? 3;
                $usedRevisions = $order->revision_count ?? 0;
                @endphp
                <div class="queue-item {{ in_array($order->status,['pending','paid']) ? 'pending-item' : ($order->status==='waiting_client' ? 'waiting-item' : ($order->status==='revision_requested' ? 'revision-requested-item' : ($order->status==='revision' ? 'revision-item' : ''))) }}">
                    <img src="{{ $order->service->image_url ?? asset('images/default-thumb.png') }}"
                        class="queue-thumb" alt="">
                    <div class="queue-info">
                        <div class="queue-service-name">{{ $order->service->title ?? 'Commission' }}</div>
                        <div class="queue-meta">
                            <span>{{ $order->client->name ?? '—' }}</span>
                            <span class="status-badge badge-{{ $order->status }}">
                                {{ [
                                    'pending'=>'Pending','paid'=>'Paid','in_progress'=>'In Progress',
                                    'revision_requested'=>'Revision Requested','revision'=>'Revising',
                                    'waiting_client'=>'Waiting Client','completed'=>'Completed','canceled'=>'Canceled'
                                ][$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </div>
                        @if($usedRevisions > 0 || $maxRevisions > 0)
                        <div class="revision-progress">
                            @for($ri=0;$ri<$maxRevisions;$ri++)
                                <div class="revision-dot {{ $ri < $usedRevisions ? 'used' : 'avail' }}">
                        </div>
                        @endfor
                        <span class="revision-label">{{ $usedRevisions }}/{{ $maxRevisions }} revisi</span>
                    </div>
                    @endif
                </div>
                <div class="queue-actions">
                    @if($order->status === 'revision_requested')
                    <form action="{{ route('order.revision') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="qa-btn accept-revision">
                            <i class="bi bi-pencil"></i> Mulai Revisi
                        </button>
                    </form>
                    @elseif(in_array($order->status,['in_progress','revision']))
                    <button class="qa-btn send" onclick="openSendModal('{{ $order->order_id }}','{{ $order->phase ?? 'sketch' }}')">
                        <i class="bi bi-upload"></i> Kirim File
                    </button>
                    @elseif($order->status === 'waiting_client')
                    <span class="qa-btn waiting-state"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                    @elseif(in_array($order->status,['pending','paid']))
                    <form action="{{ route('order.accept') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <button type="submit" class="qa-btn accept"><i class="bi bi-check-lg"></i> Terima</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
                <i class="bi bi-inbox" style="font-size:36px; opacity:.2; display:block; margin-bottom:12px;"></i>
                Tidak ada order aktif.
            </div>
            @endforelse
        </div>

</div>{{-- /overview --}}

{{-- ============ ORDERS ============ --}}
<div id="dash-orders" class="dash-tab-panel" style="display:none;">

    <div class="dash-page-header">
        <div>
            <div class="dash-page-title">Semua Orders</div>
            <div class="dash-page-sub">Kelola semua pesanan masuk</div>
        </div>
    </div>

    <div class="filter-chips">
        @foreach(['all'=>'Semua','pending'=>'Pending','in_progress'=>'In Progress','revision_requested'=>'Revision Req','revision'=>'Revising','waiting_client'=>'Waiting Client','completed'=>'Completed','canceled'=>'Dibatalkan'] as $st => $lbl)
        <div class="chip {{ $st==='all'?'active':'' }}" onclick="filterOrders('{{ $st }}',this)">
            {{ $lbl }}
            @if($st==='pending' && $pendingCommissions > 0)
            <span style="background:var(--yellow);color:#000;border-radius:999px;padding:0 5px;font-size:10px;margin-left:3px;">{{ $pendingCommissions }}</span>
            @endif
        </div>
        @endforeach
    </div>

    @if(session('success'))
    <div class="alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert-error"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="queue-list" id="orders-list">
        @forelse($incomingOrders as $order)
        @php
        $phases = ['Sketching','Waiting','Coloring','Finalizing'];
        $curPhase = match($order->phase ?? 'sketch') {
        'sketch' => 0, 'coloring' => 2, 'rendering' => 3, 'final' => 4, default => 0,
        };
        $itemClass = match($order->status) {
        'pending','paid' => 'pending-item',
        'waiting_client' => 'waiting-item',
        'revision' => 'revision-item',
        'revision_requested' => 'revision-requested-item',
        default => '',
        };
        $statusLabels = [
        'pending'=>'Pending','paid'=>'Paid','in_progress'=>'In Progress',
        'revision_requested'=>'Revision Requested','revision'=>'Revising',
        'waiting_client'=>'Waiting Client','completed'=>'Completed','canceled'=>'Canceled'
        ];
        $maxRevisions = $order->service->max_revisions ?? 3;
        $usedRevisions = $order->revision_count ?? 0;
        $remainRevisions = max(0, $maxRevisions - $usedRevisions);
        $artistReviewed = $order->reviews()->where('reviewer_type','artist')->exists();
        @endphp

        <div class="queue-item {{ $itemClass }}" data-status="{{ $order->status }}">
            <img src="{{ $order->service->image_url ?? asset('images/default-thumb.png') }}"
                class="queue-thumb" alt="">

            <div class="queue-info">
                <div class="queue-service-name">{{ $order->service->title ?? 'Commission' }}</div>
                <div class="queue-meta">
                    <span>{{ $order->client->name ?? '—' }}</span>
                    <span class="status-badge badge-{{ $order->status }}">
                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        @if($order->phase && in_array($order->status,['in_progress','revision','waiting_client']))
                        — {{ ucfirst($order->phase) }}
                        @endif
                    </span>
                </div>

                @if($maxRevisions > 0)
                <div class="revision-progress" style="margin-top:6px;">
                    @for($ri=0;$ri<$maxRevisions;$ri++)
                        <div class="revision-dot {{ $ri < $usedRevisions ? 'used' : 'avail' }}"
                        title="{{ $ri < $usedRevisions ? 'Terpakai' : 'Tersisa' }}">
                </div>
                @endfor
                <span class="revision-label">
                    {{ $usedRevisions }}/{{ $maxRevisions }} revisi
                    @if($remainRevisions === 0)
                    <span style="color:var(--red); font-weight:700;">· Habis</span>
                    @else
                    · {{ $remainRevisions }} sisa
                    @endif
                </span>
            </div>
            @endif

            <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                Rp {{ number_format($order->total_price,0,',','.') }} ·
                {{ $order->created_at->format('d M Y') }}
            </div>
        </div>

        <div class="phase-track">
            @foreach($phases as $pi => $ph)
            <div class="phase-step {{ $pi < $curPhase ? 'done' : ($pi === $curPhase ? 'active' : '') }}">
                <div class="phase-dot"></div>
                <div class="phase-label">{{ $ph }}</div>
            </div>
            @endforeach
        </div>

        <div class="queue-actions">
            @if(in_array($order->status,['pending','paid']))
            <form action="{{ route('order.accept') }}" method="POST">
                @csrf <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="qa-btn accept"><i class="bi bi-check-lg"></i> Terima</button>
            </form>
            <form action="{{ route('order.reject') }}" method="POST"
                onsubmit="return confirm('Tolak order ini?')">
                @csrf <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="qa-btn reject"><i class="bi bi-x-lg"></i> Tolak</button>
            </form>

            @elseif($order->status === 'revision_requested')
            <form action="{{ route('order.revision') }}" method="POST">
                @csrf <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <input type="hidden" name="action" value="accept">
                <button type="submit" class="qa-btn accept-revision">
                    <i class="bi bi-pencil-square"></i> Mulai Revisi
                </button>
            </form>
            <a href="{{ route('chat.index',['order_id'=>$order->order_id]) }}"
                class="qa-btn chat"><i class="bi bi-chat-dots"></i> Chat</a>

            @elseif(in_array($order->status,['in_progress','revision']))
            <button class="qa-btn send"
                onclick="openSendModal('{{ $order->order_id }}','{{ $order->phase ?? 'sketch' }}')">
                <i class="bi bi-upload"></i> Kirim File
            </button>
            <a href="{{ route('chat.index',['order_id'=>$order->order_id]) }}"
                class="qa-btn chat"><i class="bi bi-chat-dots"></i> Chat</a>

            @elseif($order->status === 'waiting_client')
            <span class="qa-btn waiting-state"><i class="bi bi-hourglass-split"></i> Menunggu</span>
            <a href="{{ route('chat.index',['order_id'=>$order->order_id]) }}"
                class="qa-btn chat"><i class="bi bi-chat-dots"></i> Chat</a>

            @elseif($order->status === 'completed')
            <span class="qa-btn done-state"><i class="bi bi-check-circle"></i> Selesai</span>
            @if(!$artistReviewed)
            <button class="qa-btn send"
                onclick="openReviewModal('{{ $order->order_id }}','{{ $order->client->name ?? 'Client' }}')">
                <i class="bi bi-star"></i> Review Client
            </button>
            @endif

            @elseif($order->status === 'canceled')
            <span class="qa-btn reject" style="pointer-events:none; opacity:.6;">Dibatalkan</span>
            @endif
        </div>

        <a href="{{ route('order.detail',$order->order_id) }}"
            class="queue-detail-link" title="Lihat Detail">
            <i class="bi bi-arrow-right" style="font-size:14px;"></i>
        </a>
    </div>
    @empty
    <div style="text-align:center; padding:60px; color:var(--muted); font-size:14px;">
        <i class="bi bi-inbox" style="font-size:40px; opacity:.2; display:block; margin-bottom:12px;"></i>
        Belum ada orders.
    </div>
    @endforelse
</div>

</div>{{-- /orders --}}

{{-- ============ LISTINGS ============ --}}
<div id="dash-listings" class="dash-tab-panel" style="display:none;">
    <div class="dash-page-header">
        <div>
            <div class="dash-page-title">Jasa Saya</div>
            <div class="dash-page-sub">Kelola semua jasa commission kamu</div>
        </div>
        @if($artist->isVerifiedArtist())
        <a href="{{ route('upload.popup') }}" class="btn-add-service">
            <i class="bi bi-plus"></i> New Service
        </a>
        @else
        <a href="{{ route('verification.create') }}" class="btn-add-service"
            style="background:var(--surface2);color:var(--muted);border:1px solid var(--border);opacity:.8;">
            <i class="bi bi-lock-fill" style="font-size:11px;"></i> New Service
        </a>
        @endif
    </div>

    @if($myServices->count())
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
        @foreach($myServices as $svc)
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
            <div style="aspect-ratio:16/9; overflow:hidden; background:var(--surface2);">
                <img src="{{ $svc->image_url ?? asset('images/default-thumb.png') }}"
                    style="width:100%; height:100%; object-fit:cover;" alt="">
            </div>
            <div style="padding:14px;">
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $svc->title }}</div>
                <div style="font-size:12px; color:var(--muted); margin-bottom:2px;">
                    {{ $svc->category->name ?? '' }} · Rp {{ number_format($svc->base_price ?? 0,0,',','.') }}
                </div>
                <div style="font-size:11px; color:var(--muted); margin-bottom:8px;">
                    Max revisi: <strong style="color:var(--text);">{{ $svc->max_revisions ?? 3 }}x</strong>
                </div>
                <span style="font-size:10px; font-weight:700; padding:3px 8px; border-radius:999px; display:inline-block; margin-bottom:10px;
                                    background:{{ $svc->status==='active' ? 'var(--green-dim)' : 'var(--surface2)' }};
                                    color:{{ $svc->status==='active' ? 'var(--green)' : 'var(--muted)' }};">
                    {{ strtoupper($svc->status) }}
                </span>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('commission.show',$svc->service_id) }}"
                        style="flex:1; text-align:center; padding:7px; border-radius:7px; border:1px solid var(--border2); background:var(--surface2); color:var(--text); font-size:12px; font-weight:600; text-decoration:none;">
                        Lihat
                    </a>
                    <form action="{{ route('commission.delete',$svc->service_id) }}" method="POST" style="flex:1;">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus jasa ini?')"
                            style="width:100%; padding:7px; border-radius:7px; border:1px solid rgba(239,68,68,.3); background:rgba(239,68,68,.1); color:#f87171; font-size:12px; font-weight:600; cursor:pointer; font-family:'Outfit',sans-serif;">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center; padding:80px; color:var(--muted);">
        <i class="bi bi-grid-3x3-gap" style="font-size:40px; opacity:.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px;">Belum ada jasa.
            @if($artist->isVerifiedArtist())
            <a href="{{ route('upload.popup') }}" style="color:var(--accent);">Buat sekarang</a>
            @else
            <a href="{{ route('verification.create') }}" style="color:var(--accent);">Verifikasi dulu</a> untuk membuka commission.
            @endif
        </p>
    </div>
    @endif
</div>{{-- /listings --}}

{{-- ============ TAB: PORTFOLIO VERIFIKASI ============ --}}
{{-- Hanya tampil jika belum ada submission sama sekali --}}
@if(!$hasSubmission)
<div id="dash-portfolio" class="dash-tab-panel" style="display:none;">

    <div class="dash-page-header">
        <div>
            <div class="dash-page-title">Verifikasi Portfolio</div>
            <div class="dash-page-sub">Buktikan karyamu adalah karya manusia asli.</div>
        </div>
    </div>

    <form action="{{ route('verification.store') }}" method="POST" enctype="multipart/form-data"
        class="verif-form">
        @csrf

        @if($errors->any())
        <div class="alert-error" style="margin-bottom:16px;">
            <i class="bi bi-exclamation-circle"></i>
            <ul style="margin:6px 0 0 16px; padding:0;">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="verif-section">
            <div class="verif-section-title">
                <i class="bi bi-images"></i> File Portofolio
            </div>
            <div class="verif-section-desc">
                Upload 3–10 karya terbaikmu. Sertakan minimal 1 file WIP (work-in-progress) atau sketch layer.
                Format: JPG, PNG, PSD, PDF · Maks 20MB per file.
            </div>
            <div class="verif-upload-area" id="portfolioDropZone">
                <input type="file" name="portfolio_files[]" id="portfolioFiles"
                    accept="image/*,.pdf,.psd,.psb" multiple required
                    onchange="updatePortfolioPreview(this)">
                <i class="bi bi-cloud-arrow-up" style="font-size:28px; color:var(--muted); display:block; margin-bottom:8px;"></i>
                <div style="font-size:13px; color:var(--muted);">Klik atau drag file ke sini</div>
                <div style="font-size:11px; color:var(--muted); margin-top:4px;">Minimal 3 file, maksimal 10 file</div>
            </div>
            <div id="portfolioPreview" class="verif-file-preview"></div>
        </div>

        <div class="verif-section">
            <div class="verif-section-title">
                <i class="bi bi-globe2"></i> Link Sosial Media / Portfolio Online
            </div>
            <div class="verif-section-desc">
                Tambahkan minimal 1 link (Instagram, ArtStation, Behance, Twitter/X, dll) yang menampilkan karya dan aktivitas aslimu.
            </div>
            <div id="socialLinksContainer">
                <div class="verif-social-row">
                    <input type="url" name="social_media_links[]"
                        class="form-input" placeholder="https://instagram.com/username"
                        style="margin-bottom:0;" required>
                    <button type="button" class="verif-remove-link" onclick="removeSocialRow(this)" style="display:none;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <button type="button" class="verif-add-link" onclick="addSocialRow()">
                <i class="bi bi-plus"></i> Tambah Link
            </button>
        </div>

        <div class="verif-section">
            <div class="verif-section-title">
                <i class="bi bi-shield-check"></i> Pernyataan
            </div>
            <label class="verif-checkbox-row">
                <input type="checkbox" name="declaration" value="1" required>
                <span>
                    Saya menyatakan bahwa semua karya yang diupload adalah hasil kerja saya sendiri,
                    bukan hasil AI generatif, dan saya berhak atas karya tersebut.
                </span>
            </label>
        </div>

        <button type="submit" class="btn-verif-submit">
            <i class="bi bi-send-check"></i> Ajukan Verifikasi
        </button>

    </form>

</div>{{-- /portfolio --}}
@endif

</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    window.artistDashboard = {
        monthlyLabels: @json(array_slice($monthlyLabels, -6)),
        monthlyEarnings: @json(array_slice($monthlyEarnings, -6)),
    };
</script>
@vite('resources/js/dashboards/artist.js')
@endsection