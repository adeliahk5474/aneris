<?php $__env->startSection('title', 'Clients'); ?>

<?php $__env->startSection('content'); ?>

<?php echo app('Illuminate\Foundation\Vite')('resources/css/admin/users.css'); ?>

<div class="page-header">
    <div>
        <div class="page-title">Clients</div>
        <div class="page-sub"><?php echo e($clients->total()); ?> client terdaftar</div>
    </div>
</div>

<div class="table-card">
    <div class="table-head">
        <div class="table-title">Semua Client</div>
        <form method="GET" action="<?php echo e(route('admin.users.clients')); ?>" class="search-form">
            <input type="text" name="search" value="<?php echo e($search); ?>"
                placeholder="Cari nama atau email..."
                class="search-input">
            <button type="submit" class="btn-secondary">
                <i class="bi bi-search"></i>
            </button>
            <?php if($search): ?>
            <a href="<?php echo e(route('admin.users.clients')); ?>" class="btn-secondary">
                <i class="bi bi-x"></i>
            </a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Total Order</th>
                <th>Bergabung</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                <td style="color:var(--text); font-size:13px;">
                    <?php echo e($user->orders_as_client_count); ?> order
                </td>
                <td style="color:var(--muted); font-size:12px; white-space:nowrap;">
                    <?php echo e($user->created_at->format('d M Y')); ?>

                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="3" style="text-align:center; padding:48px; color:var(--muted);">
                    <i class="bi bi-person" style="font-size:32px; opacity:.2; display:block; margin-bottom:10px;"></i>
                    <?php echo e($search ? 'Tidak ada client dengan pencarian "' . $search . '".' : 'Belum ada client terdaftar.'); ?>

                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($clients->hasPages()): ?>
    <div class="pagination-wrap">
        <?php echo e($clients->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/admin/users/clients.blade.php ENDPATH**/ ?>