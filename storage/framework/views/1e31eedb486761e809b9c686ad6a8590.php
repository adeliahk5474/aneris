<?php $__env->startSection('content'); ?>

<style>
    .cart-container {
        max-width: 700px;
        margin: auto;
        padding: 10px;
    }

    .cart-item {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .cart-title {
        font-weight: 600;
        font-size: 15px;
    }

    .cart-meta {
        font-size: 13px;
        color: #555;
    }

    .cart-status {
        font-size: 12px;
        margin-top: 5px;
        font-weight: 600;
    }

    .status-pending {
        color: orange;
    }

    .status-progress {
        color: blue;
    }

    .status-done {
        color: green;
    }

    .status-cancel {
        color: red;
    }
</style>

<div class="cart-container">

    <h5 style="margin-bottom:10px;">My Cart</h5>

    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

    <div class="cart-item">

        <div class="cart-title">
            <?php echo e($order->service->title ?? 'Service'); ?>

        </div>

        <div class="cart-meta">
            by <?php echo e($order->artist->name ?? 'Unknown Artist'); ?>

        </div>

        <div class="cart-meta">
            Rp <?php echo e(number_format($order->total_price, 0, ',', '.')); ?>

        </div>

        <div class="cart-status
                <?php if($order->status == 'pending'): ?> status-pending
                <?php elseif($order->status == 'in_progress'): ?> status-progress
                <?php elseif($order->status == 'completed'): ?> status-done
                <?php elseif($order->status == 'canceled'): ?> status-cancel
                <?php endif; ?>
            ">
            <?php echo e(strtoupper($order->status)); ?>

        </div>

        <?php if($order->result_file): ?>
        <a href="<?php echo e(asset('storage/'.$order->result_file)); ?>" target="_blank">
            View Result
        </a>
        <?php endif; ?>

    </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

    <p>No orders yet</p>

    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/pages/cart.blade.php ENDPATH**/ ?>