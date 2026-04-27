@extends('layouts.app')
@section('title', 'Commission Cart')

@section('content')
@php $hideBottomNavbar = true; @endphp
<div class="container" style="padding-top:12px;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link p-0 me-2"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0">Commission Cart</h5>
    </div>

    <div class="list-group">
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>Commission #001</strong>
                <div class="small text-muted">ArtistName — Completed</div>
            </div>
            <div>
                <a href="#" class="btn btn-sm btn-primary">View</a>
            </div>
        </div>
    </div>
</div>
@endsection
