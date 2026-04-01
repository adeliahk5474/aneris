{{-- resources/views/layouts/botnav.blade.php --}}
<div class="fixed-bottom bg-white border-top shadow-sm d-flex justify-content-around py-2"
     style="z-index: 1030;">

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
        <a href="{{ route('chat.index') }}" class="text-dark text-center">
            <i class="bi bi-chat-dots fs-3"></i>
        </a>
    @else
        <a href="{{ route('auth.form') }}" class="text-dark text-center">
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
