<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aneris</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #fafafa;
        }

        .insta-nav {
            height: 56px;
        }

        .insta-brand {
            font-size: 22px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    {{-- TOP NAV --}}
    <nav class="navbar navbar-light bg-white border-bottom shadow-sm sticky-top insta-nav px-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            {{-- Brand kiri --}}
            <a href="{{ route('home') }}" class="navbar-brand text-dark insta-brand">
                Aneris
            </a>

            {{-- Icon kanan --}}
            <div class="d-flex align-items-center">


                <a href="{{ route('explore') }}" class="text-dark mx-2">
                    <i class="bi bi-heart fs-4"></i>
                </a>

                @auth
                <a href="{{ route('cart.index') }}" class="text-dark mx-2">
                    <i class="bi bi-bag fs-4"></i>
                </a>
                @endauth

            </div>

        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <div class="container-fluid" style="padding-bottom: 80px;">
        @yield('content')
    </div>

    {{-- BOTTOM NAV --}}
    @include('layouts.botnav')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
