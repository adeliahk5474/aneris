@extends('layouts.app')

@section('content')

<style>
/* ===============================
   SEARCH BAR
=============================== */
.explore-search {
    padding: 10px;
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 10;
}

.search-box {
    display: flex;
    align-items: center;
    background: #f1f1f1;
    border-radius: 20px;
    padding: 8px 12px;
}

.search-box i {
    margin-right: 8px;
    color: #777;
}

.search-box input {
    border: none;
    background: transparent;
    width: 100%;
    outline: none;
}

/* ===============================
   GRID CONTENT
=============================== */
.explore-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr); /* 5 kolom */
    gap: 4px;
    padding: 6px;
}

.explore-item {
    width: 100%;
    aspect-ratio: 1/1; /* persegi */
    overflow: hidden;
    background: #eee;
    border-radius: 6px;
    border: 1px solid #000;
}

.explore-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* RESPONSIVE GRID */
@media (max-width: 1200px) {
    .explore-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
@media (max-width: 992px) {
    .explore-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 576px) {
    .explore-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="container-fluid" style="padding-bottom: 80px;">

    {{-- SEARCH BAR --}}
    <div class="p-3 bg-white sticky-top" style="z-index:1020;">
        <form action="{{ route('explore') }}" method="GET" class="search-box w-100">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search fs-4"></i>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="form-control border-start-0"
                    placeholder="Search artworks, artists..."
                    autocomplete="off"
                    style="font-size:18px; padding:12px;">
            </div>
        </form>
    </div>

    {{-- GRID CONTENT --}}
    <div class="explore-grid">
        @foreach($explore as $item)
            <div class="explore-item">
                <a href="{{ $item->preview_url ?? $item->file_url }}" target="_blank">
                    <img src="{{ $item->preview_url ?? $item->file_url }}" alt="art">
                </a>
            </div>
        @endforeach
    </div>

</div>

@include('layouts.botnav')

@endsection
