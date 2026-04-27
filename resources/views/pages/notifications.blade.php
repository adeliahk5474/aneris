@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
@php $hideBottomNavbar = true; @endphp
<div class="container" style="padding-top:12px;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link p-0 me-2"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0">Notifications</h5>
    </div>

    <div class="list-group">
        <div class="list-group-item">UserA liked your artwork</div>
        <div class="list-group-item">UserB commented: "Amazing!"</div>
        <div class="list-group-item">UserC started following you</div>
    </div>
</div>
@endsection
