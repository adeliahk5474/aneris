@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-dark text-light py-5">
    <div class="container py-4">

        {{-- Card utama --}}
        <div class="card bg-secondary text-light border-0 shadow-lg p-4 mx-auto" style="max-width: 900px; border-radius: 20px;">
            <div class="row g-4 align-items-center">
                <div class="col-md-5">
                    <img src="{{ $service->preview_url ?? '/images/default-art.jpg' }}"
                         class="img-fluid rounded-4 shadow-sm w-100" alt="{{ $service->title }}">
                </div>
                <div class="col-md-7">
                    <h2 class="fw-bold mb-2 text-warning">{{ $service->title }}</h2>
                    <p class="mb-1"><strong>Artist:</strong> {{ $service->artist->name }}</p>
                    <p class="mb-3"><strong>Category:</strong> {{ $service->category->name ?? 'Uncategorized' }}</p>

                    <p class="text-light-50">{{ $service->description }}</p>

                    <h4 class="mt-3 mb-4 text-warning">Rp {{ number_format($service->price, 0, ',', '.') }}</h4>

                    <button class="btn btn-warning btn-lg rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#orderModal">
                        <i class="bi bi-cart-plus me-2"></i> Order Now
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================== --}}
{{-- Modal Order              --}}
{{-- ======================== --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light border-0 rounded-4 shadow-lg">
      <div class="modal-header border-0">
        <h5 class="modal-title text-warning">Commission Request</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="orderStage">
          {{-- Stage 1: Form Request --}}
          <form id="orderForm">
            @csrf
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" id="description" class="form-control bg-secondary text-light border-0 rounded-3" rows="4" required></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Proposed Price (Rp)</label>
              <input type="number" name="proposed_price" id="proposed_price" class="form-control bg-secondary text-light border-0 rounded-3" value="{{ $service->price }}">
            </div>

            <div class="text-end mt-4">
              <button type="button" class="btn btn-outline-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-warning rounded-pill px-4" id="submitOrderBtn">Proceed</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ======================== --}}
{{-- JS Script AJAX Modal Flow --}}
{{-- ======================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const orderForm = document.getElementById('orderForm');
  const orderStage = document.getElementById('orderStage');
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const serviceId = "{{ $service->service_id }}";

  orderForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const fd = new FormData(orderForm);

    // Kirim request ke backend
    const res = await fetch("{{ route('commission.request', $service->service_id) }}", {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken },
      body: fd
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      if (data.needLogin) {
        window.location.href = "{{ route('auth.form') }}";
        return;
      }
      alert(data.message || 'Failed to create request');
      return;
    }

    const commissionId = data.commission_request_id;
    const amount = data.amount;

    // Ubah isi modal ke tahap pembayaran
    orderStage.innerHTML = `
      <div class="text-center py-3">
        <h5 class="text-warning mb-3">Payment</h5>
        <p>Commission created successfully.</p>
        <p class="text-light">Total: <strong>Rp ${Number(amount).toLocaleString()}</strong></p>
        <div class="d-grid gap-2 mt-4">
          <button id="payNowBtn" class="btn btn-success rounded-pill py-2">Pay Now</button>
          <button id="saveToCartBtn" class="btn btn-outline-light rounded-pill py-2">Save to Cart</button>
        </div>
      </div>
    `;

    // Handler "Save to Cart"
    document.getElementById('saveToCartBtn').addEventListener('click', async () => {
      const fd2 = new FormData();
      fd2.append('_token', csrfToken);
      fd2.append('commission_request_id', commissionId);

      const res2 = await fetch("{{ route('cart.add') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: fd2
      });

      const j2 = await res2.json();
      if (res2.ok && j2.success) {
        orderStage.innerHTML = `
          <div class="text-center text-success py-3">
            <i class="bi bi-check-circle display-4"></i>
            <p class="mt-2">Added to cart successfully.</p>
            <a href="/cart" class="btn btn-warning rounded-pill px-4 mt-2">Go to Cart</a>
          </div>
        `;
        setTimeout(() => {
          var modalEl = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
          modalEl.hide();
        }, 1200);
      } else {
        alert(j2.message || 'Failed to add to cart');
      }
    });

    // Handler "Pay Now"
    document.getElementById('payNowBtn').addEventListener('click', async () => {
      orderStage.innerHTML = `
        <div class="text-center py-4 text-success">
          <i class="bi bi-cash-stack display-5"></i>
          <p class="mt-3">Payment successful! Adding to cart...</p>
        </div>
      `;

      const fd3 = new FormData();
      fd3.append('_token', csrfToken);
      fd3.append('commission_request_id', commissionId);

      const res3 = await fetch("{{ route('cart.add') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: fd3
      });

      const j3 = await res3.json();
      if (res3.ok && j3.success) {
        orderStage.innerHTML = `
          <div class="text-center text-success py-3">
            <i class="bi bi-check2-circle display-4"></i>
            <p class="mt-2">Payment complete. Added to cart.</p>
            <a href="/cart" class="btn btn-warning rounded-pill px-4 mt-3">Go to Cart</a>
          </div>
        `;
        setTimeout(() => {
          var modalEl = bootstrap.Modal.getInstance(document.getElementById('orderModal'));
          modalEl.hide();
        }, 1500);
      } else {
        alert('Payment saved but failed to add to cart');
      }
    });
  });
});
</script>
@endsection
