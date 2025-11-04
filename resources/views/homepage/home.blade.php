{{-- resources/views/homepage/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Home')

@section('content')
    {{-- Banner --}}
    <section class="container-fluid p-0 mb-4">
        <div class="position-relative w-100" style="height: 65vh; overflow:hidden;">
            <img src="https://cdn.pixabay.com/photo/2023/04/01/15/39/art-7893095_1280.jpg"
                 class="w-100 h-100 object-fit-cover" alt="Aneris Banner">
            <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                <h1 class="fw-bold display-5">Welcome to Aneris</h1>
                <p class="fs-5 mb-4">Discover and commission talented artists from around the world</p>
                <a href="#showcase" class="btn btn-light btn-lg px-4 rounded-pill fw-semibold shadow-sm">Explore Artists</a>
            </div>
        </div>
    </section>

    {{-- Search & Filter --}}
    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form method="GET" action="{{ route('home') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2 rounded-pill"
                           placeholder="Search commission or artist..."
                           value="{{ request('search') }}">
                    <button class="btn btn-dark rounded-pill px-4">Search</button>
                </form>
            </div>
        </div>
    </section>

    {{-- Showcase Section --}}
    <section id="showcase" class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-semibold mb-0">Showcase for Artists</h3>
            <a href="#" class="text-decoration-none text-dark fw-medium small">See All →</a>
        </div>

        <div class="row">
            @forelse($services as $service)
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 hover-scale"
                         style="transition: transform .2s;">
                        <img src="{{ $service->image_url ?? 'https://cdn-icons-png.flaticon.com/512/6598/6598519.png' }}"
                             class="card-img-top" alt="Service Image"
                             style="object-fit: cover; height: 220px;">

                        <div class="card-body">
                            <h5 class="fw-semibold mb-1">{{ $service->title }}</h5>
                            <p class="text-muted small mb-2">by {{ $service->artist->name ?? 'Unknown Artist' }}</p>
                            <p class="text-secondary small">{{ Str::limit($service->description, 90) }}</p>

                            <button class="btn btn-outline-dark btn-sm rounded-pill mt-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#serviceModal{{ $service->id }}">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Detail --}}
                <div class="modal fade" id="serviceModal{{ $service->id }}" tabindex="-1"
                     aria-labelledby="serviceModalLabel{{ $service->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-sm">
                            <div class="modal-header bg-dark text-white">
                                <h5 class="modal-title" id="serviceModalLabel{{ $service->id }}">{{ $service->title }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <img src="{{ $service->image_url ?? 'https://cdn-icons-png.flaticon.com/512/6598/6598519.png' }}"
                                     class="w-100 rounded mb-3" alt="Service Image">
                                <p>{{ $service->description }}</p>
                                <p class="fw-semibold mb-0">Artist: {{ $service->artist->name ?? 'Unknown' }}</p>
                                <p class="text-muted">Price: Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-dark">Commission This</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" width="90" class="opacity-50 mb-3">
                    <p class="mb-0">No commission services available at the moment.</p>
                </div>
            @endforelse
        </div>
    </section>

    <style>
        .hover-scale:hover { transform: scale(1.02); }
    </style>
@endsection
