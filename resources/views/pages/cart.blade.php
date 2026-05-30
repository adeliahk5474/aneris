{{-- resources/views/pages/cart.blade.php --}}
@extends('layouts.app')
@section('title', 'Cart & Orders — Aneris')
@section('content')

@vite('resources/css/pages/cart.css')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ── TAB BAR ── --}}
<div class="tab-bar">
    <div class="tab-item {{ $activeTab === 'checkout' ? 'active' : '' }}"
        onclick="switchTab('checkout', this)">
        <i class="bi bi-credit-card"></i> Checkout
        @if($pendingOrders->count())
        <span class="tab-badge">{{ $pendingOrders->count() }}</span>
        @endif
    </div>
    <div class="tab-item {{ $activeTab === 'status' ? 'active' : '' }}"
        onclick="switchTab('status', this)">
        <i class="bi bi-clock-history"></i> Status Proses
        @if($inProgressOrders->count())
        <span class="tab-badge" style="background:var(--green);">{{ $inProgressOrders->count() }}</span>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════
     TAB 1: CHECKOUT
══════════════════════════════════════ --}}
<div class="tab-panel {{ $activeTab === 'checkout' ? 'active' : '' }}" id="tab-checkout">
    <div class="panel-inner">

        @if(session('success'))
        <div class="flash-ok"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="flash-err"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif

        @if($pendingOrders->count() === 0)
        <div class="empty-state">
            <i class="bi bi-bag-x"></i>
            <p>Tidak ada order yang perlu dibayar.<br>
                <a href="{{ route('explore') }}" style="color:var(--accent);">Jelajahi komisi →</a>
            </p>
        </div>
        @else
        <div class="checkout-layout">

            {{-- ── LEFT COLUMN ── --}}
            <div>
                {{-- Brief card per order --}}
                @foreach($pendingOrders as $order)
                <div class="co-card" id="co-card-{{ $order->order_id }}">
                    <div class="co-card-title">
                        <i class="bi bi-file-earmark-text"></i>
                        Commission Brief
                        <span style="font-size:11px;color:var(--muted);font-weight:400;margin-left:4px;">
                            — #ARS-{{ strtoupper(substr($order->order_id, 0, 8)) }}
                        </span>
                    </div>

                    <div class="order-preview">
                        <img src="{{ $order->service->image_url ?? asset('images/default-thumb.png') }}"
                            class="order-preview-thumb" alt="">
                        <div>
                            <div class="order-preview-title">{{ $order->service->title ?? 'Commission' }}</div>
                            <div class="order-preview-by">oleh {{ $order->artist->name ?? 'Artist' }}</div>
                        </div>
                        <div class="order-preview-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    </div>

                    {{-- Form brief — submit otomatis sebelum bayar via JS --}}
                    <form action="{{ route('order.updateBrief', $order->order_id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        id="brief-form-{{ $order->order_id }}">
                        @csrf
                        @method('PATCH')

                        <div class="fg">
                            <label class="fl" for="note-{{ $order->order_id }}">
                                Catatan / Deskripsi untuk Artist
                                <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;margin-left:4px;">(opsional tapi sangat membantu)</span>
                            </label>
                            <textarea
                                id="note-{{ $order->order_id }}"
                                name="note"
                                class="fta"
                                placeholder="Contoh:&#10;- Karakter: OC saya, nama: Luna&#10;- Style: semi-realistis, warna pastel&#10;- Background: kota malam&#10;- Pose: duduk santai menghadap kamera"
                                maxlength="2000"
                                oninput="document.getElementById('cc-{{ $order->order_id }}').textContent=this.value.length">{{ $order->note ?? '' }}</textarea>
                            <div class="char-count">
                                <span id="cc-{{ $order->order_id }}">{{ $order->noteCharCount }}</span>/2000
                            </div>
                        </div>

                        <div class="ref-label">
                            REFERENSI GAMBAR
                            <span style="color:var(--muted);font-weight:400;text-transform:none;">(maks 4)</span>
                        </div>
                        <div class="ref-grid" id="ref-grid-{{ $order->order_id }}">
                            {{-- Tampilkan existing refs (hanya string path) --}}
                            @php
                            $existingRefs = [];
                            if (!empty($order->selected_addons) && is_array($order->selected_addons)) {
                            foreach ($order->selected_addons as $ref) {
                            if (is_string($ref) && !empty($ref)) {
                            $existingRefs[] = $ref;
                            }
                            }
                            }
                            @endphp
                            @foreach($existingRefs as $ref)
                            <div class="ref-item">
                                <img src="{{ asset('storage/' . $ref) }}" alt="">
                                <button type="button" class="ref-remove"
                                    onclick="this.closest('.ref-item').remove(); checkRefLimit('{{ $order->order_id }}')">
                                    <i class="bi bi-x"></i>
                                </button>
                                <input type="hidden" name="existing_refs[]" value="{{ $ref }}">
                            </div>
                            @endforeach

                            <div class="ref-add" id="ref-add-{{ $order->order_id }}">
                                <input type="file"
                                    id="ref-input-{{ $order->order_id }}"
                                    name="reference_images[]"
                                    accept="image/png,image/jpg,image/jpeg,image/webp"
                                    multiple
                                    onchange="previewRefs(this, '{{ $order->order_id }}')">
                                <div style="font-size:20px;color:var(--muted);"><i class="bi bi-plus-lg"></i></div>
                                <div style="font-size:11px;color:var(--muted);">Tambah foto</div>
                            </div>
                        </div>
                        <div class="ref-hint">
                            <i class="bi bi-info-circle"></i> PNG · JPG · WEBP · Maks 5MB per file
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-top:14px;">
                            <button type="submit" class="btn-save-brief">
                                <i class="bi bi-check2"></i> Simpan Brief
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach

                {{-- ── METODE PEMBAYARAN ── --}}
                <div class="co-card">
                    <div class="co-card-title"><i class="bi bi-credit-card"></i> Metode Pembayaran</div>
                    <div class="pay-grid">
                        <div class="pay-opt active" data-filter="ewallet" onclick="selectPayment(this)">
                            <div class="pay-icon">📱</div>
                            <div class="pay-label">E-Wallet</div>
                            <div class="pay-sub">GoPay · ShopeePay · QRIS</div>
                        </div>
                        <div class="pay-opt" data-filter="bank" onclick="selectPayment(this)">
                            <div class="pay-icon">🏦</div>
                            <div class="pay-label">Bank Transfer</div>
                            <div class="pay-sub">BCA · BNI · BRI · Mandiri</div>
                        </div>
                        <div class="pay-opt" data-filter="credit" onclick="selectPayment(this)">
                            <div class="pay-icon">💳</div>
                            <div class="pay-label">Kartu Kredit</div>
                            <div class="pay-sub">Visa · Mastercard</div>
                        </div>
                    </div>
                    <p style="font-size:11px;color:var(--muted);text-align:center;margin-top:12px;">
                        <i class="bi bi-info-circle"></i> Semua transaksi diproses aman via Midtrans
                    </p>
                </div>
            </div>

            {{-- ── RIGHT COLUMN: Summary Card ── --}}
            <div>
                <div class="summary-card">
                    <div class="summary-title">Ringkasan Pembayaran</div>

                    @if($pendingOrders->count() > 1)
                    @foreach($pendingOrders as $order)
                    <div class="summary-row">
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px;">
                            {{ Str::limit($order->service->title ?? 'Commission', 28) }}
                        </span>
                        <span class="summary-val">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    @endif

                    <div class="summary-divider"></div>
                    <div class="summary-total-label">Total Bayar</div>
                    <div class="summary-total-price">
                        Rp {{ number_format($grand, 0, ',', '.') }}
                    </div>

                    @foreach($pendingOrders as $order)
                    <button
                        class="btn-pay"
                        id="btn-pay-{{ $order->order_id }}"
                        onclick="triggerPayment('{{ $order->order_id }}')">
                        <i class="bi bi-lock-fill"></i>
                        @if($pendingOrders->count() > 1)
                        Bayar #ARS-{{ strtoupper(substr($order->order_id, 0, 8)) }}
                        @else
                        Bayar Sekarang — Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        @endif
                    </button>
                    @endforeach

                    <div class="summary-secure">
                        <i class="bi bi-shield-check" style="color:var(--green);"></i> Aneris Protected
                    </div>
                </div>
            </div>

        </div>
        @endif

    </div>
