<div class="artwork-popup" id="paymentPopup">
    <div class="artwork-popup-content" style="max-width:400px;">

        <span class="close-popup">&times;</span>

        <h5>Payment</h5>

        <div style="margin-bottom:10px;">
            <b id="payTitle"></b><br>
            <span id="payPrice"></span>
        </div>

        <div style="margin-bottom:10px;">
            <b>Note:</b>
            <p id="payNote"></p>
        </div>

        <div style="margin-bottom:10px;">
            <b>Payment Method:</b>
            <p id="payMethod"></p>
        </div>

        <form method="POST" action="<?php echo e(route('order.pay')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="service_id" id="payServiceId">

            <button type="submit" class="btn btn-dark w-100">
                Bayar Sekarang
            </button>
        </form>

    </div>
</div>
<?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/commission/payment.blade.php ENDPATH**/ ?>