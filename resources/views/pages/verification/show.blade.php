{{-- resources/views/admin/verifications/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Verifikasi #' . $verification->id)

@section('content')
@vite('resources/css/pages/verification/show.css')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.verification.index') }}">Verifikasi</a>
    <span class="sep">/</span>
    <span class="cur">#{{ $verification->id }} — {{ $verification->artist->name ?? '—' }}</span>
</div>

@if(session('success'))
<div class="alert-ok"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-err"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
@endif

{{-- Page header --}}
<div class="page-header">
    <div>
        <div class="page-title">{{ $verification->artist->name ?? 'Unknown Artist' }}</div>
        <div class="page-sub">
            {{ $verification->artist->email ?? '' }}
            · Submit {{ $verification->created_at->format('d M Y, H:i') }}
            · <span class="badge badge-{{ $verification->status }}" style="vertical-align:middle;">
                {{ ucfirst(str_replace('_', ' ', $verification->status)) }}
            </span>
        </div>
    </div>
    <a href="{{ route('admin.verification.index') }}" class="btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="detail-grid">

    {{-- ══ KOLOM KIRI ══ --}}
    <div class="col-left">

        {{-- File portofolio --}}
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-images"></i>
                Gambar Portofolio
                <span class="card-head-meta">{{ count($verification->portfolio_files ?? []) }} file</span>
            </div>
            <div class="detail-card-body">
                <div class="file-grid">
                    @foreach($verification->portfolio_files ?? [] as $filePath)
                    @php
                    $path = is_array($filePath) ? $filePath['path'] : $filePath;
                    $name = is_array($filePath) ? ($filePath['name'] ?? basename($path)) : basename($path);
                    $size = is_array($filePath) ? ($filePath['size'] ?? null) : null;
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    // ✅ FIX: Jika path sudah berupa Cloudinary URL, langsung pakai. Jika tidak, pakai Storage::url()
                    $url = str_starts_with($path, 'http') ? $path : \Storage::url($path);

                    // ✅ FIX DOWNLOAD: Cloudinary — tambahkan fl_attachment agar browser force-download
                    if (str_contains($url, 'res.cloudinary.com')) {
                    $downloadUrl = str_replace('/upload/', '/upload/fl_attachment/', $url);
                    } else {
                    $downloadUrl = $url;
                    }

                    $base = strtolower(pathinfo($name, PATHINFO_FILENAME));
                    $wipKws = ['wip','sketch','draft','process','progress','layer','lineart','rough','thumbnail'];
                    $isWip = collect($wipKws)->contains(fn($kw) => str_contains($base, $kw));
                    $sizeKb = $size ? round($size / 1024) : null;
                    @endphp
                    <div class="file-thumb"
                        onclick="openLightbox('{{ $url }}', '{{ addslashes($name) }}')"
                        title="{{ $name }}">
                        @if($isWip)
                        <span class="file-badge wip">WIP</span>
                        @endif
                        <img src="{{ $url }}" alt="{{ $name }}" loading="lazy">
                        @if($sizeKb)
                        <span class="file-size">{{ $sizeKb >= 1024 ? round($sizeKb/1024, 1).'MB' : $sizeKb.'KB' }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Download list --}}
                @if(count($verification->portfolio_files ?? []) > 0)
                <div class="download-list">
                    @foreach($verification->portfolio_files ?? [] as $filePath)
                    @php
                    $path = is_array($filePath) ? $filePath['path'] : $filePath;
                    $name = is_array($filePath) ? ($filePath['name'] ?? basename($path)) : basename($path);

                    // ✅ FIX: Resolusi URL Cloudinary vs local
                    $url = str_starts_with($path, 'http') ? $path : \Storage::url($path);

                    // ✅ FIX DOWNLOAD: Cloudinary fl_attachment untuk force download
                    if (str_contains($url, 'res.cloudinary.com')) {
                    $downloadUrl = str_replace('/upload/', '/upload/fl_attachment/', $url);
                    } else {
                    $downloadUrl = $url;
                    }
                    @endphp
                    <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="btn-download">
                        <i class="bi bi-download"></i> {{ $name }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Sosial media --}}
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-globe2"></i>
                Link Sosial Media
                <span class="card-head-meta">{{ count($verification->social_media_links ?? []) }} link</span>
            </div>
            <div class="detail-card-body social-body">
                @forelse($verification->social_media_links ?? [] as $link)
                @php
                $host = ltrim(strtolower(parse_url($link, PHP_URL_HOST) ?? $link), 'www.');
                $icon = match(true) {
                str_contains($host, 'instagram') => 'bi-instagram',
                str_contains($host, 'tiktok') => 'bi-tiktok',
                str_contains($host, 'twitter')
                || str_contains($host, 'x.com') => 'bi-twitter-x',
                str_contains($host, 'youtube') => 'bi-youtube',
                str_contains($host, 'deviantart') => 'bi-palette',
                str_contains($host, 'pixiv') => 'bi-image',
                str_contains($host, 'artstation') => 'bi-brush',
                str_contains($host, 'behance') => 'bi-behance',
                str_contains($host, 'facebook') => 'bi-facebook',
                default => 'bi-link-45deg',
                };
                $platform = match(true) {
                str_contains($host, 'instagram') => 'Instagram',
                str_contains($host, 'tiktok') => 'TikTok',
                str_contains($host, 'twitter')
                || str_contains($host, 'x.com') => 'X/Twitter',
                str_contains($host, 'youtube') => 'YouTube',
                str_contains($host, 'deviantart') => 'DeviantArt',
                str_contains($host, 'pixiv') => 'Pixiv',
                str_contains($host, 'artstation') => 'ArtStation',
                str_contains($host, 'behance') => 'Behance',
                default => $host,
                };
                @endphp
                <div class="social-link-item">
                    <div class="social-link-icon"><i class="bi {{ $icon }}"></i></div>
                    <div class="social-link-info">
                        <div class="social-platform">{{ $platform }}</div>
                        <a href="{{ $link }}" target="_blank" rel="noopener" class="social-url">
                            {{ $link }}
                        </a>
                    </div>
                    <a href="{{ $link }}" target="_blank" rel="noopener" class="btn-open-link">
                        <i class="bi bi-box-arrow-up-right"></i> Buka
                    </a>
                </div>
                @empty
                <span class="empty-text">Tidak ada link sosial media.</span>
                @endforelse

                {{-- Checklist pemeriksaan sosmed untuk admin --}}
                <div class="sosmed-checklist">
                    <div class="checklist-head">
                        <i class="bi bi-clipboard-check"></i> Yang perlu diperiksa:
                    </div>
                    <label class="check-item">
                        <input type="checkbox" id="chk_usia"> Usia akun > 1 bulan
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_wip"> Ada timelapse / WIP / proses
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_style"> Style konsisten dengan portofolio
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_interaksi"> Ada interaksi organik
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_bukan_ai"> Tidak terindikasi AI-generated
                    </label>
                </div>
            </div>
        </div>

    </div>{{-- /col-left --}}

    {{-- ══ KOLOM KANAN ══ --}}
    <div class="col-right">

        {{-- Hasil keputusan (sudah selesai) --}}
        @if(in_array($verification->status, ['approved', 'rejected']))
        <div class="detail-card decision-card">
            <div class="detail-card-head">
                <i class="bi bi-flag-fill"></i> Keputusan
            </div>
            <div class="detail-card-body">
                <div class="decision-row">
                    <span class="badge badge-{{ $verification->status }} badge-lg">
                        @if($verification->status === 'approved')
                        <i class="bi bi-check-lg"></i> Approved
                        @else
                        <i class="bi bi-x-lg"></i> Rejected
                        @endif
                    </span>
                    <span class="decision-date">
                        {{ $verification->reviewed_at?->format('d M Y, H:i') }}
                    </span>
                </div>
                @if($verification->total_score !== null)
                <div class="total-score-result">
                    Total skor: <strong>{{ $verification->total_score }}/100</strong>
                </div>
                @endif
                @if($verification->admin_notes_final)
                <div class="final-notes-box">
                    <strong>Catatan final:</strong>
                    <p>{{ $verification->admin_notes_final }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Form scoring (belum approved) --}}
        @if($verification->status !== 'approved')
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-sliders"></i> Penilaian Admin
            </div>
            <div class="detail-card-body">

                {{-- Preview total --}}
                <div class="total-score-display">
                    <div>
                        <div class="total-score-label">Total Skor</div>
                        <div class="score-threshold-hint">Minimum lulus: 60/100</div>
                    </div>
                    <div class="total-score-value" id="previewValue">—</div>
                </div>

                {{-- Sosmed (40 poin) --}}
                <div class="score-section-label">
                    Sosial Media
                    <span class="score-section-max">maks 40</span>
                </div>

                @php
                $socialCriteria = [
                ['key' => 'score_social_style', 'label' => 'Style & konsistensi visual', 'max' => 10],
                ['key' => 'score_social_age', 'label' => 'Usia akun & riwayat posting', 'max' => 10],
                ['key' => 'score_social_wip', 'label' => 'Ada WIP/proses di sosmed', 'max' => 10],
                ['key' => 'score_social_comments', 'label' => 'Interaksi & komentar organik', 'max' => 10],
                ];
                @endphp

                @foreach($socialCriteria as $c)
                <div class="score-input-group">
                    <div class="score-input-label">
                        {{ $c['label'] }}
                        <span class="score-input-val" id="val_{{ $c['key'] }}">
                            {{ $verification->{$c['key']} ?? 0 }}
                        </span>
                        <span class="score-input-max">/{{ $c['max'] }}</span>
                    </div>
                    <input type="range" min="0" max="{{ $c['max'] }}"
                        value="{{ $verification->{$c['key']} ?? 0 }}"
                        data-key="{{ $c['key'] }}"
                        oninput="updateScore(this)">
                    <div class="range-labels">
                        <span>0</span><span>{{ $c['max'] }}</span>
                    </div>
                </div>
                @endforeach

                {{-- Portfolio (60 poin) --}}
                <div class="score-section-label" style="margin-top:18px;">
                    Portfolio
                    <span class="score-section-max">maks 60</span>
                </div>
                <div class="score-input-group">
                    <div class="score-input-label">
                        Kualitas & keaslian portofolio
                        <span class="score-input-val" id="val_score_portfolio">
                            {{ $verification->score_portfolio ?? 0 }}
                        </span>
                        <span class="score-input-max">/60</span>
                    </div>
                    <input type="range" min="0" max="60"
                        value="{{ $verification->score_portfolio ?? 0 }}"
                        data-key="score_portfolio"
                        oninput="updateScore(this)">
                    <div class="range-labels"><span>0</span><span>60</span></div>
                </div>

                {{-- Catatan --}}
                <div class="notes-group">
                    <label class="form-label">Catatan Sosmed <span class="optional">(opsional)</span></label>
                    <textarea class="admin-textarea" id="notes_social" rows="2"
                        placeholder="Komentar tentang akun sosmed artist...">{{ $verification->admin_notes_social ?? '' }}</textarea>
                </div>
                <div class="notes-group">
                    <label class="form-label">Catatan Portfolio <span class="optional">(opsional)</span></label>
                    <textarea class="admin-textarea" id="notes_portfolio" rows="2"
                        placeholder="Komentar tentang kualitas karya...">{{ $verification->admin_notes_portfolio ?? '' }}</textarea>
                </div>
                <div class="notes-group">
                    <label class="form-label">
                        Catatan Final untuk Artist
                        <span class="required-star">*</span>
                    </label>
                    <textarea class="admin-textarea" id="notes_final" rows="3" required
                        placeholder="Catatan ini ditampilkan ke artist. Jika reject, jelaskan alasan spesifik.">{{ $verification->admin_notes_final ?? '' }}</textarea>
                    <div class="notes-hint">Wajib diisi sebelum kirim keputusan.</div>
                </div>

                {{-- Aksi --}}
                <div class="action-buttons">
                    <form action="{{ route('admin.verification.decide', $verification->id) }}"
                        method="POST" id="approveForm">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="score_social_style" id="hid_score_social_style">
                        <input type="hidden" name="score_social_age" id="hid_score_social_age">
                        <input type="hidden" name="score_social_wip" id="hid_score_social_wip">
                        <input type="hidden" name="score_social_comments" id="hid_score_social_comments">
                        <input type="hidden" name="score_portfolio" id="hid_score_portfolio">
                        <input type="hidden" name="admin_notes_social" id="hid_notes_social">
                        <input type="hidden" name="admin_notes_portfolio" id="hid_notes_portfolio">
                        <input type="hidden" name="admin_notes_final" id="hid_notes_final">
                        <button type="submit" class="btn-approve"
                            onclick="return syncAndConfirm(this.form, 'Approve submisi ini? Artist akan langsung berstatus Verified.')">
                            <i class="bi bi-patch-check-fill"></i> Approve
                        </button>
                    </form>

                    <form action="{{ route('admin.verification.decide', $verification->id) }}"
                        method="POST" id="rejectForm">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="score_social_style" id="hid2_score_social_style">
                        <input type="hidden" name="score_social_age" id="hid2_score_social_age">
                        <input type="hidden" name="score_social_wip" id="hid2_score_social_wip">
                        <input type="hidden" name="score_social_comments" id="hid2_score_social_comments">
                        <input type="hidden" name="score_portfolio" id="hid2_score_portfolio">
                        <input type="hidden" name="admin_notes_social" id="hid2_notes_social">
                        <input type="hidden" name="admin_notes_portfolio" id="hid2_notes_portfolio">
                        <input type="hidden" name="admin_notes_final" id="hid2_notes_final">
                        <button type="submit" class="btn-reject"
                            onclick="return syncAndConfirm(this.form, 'Reject submisi ini? Artist akan dikunci 30 hari.')">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- Tandai in_review --}}
        @if($verification->status === 'pending')
        <form action="{{ route('admin.verification.take', $verification->id) }}"
            method="POST" style="margin-top:10px;">
            @csrf @method('PATCH')
            <button type="submit" class="btn-secondary btn-full">
                <i class="bi bi-eye"></i> Tandai sebagai "In Review"
            </button>
        </form>
        @endif
        @endif

    </div>{{-- /col-right --}}

</div>

{{-- Lightbox --}}
<div class="lightbox" id="lightbox" onclick="if(event.target===this)closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">×</span>
    <img id="lightboxImg" src="" alt="">
    <div class="lightbox-name" id="lightboxName"></div>
</div>

@vite('resources/js/pages/verification/show.js')
@endsection
