<?php $__env->startSection('title', 'Aneris — For the love of human creativity'); ?>
<?php $__env->startSection('content'); ?>

<?php echo app('Illuminate\Foundation\Vite')('resources/css/homepage/home.css'); ?>

<?php
$hs = \App\Models\HomeSetting::getAllKeyed();
?>


<?php
$heroStyle = !empty($hs['hero_image'])
? 'background-image:url(' . e($hs['hero_image']) . ');background-size:cover;background-position:center;'
: '';
?>
<div class="home-hero" style="<?php echo e($heroStyle); ?>">
    <h1><?php echo e($hs['hero_title'] ?? 'For the love of human creativity'); ?></h1>
    <?php if(!empty($hs['hero_subtitle'])): ?>
    <p class="hero-subtitle"><?php echo e($hs['hero_subtitle']); ?></p>
    <?php endif; ?>
    <form action="<?php echo e(route('explore')); ?>" method="GET">
        <div class="hero-search-wrap">
            <span class="hero-search-icon"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="hero-search-input"
                placeholder="Search commissions, artists..." autocomplete="off">
        </div>
    </form>
</div>


<?php
$b1Style = !empty($hs['banner1_image'])
? 'background-image:url(' . e($hs['banner1_image']) . ');background-size:cover;background-position:center;'
: 'background:' . ($hs['banner1_color'] ?? '#1a1a2e') . ';';

$b2Style = !empty($hs['banner2_image'])
? 'background-image:url(' . e($hs['banner2_image']) . ');background-size:cover;background-position:center;'
: 'background:' . ($hs['banner2_color'] ?? '#0a1a10') . ';';
?>
<div class="banner-section">
    <div class="banner-grid">
        <a href="<?php echo e(route('explore', ['category' => 'Illustrations'])); ?>" class="banner-card"
            style="<?php echo e($b1Style); ?>">
            <div class="banner-text">
                <div class="banner-title"><?php echo e($hs['banner1_title'] ?? 'Made for creators'); ?></div>
                <div class="banner-sub"><?php echo e($hs['banner1_subtitle'] ?? 'Illustrations, avatars, emotes, live2d — made by humans who love what they do.'); ?></div>
            </div>
        </a>
        <a href="<?php echo e(route('explore')); ?>" class="banner-card"
            style="<?php echo e($b2Style); ?>">
            <div class="banner-text">
                <div class="banner-title"><?php echo e($hs['banner2_title'] ?? 'No Generative AI'); ?></div>
                <div class="banner-sub"><?php echo e($hs['banner2_subtitle'] ?? 'Until generative AI is made with Consent, Credit, and Compensation, it is not welcome here.'); ?></div>
            </div>
        </a>
    </div>
</div>


<div class="home-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-star-fill" style="color:var(--yellow);font-size:16px;"></i>
            Top Rated
            <span class="section-badge"><?php echo e(number_format($totalServices)); ?>+ services</span>
        </div>
        <a href="<?php echo e(route('explore', ['sort' => 'top_rated'])); ?>" class="section-link">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="category-pills">
        <a href="<?php echo e(route('explore')); ?>" class="cat-pill"><span>🔥</span> All</a>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('explore', ['category' => $cat->name])); ?>" class="cat-pill">
            <span>
                <?php switch(strtolower($cat->name)):
                case ('illustrations'): ?> 🎨 <?php break; ?>
                <?php case ('2d avatars'): ?> 🧑 <?php break; ?>
                <?php case ('3d models'): ?> 🗿 <?php break; ?>
                <?php case ('emotes'): ?> 😀 <?php break; ?>
                <?php case ('live2d'): ?> 🎭 <?php break; ?>
                <?php default: ?> 🖼️
                <?php endswitch; ?>
            </span>
            <?php echo e($cat->name); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($services->count()): ?>
    <div class="service-grid">
        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('commission.show', $service->service_id)); ?>" class="service-card">
            <div class="service-thumb">
                <img src="<?php echo e($service->image_url ?? asset('images/default-thumb.png')); ?>"
                    alt="<?php echo e($service->title); ?>" loading="lazy">

                <?php if($i < 3 && $service->review_count > 0): ?>
                    <div class="service-rank">
                        <?php echo e($i === 0 ? '🥇' : ($i === 1 ? '🥈' : '🥉')); ?>

                    </div>
                    <?php else: ?>
                    <?php if($service->status === 'active'): ?>
                    <span class="service-badge">OPEN</span>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if($service->like_count > 0): ?>
                    <div class="service-like-badge">
                        <i class="bi bi-heart-fill" style="font-size:9px;"></i>
                        <?php echo e($service->like_count); ?>

                    </div>
                    <?php endif; ?>
            </div>
            <div class="service-info">
                <div class="service-name"><?php echo e($service->title); ?></div>
                <div class="service-artist">
                    <div class="artist-dot">
                        <img src="<?php echo e($service->artist->avatar ?? asset('images/default-avatar.png')); ?>"
                            alt="<?php echo e($service->artist->name ?? ''); ?>">
                    </div>
                    <span class="artist-name-sm"><?php echo e($service->artist->name ?? 'Unknown'); ?></span>
                </div>
                <div class="service-meta">
                    <div class="service-rating">
                        <?php if($service->review_count > 0): ?>
                        <i class="bi bi-star-fill"></i>
                        <span><?php echo e(number_format($service->avg_rating, 1)); ?></span>
                        <span style="opacity:.6;">(<?php echo e($service->review_count); ?>)</span>
                        <?php else: ?>
                        <span class="no-review"><i class="bi bi-star" style="font-size:11px;"></i> Baru</span>
                        <?php endif; ?>
                    </div>
                    <div class="service-price">Rp <?php echo e(number_format($service->base_price ?? 0, 0, ',', '.')); ?></div>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="home-empty">
        <i class="bi bi-grid-3x3-gap" style="font-size:36px;opacity:.2;display:block;margin-bottom:12px;"></i>
        Belum ada commission service.
    </div>
    <?php endif; ?>
