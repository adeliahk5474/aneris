@extends('layouts.app')
@section('title', 'Service')

@section('content')
<div class="container-fluid" style="padding-top:12px; padding-bottom:80px;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-link p-0 me-2"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0">Service Detail</h5>
    </div>

    <div class="card">
        <div class="row g-0">
            <div class="col-12 col-md-6">
                <img src="https://via.placeholder.com/800x800" class="w-100" alt="service">
            </div>
            <div class="col-12 col-md-6">
                <div class="card-body">
                    <h5>Service Title</h5>
                    <p class="text-muted">by <strong>ArtistName</strong></p>
                    <p>Service description and pricing details go here.</p>

                    <div class="mt-4">
                                        <button class="btn btn-primary btn-lg" id="openOrderBtn" type="button">Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                @include('partials.order_modal')

                @section('scripts')
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const openBtn = document.getElementById('openOrderBtn');
                    // set artist id if available (for now use placeholder)
                    const artistId = '{{ request()->user()?->user_id ?? "" }}' || '{{ $id ?? "" }}';
                    const orderArtistInput = document.getElementById('order_artist_id');
                    if (orderArtistInput) orderArtistInput.value = artistId;

                    openBtn?.addEventListener('click', function () {
                        var orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                        orderModal.show();
                    });

                    // Submit order via fetch to /commission
                    const submitBtn = document.getElementById('orderSubmit');
                    submitBtn?.addEventListener('click', async function () {
                        const form = document.getElementById('orderForm');
                        const formData = new FormData(form);

                        // convert paid checkbox
                        if (formData.get('paid') === 'on' || formData.get('paid') === '1') {
                            formData.set('paid', 1);
                        }

                        const alertBox = document.getElementById('orderAlert');
                        alertBox.classList.add('d-none');

                        try {
                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            const resp = await fetch('{{ route('commission.store') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            const data = await resp.json();

                            if (!resp.ok) {
                                alertBox.classList.remove('d-none');
                                alertBox.classList.add('alert-danger');
                                alertBox.textContent = data.message || 'Failed to create order';
                                return;
                            }

                            alertBox.classList.remove('d-none');
                            alertBox.classList.remove('alert-danger');
                            alertBox.classList.add('alert-success');
                            alertBox.textContent = data.message || 'Order created';

                            // close modal after short delay and redirect to chat thread (or open it inline)
                            setTimeout(() => {
                                var bsModal = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
                                bsModal.hide();
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else if (data.chat_id) {
                                    window.location.href = '/chat/' + data.chat_id;
                                } else {
                                    window.location.reload();
                                }
                            }, 900);

                        } catch (err) {
                            alertBox.classList.remove('d-none');
                            alertBox.classList.add('alert-danger');
                            alertBox.textContent = err.message || 'Network error';
                        }
                    });
                });
                </script>
                @endsection
@endsection