</div>

{{-- ══════════════════════════════════════
     TAB 2: STATUS PROSES
══════════════════════════════════════ --}}
<div class="tab-panel {{ $activeTab === 'status' ? 'active' : '' }}" id="tab-status">
    <div class="panel-inner">

        @if(session('success'))
        <div class="flash-ok"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="flash-err"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
        @endif

        @forelse($statusOrders as $order)
        <div class="progress-item">
            <div class="progress-item-head">
                <img src="{{ $order->service->image_url ?? asset('images/default-thumb.png') }}"
                    class="progress-thumb" alt="">
                <div>
                    <div class="progress-title">{{ $order->service->title ?? 'Commission' }}</div>
                    <div class="progress-artist">oleh {{ $order->artist->name ?? 'Artist' }}</div>
                </div>
                <div class="progress-actions">
                    <span class="status-pill sp-{{ $order->status }}">
                        <i class="bi bi-circle-fill" style="font-size:6px;"></i>
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>

                    @if($order->late_status)
                    <span class="late-badge late-{{ $order->late_status }}">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:9px;"></i>
                        {{ ucfirst($order->late_status) }}
                    </span>
                    @endif

                    <a href="{{ route('order.detail', $order->order_id) }}" class="btn-sm-outline">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    <a href="{{ route('chat.index', ['order_id' => $order->order_id]) }}" class="btn-sm-outline">
                        <i class="bi bi-chat-dots"></i> Chat
                    </a>
                </div>
            </div>

            @if($order->deadline_at && !in_array($order->status, ['completed','canceled']))
            <div style="font-size:11px;color:{{ $order->late_status ? 'var(--red)' : 'var(--muted)' }};margin-bottom:12px;display:flex;align-items:center;gap:5px;">
                <i class="bi bi-clock{{ $order->late_status ? '-fill' : '' }}"></i>
                {{ $order->time_remaining }}
                <span style="color:var(--muted);">· deadline {{ $order->deadline_at->format('d M Y') }}</span>
            </div>
            @endif

            <div class="steps-track">
                @foreach($progressSteps as $i => $step)
                <div class="step {{ $i < $order->progressStepCurrent ? 'done' : ($i === $order->progressStepCurrent ? 'active' : '') }}">
                    <div class="step-dot">
                        @if($i < $order->progressStepCurrent)
                            <i class="bi bi-check-lg" style="font-size:10px;"></i>
                            @elseif($i === $order->progressStepCurrent)
                            <i class="bi bi-circle-fill" style="font-size:6px;"></i>
                            @endif
                    </div>
                    <div class="step-label">{{ $step }}</div>
                </div>
                @endforeach
            </div>

            @if($order->status === 'waiting_client' && auth()->user()->user_id === $order->client_id)
            <div style="display:flex;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border);">
                @if($order->revisionsLeft() > 0)
                <form action="{{ route('order.revision') }}" method="POST" style="flex:1;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <button type="submit" class="btn-sm-outline" style="width:100%;justify-content:center;">
                        <i class="bi bi-arrow-counterclockwise"></i> Revisi (sisa {{ $order->revisionsLeft() }}x)
                    </button>
                </form>
                @endif
                <form action="{{ route('order.accept') }}" method="POST" style="flex:1;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <button type="submit" class="btn-pay" style="margin:0;box-shadow:none;">
                        <i class="bi bi-check-circle-fill"></i> Approve & Lanjut
                    </button>
                </form>
            </div>
            @endif

            @if($order->extension_status === 'pending' && auth()->user()->user_id === $order->client_id)
            <div style="margin-top:12px;padding:12px 14px;background:rgba(250,204,21,.07);border:1px solid rgba(250,204,21,.2);border-radius:10px;font-size:12px;">
                <div style="font-weight:700;color:var(--yellow);margin-bottom:4px;">
                    <i class="bi bi-clock-history"></i> Artist minta tambahan waktu {{ $order->extension_days }} hari
                </div>
                <div style="color:var(--muted);margin-bottom:10px;">{{ $order->extension_reason }}</div>
                <div style="display:flex;gap:8px;">
                    <form action="{{ route('order.respondExtension') }}" method="POST" style="flex:1;">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn-sm-outline" style="width:100%;justify-content:center;border-color:rgba(34,197,94,.4);color:var(--green);">
                            <i class="bi bi-check-lg"></i> Setuju
                        </button>
                    </form>
                    <form action="{{ route('order.respondExtension') }}" method="POST" style="flex:1;">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn-sm-outline" style="width:100%;justify-content:center;border-color:rgba(239,68,68,.4);color:var(--red);">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>
        @empty
        <div class="status-empty">
            <i class="bi bi-clock-history" style="font-size:40px;opacity:.2;display:block;margin-bottom:12px;"></i>
            <p style="font-size:14px;color:var(--muted);">Belum ada order yang sedang diproses.</p>
        </div>
        @endforelse

    </div>
</div>

{{-- PAYMENT OVERLAY --}}
<div id="payment-overlay">
    <div class="overlay-spinner"></div>
    <p style="color:var(--muted);font-size:14px;font-weight:500;">Menyiapkan pembayaran…</p>
</div>

{{-- Midtrans Snap.js --}}
<script
    src="{{ config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script>
    window.cartPage = {
        paymentCheckoutUrl: "{{ route('payment.checkout') }}",
        cartStatusUrl: "{{ route('cart.index', ['tab' => 'status']) }}"
    };
</script>
@vite('resources/js/pages/cart.js')

@endsection
