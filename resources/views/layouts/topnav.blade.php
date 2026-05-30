{{-- resources/views/layouts/topnav.blade.php --}}
{{-- Top navigation khusus halaman Explore --}}
@vite('resources/css/layouts/topnav.css')

<nav id="aneris-topnav" role="banner">

    {{-- LOGO --}}
    <a href="{{ route('home') }}" class="aneris-logo">
        <div class="aneris-logo-mark">A</div>
        <span class="aneris-logo-text">Aneris</span>
    </a>

    {{-- SEARCH BAR --}}
    <div class="aneris-search-wrap">
        <form action="{{ route('explore') }}" method="GET" class="aneris-search-form" role="search">
            <span class="aneris-search-icon" aria-hidden="true"><i class="bi bi-search"></i></span>
            <input type="text" name="search" value="{{ request('search') }}"
                class="aneris-search-input"
                placeholder="Search creators, styles, or services..."
                autocomplete="off" aria-label="Search creators, styles, or services"
                id="aneris-search-input">
            <div class="aneris-search-kbd" aria-hidden="true"><kbd>⌘</kbd><kbd>K</kbd></div>
        </form>
    </div>

    {{-- NAV LINKS --}}
    <nav class="aneris-nav-links" aria-label="Main navigation">

        <a href="{{ route('home') }}"
            class="aneris-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
            aria-label="Home">
            <i class="bi {{ request()->routeIs('home') ? 'bi-house-fill' : 'bi-house' }}"></i>
            <span>Home</span>
        </a>

        <a href="{{ route('chat.list') }}"
            class="aneris-nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}"
            aria-label="Chat">
            <span style="position:relative; display:inline-flex; align-items:center;">
                <i class="bi {{ request()->routeIs('chat.*') ? 'bi-chat-fill' : 'bi-chat' }}"></i>
                <span id="chat-nav-badge" class="chat-nav-badge {{ $unreadChatCount > 0 ? 'visible' : '' }}">
                    {{ $unreadChatCount > 99 ? '99+' : ($unreadChatCount ?: '') }}
                </span>
            </span>
            <span>Chat</span>
        </a>

        {{-- Cart: hanya untuk client (non-artist) --}}
        @if(!$isArtist)
        <a href="{{ route('cart.index') }}"
            class="aneris-nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}"
            aria-label="Cart">
            <i class="bi {{ request()->routeIs('cart.*') ? 'bi-bag-fill' : 'bi-bag' }}"></i>
            <span>Cart</span>
        </a>
        @endif

        <a href="{{ route('notifications.index') }}"
            class="aneris-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
            aria-label="Notifications">
            <i class="bi {{ request()->routeIs('notifications.*') ? 'bi-bell-fill' : 'bi-bell' }}"></i>
            <span>Notif</span>
        </a>

        @guest
        <a href="{{ route('auth.form') }}" class="aneris-nav-link" aria-label="Sign In">
            <i class="bi bi-person"></i><span>Sign In</span>
        </a>
        @endguest

        @auth
        <div class="tn-profile-wrap" id="tnProfileWrap">
            <button class="tn-profile-trigger {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                id="tnProfileTrigger" type="button"
                aria-haspopup="true" aria-expanded="false"
                onclick="toggleTnProfileMenu(event)">
                @if($authUser->avatar)
                <img src="{{ $authUser->avatar }}" alt="avatar">
                @else
                <i class="bi {{ request()->routeIs('profile.*') ? 'bi-person-fill' : 'bi-person' }}"></i>
                @endif
                <span>Profile</span>
            </button>

            <div class="tn-profile-dropdown" id="tnProfileDropdown" role="menu">
                <div class="tn-pd-user-info">
                    <div class="tn-pd-user-name">{{ $authUser->name }}</div>
                    <div class="tn-pd-user-email">{{ $authUser->email }}</div>
                </div>

                <a href="{{ route('profile.show', $authUser->user_id) }}" class="tn-pd-item" role="menuitem">
                    <i class="bi bi-person"></i> My Profile
                </a>

                {{-- My Orders: hanya untuk client --}}
                @if(!$isArtist)
                <a href="{{ route('cart.index') }}" class="tn-pd-item" role="menuitem">
                    <i class="bi bi-bag"></i> My Orders
                </a>
                @endif

                <a href="{{ route('notifications.index') }}" class="tn-pd-item" role="menuitem">
                    <i class="bi bi-bell"></i> Notifications
                </a>

                @if($isArtist)
                <a href="{{ route('artist.dashboard') }}" class="tn-pd-item" role="menuitem">
                    <i class="bi bi-brush"></i> Artist Dashboard
                </a>
                @endif

                <div class="tn-pd-divider"></div>
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="tn-pd-item danger" role="menuitem">
                        <i class="bi bi-box-arrow-right"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
        @endauth

    </nav>
</nav>

<div class="aneris-topnav-spacer" aria-hidden="true"></div>
@vite('resources/js/layouts/topnav.js')
