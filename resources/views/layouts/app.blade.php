{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aneris')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite('resources/css/layouts/app.css')
    @stack('styles')
</head>

<body>

    <nav id="app-topnav" role="banner">

        {{-- LOGO --}}
        <a href="{{ route('home') }}" class="app-logo">
            <div class="app-logo-mark">A</div>
            <span class="app-logo-text">Aneris</span>
        </a>

        {{-- NAV LINKS TENGAH --}}
        <div class="app-nav-center">
            <a href="{{ route('home') }}"
                class="app-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <span>Home</span>
            </a>
            <a href="{{ route('explore') }}"
                class="app-nav-link {{ request()->routeIs('explore') ? 'active' : '' }}">
                <span>Search</span>
            </a>
            <a href="{{ route('chat.list') }}"
                class="app-nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <span style="position:relative; display:inline-flex; align-items:center; gap:6px;">
                    Chat
                    <span id="chat-nav-badge" class="chat-nav-badge {{ $unreadChatCount > 0 ? 'visible' : '' }}">
                        {{ $unreadChatCount > 99 ? '99+' : ($unreadChatCount ?: '') }}
                    </span>
                </span>
            </a>
            <a href="{{ auth()->check() ? route('profile.show', $authUser->user_id) : route('auth.form') }}"
                class="app-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span>Profile</span>
            </a>
        </div>

        {{-- NAV KANAN --}}
        <div class="app-nav-right">

            @if(!$isArtist)
            <a href="{{ route('cart.index') }}" class="app-icon-btn" aria-label="Cart">
                <i class="bi bi-bag"></i>
            </a>
            @endif

            {{-- Notif dengan unread dot --}}
            <a href="{{ route('notifications.index') }}"
                class="app-icon-btn {{ $unreadNotifCount > 0 ? 'has-unread' : '' }}"
                aria-label="Notifications">
                <i class="bi bi-bell"></i>
                <span class="notif-badge" aria-hidden="true"></span>
            </a>

            @guest
            <a href="{{ route('auth.form') }}" class="btn-artist">I'm an artist+</a>
            <a href="{{ route('auth.form') }}" class="btn-signup">Sign up</a>
            @endguest

            @auth
            <div class="profile-menu-wrap" id="profileMenuWrap">
                <button class="profile-trigger" id="profileTrigger"
                    aria-haspopup="true" aria-expanded="false"
                    onclick="toggleProfileMenu(event)" type="button">
                    <img src="{{ $authUser->avatar ?? asset('images/default-avatar.png') }}"
                        alt="{{ $authUser->name }}" class="app-avatar">
                    <span class="profile-trigger-name">{{ $authUser->name }}</span>
                    <i class="bi bi-chevron-down profile-trigger-chevron"></i>
                </button>

                <div class="profile-dropdown" id="profileDropdown" role="menu">
                    <div class="pd-user-info">
                        <div class="pd-user-name">{{ $authUser->name }}</div>
                        <div class="pd-user-email">{{ $authUser->email }}</div>
                    </div>

                    <a href="{{ route('profile.show', $authUser->user_id) }}" class="pd-item" role="menuitem">
                        <i class="bi bi-person"></i> My Profile
                    </a>

                    @if(!$isArtist)
                    <a href="{{ route('cart.index') }}" class="pd-item" role="menuitem">
                        <i class="bi bi-bag"></i> My Orders
                    </a>
                    @endif

                    <a href="{{ route('notifications.index') }}" class="pd-item" role="menuitem">
                        <i class="bi bi-bell"></i> Notifications
                        @if($unreadNotifCount > 0)
                        <span style="margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:999px;">
                            {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                        </span>
                        @endif
                    </a>

                    @if($isArtist)
                    <a href="{{ route('artist.dashboard') }}" class="pd-item" role="menuitem">
                        <i class="bi bi-brush"></i> Artist Dashboard
                    </a>
                    @endif

                    <div class="pd-divider"></div>
                    <form action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="pd-item danger" role="menuitem">
                            <i class="bi bi-box-arrow-right"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
            @endauth

        </div>
    </nav>

    <div class="app-topnav-spacer" aria-hidden="true"></div>

    <main class="app-content">
        @yield('content')
    </main>

    {{-- CHAT NOTIFICATION TOAST --}}
    @auth
    <div id="chat-notif-toast">
        <div id="chat-notif-top">
            <div id="chat-notif-left">
                <img id="chat-notif-avatar" src="" alt="">
                <div>
                    <div id="chat-notif-name"></div>
                    <div id="chat-notif-message"></div>
                </div>
            </div>
            <button id="chat-notif-close" onclick="window._closeChatNotif()">&times;</button>
        </div>
    </div>

    <meta name="auth-user-id" content="{{ Auth::user()->user_id }}">
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/layouts/app.js')
    @stack('scripts')
</body>

</html>
