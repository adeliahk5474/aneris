{{-- resources/views/pages/notifications.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi — Aneris')

@section('content')

@vite('resources/css/pages/notifications.css')

<div class="notif-wrap">

    {{-- Header --}}
    <div class="notif-header">
        <div class="notif-title">Notifikasi</div>
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn-read-all">Tandai semua dibaca</button>
        </form>
    </div>

    {{-- Filter tabs --}}
    <div class="notif-tabs">
        @foreach($tabs as $key => $tab)
        <a href="{{ route('notifications.index', $key ? ['type' => $key] : []) }}"
            class="notif-tab {{ ($type ?? '') === $key ? 'active' : '' }}">
            <i class="bi {{ $tab['icon'] }}"></i>
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>

    {{-- Notification list --}}
    @if($notifications->count())
    <div class="notif-list">
        @foreach($notifications as $notif)

        <a href="{{ $notif->linkUrl }}"
            class="notif-item {{ $notif->isUnread ? 'unread' : '' }}"
            onclick="markRead('{{ $notif->notif_id }}')">

            <div class="notif-icon {{ $notif->iconClass }}">
                <i class="bi {{ $notif->iconName }}"></i>
            </div>

            <div class="notif-content">
                <div class="notif-message">{{ $notif->message }}</div>
                <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
            </div>

            @if($notif->isUnread)
            <div class="notif-dot"></div>
            @endif

        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="notif-pagination">
        @if($notifications->onFirstPage())
        <span class="page-btn disabled">← Prev</span>
        @else
        <a href="{{ $notifications->previousPageUrl() }}" class="page-btn">← Prev</a>
        @endif

        @foreach($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
        <a href="{{ $url }}"
            class="page-btn {{ $notifications->currentPage() === $page ? 'active' : '' }}">
            {{ $page }}
        </a>
        @endforeach

        @if($notifications->hasMorePages())
        <a href="{{ $notifications->nextPageUrl() }}" class="page-btn">Next →</a>
        @else
        <span class="page-btn disabled">Next →</span>
        @endif
    </div>
    @endif

    @else
    <div class="empty-state">
        <i class="bi bi-bell-slash"></i>
        <p>
            @if(!empty($type) && $type !== 'all')
                Tidak ada notifikasi {{ $tabs[$type]['label'] ?? $type }}.
            @else
                Belum ada notifikasi.
            @endif
        </p>
    </div>
    @endif

</div>

<script>
window.notificationsPage = @json([
    'markReadUrlTemplate' => route('notifications.read', ['notifId' => '__NOTIF_ID__']),
]);
</script>
@vite('resources/js/pages/notifications.js')

@endsection
