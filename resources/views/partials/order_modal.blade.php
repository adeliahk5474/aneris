<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderModalLabel">Place Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="orderForm">
          <input type="hidden" name="artist_id" id="order_artist_id" value="">
          <div class="mb-3">
            <label class="form-label">Notes for artist (optional)</label>
            <textarea class="form-control" name="notes" id="order_notes" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Total Price</label>
            <input type="number" class="form-control" name="total_price" id="order_price" value="0" min="0" step="0.01" required>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" value="1" id="order_paid" name="paid">
            <label class="form-check-label" for="order_paid">I will pay now (mock)</label>
          </div>
          <div id="orderAlert" class="alert d-none" role="alert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="orderSubmit" class="btn btn-primary">Confirm Order</button>
      </div>
    </div>
  </div>
</div>
