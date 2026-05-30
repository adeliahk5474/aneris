{{-- resources/views/commission/order-detail.blade.php --}}
@extends('layouts.app')
@section('title', 'Order Detail — Aneris')
@section('content')

@include('layouts.ordernav')

@vite('resources/css/commission/order-detail.css')

{{-- ── SEND RESULT MODAL ──────────────────────────── --}}
<div class="modal-overlay" id="sendModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal('sendModal')"><i class="bi bi-x"></i></button>
        <div class="modal-title">Kirim Hasil Kerja</div>
        <div class="modal-sub">Upload file untuk dikirim ke client — mereka akan review dan approve / minta revisi.</div>
        <form action="{{ route('order.send') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <div class="send-upload-area" onclick="document.getElementById('resultFile').click()">
                <input type="file" name="result_file" id="resultFile"
                    accept="image/*,.pdf,.zip,.rar" required onchange="showFileName(this)">
                <div class="send-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                <div class="send-upload-text" id="uploadLabel">Klik untuk upload file hasil</div>
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">JPG · PNG · PDF · ZIP — Maks 20MB</div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">
                <i class="bi bi-send-fill"></i> Kirim ke Client
            </button>
        </form>
    </div>
</div>

{{-- ── REVISION MODAL ────────────────────────────── --}}
<div class="modal-overlay" id="revisionModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal('revisionModal')"><i class="bi bi-x"></i></button>
        <div class="modal-title">Request Revisi</div>
        <div class="modal-sub">
            Sisa revisi: <strong style="color:{{ $revisionsLeft > 0 ? 'var(--green)' : 'var(--red)' }}">{{ $revisionsLeft }}x</strong>
            dari {{ $order->revision_limit }}x
        </div>
        <form action="{{ route('order.revision') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <div class="mfg">
                <label class="mfl">Apa yang perlu direvisi?</label>
                <textarea name="reason" class="mfta"
                    placeholder="Jelaskan detail perubahan yang kamu inginkan..."></textarea>
            </div>
            <button type="submit" class="btn-primary"
                style="width:100%;background:var(--red);box-shadow:0 4px 14px rgba(239,68,68,.2);">
                <i class="bi bi-arrow-counterclockwise"></i> Kirim Permintaan Revisi
            </button>
        </form>
    </div>
</div>

{{-- ── EXTENSION MODAL ───────────────────────────── --}}
<div class="modal-overlay" id="extensionModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal('extensionModal')"><i class="bi bi-x"></i></button>
        <div class="modal-title">Minta Tambahan Waktu</div>
        <div class="modal-sub">Jelaskan alasan kamu butuh tambahan waktu. Client akan approve atau reject.</div>
        <form action="{{ route('order.requestExtension') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <div class="mfg">
                <label class="mfl">Tambahan hari <span style="color:var(--muted);font-weight:400;">(maks 14 hari)</span></label>
                <input type="number" name="extension_days" class="mfi"
                    placeholder="e.g. 3" min="1" max="14" required>
            </div>
            <div class="mfg">
                <label class="mfl">Alasan</label>
                <textarea name="reason" class="mfta"
                    placeholder="Kenapa butuh tambahan waktu?" required></textarea>
            </div>
            <button type="submit" class="btn-primary"
                style="width:100%;background:var(--orange);box-shadow:0 4px 14px rgba(249,115,22,.2);">
                <i class="bi bi-hourglass-split"></i> Kirim Permintaan
            </button>
        </form>
    </div>
</div>

{{-- ── CANCEL MODAL ──────────────────────────────── --}}
<div class="modal-overlay" id="cancelModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal('cancelModal')"><i class="bi bi-x"></i></button>
        <div class="modal-title">Cancel Order</div>
        <div class="modal-sub" style="color:var(--red);">
            Tindakan ini tidak bisa dibatalkan. Yakin ingin membatalkan order ini?
        </div>
        <form action="{{ route('order.clientCancel') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <div class="mfg">
                <label class="mfl">Alasan cancel <span style="color:var(--muted);font-weight:400;">(opsional)</span></label>
                <textarea name="reason" class="mfta" placeholder="Ceritakan alasanmu..."></textarea>
            </div>
            <button type="submit" class="btn-primary"
                style="width:100%;background:var(--red);box-shadow:0 4px 14px rgba(239,68,68,.2);">
                <i class="bi bi-x-circle-fill"></i> Ya, Cancel Order
            </button>
        </form>
    </div>
