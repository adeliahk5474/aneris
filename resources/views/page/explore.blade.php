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
        grid-template-columns: repeat(5, 1fr);
        /* 5 kolom */
        gap: 4px;
        padding: 6px;
    }

    .explore-item {
        position: relative;
        /* penting buat label */
        width: 100%;
        aspect-ratio: 1/1;
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

    /* FORCE POPUP HIDDEN */
    #servicePopup {
        display: none;
    }

    /* ================= POPUP FIX ================= */
    .artwork-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        background: rgba(0, 0, 0, 0.8);

        justify-content: center;
        align-items: center;

        z-index: 9999;
    }

    .artwork-popup-content {
        background: #fff;
        width: 90%;
        max-width: 400px;
        border-radius: 10px;

        position: relative;

        max-height: 90vh;
        overflow-y: auto;
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

            @if($item->type === 'artwork')
            <a href="{{ $item->preview_url ?? $item->file_url }}" target="_blank">
                <img src="{{ $item->preview_url ?? $item->file_url }}" alt="art">
            </a>
            @else
            <a href="javascript:void(0)"
                class="service-trigger"
                data-id="{{ $item->service_id }}"
                data-title="{{ $item->title }}"
                data-price="{{ $item->price }}"
                data-description="{{ $item->description }}"
                data-image="{{ $item->image_url }}"
                data-user-name="{{ $item->artist->name ?? 'Unknown' }}"
                data-user-avatar="{{ $item->artist->avatar ?? '/default-avatar.png' }}"

                <img src="{{ $item->image_url }}" alt="service">

                {{-- label kecil --}}
                <div style="
                    position:absolute;
                    background:rgba(0,0,0,0.6);
                    color:#fff;
                    font-size:10px;
                    padding:2px 5px;
                    border-radius:4px;
                    margin:4px;
                ">
                    COMMISSION
                </div>
            </a>
            @endif

        </div>
        @endforeach
    </div>

</div>

@include('layouts.botnav')
@include('commission.show')
@include('commission.order')
@include('commission.payment')

<script src="{{ asset('js/explore-popup.js') }}"></script>
<script src="{{ asset('js/order-popup.js') }}"></script>


@endsection
