
<div class="fixed-bottom bg-white border-top shadow-sm d-flex justify-content-around py-2"
     style="z-index: 1030;">

    
    <a href="<?php echo e(route('home')); ?>" class="text-dark text-center">
        <i class="bi bi-house-door fs-3"></i>
    </a>

    
    <a href="<?php echo e(route('explore')); ?>" class="text-dark text-center">
        <i class="bi bi-search fs-3"></i>
    </a>

    
    <?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('upload.popup')); ?>" class="text-dark text-center">
                <i class="bi bi-plus-square fs-3"></i>
            </a>
    <?php else: ?>
        <a href="<?php echo e(route('auth.form')); ?>" class="text-dark text-center">
            <i class="bi bi-plus-square fs-3"></i>
        </a>
    <?php endif; ?>

    
    <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('chat.index')); ?>" class="text-dark text-center">
            <i class="bi bi-chat-dots fs-3"></i>
        </a>
    <?php else: ?>
        <a href="<?php echo e(route('auth.form')); ?>" class="text-dark text-center">
            <i class="bi bi-chat-dots fs-3"></i>
        </a>
    <?php endif; ?>

    
    <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('profile.show', Auth::id())); ?>" class="text-dark text-center">
            <i class="bi bi-person-circle fs-3"></i>
        </a>
    <?php else: ?>
        <a href="<?php echo e(route('auth.form')); ?>" class="text-dark text-center">
            <i class="bi bi-person-circle fs-3"></i>
        </a>
    <?php endif; ?>

</div>
<?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/layouts/botnav.blade.php ENDPATH**/ ?>