</div>

{{-- ── REFUND MODAL ──────────────────────────────── --}}
<div class="modal-overlay" id="refundModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal('refundModal')"><i class="bi bi-x"></i></button>
        <div class="modal-title">Request Refund</div>
        <div class="modal-sub">
            Refund bisa diminta karena order sudah melewati batas waktu
            (<strong style="color:var(--orange);">overdue/delayed</strong>).
        </div>
        <form action="{{ route('order.requestRefund') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <div class="mfg">
                <label class="mfl">Alasan refund <span style="color:var(--red);">*</span></label>
                <textarea name="reason" class="mfta"
                    placeholder="Jelaskan mengapa kamu meminta refund..." required></textarea>
            </div>
            <button type="submit" class="btn-primary"
                style="width:100%;background:var(--orange);box-shadow:0 4px 14px rgba(249,115,22,.2);">
                <i class="bi bi-arrow-counterclockwise"></i> Kirim Permintaan Refund
            </button>
        </form>
    </div>
</div>

{{-- ── REVIEW MODAL ──────────────────────────────── --}}
<div class="modal-overlay" id="reviewModal">
    <div class="modal-card">
        <button class="modal-close" onclick="closeModal('reviewModal')"><i class="bi bi-x"></i></button>
        <div class="modal-title">Berikan Review</div>
        <div class="modal-sub">
            Review kamu tersembunyi dulu — akan tampil setelah kedua pihak submit atau batas waktu berlalu.
        </div>
        <form action="{{ route('review.store') }}" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
            <input type="hidden" name="overall_rating" id="overallRating" value="5">

            <div style="text-align:center;margin-bottom:16px;">
                <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">
                    Overall Rating
                </div>
                <div class="star-row" id="overallStars">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="star-btn active" data-val="{{ $i }}"
                        onclick="setOverallStar({{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
            </div>

            @if($isClient)
            {{-- Client → Artist --}}
            <div class="review-category">
                <div class="review-cat-title">Kualitas Artwork</div>
                <div class="mini-stars" id="stars-quality">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="mini-star active" data-cat="quality" data-val="{{ $i }}"
                        onclick="setMiniStar('quality', {{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
                <input type="hidden" name="rating_quality" id="rating-quality" value="5">
            </div>
            <div class="review-category">
                <div class="review-cat-title">Ketepatan Waktu</div>
                <div class="mini-stars" id="stars-timeliness">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="mini-star active" data-cat="timeliness" data-val="{{ $i }}"
                        onclick="setMiniStar('timeliness', {{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
                <input type="hidden" name="rating_timeliness" id="rating-timeliness" value="5">
            </div>
            <div class="review-category">
                <div class="review-cat-title">Komunikasi</div>
                <div class="mini-stars" id="stars-communication">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="mini-star active" data-cat="communication" data-val="{{ $i }}"
                        onclick="setMiniStar('communication', {{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
                <input type="hidden" name="rating_communication" id="rating-communication" value="5">
            </div>
            @else
            {{-- Artist → Client --}}
            <div class="review-category">
                <div class="review-cat-title">Kejelasan Brief</div>
                <div class="mini-stars" id="stars-brief">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="mini-star active" data-cat="brief" data-val="{{ $i }}"
                        onclick="setMiniStar('brief', {{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
                <input type="hidden" name="rating_brief" id="rating-brief" value="5">
            </div>
            <div class="review-category">
                <div class="review-cat-title">Sikap & Komunikasi</div>
                <div class="mini-stars" id="stars-attitude">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="mini-star active" data-cat="attitude" data-val="{{ $i }}"
                        onclick="setMiniStar('attitude', {{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
                <input type="hidden" name="rating_attitude" id="rating-attitude" value="5">
            </div>
            <div class="review-category">
                <div class="review-cat-title">Revisi (wajar?)</div>
                <div class="mini-stars" id="stars-revision">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="mini-star active" data-cat="revision" data-val="{{ $i }}"
                        onclick="setMiniStar('revision', {{ $i }})">
                        <i class="bi bi-star-fill"></i>
                        </button>
                        @endfor
                </div>
                <input type="hidden" name="rating_revision" id="rating-revision" value="5">
            </div>
            @endif

            <div class="mfg" style="margin-top:10px;">
                <label class="mfl">Komentar <span style="color:var(--muted);font-weight:400;">(opsional)</span></label>
                <textarea name="comment" class="mfta" placeholder="Bagikan pengalamanmu..."></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">
                <i class="bi bi-star-fill"></i> Kirim Review
            </button>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ --}}
