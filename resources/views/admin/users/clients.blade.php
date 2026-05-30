{{-- resources/views/pages/admin/users/clients.blade.php --}}
@extends('layouts.admin')
@section('title', 'Clients')

@section('content')

@vite('resources/css/admin/users.css')

<div class="page-header">
    <div>
        <div class="page-title">Clients</div>
        <div class="page-sub">{{ $clients->total() }} client terdaftar</div>
    </div>
</div>

<div class="table-card">
    <div class="table-head">
        <div class="table-title">Semua Client</div>
        <form method="GET" action="{{ route('admin.users.clients') }}" class="search-form">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="Cari nama atau email..."
                class="search-input">
            <button type="submit" class="btn-secondary">
                <i class="bi bi-search"></i>
            </button>
            @if($search)
            <a href="{{ route('admin.users.clients') }}" class="btn-secondary">
                <i class="bi bi-x"></i>
            </a>
            @endif
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Total Order</th>
                <th>Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $user)
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
                <td style="color:var(--text); font-size:13px;">
                    {{ $user->orders_as_client_count }} order
                </td>
                <td style="color:var(--muted); font-size:12px; white-space:nowrap;">
                    {{ $user->created_at->format('d M Y') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center; padding:48px; color:var(--muted);">
                    <i class="bi bi-person" style="font-size:32px; opacity:.2; display:block; margin-bottom:10px;"></i>
                    {{ $search ? 'Tidak ada client dengan pencarian "' . $search . '".' : 'Belum ada client terdaftar.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($clients->hasPages())
    <div class="pagination-wrap">
        {{ $clients->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
