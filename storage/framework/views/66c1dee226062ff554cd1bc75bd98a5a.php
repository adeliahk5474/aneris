<?php $__env->startSection('title', 'Antrean Verifikasi'); ?>

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/css/pages/verification/index.css'); ?>

<div class="page-header">
    <div>
        <div class="page-title">Antrean Verifikasi Portfolio</div>
        <div class="page-sub"><?php echo e($total); ?> total submission</div>
    </div>
</div>

<div class="table-card">
    <div class="table-head">
        <div class="table-title">Semua Submission</div>

        <div class="table-controls">
            
            <div class="filter-bar">
                <?php $__currentLoopData = ['all' => 'Semua', 'pending' => 'Pending', 'in_review' => 'In Review', 'approved' => 'Approved', 'rejected' => 'Rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.verification.index', array_merge(request()->query(), ['status' => $val, 'page' => 1]))); ?>"
                    class="filter-chip <?php echo e($currentStatus === $val ? 'active' : ''); ?>">
                    <?php echo e($lbl); ?>

                    <?php if($val !== 'all' && isset($counts[$val]) && $counts[$val] > 0): ?>
                    <span class="filter-count"><?php echo e($counts[$val]); ?></span>
                    <?php endif; ?>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <select class="sort-select" onchange="window.location=this.value">
                <?php $__currentLoopData = [
                'latest' => 'Terbaru dulu',
                'oldest' => 'Terlama dulu',
                'score_asc' => 'Skor AI rendah dulu',
                'score_desc' => 'Skor AI tinggi dulu',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option
                    value="<?php echo e(route('admin.verification.index', array_merge(request()->query(), ['sort' => $val, 'page' => 1]))); ?>"
                    <?php echo e($currentSort === $val ? 'selected' : ''); ?>>
                    <?php echo e($lbl); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <table class="verif-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Artist</th>
                <th>File</th>
                <th>Sosmed</th>
                <th>Skor AI</th>
                <th>Status</th>
                <th>Dikirim</th>
                <th>Direview</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $verifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
            $sc = $v->ai_score_reference;
            $cls = $sc !== null ? ($sc >= 60 ? 'high' : ($sc >= 35 ? 'medium' : 'low')) : '';
            ?>
            <tr onclick="window.location='<?php echo e(route('admin.verification.show', $v->id)); ?>'"
                class="verif-row">
                <td class="col-id"><?php echo e($v->id); ?></td>
                <td>
                    <div class="artist-name"><?php echo e($v->artist->name ?? '—'); ?></div>
                    <div class="artist-email"><?php echo e($v->artist->email ?? ''); ?></div>
                </td>
                <td class="col-meta">
                    <i class="bi bi-images"></i>
                    <?php echo e(count($v->portfolio_files ?? [])); ?> gambar
                </td>
                <td class="col-meta">
                    <i class="bi bi-link-45deg"></i>
                    <?php echo e(count($v->social_media_links ?? [])); ?> link
                </td>
                <td>
                    <?php if($sc !== null): ?>
                    <div class="score-bar-wrap">
                        <div class="score-bar">
                            <div class="score-bar-fill <?php echo e($cls); ?>" style="width:<?php echo e($sc); ?>%"></div>
                        </div>
                        <span class="score-num <?php echo e($cls); ?>"><?php echo e($sc); ?></span>
                    </div>
                    <?php else: ?>
                    <span class="col-empty">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge badge-<?php echo e($v->status); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $v->status))); ?>

                    </span>
                </td>
                <td class="col-date"><?php echo e($v->created_at->format('d M Y')); ?></td>
                <td class="col-date"><?php echo e($v->reviewed_at ? $v->reviewed_at->format('d M Y') : '—'); ?></td>
                <td><i class="bi bi-arrow-right col-arrow"></i></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="empty-state">
                    <i class="bi bi-inbox"></i>
                    Tidak ada submission untuk filter ini.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if($verifications->hasPages()): ?>
    <div class="pagination-wrap">
        <?php echo e($verifications->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/pages/verification/index.blade.php ENDPATH**/ ?>