<div class="od-wrap">

    {{-- ══════════ MAIN COLUMN ══════════ --}}
    <div class="od-main">

        @if(session('success'))
        <div class="alert-ok"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert-err"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif
        @if(session('info'))
        <div class="alert-ok" style="background:rgba(56,189,248,.1);border-color:rgba(56,189,248,.2);color:#38bdf8;">
            <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
        </div>
        @endif

        {{-- ── ORDER HEADER ─────────────────────── --}}
        <div class="order-header-card">
            <div class="order-id-row">
                <div>
                    <span class="order-id-text">Order ID: </span>
                    <span class="order-id-value">#ARS-{{ strtoupper(substr($order->order_id, 0, 8)) }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    @if($lc)
                    <div class="status-pill" style="background:{{ $lc['bg'] }};color:{{ $lc['color'] }};">
                        <i class="{{ $lc['icon'] }}"></i> {{ $lc['label'] }}
                    </div>
                    @endif
                    <div class="status-pill" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                        <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                        {{ $sc['label'] }}
                        @if($order->phase && in_array($order->status, ['in_progress','revision','waiting_client']))
                        — {{ ucfirst($order->phase) }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Late Banner --}}
            @if($lc)
            <div class="late-banner"
                style="background:{{ $lc['bg'] }};border:1px solid {{ $lc['color'] }}22;color:{{ $lc['color'] }};">
                <i class="{{ $lc['icon'] }}" style="font-size:18px;"></i>
                <div>
                    <div>
                        @if($order->late_status === 'late')
                        Order ini <strong>terlambat</strong> dari estimasi. Artist masih bisa lanjut.
                        @elseif($order->late_status === 'overdue')
                        Order ini sudah <strong>melewati batas waktu</strong> lebih dari 24 jam.
                        @else
                        Order ini sudah <strong>sangat terlambat</strong>. Client bisa cancel atau minta refund.
                        @endif
                    </div>
                    @if($order->deadline_at)
                    <div class="late-banner-sub">Deadline: {{ $order->deadline_at->format('d M Y, H:i') }}</div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Extension Status --}}
            @if($order->extension_status === 'pending')
            <div class="extension-box">
                <div class="extension-title">
                    <i class="bi bi-hourglass-split"></i>
                    Permintaan Tambahan Waktu +{{ $order->extension_days }} Hari
                </div>
                <div class="extension-reason">
                    <strong>Alasan:</strong> {{ $order->extension_reason ?? '-' }}
                    <br><small>Diminta {{ $order->extension_requested_at?->diffForHumans() }}</small>
                </div>
                @if($isClient)
                <div class="ext-btns">
                    <form action="{{ route('order.respondExtension') }}" method="POST" style="flex:1">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn-ext-approve" style="width:100%;">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('order.respondExtension') }}" method="POST" style="flex:1">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn-ext-reject" style="width:100%;">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    </form>
                </div>
                @else
                <div style="font-size:12px;color:var(--orange);font-weight:600;">
                    <i class="bi bi-hourglass"></i> Menunggu persetujuan client...
                </div>
                @endif
            </div>
            @endif

            @if($order->extension_status === 'approved')
            <div style="background:var(--gdim);border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:var(--green);display:flex;align-items:center;gap:7px;">
                <i class="bi bi-check-circle-fill"></i> Perpanjangan +{{ $order->extension_days }} hari disetujui
            </div>
            @endif
            @if($order->extension_status === 'rejected')
            <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:var(--red);display:flex;align-items:center;gap:7px;">
                <i class="bi bi-x-circle-fill"></i> Permintaan perpanjangan ditolak client
            </div>
            @endif

            {{-- Service row --}}
            <div class="svc-row">
                <img src="{{ $order->service->image_url ?? asset('images/default-thumb.png') }}"
                    class="svc-thumb" alt="{{ $order->service->title }}">
                <div class="svc-info">
                    <div class="svc-name">{{ $order->service->title ?? 'Commission Service' }}</div>
                    <div class="svc-meta">
                        @if($order->service->category)
                        <span class="svc-cat">{{ $order->service->category->name }}</span>
                        @endif
                        <span>{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- ── PHASE TRACKER ── --}}
            @if(!in_array($order->status, ['pending', 'paid', 'canceled']))
            <div class="phase-track-wrap">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">
                    Progress
                </div>
                <div class="phase-track">
                    @foreach($phases as $phKey => $ph)
                    @php
                    // Saat completed: SEMUA phase jadi 'done'
                    if ($order->status === 'completed') {
                    $phState = 'done';
                    } else {
                    $phState = $ph['idx'] < $currentPhaseIdx ? 'done'
                        : ($ph['idx']===$currentPhaseIdx ? 'active' : '' );
                        }
                        @endphp
                        <div class="phase-step {{ $phState }}">
                        <div class="phase-dot">
                            @if($phState === 'done')
                            <i class="bi bi-check-lg" style="font-size:13px;"></i>
                            @elseif($phState === 'active')
                            <i class="{{ $ph['icon'] }}" style="font-size:12px;"></i>
                            @else
                            <i class="{{ $ph['icon'] }}" style="font-size:12px;opacity:.4;"></i>
                            @endif
                        </div>
                        <div class="phase-label">{{ $ph['label'] }}</div>
                </div>
                @endforeach

                {{-- Step "Done" — hanya muncul saat completed --}}
                @if($order->status === 'completed')
                <div class="phase-step done">
                    <div class="phase-dot"
                        style="background:var(--green);border-color:var(--green);box-shadow:0 0 0 5px rgba(34,197,94,.15);">
                        <i class="bi bi-check-all" style="font-size:13px;color:#fff;"></i>
                    </div>
                    <div class="phase-label" style="color:var(--green);font-weight:700;">Done 🎉</div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- /order-header-card --}}

    {{-- ── DEADLINE & TIME ──────────────────── --}}
    @if($order->deadline_at && !in_array($order->status, ['canceled']))
    <div class="card">
        <div class="card-title"><i class="bi bi-alarm-fill"></i> Deadline & Waktu</div>
        <div class="deadline-box">
            <div class="deadline-row">
                <span class="deadline-label">Deadline</span>
                <span class="deadline-value {{ $order->late_status ?? '' }}">
                    {{ $order->deadline_at->format('d M Y, H:i') }}
                </span>
            </div>
            @if($order->status !== 'completed')
            <div style="font-size:26px;font-weight:800;letter-spacing:-0.5px;margin:8px 0 4px;
                        color:{{ now()->gt($order->deadline_at) ? 'var(--red)' : 'var(--text)' }};">
                {{ $order->time_remaining ?? '—' }}
            </div>
            @php
            $totalSecs = $order->deadline_at->diffInSeconds($order->created_at);
            $elapsed = $order->created_at->diffInSeconds(now());
            $pct = $totalSecs > 0 ? min(100, round(($elapsed / $totalSecs) * 100)) : 100;
            $barColor = $pct >= 100 ? 'var(--red)'
            : ($pct >= 80 ? 'var(--orange)'
            : ($pct >= 60 ? 'var(--yellow)' : 'var(--green)'));
            @endphp
            <div class="deadline-bar-wrap">
                <div class="deadline-bar" style="width:{{ $pct }}%;background:{{ $barColor }};"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-top:5px;">
                <span>Order placed</span>
                <span>{{ $pct }}% elapsed</span>
                <span>Deadline</span>
            </div>
            @else
            <div style="font-size:13px;color:var(--green);font-weight:600;display:flex;align-items:center;gap:5px;margin-top:6px;">
                <i class="bi bi-check-circle-fill"></i> Selesai {{ $order->updated_at->diffForHumans() }}
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── REVISION TRACKER ─────────────────── --}}
    @if(!in_array($order->status, ['pending', 'paid', 'canceled']))
    <div class="card">
        <div class="card-title"><i class="bi bi-arrow-counterclockwise"></i> Revisi</div>
        <div class="revision-track">
            <span class="rev-label">Dipakai</span>
            <div class="rev-dots">
                @for($i = 0; $i < ($order->revision_limit ?? 2); $i++)
                    <div class="rev-dot {{ $i < $order->revision_count ? 'used' : 'avail' }}">
                        {{ $i < $order->revision_count ? '✕' : ($i + 1) }}
                    </div>
                    @endfor
            </div>
            <span class="rev-count">{{ $order->revision_count }}/{{ $order->revision_limit ?? 2 }}</span>
        </div>
        @if($order->revision_count > 0)
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">
            Revisi #{{ $order->revision_count }} diminta.
            Sisa: <strong style="color:{{ $revisionsLeft > 0 ? 'var(--green)' : 'var(--red)' }}">{{ $revisionsLeft }}x</strong>
        </div>
        @endif
        @if($order->status === 'revision')
        <div style="margin-top:10px;padding:10px 14px;background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.15);border-radius:8px;font-size:12px;color:var(--accent);display:flex;align-items:center;gap:6px;">
            <i class="bi bi-arrow-counterclockwise"></i>
            <strong>Revising</strong> — Artist sedang merevisi
        </div>
        @elseif($order->status === 'revision_requested')
        <div style="margin-top:10px;padding:10px 14px;background:rgba(250,204,21,.08);border:1px solid rgba(250,204,21,.15);border-radius:8px;font-size:12px;color:var(--yellow);display:flex;align-items:center;gap:6px;">
            <i class="bi bi-hourglass-split"></i>
            <strong>Revision Requested</strong> — Menunggu artist menerima
        </div>
        @endif
    </div>
    @endif

    {{-- ── HASIL KERJA ──────────────────────── --}}
    @if($order->final_file)
    <div class="card">
        <div class="card-title"><i class="bi bi-file-earmark-image-fill"></i> Hasil Kerja</div>
        <div class="result-preview">
            @php
            $ext = pathinfo($order->final_file, PATHINFO_EXTENSION);
            $isImage = in_array(strtolower($ext), ['jpg','jpeg','png','webp','gif']);
            @endphp
            @if($isImage)
            <img src="{{ asset('storage/'.$order->final_file) }}"
                class="result-thumb" alt="result">
            @else
            <div class="result-thumb"
                style="display:flex;align-items:center;justify-content:center;font-size:24px;color:var(--muted);">
                <i class="bi bi-file-earmark-zip"></i>
            </div>
            @endif
            <div class="result-info" style="flex:1">
                <div class="result-name">{{ basename($order->final_file) }}</div>
                <div class="result-sub">Dikirim {{ $order->updated_at->diffForHumans() }}</div>
            </div>
            <a href="{{ asset('storage/'.$order->final_file) }}"
                class="result-download" download target="_blank">
                <i class="bi bi-download"></i> Download
            </a>
        </div>

        {{-- Client actions on result --}}
        @if($isClient && $order->status === 'waiting_client')
        <div class="action-section" style="margin-top:14px;">
            <form action="{{ route('order.accept') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="btn-primary green" style="width:100%;">
                    <i class="bi bi-check-circle-fill"></i> Approve & Lanjut ke Fase Berikutnya
                </button>
            </form>
            @if($revisionsLeft > 0)
            <button type="button" class="btn-secondary red" style="width:100%;"
                onclick="openModal('revisionModal')">
                <i class="bi bi-arrow-counterclockwise"></i> Minta Revisi (sisa {{ $revisionsLeft }}x)
            </button>
            @else
            <div class="btn-disabled" style="width:100%;">
                <i class="bi bi-slash-circle"></i> Batas revisi habis ({{ $order->revision_limit }}x)
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- ── BRIEF ────────────────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-file-text-fill"></i> Brief dari Client</div>

        @if($order->note)
        <div class="brief-text">{{ $order->note }}</div>
        @else
        <div class="brief-empty">Tidak ada catatan khusus dari client.</div>
        @endif

        @php
        $refImages = [];
        if ($order->selected_addons && is_array($order->selected_addons)) {
        foreach ($order->selected_addons as $item) {
        if (is_string($item) && !empty($item)) {
        $refImages[] = $item;
        }
        }
        }
        @endphp

        @if(count($refImages) > 0)
        <div style="margin-top:12px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">
                Referensi Gambar
            </div>
            <div class="ref-grid">
                @foreach($refImages as $ref)
                <img src="{{ asset('storage/' . $ref) }}"
                    class="ref-img" alt="reference"
                    onclick="window.open(this.src,'_blank')"
                    onerror="this.style.display='none'">
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── TIMELINE ──────────────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-clock-history"></i> Riwayat Order</div>
        <div class="timeline">
            <div class="tl-item">
                <div class="tl-dot accent"><i class="bi bi-bag-plus-fill" style="font-size:11px;"></i></div>
                <div class="tl-content">
                    <div class="tl-title">Order dibuat</div>
                    <div class="tl-time">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
            @if($order->paid_at ?? null)
            <div class="tl-item">
                <div class="tl-dot green"><i class="bi bi-credit-card-fill" style="font-size:11px;"></i></div>
                <div class="tl-content">
                    <div class="tl-title">Pembayaran diterima</div>
                    <div class="tl-time">{{ $order->paid_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
            @endif
            @if(in_array($order->status, ['in_progress','revision','revision_requested','waiting_client','completed']))
            <div class="tl-item">
                <div class="tl-dot accent"><i class="bi bi-play-fill" style="font-size:11px;"></i></div>
                <div class="tl-content">
                    <div class="tl-title">Artist mulai pengerjaan</div>
                    <div class="tl-time">{{ $order->updated_at->format('d M Y') }}</div>
                </div>
            </div>
            @endif
            @if($order->revision_count > 0)
            <div class="tl-item">
                <div class="tl-dot red"><i class="bi bi-arrow-counterclockwise" style="font-size:11px;"></i></div>
                <div class="tl-content">
                    <div class="tl-title">{{ $order->revision_count }}x revisi diminta</div>
                    <div class="tl-time">Sisa {{ $revisionsLeft }}x</div>
                </div>
            </div>
            @endif
            @if($order->extension_status === 'approved')
            <div class="tl-item">
                <div class="tl-dot" style="background:rgba(249,115,22,.1);border-color:var(--orange);color:var(--orange);">
                    <i class="bi bi-hourglass-split" style="font-size:11px;"></i>
                </div>
                <div class="tl-content">
                    <div class="tl-title">Perpanjangan +{{ $order->extension_days }} hari disetujui</div>
                    <div class="tl-time">{{ $order->extension_requested_at?->format('d M Y') }}</div>
                </div>
            </div>
            @endif
            @if($order->status === 'completed')
            <div class="tl-item">
                <div class="tl-dot green"><i class="bi bi-check-lg" style="font-size:13px;"></i></div>
                <div class="tl-content">
                    <div class="tl-title">Order selesai 🎉</div>
                    <div class="tl-time">{{ $order->updated_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>{{-- /.od-main --}}

{{-- ══════════ SIDEBAR ══════════ --}}
<div class="od-sidebar">

    {{-- ── ACTIONS ──────────────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-lightning-fill"></i> Aksi</div>
        <div class="action-section">

            @if($isArtist)
            {{-- ── ARTIST ACTIONS ── --}}
            @if(in_array($order->status, ['pending', 'paid']))
            <form action="{{ route('order.accept') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="btn-primary green" style="width:100%;">
                    <i class="bi bi-check-circle-fill"></i> Terima Order
                </button>
            </form>
            <form action="{{ route('order.reject') }}" method="POST"
                onsubmit="return confirm('Tolak order ini?')">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="btn-secondary red" style="width:100%;">
                    <i class="bi bi-x-circle"></i> Tolak
                </button>
            </form>

            @elseif($order->status === 'revision_requested')
            <form action="{{ route('order.revision') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <input type="hidden" name="action" value="accept">
                <button type="submit" class="btn-primary" style="width:100%;background:var(--yellow);color:#000;box-shadow:0 4px 14px rgba(250,204,21,.2);">
                    <i class="bi bi-pencil-square"></i> Mulai Revisi
                </button>
            </form>

            @elseif(in_array($order->status, ['in_progress', 'revision']))
            <button class="btn-primary" style="width:100%;" onclick="openModal('sendModal')">
                <i class="bi bi-upload"></i> Kirim Hasil Kerja
            </button>
            @if($order->deadline_at && !$order->hasExtensionRequest())
            <button class="btn-secondary orange" style="width:100%;"
                onclick="openModal('extensionModal')">
                <i class="bi bi-hourglass-split"></i> Minta Tambahan Waktu
            </button>
            @endif

            @elseif($order->status === 'waiting_client')
            <div class="btn-disabled" style="width:100%;">
                <i class="bi bi-hourglass-split"></i> Menunggu Client Review
            </div>

            @elseif($order->status === 'completed')
            <div class="btn-disabled" style="width:100%;color:var(--green);border-color:rgba(34,197,94,.3);">
                <i class="bi bi-check-circle-fill"></i> Order Selesai
            </div>
            @if($canReview)
            <button class="btn-secondary" style="width:100%;" onclick="openModal('reviewModal')">
                <i class="bi bi-star-fill"></i> Beri Review ke Client
            </button>
            @endif
            @endif

            @elseif($isClient)
            {{-- ── CLIENT ACTIONS ── --}}
            @if(in_array($order->status, ['pending', 'paid']))
            <button class="btn-secondary red" style="width:100%;" onclick="openModal('cancelModal')">
                <i class="bi bi-x-circle"></i> Cancel Order
            </button>

            @elseif($order->status === 'revision_requested')
            <div class="btn-disabled" style="width:100%;color:var(--yellow);border-color:rgba(250,204,21,.3);">
                <i class="bi bi-hourglass-split"></i> Menunggu Artist Mulai Revisi
            </div>

            @elseif($order->status === 'waiting_client')
            <form action="{{ route('order.accept') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                <button type="submit" class="btn-primary green" style="width:100%;">
                    <i class="bi bi-check-circle-fill"></i> Approve Hasil
                </button>
            </form>
            @if($revisionsLeft > 0)
            <button class="btn-secondary red" style="width:100%;"
                onclick="openModal('revisionModal')">
                <i class="bi bi-arrow-counterclockwise"></i> Minta Revisi ({{ $revisionsLeft }}x)
            </button>
            @endif

            @elseif($order->status === 'completed')
            <div class="btn-disabled" style="width:100%;color:var(--green);border-color:rgba(34,197,94,.3);">
                <i class="bi bi-check-circle-fill"></i> Order Selesai
            </div>
            @if($canReview)
            <button class="btn-primary" style="width:100%;" onclick="openModal('reviewModal')">
                <i class="bi bi-star-fill"></i> Beri Review
            </button>
            @endif
            @endif

            {{-- Late actions for client --}}
            @if($isClient && in_array($order->late_status, ['overdue','delayed']) && $order->status !== 'completed')
            <div class="divider"></div>
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">
                Order Terlambat
            </div>
            @if($order->payment_status === 'paid')
            <button class="btn-secondary orange" style="width:100%;"
                onclick="openModal('refundModal')">
                <i class="bi bi-arrow-counterclockwise"></i> Request Refund
            </button>
            @endif
            @if($order->status !== 'canceled')
            <button class="btn-secondary red" style="width:100%;"
                onclick="openModal('cancelModal')">
                <i class="bi bi-x-circle"></i> Cancel Order
            </button>
            @endif
            @endif
            @endif

            {{-- Chat always available --}}
            @if($order->status !== 'canceled')
            <div class="divider" style="margin:4px 0;"></div>
            <a href="{{ route('chat.index', ['order_id' => $order->order_id]) }}"
                class="btn-secondary sky" style="width:100%;">
                <i class="bi bi-chat-dots-fill"></i>
                Chat {{ $isArtist ? 'Client' : 'Artist' }}
            </a>
            @endif

        </div>
    </div>

    {{-- ── PRICE BREAKDOWN ──────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-receipt"></i> Ringkasan Pembayaran</div>
        <div class="price-row total">
            <span>Total</span>
            <span class="price-val total">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        <div style="margin-top:10px;padding:8px 12px;background:var(--s2);border-radius:8px;font-size:12px;color:var(--muted);display:flex;align-items:center;justify-content:space-between;">
            <span>Status Bayar</span>
            <span style="font-weight:700;color:{{ $order->payment_status === 'paid' ? 'var(--green)' : 'var(--yellow)' }}">
                {{ strtoupper($order->payment_status) }}
            </span>
        </div>
    </div>

    {{-- ── QUEUE ────────────────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-people-fill"></i> Queue Posisi</div>
        <div class="queue-visual">
            <div>
                <div class="q-pos-big">#{{ $queuePosition }}</div>
                <div style="font-size:9px;color:var(--muted);text-align:center;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                    dari {{ $totalQueue }}
                </div>
            </div>
            <div style="flex:1;">
                <div class="q-label">Posisi kamu di antrian</div>
                <div class="q-total">{{ $totalQueue }} order aktif pada jasa ini</div>
                @if($order->deadline_at)
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="bi bi-calendar-check"></i>
                    Est. selesai: {{ $order->deadline_at->format('d M Y') }}
                </div>
                @endif
            </div>
        </div>

        @php
        $queueOrders = \App\Models\Order::where('service_id', $order->service_id)
        ->whereNotIn('status', ['completed','canceled'])
        ->with('client')
        ->orderBy('created_at')
        ->take(6)
        ->get();
        @endphp

        @if($queueOrders->count() > 1)
        <div style="margin-top:10px;">
            <div style="font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">
                Antrian sekarang
            </div>
            @foreach($queueOrders as $qi => $qo)
            <div style="display:flex;align-items:center;gap:8px;padding:6px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                <span style="font-size:11px;font-weight:700;min-width:20px;
                        color:{{ $qo->order_id === $order->order_id ? 'var(--accent)' : 'var(--muted)' }};">
                    #{{ $qi + 1 }}
                </span>
                <img src="{{ $qo->client->avatar ?? asset('images/default-avatar.png') }}"
                    style="width:24px;height:24px;border-radius:50%;object-fit:cover;flex-shrink:0;
                            border:{{ $qo->order_id === $order->order_id ? '2px solid var(--accent)' : '1px solid var(--border)' }};"
                    alt="">
                <span style="font-size:12px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                        color:{{ $qo->order_id === $order->order_id ? 'var(--text)' : 'var(--muted)' }};
                        font-weight:{{ $qo->order_id === $order->order_id ? '700' : '400' }};">
                    {{ $qo->order_id === $order->order_id ? 'Kamu' : 'Client' }}
                </span>
                <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:999px;background:rgba(139,92,246,.1);color:var(--accent);">
                    {{ ucfirst(str_replace('_', ' ', $qo->status)) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ── PEOPLE ───────────────────────────── --}}
    <div class="card">
        <div class="card-title"><i class="bi bi-person-fill"></i> Pihak Terlibat</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('profile.show', $order->artist_id) }}" class="person-row">
                <img src="{{ $order->artist->avatar ?? asset('images/default-avatar.png') }}"
                    class="person-avatar" alt="">
                <div>
                    <div class="person-name">{{ $order->artist->name ?? 'Artist' }}</div>
                    <div class="person-role">Digital Artist</div>
                </div>
                <span class="person-badge">Artist</span>
            </a>
            <a href="{{ route('profile.show', $order->client_id) }}" class="person-row">
                <img src="{{ $order->client->avatar ?? asset('images/default-avatar.png') }}"
                    class="person-avatar" alt="">
                <div>
                    <div class="person-name">{{ $order->client->name ?? 'Client' }}</div>
                    <div class="person-role">Client</div>
                </div>
                <span class="person-badge green">Client</span>
            </a>
        </div>
    </div>

    {{-- ── MY REVIEW STATUS ─────────────────── --}}
    @if($order->status === 'completed')
    <div class="card">
        <div class="card-title"><i class="bi bi-star-fill"></i> Review</div>
        @if($myReview)
        <div style="padding:12px;background:var(--gdim);border:1px solid rgba(34,197,94,.2);border-radius:10px;text-align:center;">
            <div style="color:var(--yellow);font-size:18px;margin-bottom:4px;">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $myReview->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
            </div>
            <div style="font-size:12px;color:var(--green);font-weight:600;">
                <i class="bi bi-check-circle-fill"></i> Review sudah dikirim
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                Tersembunyi sampai kedua pihak submit
            </div>
        </div>
        @else
        <div style="text-align:center;padding:14px 0;">
            <div style="font-size:13px;color:var(--muted);margin-bottom:12px;">Bagikan pengalamanmu</div>
            <button class="btn-primary" onclick="openModal('reviewModal')" style="width:100%;">
                <i class="bi bi-star-fill"></i> Tulis Review
            </button>
        </div>
        @endif
    </div>
    @endif

</div>{{-- /.od-sidebar --}}

</div>{{-- /.od-wrap --}}

@vite('resources/js/commission/order-detail.js')

@endsection
