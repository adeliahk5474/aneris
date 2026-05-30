{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">
            Selamat datang, {{ Auth::guard('admin')->user()->name }}.
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
</div>

{{-- Stat cards --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-label">Pending Review</div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-eye"></i></div>
        <div>
            <div class="stat-label">In Review</div>
            <div class="stat-value">{{ $stats['in_review'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-patch-check-fill"></i></div>
        <div>
            <div class="stat-label">Approved</div>
            <div class="stat-value">{{ $stats['approved'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
        <div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ $stats['rejected'] }}</div>
        </div>
    </div>
</div>

{{-- Tabel terbaru --}}
<div class="table-card">
    <div class="table-head">
        <div class="table-title">Submission Terbaru</div>
        <a href="{{ route('admin.verification.index') }}" class="btn-secondary">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Artist</th>
                <th>Dikirim</th>
                <th>Skor AI</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent as $v)
            <tr onclick="window.location='{{ route('admin.verification.show', $v->id) }}'">
                <td>
                    <div style="font-weight:600;">{{ $v->artist->name ?? '—' }}</div>
                    <div style="font-size:11px; color:var(--muted);">{{ $v->artist->email ?? '' }}</div>
                </td>
                <td style="color:var(--muted); font-size:12px;">
                    {{ $v->created_at->diffForHumans() }}
                </td>
                <td>
                    @if($v->ai_score_reference !== null)
                    @php $sc = $v->ai_score_reference; $cls = $sc >= 60 ? 'high' : ($sc >= 35 ? 'medium' : 'low'); @endphp
                    <div class="score-bar-wrap">
                        <div class="score-bar">
                            <div class="score-bar-fill {{ $cls }}" style="width:{{ $sc }}%"></div>
                        </div>
                        <span class="score-num {{ $cls }}">{{ $sc }}</span>
                    </div>
                    @else
                    <span style="color:var(--muted); font-size:11px;">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $v->status }}">
                        {{ ucfirst(str_replace('_', ' ', $v->status)) }}
                    </span>
                </td>
                <td><i class="bi bi-arrow-right" style="color:var(--muted);"></i></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:32px; color:var(--muted);">
                    Belum ada submission.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
