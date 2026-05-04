<?php
$hideBottomNavbar = true;
?>



<?php $__env->startSection('content'); ?>

<style>
    .dashboard {
        max-width: 1000px;
        margin: auto;
        padding: 20px;
    }

    /* HEADER */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .dashboard-header img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* CARD */
    .card-box {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 16px;
    }

    /* GRID */
    .grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    @media(max-width:768px) {
        .grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-3 {
            grid-template-columns: 1fr;
        }
    }

    /* ORDER */
    .order-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .order-title {
        font-weight: 600;
    }

    .order-meta {
        font-size: 13px;
        color: #555;
    }

    /* BUTTON */
    .btn-sm {
        padding: 5px 8px;
        font-size: 12px;
        border: none;
        border-radius: 6px;
        color: #fff;
    }

    .btn-green {
        background: #16a34a;
    }

    .btn-red {
        background: #dc2626;
    }

    .btn-blue {
        background: #2563eb;
    }

    /* STATUS */
    .status {
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
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

<div class="dashboard">

    ```
    
    <div class="dashboard-header">
        <a href="<?php echo e(route('profile.show', auth()->id())); ?>">← Back</a>

        <h3>Artist Dashboard</h3>

        <div style="display:flex;align-items:center;gap:10px;">
            <img src="<?php echo e($artist->avatar ?? '/default-avatar.png'); ?>">
            <b><?php echo e($artist->name); ?></b>
        </div>
    </div>

    
    <div class="grid-4 mb-3">

        <div class="card-box text-center">
            <div>Active</div>
            <b><?php echo e($activeCommissions); ?></b>
        </div>

        <div class="card-box text-center">
            <div>Earnings</div>
            <b>Rp <?php echo e(number_format($totalEarnings,0,',','.')); ?></b>
        </div>

        <div class="card-box text-center">
            <div>Rating</div>
            <b><?php echo e($averageRating ? number_format($averageRating,1).'★' : '-'); ?></b>
        </div>

        <div class="card-box text-center">
            <div>Services</div>
            <b><?php echo e($totalServices); ?></b>
        </div>

    </div>

    
    <div class="grid-3 mb-4">

        <div class="card-box text-center">
            <div>Clients</div>
            <b><?php echo e($activeClients); ?></b>
        </div>

        <div class="card-box text-center">
            <div>Pending</div>
            <b><?php echo e($pendingCommissions); ?></b>
        </div>

        <div class="card-box text-center">
            <div>Notif</div>
            <b><?php echo e($recentNotifications); ?></b>
        </div>

    </div>

    
    <div class="card-box mb-4">
        <h5>Monthly Earnings</h5>
        <canvas id="chart"></canvas>
    </div>

    
    <div class="card-box">
        <h5>Incoming Orders</h5>

        <?php $__empty_1 = true; $__currentLoopData = $incomingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

        <div class="order-card">

            <div class="order-title">
                <?php echo e($order->service->title ?? 'Service'); ?>

            </div>

            <div class="order-meta">
                Client: <?php echo e($order->client->name ?? 'Unknown'); ?>

            </div>

            <div class="order-meta">
                Rp <?php echo e(number_format($order->total_price,0,',','.')); ?>

            </div>

            <div class="status
                <?php if($order->status=='pending'): ?> status-pending
                <?php elseif($order->status=='in_progress'): ?> status-progress
                <?php elseif($order->status=='completed'): ?> status-done
                <?php elseif($order->status=='canceled'): ?> status-cancel
                <?php endif; ?>
            ">
                <?php echo e(strtoupper($order->status)); ?>

            </div>

            
            <div style="display:flex;gap:6px;margin-top:6px;">

                <?php if($order->status=='pending'): ?>
                <form method="POST" action="<?php echo e(route('order.accept')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                    <button class="btn-sm btn-green">Accept</button>
                </form>

                <form method="POST" action="<?php echo e(route('order.reject')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                    <button class="btn-sm btn-red">Reject</button>
                </form>
                <?php endif; ?>

                <?php if($order->status=='in_progress'): ?>

                <form method="POST" action="<?php echo e(route('order.complete')); ?>" enctype="multipart/form-data" style="display:flex;gap:6px;align-items:center;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">

                    
                    <input type="file" name="result_file" required style="font-size:12px;">

                    
                    <button class="btn-sm btn-blue">Done</button>
                </form>

                <?php endif; ?>

            </div>

        </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>No orders</p>
        <?php endif; ?>

    </div>
    ```

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('chart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($monthlyLabels, 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($monthlyEarnings, 15, 512) ?>,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/dashboards/artist.blade.php ENDPATH**/ ?>