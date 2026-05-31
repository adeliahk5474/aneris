{{-- resources/views/admin/verifications/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Antrean Verifikasi')

@section('content')
@vite('resources/css/pages/verification/index.css')

<div class="page-header">
    <div>
        <div class="page-title">Antrean Verifikasi Portfolio</div>
        <div class="page-sub">{{ $total }} total submission</div>
    </div>
</div>

<div class="table-card">
    <div class="table-head">
        <div class="table-title">Semua Submission</div>

        <div class="table-controls">
            {{-- Filter status --}}
            <div class="filter-bar">
                @foreach(['all' => 'Semua', 'pending' => 'Pending', 'in_review' => 'In Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $lbl)
                <a href="{{ route('admin.verification.index', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
                    class="filter-chip {{ $currentStatus === $val ? 'active' : '' }}">
                    {{ $lbl }}
                    @if($val !== 'all' && isset($counts[$val]) && $counts[$val] > 0)
                    <span class="filter-count">{{ $counts[$val] }}</span>
                    @endif
                </a>
                @endforeach
            </div>

            {{-- Sort --}}
            <select class="sort-select" onchange="window.location=this.value">
                @foreach([
                'latest' => 'Terbaru dulu',
                'oldest' => 'Terlama dulu',
                'score_asc' => 'Skor AI rendah dulu',
                'score_desc' => 'Skor AI tinggi dulu',
                ] as $val => $lbl)
                <option
                    value="{{ route('admin.verification.index', array_merge(request()->query(), ['sort' => $val, 'page' => 1])) }}"
                    {{ $currentSort === $val ? 'selected' : '' }}>
                    {{ $lbl }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <table class="verif-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Artist</th>
                <th>File</th>
                <th>Sosmed</th>
                <th>Status</th>
                <th>Dikirim</th>
                <th>Direview</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($verifications as $v)
            <tr onclick="window.location='{{ route('admin.verification.show', $v->id) }}'"
                class="verif-row">
                <td class="col-id">{{ $v->id }}</td>
                <td>
                    <div class="artist-name">{{ $v->artist->name ?? '—' }}</div>
                    <div class="artist-email">{{ $v->artist->email ?? '' }}</div>
                </td>
                <td class="col-meta">
                    <i class="bi bi-images"></i>
                    {{ count($v->portfolio_files ?? []) }} gambar
                </td>
                <td class="col-meta">
                    <i class="bi bi-link-45deg"></i>
                    {{ count($v->social_media_links ?? []) }} link
                </td>
                <td>
                    <span class="badge badge-{{ $v->status }}">
                        {{ ucfirst(str_replace('_', ' ', $v->status)) }}
                    </span>
                </td>
                <td class="col-date">{{ $v->created_at->format('d M Y') }}</td>
                <td class="col-date">{{ $v->reviewed_at ? $v->reviewed_at->format('d M Y') : '—' }}</td>
                <td><i class="bi bi-arrow-right col-arrow"></i></td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="empty-state">
                    <i class="bi bi-inbox"></i>
                    Tidak ada submission untuk filter ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($verifications->hasPages())
    <div class="pagination-wrap">
        {{ $verifications->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
