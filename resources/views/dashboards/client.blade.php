{{-- resources/views/dashboards/client.blade.php --}}
@extends('layouts.app')

@section('content')

@php
    $bannerUrl = $user->banner ?? 'https://cdn-icons-png.flaticon.com/512/1828/1828817.png'; // default banner
@endphp

<div class="container-fluid px-0">

    {{-- Banner Full-Width --}}
    <div class="position-relative mb-4">
        <img src="{{ $bannerUrl }}" class="img-fluid w-100 rounded" style="height: 180px; object-fit: cover;" alt="Banner">

        {{-- User Name + Settings Icon Overlay --}}
        <div class="position-absolute top-0 start-0 d-flex align-items-center p-3">
            <h4 class="text-white fw-bold me-2">{{ $user->name }}</h4>
            <!-- Settings Icon -->
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#bannerModal">
                <i class="fa fa-cog"></i>
            </button>
        </div>
    </div>

    {{-- 3 Columns Grid --}}
    <div class="row">
        {{-- Grid Kiri: Order & Riwayat --}}
        <div class="col-lg-4 mb-4">
            <div class="mb-4">
                <h5 class="fw-semibold">Order Aktif</h5>
                <div class="row">
                    @if($activeOrders && $activeOrders->count())
                        @foreach($activeOrders as $order)
                            <div class="col-6 mb-3">
                                <div class="card h-100">
                                    <img src="{{ $order->image ?? 'https://cdn-icons-png.flaticon.com/512/6598/6598519.png' }}" class="card-img-top" alt="Order Image">
                                    <div class="card-body p-2">
                                        <p class="card-text small mb-0">{{ $order->title }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center text-muted py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" width="80" class="opacity-50 mb-2" alt="no order">
                            <p class="mb-0">Belum ada order aktif</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h5 class="fw-semibold">Riwayat</h5>
                <div class="row">
                    @if($orderHistory && $orderHistory->count())
                        @foreach($orderHistory as $history)
                            <div class="col-6 mb-3">
                                <div class="card h-100">
                                    <img src="{{ $history->image ?? 'https://cdn-icons-png.flaticon.com/512/6598/6598519.png' }}" class="card-img-top" alt="History Image">
                                    <div class="card-body p-2">
                                        <p class="card-text small mb-0">{{ $history->title }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center text-muted py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" width="80" class="opacity-50 mb-2" alt="no history">
                            <p class="mb-0">Belum ada riwayat</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grid Tengah: Rating & Review --}}
        <div class="col-lg-4 mb-4">
            <h5 class="fw-semibold">Rating & Review</h5>
            @if($reviews && $reviews->count())
                @foreach($reviews as $review)
                    <div class="card mb-3">
                        <div class="card-body d-flex align-items-start">
                            <img src="{{ $review->artist->avatar ?? 'https://ui-avatars.com/api/?name=Artist' }}" class="rounded-circle me-2" width="40" height="40" alt="Artist Avatar">
                            <div>
                                <small class="fw-bold">{{ $review->artist->name }}</small>
                                <div class="text-warning small">
                                    {!! str_repeat('<i class="fa fa-star"></i>', $review->rating) !!}
                                    {!! str_repeat('<i class="fa fa-star-o"></i>', 5 - $review->rating) !!}
                                </div>
                                <p class="mb-0 small">{{ $review->text }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted py-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" width="80" class="opacity-50 mb-2" alt="no reviews">
                    <p class="mb-0">Belum ada review</p>
                </div>
            @endif
        </div>

        {{-- Grid Kanan: Hasil Order / Artwork --}}
        <div class="col-lg-4 mb-4">
            <h5 class="fw-semibold">Hasil Order</h5>
            <div class="row">
                @if($results && $results->count())
                    @foreach($results as $result)
                        <div class="col-6 mb-3">
                            <div class="card h-100">
                                <img src="{{ $result->image ?? 'https://cdn-icons-png.flaticon.com/512/6598/6598519.png' }}" class="card-img-top" alt="Result Image">
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center text-muted py-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" width="80" class="opacity-50 mb-2" alt="no result">
                        <p class="mb-0">Belum ada hasil order</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Banner Upload Modal --}}
<div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="bannerModalLabel">Upload Banner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('client.banner.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="file" name="banner" class="form-control" accept="image/*" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
