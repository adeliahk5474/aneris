

<div class="artwork-popup" id="orderPopup">
    <div class="artwork-popup-content" style="max-width:400px;">

        <span class="close-popup">&times;</span>

        <h5 style="margin-bottom:10px;">Order Commission</h5>

        <form method="POST" action="<?php echo e(route('order.store')); ?>">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="service_id" id="orderServiceId">

            
            <div style="margin-bottom:10px;">
                <b id="orderTitle"></b><br>
                <span id="orderPrice" style="color:#555;"></span>
            </div>

            
            <label>Request / Note</label>
            <textarea name="note" class="form-control mb-2" rows="3"
                placeholder="Describe what you want..."></textarea>

            
            <label>Payment Method</label>
            <select name="payment_method" class="form-control mb-3" required>
                <option value="">-- Choose Payment --</option>
                <option value="bank">Bank Transfer</option>
                <option value="ewallet">E-Wallet</option>
            </select>

            <button type="button" id="continueToPayment" class="btn btn-dark w-100">
                Continue
            </button>
        </form>

    </div>
</div>
<?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/commission/order.blade.php ENDPATH**/ ?>