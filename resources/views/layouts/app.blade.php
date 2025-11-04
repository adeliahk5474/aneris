{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aneris — @yield('title', 'Dashboard')</title>

    <!-- Font dan Tailwind -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fafafa;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar style */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-weight: 600;
            color: #5b2ecc !important;
            letter-spacing: 0.5px;
        }

        .navbar-brand:hover {
            color: #4a25a8 !important;
        }

        .nav-link {
            color: #555 !important;
            font-weight: 500;
            margin-right: 1rem;
        }

        .nav-link.disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .nav-link:hover {
            color: #000 !important;
        }

        .icon-btn {
            background: none;
            border: none;
            color: #555;
            font-size: 1.3rem;
            margin-right: 1rem;
            transition: color 0.2s;
        }

        .icon-btn:hover {
            color: #000;
        }

        footer {
            border-top: 1px solid #eaeaea;
            padding: 1rem 0;
            text-align: center;
            color: #888;
            font-size: 0.9rem;
            margin-top: auto;
        }
    </style>
</head>

<body>

<div class="min-vh-100 d-flex flex-column bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container-fluid px-4">
            <!-- Logo -->
            <a class="navbar-brand fw-bold fs-4" href="#" style="letter-spacing: 1px;">
                Aneris
            </a>

            <!-- Navbar Right -->
            <div class="d-flex align-items-center">
                <!-- Menu Links -->
                <ul class="navbar-nav me-3 d-none d-md-flex">
                    <li class="nav-item mx-2">
                        <a class="nav-link text-muted disabled">Artwork</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link text-muted disabled">Commission</a>
                    </li>
                </ul>

                {{-- Tambahan: Navbar dinamis guest / auth --}}
                @guest
                    <a href="{{ route('auth.form') }}" class="btn btn-outline-primary">Login / Register</a>
                @else
                    <!-- Icons -->
                    <button class="btn btn-link text-dark me-2" data-bs-toggle="modal" data-bs-target="#cartModal">
                        <i class="bi bi-cart3 fs-5"></i>
                    </button>
                    <button class="btn btn-link text-dark me-3" data-bs-toggle="modal" data-bs-target="#chatModal">
                        <i class="bi bi-chat-dots fs-5"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" class="rounded-circle me-2" width="32" height="32" alt="avatar">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('auth.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </nav>
</div>

{{-- MAIN CONTENT --}}
<main class="container-fluid py-4">
    @yield('content')
</main>

{{-- FOOTER --}}
<footer>
    &copy; {{ date('Y') }} Aneris Platform. All rights reserved.
</footer>

<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="chatModalLabel">Chat dengan Artist</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="height: 60vh; overflow-y: auto;">
                <div class="text-center text-muted my-5">
                    <p>Belum ada pesan.</p>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <input type="text" class="form-control me-2" placeholder="Ketik pesan..." disabled>
                <button class="btn btn-dark" disabled>Kirim</button>
            </div>
        </div>
    </div>
</div>

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="cartModalLabel">Keranjang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center text-muted my-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077976.png" width="100" class="opacity-75 mb-3" alt="empty cart">
                    <p>Keranjang kamu masih kosong.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
