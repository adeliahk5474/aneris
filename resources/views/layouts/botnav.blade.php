{{-- resources/views/layouts/botnav.blade.php --}}
<div class="fixed-bottom bg-white border-top shadow-sm d-flex justify-content-around"
    style="height: 65px; z-index: 1030;">

    {{-- HOME --}}
    <a href="{{ route('home') }}" class="text-dark text-center">
        <i class="bi bi-house-door fs-3"></i>
    </a>

    {{-- SEARCH PAGE --}}
    <a href="{{ route('explore') }}" class="text-dark text-center">
        <i class="bi bi-search fs-3"></i>
    </a>

    {{-- CREATE (artist only) --}}
    @auth
    <a href="{{ route('upload.popup') }}" class="text-dark text-center">
        <i class="bi bi-plus-square fs-3"></i>
    </a>
    @else
    <a href="{{ route('auth.form') }}" class="text-dark text-center">
        <i class="bi bi-plus-square fs-3"></i>
    </a>
    @endauth

    {{-- CHAT / DM --}}
    @auth

    @php

    $unreadCount = \App\Models\Chat::where('receiver_id', auth()->user()->user_id)
    ->where('is_read', false)
    ->distinct('sender_id')
    ->count('sender_id');

    @endphp

    <a href="{{ route('chat.list') }}"
        class="text-dark text-center position-relative">

        <i class="bi bi-chat-dots fs-3"></i>

        {{-- NOTIFICATION --}}
        @if($unreadCount > 0)

        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="
                    font-size:10px;
                    min-width:18px;
                    height:18px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                  ">

            {{ $unreadCount > 99 ? '99+' : $unreadCount }}

        </span>

        @endif

    </a>

    @else

    <a href="{{ route('auth.form') }}"
        class="text-dark text-center">

        <i class="bi bi-chat-dots fs-3"></i>

    </a>

    @endauth

    {{-- PROFILE --}}
    @auth
    <a href="{{ route('profile.show', Auth::id()) }}" class="text-dark text-center">
        <i class="bi bi-person-circle fs-3"></i>
    </a>
    @else
    <a href="{{ route('auth.form') }}" class="text-dark text-center">
        <i class="bi bi-person-circle fs-3"></i>
    </a>
    @endauth

</div>
