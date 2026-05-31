<?php $__env->startSection('title', 'Explore — Aneris'); ?>

<?php $__env->startSection('content'); ?>

<?php echo app('Illuminate\Foundation\Vite')('resources/css/page/explore.css'); ?>


<div class="filter-bar">

    
    <div class="filter-categories">
        <div class="filter-pills">
            <a href="<?php echo e(route('explore', array_merge(request()->except('category'), ['sort' => $sort]))); ?>"
                class="pill <?php echo e(!$category ? 'active' : ''); ?>">
                All
            </a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('explore', array_merge(request()->except('category'), ['category' => $cat->name, 'sort' => $sort]))); ?>"
                class="pill <?php echo e($category === $cat->name ? 'active' : ''); ?>">
                <?php echo e($cat->name); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="filter-sort-row">
        <span style="font-size:11px;color:var(--muted);white-space:nowrap;flex-shrink:0;">Urutkan:</span>

        <?php $__currentLoopData = [
            'popular'   => ['🔥', 'Populer'],
            'newest'    => ['✨', 'Terbaru'],
            'top_rated' => ['⭐', 'Top Rating'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('explore', array_merge(request()->except('sort'), ['sort' => $key]))); ?>"
            class="pill sort-pill <?php echo e($sort === $key ? 'active' : ''); ?>">
            <?php echo e($icon); ?> <?php echo e($label); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="filter-divider"></div>

        
        <form action="<?php echo e(route('explore')); ?>" method="GET" class="explore-search-form">
            <?php if($category): ?>
            <input type="hidden" name="category" value="<?php echo e($category); ?>">
            <?php endif; ?>
            <input type="hidden" name="sort" value="<?php echo e($sort); ?>">
            <i class="bi bi-search"></i>
            <input type="text" name="search" placeholder="Search..." value="<?php echo e($search); ?>" autocomplete="off">
        </form>
    </div>

</div>

<main class="explore-main">

    
    <?php if($users->count()): ?>
    <div class="user-section">
        <div class="section-label">Users</div>
        <div class="user-scroll">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('profile.show', $user->user_id)); ?>" class="user-card">
                <img src="<?php echo e($user->avatar ?? asset('images/default-avatar.png')); ?>"
                    alt="<?php echo e($user->name); ?>" class="user-avatar" loading="lazy">
                <span class="user-name"><?php echo e($user->name); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($explore->count()): ?>
    <div class="masonry-grid">
        <?php $__currentLoopData = $explore; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if($item->type === 'artwork'): ?>
        
        <div class="masonry-item">
            <a href="<?php echo e($item->image_url); ?>" target="_blank">
                <img src="<?php echo e($item->image_url); ?>"
                    alt="<?php echo e($item->caption ?? 'artwork'); ?>" loading="lazy">
            </a>

            <span class="item-badge badge-artwork">
                <?php echo e($item->category->name ?? 'Artwork'); ?>

            </span>

            <div class="masonry-overlay">
                <div class="overlay-row">
                    <a href="<?php echo e(route('profile.show', $item->user->user_id ?? '#')); ?>" class="overlay-artist">
                        <img src="<?php echo e($item->user->avatar ?? asset('images/default-avatar.png')); ?>"
                            class="overlay-avatar" alt="">
                        <span class="overlay-name"><?php echo e($item->user->name ?? ''); ?></span>
                    </a>
                    
                    <?php if(auth()->guard()->check()): ?>
                    <button class="overlay-like"
                        onclick="event.preventDefault();event.stopPropagation();toggleLike(this,'<?php echo e($item->artwork_id ?? ''); ?>','artwork')"
                        aria-label="Like artwork">
                        <i class="bi bi-heart"></i>
                    </button>
                    <?php else: ?>
                    <a href="<?php echo e(route('auth.form')); ?>" class="overlay-like" aria-label="Like">
                        <i class="bi bi-heart"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        
        <div class="masonry-item"
            data-service-id="<?php echo e($item->service_id); ?>"
            data-liked="<?php echo e($item->is_liked ? 'true' : 'false'); ?>"
            data-like-count="<?php echo e($item->lc); ?>">

            <a href="<?php echo e(route('commission.show', $item->service_id)); ?>">
                <img src="<?php echo e($item->image_url ?? asset('images/default-thumb.png')); ?>"
                    alt="<?php echo e($item->title); ?>" loading="lazy">
            </a>

            <span class="item-badge <?php echo e($item->isLive2D ? 'badge-live2d' : 'badge-commission'); ?>">
                <?php echo e($item->catName); ?>

            </span>

            
            <div class="price-badge">
                Rp <?php echo e(number_format($item->base_price ?? 0, 0, ',', '.')); ?>

            </div>

            
            <div class="like-count-badge <?php echo e($item->lc === 0 ? 'hidden' : ''); ?>"
                id="lc-<?php echo e($item->service_id); ?>">
                <i class="bi bi-heart-fill" style="font-size:8px;"></i>
                <span><?php echo e($item->lc); ?></span>
            </div>

            
            <?php if($item->avgR > 0): ?>
            <div class="rating-badge">
                <i class="bi bi-star-fill" style="font-size:9px;"></i>
                <?php echo e(number_format($item->avgR, 1)); ?>

            </div>
            <?php endif; ?>

            <div class="masonry-overlay">
                <div class="overlay-row">
                    <a href="<?php echo e(route('profile.show', $item->artist->user_id ?? '#')); ?>" class="overlay-artist">
                        <img src="<?php echo e($item->artist->avatar ?? asset('images/default-avatar.png')); ?>"
                            class="overlay-avatar" alt="">
                        <span class="overlay-name"><?php echo e($item->artist->name ?? ''); ?></span>
                    </a>

                    <?php if(auth()->guard()->check()): ?>
                    <button class="overlay-like <?php echo e($item->is_liked ? 'liked' : ''); ?>"
                        onclick="event.preventDefault();event.stopPropagation();toggleLike(this,'<?php echo e($item->service_id); ?>','commission_service')"
                        aria-label="Like">
                        <i class="bi bi-heart<?php echo e($item->is_liked ? '-fill' : ''); ?>"></i>
                    </button>
                    <?php else: ?>
                    <a href="<?php echo e(route('auth.form')); ?>" class="overlay-like"
                        onclick="event.stopPropagation()"
                        aria-label="Like">
                        <i class="bi bi-heart"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-search empty-icon"></i>
        <span class="empty-title">
            <?php if($search): ?> No results for "<?php echo e($search); ?>"
            <?php elseif($category): ?> No content in "<?php echo e($category); ?>"
            <?php else: ?> Nothing here yet
            <?php endif; ?>
        </span>
        <span class="empty-sub">Try a different keyword or category</span>
    </div>
    <?php endif; ?>

</main>

<?php if(auth()->guard()->check()): ?>
<a href="<?php echo e(route('upload.popup')); ?>" class="fab" aria-label="Upload">
    <i class="bi bi-plus-lg"></i>
</a>
<?php endif; ?>

<?php
    $explorePageConfig = [
        'isAuth'        => auth()->check(),
        'likeToggleUrl' => route('like.toggle'),
        'authFormUrl'   => route('auth.form'),
    ];
?>

<script>
window.explorePage = <?php echo json_encode($explorePageConfig, 15, 512) ?>;
</script>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/page/explore.js'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/page/explore.blade.php ENDPATH**/ ?>