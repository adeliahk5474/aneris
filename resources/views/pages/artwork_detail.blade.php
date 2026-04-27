@extends('layouts.app')
@section('title', 'Artwork')

@section('content')
<div class="container-fluid" style="padding-top:10px;">
    <div class="d-flex align-items-center mb-2">
        <a href="{{ url()->previous() }}" class="btn btn-link p-0 me-2"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0">Artwork</h5>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <img src="https://via.placeholder.com/900x900" class="w-100" alt="Artwork">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>Artist Name</strong>
                            <div class="text-muted small">@artist_handle</div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-light"><i class="bi bi-heart"></i></button>
                            <button class="btn btn-light"><i class="bi bi-chat"></i></button>
                        </div>
                    </div>

                    <p class="mt-3">Caption and artwork info here. Tags • Details</p>

                    <hr>

                    <h6>Comments</h6>
                    <div class="mt-2">
                        <div class="mb-2"><strong>UserA</strong> Great art!</div>
                        <div class="mb-2"><strong>UserB</strong> Love the colors.</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
