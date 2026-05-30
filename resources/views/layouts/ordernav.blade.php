{{-- resources/views/layouts/ordernav.blade.php --}}
{{-- Dipakai di: commission/detail, order detail, artist dashboard --}}
@vite('resources/css/layouts/ordernav.css')

<nav id="order-topnav" role="navigation">

    {{-- BACK --}}
    <a href="javascript:history.back()" class="ordernav-back" aria-label="Go back">
        <i class="bi bi-arrow-left"></i>
    </a>

    {{-- NAV TENGAH --}}
    <div class="ordernav-center">
        <a href="{{ route('home') }}"
            class="ordernav-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <span>Home</span>
        </a>
        <a href="{{ route('explore') }}"
            class="ordernav-link {{ request()->routeIs('explore') ? 'active' : '' }}">
            <span>Search</span>
        </a>
        <a href="{{ route('chat.list') }}"
            class="ordernav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
            <span style="position:relative; display:inline-flex; align-items:center; gap:6px;">
                Chat
                <span id="chat-nav-badge" class="chat-nav-badge {{ $unreadChatCount > 0 ? 'visible' : '' }}">
                    {{ $unreadChatCount > 99 ? '99+' : ($unreadChatCount ?: '') }}
                </span>
            </span>
        </a>
        <a href="{{ auth()->check() ? route('profile.show', auth()->user()->user_id) : route('auth.form') }}"
            class="app-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span>Profile</span>
        </a>
    </div>

    {{-- KANAN: cart + notif + profile --}}
    <div class="ordernav-right">
        @if(!$isArtist)
        <a href="{{ route('cart.index') }}" class="app-icon-btn" aria-label="Cart">
            <i class="bi bi-bag"></i>
        </a>
        @endif
        <a href="{{ route('notifications.index') }}" class="ordernav-icon" aria-label="Notifications">
            <i class="bi bi-bell"></i>
        </a>

        @auth
        {{-- Profile dropdown --}}
        <div class="on-profile-wrap" id="onProfileWrap">

            <button class="on-profile-trigger" id="onProfileTrigger"
                type="button" aria-haspopup="true" aria-expanded="false"
                onclick="toggleOnProfileMenu(event)">
                @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                @else
                <i class="bi bi-person"></i>
                @endif
            </button>

            <div class="on-profile-dropdown" id="onProfileDropdown" role="menu">

                <div class="on-pd-user-info">
                    <div class="on-pd-user-name">{{ auth()->user()->name }}</div>
                    <div class="on-pd-user-email">{{ auth()->user()->email }}</div>
                </div>

                <a href="{{ route('profile.show', auth()->user()->user_id) }}"
                    class="on-pd-item" role="menuitem">
                    <i class="bi bi-person"></i> My Profile
                </a>

                @if(!$isArtist)
                <a href="{{ route('cart.index') }}" class="pd-item" role="menuitem">
                    <i class="bi bi-bag"></i> My Orders
                </a>
                @endif

                @if(auth()->user()->role === 'artist')
                <a href="{{ route('artist.dashboard') }}"
                    class="on-pd-item" role="menuitem">
                    <i class="bi bi-brush"></i> Artist Dashboard
                </a>
                @endif

                <div class="on-pd-divider"></div>

                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="on-pd-item danger" role="menuitem">
                        <i class="bi bi-box-arrow-right"></i> Log Out
                    </button>
                </form>

            </div>
        </div>
        @endauth

    </div>

</nav>

<div class="ordernav-spacer" aria-hidden="true"></div>
@vite('resources/js/layouts/ordernav.js')
