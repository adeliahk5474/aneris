
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> — Aneris</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/admin.css'); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

    <div class="admin-wrap">

        
        <header class="admin-topbar">
            <div class="topbar-brand">
                <div class="topbar-brand-icon">A</div>
                <span class="topbar-brand-name">Aneris</span>
                <span class="topbar-brand-badge">ADMIN</span>
            </div>

            <div class="topbar-right">
                <span class="topbar-admin-name">
                    <i class="bi bi-person-circle"></i>
                    <?php echo e(Auth::guard('admin')->user()->name ?? 'Admin'); ?>

                </span>
                <form action="<?php echo e(route('admin.logout')); ?>" method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="topbar-logout">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        
        <aside class="admin-sidebar">
            <div class="sidebar-section-label">Overview</div>
            <a href="<?php echo e(route('admin.dashboard')); ?>"
                class="sidebar-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            <div class="sidebar-section-label">Verifikasi</div>
            <a href="<?php echo e(route('admin.verification.index')); ?>"
                class="sidebar-item <?php echo e(request()->routeIs('admin.verification.*') ? 'active' : ''); ?>">
                <i class="bi bi-patch-check"></i> Antrean Verifikasi
                <?php
                $pendingCount = \App\Models\PortfolioVerification::whereIn('status', ['pending','in_review'])->count();
                ?>
                <?php if($pendingCount > 0): ?>
                <span class="sidebar-badge yellow"><?php echo e($pendingCount); ?></span>
                <?php endif; ?>
            </a>

            <div class="sidebar-section-label">Users</div>
            <a href="<?php echo e(route('admin.users.artists')); ?>"
                class="sidebar-item <?php echo e(request()->routeIs('admin.users.artists') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i> Artists
            </a>
            <a href="<?php echo e(route('admin.users.clients')); ?>"
                class="sidebar-item <?php echo e(request()->routeIs('admin.users.clients') ? 'active' : ''); ?>">
                <i class="bi bi-person"></i> Clients
            </a>

            <div class="sidebar-section-label">Konten</div>
            <a href="<?php echo e(route('admin.home-setting.edit')); ?>"
                class="sidebar-item <?php echo e(request()->routeIs('admin.home-setting.*') ? 'active' : ''); ?>">
                <i class="bi bi-house-gear"></i> Tampilan Home
            </a>
        </aside>

        
        <main class="admin-main">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

    </div>

    
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <span class="lightbox-close" onclick="closeLightbox()">×</span>
        <img id="lightboxImg" src="" alt="">
    </div>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/admin/admin-layout.js'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/layouts/admin.blade.php ENDPATH**/ ?>