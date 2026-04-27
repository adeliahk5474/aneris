<?php $__env->startSection('content'); ?>

<style>
    /* Grid 2 kolom */
    .grid-feed {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding-bottom: 80px;
    }

    .post {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
    }

    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
    }

    .post-header .user {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13px;
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        background: #ccc;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* FIXED 4:5 */
    .post-image {
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #000;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .post-image img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* utuh tanpa crop */
        object-position: center;
    }

    .post-footer {
        padding: 10px;
    }

    .buttons {
        display: flex;
        gap: 14px;
        margin-bottom: 6px;
        align-items: center;
    }

    .buttons svg {
        width: 22px;
        height: 22px;
        stroke-width: 1.6;
        cursor: pointer;
    }

    .icon-group {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .counter {
        font-size: 12px;
        color: #555;
    }

    .likes {
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .caption {
        font-size: 13px;
        color: #333;
    }
</style>

<div class="container">
    <div class="grid-feed">

<?php $__currentLoopData = $feed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="post">

    
    <div class="post-header">
        <div class="user">
            <div class="avatar">
                <?php if($item->type === 'artwork'): ?>
                    <img src="<?php echo e($item->user->avatar ?? '/default-avatar.png'); ?>">
                <?php else: ?>
                    <img src="<?php echo e($item->artist->avatar ?? '/default-avatar.png'); ?>">
                <?php endif; ?>
            </div>

            <?php if($item->type === 'artwork'): ?>
                <?php echo e($item->user->name ?? 'Unknown Artist'); ?>

            <?php else: ?>
                <?php echo e($item->artist->name ?? 'Unknown Artist'); ?>

            <?php endif; ?>
        </div>
        <div>⋯</div>
    </div>

    
    <div class="post-image">
        <?php if($item->type === 'artwork'): ?>
            <img src="<?php echo e($item->preview_url ?? $item->file_url); ?>">
        <?php else: ?>
            <img src="<?php echo e($item->image_url); ?>">
        <?php endif; ?>
    </div>

    
    <div class="post-footer">

        <div class="buttons">

            <div class="icon-group">
                <?php echo e(svg('heroicon-o-heart')); ?>
                <span class="counter">
                    <?php echo e($item->likes ?? 0); ?>

                </span>
            </div>

            <div class="icon-group">
                <?php echo e(svg('heroicon-o-chat-bubble-left')); ?>
                <span class="counter">
                    <?php echo e($item->comments ?? 0); ?>

                </span>
            </div>

            <div class="icon-group">
                <?php echo e(svg('heroicon-o-paper-airplane')); ?>
                <span class="counter">
                    <?php echo e($item->shares ?? 0); ?>

                </span>
            </div>

        </div>

        
        <div class="caption">
            <?php if($item->type === 'artwork'): ?>
                <?php echo e($item->caption ?? ''); ?>

            <?php else: ?>
                <b><?php echo e($item->title); ?></b><br>
                Rp <?php echo e(number_format($item->price, 0, ',', '.')); ?>

            <?php endif; ?>
        </div>

    </div>

</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</div>

<?php echo $__env->make('layouts.botnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/homepage/home.blade.php ENDPATH**/ ?>