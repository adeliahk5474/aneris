<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div>
        <div class="page-title">Dashboard</div>
        <div class="page-sub">
            Selamat datang, <?php echo e(Auth::guard('admin')->user()->name); ?>.
            <?php echo e(now()->translatedFormat('l, d F Y')); ?>

        </div>
    </div>
</div>


<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
        <div>
            <div class="stat-label">Pending Review</div>
            <div class="stat-value"><?php echo e($stats['pending']); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-eye"></i></div>
        <div>
            <div class="stat-label">In Review</div>
            <div class="stat-value"><?php echo e($stats['in_review']); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-patch-check-fill"></i></div>
        <div>
            <div class="stat-label">Approved</div>
            <div class="stat-value"><?php echo e($stats['approved']); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
        <div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value"><?php echo e($stats['rejected']); ?></div>
        </div>
    </div>
</div>


<div class="table-card">
    <div class="table-head">
        <div class="table-title">Submission Terbaru</div>
        <a href="<?php echo e(route('admin.verification.index')); ?>" class="btn-secondary">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Artist</th>
                <th>Dikirim</th>
                <th>Skor AI</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr onclick="window.location='<?php echo e(route('admin.verification.show', $v->id)); ?>'">
                <td>
                    <div style="font-weight:600;"><?php echo e($v->artist->name ?? '—'); ?></div>
                    <div style="font-size:11px; color:var(--muted);"><?php echo e($v->artist->email ?? ''); ?></div>
                </td>
                <td style="color:var(--muted); font-size:12px;">
                    <?php echo e($v->created_at->diffForHumans()); ?>

                </td>
                <td>
                    <?php if($v->ai_score_reference !== null): ?>
                    <?php $sc = $v->ai_score_reference; $cls = $sc >= 60 ? 'high' : ($sc >= 35 ? 'medium' : 'low'); ?>
                    <div class="score-bar-wrap">
                        <div class="score-bar">
                            <div class="score-bar-fill <?php echo e($cls); ?>" style="width:<?php echo e($sc); ?>%"></div>
                        </div>
                        <span class="score-num <?php echo e($cls); ?>"><?php echo e($sc); ?></span>
                    </div>
                    <?php else: ?>
                    <span style="color:var(--muted); font-size:11px;">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge badge-<?php echo e($v->status); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $v->status))); ?>

                    </span>
                </td>
                <td><i class="bi bi-arrow-right" style="color:var(--muted);"></i></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:32px; color:var(--muted);">
                    Belum ada submission.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>