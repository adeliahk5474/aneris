

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
    grid-template-columns: repeat(5, 1fr); /* 5 kolom */
    gap: 4px;
    padding: 6px;
}

.explore-item {
    width: 100%;
    aspect-ratio: 1/1; /* persegi */
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
                <a href="<?php echo e($item->preview_url ?? $item->file_url); ?>" target="_blank">
                    <img src="<?php echo e($item->preview_url ?? $item->file_url); ?>" alt="art">
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</div>

<?php echo $__env->make('layouts.botnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/page/explore.blade.php ENDPATH**/ ?>