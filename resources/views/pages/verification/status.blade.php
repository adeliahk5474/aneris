{{-- resources/views/pages/verification/status.blade.php --}}
@extends('layouts.app')

@section('title', 'Status Verifikasi — Aneris')

@section('content')
@vite('resources/css/pages/verification/status.css')

<div class="status-wrap">
    <div class="status-card">

        @if(Auth::user()->is_verified)
        {{-- ══ APPROVED ══ --}}
        <div class="status-icon approved">
            <i class="bi bi-patch-check-fill"></i>
        </div>
        <div class="status-title">Kamu Sudah Terverifikasi!</div>
        <div class="status-sub">
            Akunmu telah mendapat badge <strong>Verified Artist</strong>.
            Fitur commission sudah terbuka untuk kamu.
        </div>
        <a href="{{ route('artist.dashboard') }}" class="btn-primary">
            <i class="bi bi-brush"></i> Buka Artist Dashboard
        </a>

        @elseif($latest && $latest->status === 'pending')
        {{-- ══ PENDING ══ --}}
        <div class="status-icon pending">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="status-title">Dalam Antrian Review</div>
        <div class="status-sub">
            Submisimu sudah diterima dan sedang menunggu giliran direview oleh tim kami.
            Estimasi: <strong>3–5 hari kerja</strong>.
        </div>
        <div class="status-meta-list">
            <div class="status-meta-row">
                <span class="meta-label">Dikirim</span>
                <span class="meta-value">{{ $latest->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="status-meta-row">
                <span class="meta-label">Jumlah file</span>
                <span class="meta-value">{{ count($latest->portfolio_files ?? []) }} file</span>
            </div>
            <div class="status-meta-row">
                <span class="meta-label">Link sosmed</span>
                <span class="meta-value">{{ count($latest->social_media_links ?? []) }} link</span>
            </div>
        </div>
        <div class="status-tip">
            <i class="bi bi-bell"></i>
            Kamu akan mendapat notifikasi setelah review selesai.
        </div>
        <a href="{{ route('artist.dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>

        @elseif($latest && $latest->status === 'in_review')
        {{-- ══ IN REVIEW ══ --}}
        <div class="status-icon in-review">
            <i class="bi bi-eye"></i>
        </div>
        <div class="status-title">Sedang Direview</div>
        <div class="status-sub">
            Tim admin sedang memeriksa portofolio dan akun sosial media kamu.
            Harap tunggu — proses ini biasanya selesai dalam 1–2 hari kerja.
        </div>
        <div class="status-meta-list">
            <div class="status-meta-row">
                <span class="meta-label">Dikirim</span>
                <span class="meta-value">{{ $latest->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="status-meta-row">
                <span class="meta-label">Mulai direview</span>
                <span class="meta-value">{{ $latest->updated_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
        <a href="{{ route('artist.dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>

        @elseif($latest && $latest->status === 'rejected')
        {{-- ══ REJECTED ══ --}}
        <div class="status-icon rejected">
            <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="status-title">Submisi Ditolak</div>
        <div class="status-sub">
            Sayangnya submisimu belum memenuhi kriteria verifikasi kami.
            Kamu bisa submit ulang setelah masa cooldown selesai.
        </div>

        @if($latest->admin_notes_final)
        <div class="rejection-notes">
            <div class="rejection-notes-head">
                <i class="bi bi-chat-left-text"></i> Catatan dari Admin
            </div>
            <div class="rejection-notes-body">{{ $latest->admin_notes_final }}</div>
        </div>
        @endif

        @if($latest->total_score !== null)
        <div class="score-summary">
            <div class="score-summary-label">Skor kamu</div>
            <div class="score-summary-value {{ $latest->total_score >= 60 ? 'pass' : 'fail' }}">
                {{ $latest->total_score }}<span>/100</span>
            </div>
            <div class="score-summary-min">Minimum lulus: 60</div>
        </div>
        @endif

        <div class="cooldown-box">
            <i class="bi bi-clock"></i>
            <div>
                <strong>Masa tunggu submit ulang:</strong><br>
                @if($latest->next_eligible_at && now()->lt($latest->next_eligible_at))
                {{ now()->diffForHumans($latest->next_eligible_at, ['parts' => 2]) }} lagi
                ({{ $latest->next_eligible_at->format('d M Y') }})
                @else
                Masa tunggu sudah selesai — kamu bisa submit ulang sekarang.
                @endif
            </div>
        </div>

        @if(!$latest->next_eligible_at || now()->gte($latest->next_eligible_at))
        <a href="{{ route('verification.create') }}" class="btn-primary">
            <i class="bi bi-arrow-clockwise"></i> Submit Ulang
        </a>
        @endif

        <div class="rejection-tips">
            <div class="tips-head"><i class="bi bi-lightbulb"></i> Tips untuk submit ulang</div>
            <ul>
                <li>Upload lebih banyak karya (minimal 5–8 gambar)</li>
                <li>Sertakan gambar dengan nama mengandung kata <em>sketch</em>, <em>wip</em>, atau <em>draft</em></li>
                <li>Pastikan akun sosial media sudah aktif lebih dari 1 bulan</li>
                <li>Tambahkan link ke postingan timelapse atau proses menggambar</li>
                <li>Pastikan style di sosmed konsisten dengan karya yang diupload</li>
            </ul>
        </div>

        <a href="{{ route('artist.dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>

        @else
        {{-- ══ BELUM ADA SUBMISI ══ --}}
        <div class="status-icon empty">
            <i class="bi bi-folder-x"></i>
        </div>
        <div class="status-title">Belum Ada Submisi</div>
        <div class="status-sub">
            Kamu belum pernah mengajukan verifikasi. Mulai sekarang untuk membuka
            fitur commission di Aneris.
        </div>
        <a href="{{ route('verification.create') }}" class="btn-primary">
            <i class="bi bi-send"></i> Ajukan Verifikasi
        </a>
        <a href="{{ route('artist.dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        @endif

    </div>
</div>

@endsection
