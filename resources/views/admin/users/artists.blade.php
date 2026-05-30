{{-- resources/views/pages/admin/users/artists.blade.php --}}
@extends('layouts.admin')
@section('title', 'Artists')

@section('content')

@vite('resources/css/admin/users.css')

<div class="page-header">
    <div>
        <div class="page-title">Artists</div>
        <div class="page-sub">{{ $artists->total() }} artist terdaftar</div>
    </div>
</div>

<div class="table-card">
    <div class="table-head">
        <div class="table-title">Semua Artist</div>
        <form method="GET" action="{{ route('admin.users.artists') }}" class="search-form">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="Cari nama atau email..."
                class="search-input">
            <button type="submit" class="btn-secondary">
                <i class="bi bi-search"></i>
            </button>
            @if($search)
            <a href="{{ route('admin.users.artists') }}" class="btn-secondary">
                <i class="bi bi-x"></i>
            </a>
            @endif
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Artist</th>
                <th>Status</th>
                <th>Artwork</th>
                <th>Commission</th>
                <th>Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @forelse($artists as $user)
            <tr>
                <td>
                    <div class="user-cell">
                        <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}"
                            class="user-avatar" alt="{{ $user->name }}">
                        <div>
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($user->is_verified)
                    <span class="badge badge-approved">
                        <i class="bi bi-patch-check-fill"></i> Verified
                    </span>
                    @else
                    <span class="badge badge-pending">Unverified</span>
                    @endif
                </td>
                <td style="color:var(--text); font-size:13px;">
                    {{ $user->artworks_count }}
                </td>
                <td style="color:var(--text); font-size:13px;">
                    {{ $user->commission_services_count }}
                </td>
                <td style="color:var(--muted); font-size:12px; white-space:nowrap;">
                    {{ $user->created_at->format('d M Y') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:48px; color:var(--muted);">
                    <i class="bi bi-people" style="font-size:32px; opacity:.2; display:block; margin-bottom:10px;"></i>
                    {{ $search ? 'Tidak ada artist dengan pencarian "' . $search . '".' : 'Belum ada artist terdaftar.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($artists->hasPages())
    <div class="pagination-wrap">
        {{ $artists->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
