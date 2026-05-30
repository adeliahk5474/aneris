{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Aneris</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite('resources/css/admin/admin.css')
    @stack('styles')
</head>

<body>

    <div class="admin-wrap">

        {{-- ── TOPBAR ── --}}
        <header class="admin-topbar">
            <div class="topbar-brand">
                <div class="topbar-brand-icon">A</div>
                <span class="topbar-brand-name">Aneris</span>
                <span class="topbar-brand-badge">ADMIN</span>
            </div>

            <div class="topbar-right">
                <span class="topbar-admin-name">
                    <i class="bi bi-person-circle"></i>
                    {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
                </span>
                <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="topbar-logout">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- ── SIDEBAR ── --}}
        <aside class="admin-sidebar">
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            <div class="sidebar-section-label">Verifikasi</div>
            <a href="{{ route('admin.verification.index') }}"
                class="sidebar-item {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}">
                <i class="bi bi-patch-check"></i> Antrean Verifikasi
                @php
                $pendingCount = \App\Models\PortfolioVerification::whereIn('status', ['pending','in_review'])->count();
                @endphp
                @if($pendingCount > 0)
                <span class="sidebar-badge yellow">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="sidebar-section-label">Users</div>
            <a href="{{ route('admin.users.artists') }}"
                class="sidebar-item {{ request()->routeIs('admin.users.artists') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Artists
            </a>
            <a href="{{ route('admin.users.clients') }}"
                class="sidebar-item {{ request()->routeIs('admin.users.clients') ? 'active' : '' }}">
                <i class="bi bi-person"></i> Clients
            </a>

            <div class="sidebar-section-label">Konten</div>
            <a href="{{ route('admin.home-setting.edit') }}"
                class="sidebar-item {{ request()->routeIs('admin.home-setting.*') ? 'active' : '' }}">
                <i class="bi bi-house-gear"></i> Tampilan Home
            </a>
        </aside>

        {{-- ── MAIN ── --}}
        <main class="admin-main">
            @yield('content')
        </main>

    </div>

    {{-- Lightbox global --}}
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <span class="lightbox-close" onclick="closeLightbox()">×</span>
        <img id="lightboxImg" src="" alt="">
    </div>

    @vite('resources/js/admin/admin-layout.js')
    @stack('scripts')

</body>

</html>