</div>


<?php if(isset($mostLiked) && $mostLiked->count()): ?>
<div class="home-divider"></div>
<div class="home-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-heart-fill" style="color:var(--pink);font-size:15px;"></i>
            Most Liked
            <span class="section-badge" style="background:rgba(244,114,182,.1);color:var(--pink);border-color:rgba(244,114,182,.25);">
                Community Favorites
            </span>
        </div>
        <a href="<?php echo e(route('explore', ['sort' => 'popular'])); ?>" class="section-link">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="service-grid">
        <?php $__currentLoopData = $mostLiked; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('commission.show', $service->service_id)); ?>" class="service-card">
            <div class="service-thumb">
                <img src="<?php echo e($service->image_url ?? asset('images/default-thumb.png')); ?>"
                    alt="<?php echo e($service->title); ?>" loading="lazy">
                <div class="service-like-badge">
                    <i class="bi bi-heart-fill" style="font-size:9px;"></i>
                    <?php echo e($service->like_count); ?>

                </div>
                <?php if($i === 0): ?>
                <div class="service-rank" style="color:var(--pink);">❤️</div>
                <?php endif; ?>
            </div>
            <div class="service-info">
                <div class="service-name"><?php echo e($service->title); ?></div>
                <div class="service-artist">
                    <div class="artist-dot">
                        <img src="<?php echo e($service->artist->avatar ?? asset('images/default-avatar.png')); ?>"
                            alt="<?php echo e($service->artist->name ?? ''); ?>">
                    </div>
                    <span class="artist-name-sm"><?php echo e($service->artist->name ?? 'Unknown'); ?></span>
                </div>
                <div class="service-meta">
                    <div class="service-rating">
                        <?php if($service->review_count > 0): ?>
                        <i class="bi bi-star-fill"></i>
                        <span><?php echo e(number_format($service->avg_rating, 1)); ?></span>
                        <span style="opacity:.6;">(<?php echo e($service->review_count); ?>)</span>
                        <?php else: ?>
                        <span class="no-review"><i class="bi bi-heart" style="font-size:11px;"></i> Disukai</span>
                        <?php endif; ?>
                    </div>
                    <div class="service-price">Rp <?php echo e(number_format($service->base_price ?? 0, 0, ',', '.')); ?></div>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<?php if(isset($newServices) && $newServices->count()): ?>
<div class="home-divider"></div>
<div class="home-section">
    <div class="section-header">
        <div class="section-title">
            <i class="bi bi-lightning-fill" style="color:var(--accent);font-size:15px;"></i>
            Rising Artists
            <span class="section-badge" style="background:rgba(34,197,94,.12);color:var(--green);border-color:rgba(34,197,94,.25);">New</span>
        </div>
        <a href="<?php echo e(route('explore', ['sort' => 'newest'])); ?>" class="section-link">
            See all <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="service-grid">
        <?php $__currentLoopData = $newServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('commission.show', $service->service_id)); ?>" class="service-card">
            <div class="service-thumb">
                <img src="<?php echo e($service->image_url ?? asset('images/default-thumb.png')); ?>"
                    alt="<?php echo e($service->title); ?>" loading="lazy">
                <span class="service-badge new">NEW</span>
                <?php if($service->like_count > 0): ?>
                <div class="service-like-badge">
                    <i class="bi bi-heart-fill" style="font-size:9px;"></i>
                    <?php echo e($service->like_count); ?>

                </div>
                <?php endif; ?>
            </div>
            <div class="service-info">
                <div class="service-name"><?php echo e($service->title); ?></div>
                <div class="service-artist">
                    <div class="artist-dot">
                        <img src="<?php echo e($service->artist->avatar ?? asset('images/default-avatar.png')); ?>"
                            alt="<?php echo e($service->artist->name ?? ''); ?>">
                    </div>
                    <span class="artist-name-sm"><?php echo e($service->artist->name ?? 'Unknown'); ?></span>
                </div>
                <div class="service-meta">
                    <div class="service-rating">
                        <span class="no-review"><i class="bi bi-star" style="font-size:11px;"></i> Belum ada review</span>
                    </div>
                    <div class="service-price">Rp <?php echo e(number_format($service->base_price ?? 0, 0, ',', '.')); ?></div>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/homepage/home.blade.php ENDPATH**/ ?>