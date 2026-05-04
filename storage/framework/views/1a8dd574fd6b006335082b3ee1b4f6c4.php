<?php $__env->startSection('content'); ?>

<style>
    /* ===============================
   SEARCH BAR
=============================== */
    .explore-search {
        padding: 10px;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: #f1f1f1;
        border-radius: 20px;
        padding: 8px 12px;
    }

    .search-box i {
        margin-right: 8px;
        color: #777;
    }

    .search-box input {
        border: none;
        background: transparent;
        width: 100%;
        outline: none;
    }

    /* ===============================
   GRID CONTENT
=============================== */
    .explore-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        /* 5 kolom */
        gap: 4px;
        padding: 6px;
    }

    .explore-item {
        position: relative;
        /* penting buat label */
        width: 100%;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #eee;
        border-radius: 6px;
        border: 1px solid #000;
    }

    .explore-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* RESPONSIVE GRID */
    @media (max-width: 1200px) {
        .explore-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .explore-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 576px) {
        .explore-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* FORCE POPUP HIDDEN */
    #servicePopup {
        display: none;
    }

    /* ================= POPUP FIX ================= */
    .artwork-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        background: rgba(0, 0, 0, 0.8);

        justify-content: center;
        align-items: center;

        z-index: 9999;
    }

    .artwork-popup-content {
        background: #fff;
        width: 90%;
        max-width: 400px;
        border-radius: 10px;

        position: relative;

        max-height: 90vh;
        overflow-y: auto;
    }
</style>

<div class="container-fluid" style="padding-bottom: 80px;">

    
    <div class="p-3 bg-white sticky-top" style="z-index:1020;">
        <form action="<?php echo e(route('explore')); ?>" method="GET" class="search-box w-100">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search fs-4"></i>
                </span>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>"
                    class="form-control border-start-0"
                    placeholder="Search artworks, artists..."
                    autocomplete="off"
                    style="font-size:18px; padding:12px;">
            </div>
        </form>
    </div>

    
    <div class="explore-grid">
        <?php $__currentLoopData = $explore; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="explore-item">

            <?php if($item->type === 'artwork'): ?>
            <a href="<?php echo e($item->preview_url ?? $item->file_url); ?>" target="_blank">
                <img src="<?php echo e($item->preview_url ?? $item->file_url); ?>" alt="art">
            </a>
            <?php else: ?>
            <a href="javascript:void(0)"
                class="service-trigger"
                data-id="<?php echo e($item->service_id); ?>"
                data-title="<?php echo e($item->title); ?>"
                data-price="<?php echo e($item->price); ?>"
                data-description="<?php echo e($item->description); ?>"
                data-image="<?php echo e($item->image_url); ?>"
                data-user-name="<?php echo e($item->artist->name ?? 'Unknown'); ?>"
                data-user-avatar="<?php echo e($item->artist->avatar ?? '/default-avatar.png'); ?>"

                <img src="<?php echo e($item->image_url); ?>" alt="service">

                
                <div style="
                    position:absolute;
                    background:rgba(0,0,0,0.6);
                    color:#fff;
                    font-size:10px;
                    padding:2px 5px;
                    border-radius:4px;
                    margin:4px;
                ">
                    COMMISSION
                </div>
            </a>
            <?php endif; ?>

        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</div>

<?php echo $__env->make('layouts.botnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('commission.show', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('commission.order', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('commission.payment', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script src="<?php echo e(asset('js/explore-popup.js')); ?>"></script>
<script src="<?php echo e(asset('js/order-popup.js')); ?>"></script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/page/explore.blade.php ENDPATH**/ ?>