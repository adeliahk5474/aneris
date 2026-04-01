
<?php $__env->startSection('title', 'Chats'); ?>

<?php $__env->startSection('content'); ?>
<div class="container" style="padding-top:12px; padding-bottom:80px;">
    <div class="d-flex align-items-center mb-3">
        <h5 class="mb-0">Messages</h5>
    </div>

    <div class="list-group">
        
        <a href="<?php echo e(route('chat.thread', 1)); ?>" class="list-group-item list-group-item-action d-flex align-items-center">
            <img src="https://via.placeholder.com/48" class="rounded-circle me-3" alt="avatar">
            <div class="flex-fill">
                <div class="d-flex justify-content-between">
                    <strong>UserArtist</strong>
                    <small class="text-muted">2h</small>
                </div>
                <div class="text-muted small">Hey, I finished the sketch — want to see?</div>
            </div>
        </a>

        <a href="<?php echo e(route('chat.thread', 2)); ?>" class="list-group-item list-group-item-action d-flex align-items-center">
            <img src="https://via.placeholder.com/48" class="rounded-circle me-3" alt="avatar">
            <div class="flex-fill">
                <div class="d-flex justify-content-between">
                    <strong>Client123</strong>
                    <small class="text-muted">1d</small>
                </div>
                <div class="text-muted small">Is the final file ready?</div>
            </div>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/pages/chat_index.blade.php ENDPATH**/ ?>