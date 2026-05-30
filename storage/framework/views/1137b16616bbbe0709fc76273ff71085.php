<?php $__env->startSection('title', 'Artists'); ?>

<?php $__env->startSection('content'); ?>

<?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/users.css'); ?>

<div class="page-header">
    <div>
        <div class="page-title">Artists</div>
        <div class="page-sub"><?php echo e($artists->total()); ?> artist terdaftar</div>
    </div>
</div>

<div class="table-card">
    <div class="table-head">
        <div class="table-title">Semua Artist</div>
        <form method="GET" action="<?php echo e(route('admin.users.artists')); ?>" class="search-form">
            <input type="text" name="search" value="<?php echo e($search); ?>"
                placeholder="Cari nama atau email..."
                class="search-input">
            <button type="submit" class="btn-secondary">
                <i class="bi bi-search"></i>
            </button>
            <?php if($search): ?>
            <a href="<?php echo e(route('admin.users.artists')); ?>" class="btn-secondary">
                <i class="bi bi-x"></i>
            </a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Artist</th>
                <th>Status</th>
                <th>Artwork</th>
                <th>Commission</th>
                <th>Bergabung</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <div class="user-cell">
                        <img src="<?php echo e($user->avatar ?? asset('images/default-avatar.png')); ?>"
                            class="user-avatar" alt="<?php echo e($user->name); ?>">
                        <div>
                            <div class="user-name"><?php echo e($user->name); ?></div>
                            <div class="user-email"><?php echo e($user->email); ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <?php if($user->is_verified): ?>
                    <span class="badge badge-approved">
                        <i class="bi bi-patch-check-fill"></i> Verified
                    </span>
                    <?php else: ?>
                    <span class="badge badge-pending">Unverified</span>
                    <?php endif; ?>
                </td>
                <td style="color:var(--text); font-size:13px;">
                    <?php echo e($user->artworks_count); ?>

                </td>
                <td style="color:var(--text); font-size:13px;">
                    <?php echo e($user->commission_services_count); ?>

                </td>
                <td style="color:var(--muted); font-size:12px; white-space:nowrap;">
                    <?php echo e($user->created_at->format('d M Y')); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:48px; color:var(--muted);">
                    <i class="bi bi-people" style="font-size:32px; opacity:.2; display:block; margin-bottom:10px;"></i>
                    <?php echo e($search ? 'Tidak ada artist dengan pencarian "' . $search . '".' : 'Belum ada artist terdaftar.'); ?>

                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($artists->hasPages()): ?>
    <div class="pagination-wrap">
        <?php echo e($artists->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/admin/users/artists.blade.php ENDPATH**/ ?>