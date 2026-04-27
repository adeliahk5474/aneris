@extends('layouts.app')
@section('title', 'Chats')

@section('content')
<div class="container" style="padding-top:12px; padding-bottom:80px;">
    <div class="d-flex align-items-center mb-3">
        <h5 class="mb-0">Messages</h5>
    </div>

    <div class="list-group">
        {{-- Example conversation items --}}
        <a href="{{ route('chat.thread', 1) }}" class="list-group-item list-group-item-action d-flex align-items-center">
            <img src="https://via.placeholder.com/48" class="rounded-circle me-3" alt="avatar">
            <div class="flex-fill">
                <div class="d-flex justify-content-between">
                    <strong>UserArtist</strong>
                    <small class="text-muted">2h</small>
                </div>
                <div class="text-muted small">Hey, I finished the sketch — want to see?</div>
            </div>
        </a>

        <a href="{{ route('chat.thread', 2) }}" class="list-group-item list-group-item-action d-flex align-items-center">
            <img src="https://via.placeholder.com/48" class="rounded-circle me-3" alt="avatar">
            <div class="flex-fill">
                <div class="d-flex justify-content-between">
                    <strong>Client123</strong>
                    <small class="text-muted">1d</small>
                </div>
                <div class="text-muted small">Is the final file ready?</div>
            </div>
        </a>
    </div>
</div>
@endsection
