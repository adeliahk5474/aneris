@extends('layouts.app')
@section('title', 'Reviews')

@section('content')
<div class="container" style="padding-top:12px; padding-bottom:80px;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link p-0 me-2"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0">Artist Reviews</h5>
    </div>

    <div class="list-group">
        <div class="list-group-item">
            <strong>UserA</strong>
            <div class="small text-muted">5 stars</div>
            <div>Great work, very professional.</div>
        </div>
        <div class="list-group-item">
            <strong>UserB</strong>
            <div class="small text-muted">4 stars</div>
            <div>Nice result, minor tweaks requested.</div>
        </div>
    </div>
</div>
@endsection
