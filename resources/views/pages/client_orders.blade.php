@extends('layouts.app')
@section('title', 'Your Orders')

@section('content')
<div class="container" style="padding-top:12px; padding-bottom:80px;">
    <div class="d-flex align-items-center mb-3">
        <h5 class="mb-0">Your Orders</h5>
    </div>

    <div class="list-group">
        <div class="list-group-item">
            <div class="d-flex justify-content-between">
                <div>
                    <strong>Order #123</strong>
                    <div class="small text-muted">ArtistName — Paid / On Progress</div>
                </div>
                <div>
                    <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
