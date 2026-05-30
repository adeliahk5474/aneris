<?php $__env->startSection('title', 'Detail Verifikasi #' . $verification->id); ?>

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/css/pages/verification/show.css'); ?>


<div id="score-meta" style="display:none;"
    data-score-social-style="<?php echo e($verification->score_social_style ?? 0); ?>"
    data-score-social-age="<?php echo e($verification->score_social_age ?? 0); ?>"
    data-score-social-wip="<?php echo e($verification->score_social_wip ?? 0); ?>"
    data-score-social-comments="<?php echo e($verification->score_social_comments ?? 0); ?>"
    data-score-portfolio="<?php echo e($verification->score_portfolio ?? 0); ?>">
</div>


<div class="breadcrumb">
    <a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <a href="<?php echo e(route('admin.verification.index')); ?>">Verifikasi</a>
    <span class="sep">/</span>
    <span class="cur">#<?php echo e($verification->id); ?> — <?php echo e($verification->artist->name ?? '—'); ?></span>
</div>

<?php if(session('success')): ?>
<div class="alert-ok"><i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert-err"><i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?></div>
<?php endif; ?>


<div class="page-header">
    <div>
        <div class="page-title"><?php echo e($verification->artist->name ?? 'Unknown Artist'); ?></div>
        <div class="page-sub">
            <?php echo e($verification->artist->email ?? ''); ?>

            · Submit <?php echo e($verification->created_at->format('d M Y, H:i')); ?>

            · <span class="badge badge-<?php echo e($verification->status); ?>" style="vertical-align:middle;">
                <?php echo e(ucfirst(str_replace('_', ' ', $verification->status))); ?>

            </span>
        </div>
    </div>
    <a href="<?php echo e(route('admin.verification.index')); ?>" class="btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="detail-grid">

    
    <div class="col-left">

        
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-images"></i>
                Gambar Portofolio
                <span class="card-head-meta"><?php echo e(count($verification->portfolio_files ?? [])); ?> file</span>
            </div>
            <div class="detail-card-body">
                <div class="file-grid">
                    <?php $__currentLoopData = $verification->portfolio_files ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filePath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $path = is_array($filePath) ? $filePath['path'] : $filePath;
                    $name = is_array($filePath) ? ($filePath['name'] ?? basename($path)) : basename($path);
                    $size = is_array($filePath) ? ($filePath['size'] ?? null) : null;
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    // ✅ FIX: Jika path sudah berupa Cloudinary URL, langsung pakai. Jika tidak, pakai Storage::url()
                    $url = str_starts_with($path, 'http') ? $path : \Storage::url($path);

                    // ✅ FIX DOWNLOAD: Cloudinary — tambahkan fl_attachment agar browser force-download
                    if (str_contains($url, 'res.cloudinary.com')) {
                    $downloadUrl = str_replace('/upload/', '/upload/fl_attachment/', $url);
                    } else {
                    $downloadUrl = $url;
                    }

                    $base = strtolower(pathinfo($name, PATHINFO_FILENAME));
                    $wipKws = ['wip','sketch','draft','process','progress','layer','lineart','rough','thumbnail'];
                    $isWip = collect($wipKws)->contains(fn($kw) => str_contains($base, $kw));
                    $sizeKb = $size ? round($size / 1024) : null;
                    ?>
                    <div class="file-thumb"
                        onclick="openLightbox('<?php echo e($url); ?>', '<?php echo e(addslashes($name)); ?>')"
                        title="<?php echo e($name); ?>">
                        <?php if($isWip): ?>
                        <span class="file-badge wip">WIP</span>
                        <?php endif; ?>
                        <img src="<?php echo e($url); ?>" alt="<?php echo e($name); ?>" loading="lazy">
                        <?php if($sizeKb): ?>
                        <span class="file-size"><?php echo e($sizeKb >= 1024 ? round($sizeKb/1024, 1).'MB' : $sizeKb.'KB'); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php if(count($verification->portfolio_files ?? []) > 0): ?>
                <div class="download-list">
                    <?php $__currentLoopData = $verification->portfolio_files ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filePath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $path = is_array($filePath) ? $filePath['path'] : $filePath;
                    $name = is_array($filePath) ? ($filePath['name'] ?? basename($path)) : basename($path);

                    // ✅ FIX: Resolusi URL Cloudinary vs local
                    $url = str_starts_with($path, 'http') ? $path : \Storage::url($path);

                    // ✅ FIX DOWNLOAD: Cloudinary fl_attachment untuk force download
                    if (str_contains($url, 'res.cloudinary.com')) {
                    $downloadUrl = str_replace('/upload/', '/upload/fl_attachment/', $url);
                    } else {
                    $downloadUrl = $url;
                    }
                    ?>
                    <a href="<?php echo e($downloadUrl); ?>" target="_blank" rel="noopener" class="btn-download">
                        <i class="bi bi-download"></i> <?php echo e($name); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-globe2"></i>
                Link Sosial Media
                <span class="card-head-meta"><?php echo e(count($verification->social_media_links ?? [])); ?> link</span>
            </div>
            <div class="detail-card-body social-body">
                <?php $__empty_1 = true; $__currentLoopData = $verification->social_media_links ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                $host = ltrim(strtolower(parse_url($link, PHP_URL_HOST) ?? $link), 'www.');
                $icon = match(true) {
                str_contains($host, 'instagram') => 'bi-instagram',
                str_contains($host, 'tiktok') => 'bi-tiktok',
                str_contains($host, 'twitter')
                || str_contains($host, 'x.com') => 'bi-twitter-x',
                str_contains($host, 'youtube') => 'bi-youtube',
                str_contains($host, 'deviantart') => 'bi-palette',
                str_contains($host, 'pixiv') => 'bi-image',
                str_contains($host, 'artstation') => 'bi-brush',
                str_contains($host, 'behance') => 'bi-behance',
                str_contains($host, 'facebook') => 'bi-facebook',
                default => 'bi-link-45deg',
                };
                $platform = match(true) {
                str_contains($host, 'instagram') => 'Instagram',
                str_contains($host, 'tiktok') => 'TikTok',
                str_contains($host, 'twitter')
                || str_contains($host, 'x.com') => 'X/Twitter',
                str_contains($host, 'youtube') => 'YouTube',
                str_contains($host, 'deviantart') => 'DeviantArt',
                str_contains($host, 'pixiv') => 'Pixiv',
                str_contains($host, 'artstation') => 'ArtStation',
                str_contains($host, 'behance') => 'Behance',
                default => $host,
                };
                ?>
                <div class="social-link-item">
                    <div class="social-link-icon"><i class="bi <?php echo e($icon); ?>"></i></div>
                    <div class="social-link-info">
                        <div class="social-platform"><?php echo e($platform); ?></div>
                        <a href="<?php echo e($link); ?>" target="_blank" rel="noopener" class="social-url">
                            <?php echo e($link); ?>

                        </a>
                    </div>
                    <a href="<?php echo e($link); ?>" target="_blank" rel="noopener" class="btn-open-link">
                        <i class="bi bi-box-arrow-up-right"></i> Buka
                    </a>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span class="empty-text">Tidak ada link sosial media.</span>
                <?php endif; ?>

                
                <div class="sosmed-checklist">
                    <div class="checklist-head">
                        <i class="bi bi-clipboard-check"></i> Yang perlu diperiksa:
                    </div>
                    <label class="check-item">
                        <input type="checkbox" id="chk_usia"> Usia akun > 1 bulan
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_wip"> Ada timelapse / WIP / proses
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_style"> Style konsisten dengan portofolio
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_interaksi"> Ada interaksi organik
                    </label>
                    <label class="check-item">
                        <input type="checkbox" id="chk_bukan_ai"> Tidak terindikasi AI-generated
                    </label>
                </div>
            </div>
        </div>

        
        <?php if($verification->ai_score_reference !== null): ?>
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-robot"></i>
                AI Pre-screening
                <span class="card-head-meta">Referensi saja — keputusan ada pada admin</span>
            </div>
            <div class="detail-card-body">
                <?php
                $breakdown = $verification->ai_breakdown ?? [];
                $labels = [
                'file_count' => ['label' => 'Jumlah file', 'max' => 25],
                'resolution' => ['label' => 'Resolusi gambar', 'max' => 30],
                'wip' => ['label' => 'Indikasi WIP di nama', 'max' => 20],
                'file_size' => ['label' => 'Ukuran file', 'max' => 25],
                'social' => ['label' => 'Sosial media (manual)', 'max' => 0],
                ];
                ?>

                <div class="ai-total-row">
                    <?php
                    $sc = $verification->ai_score_reference;
                    $cls = $sc >= 60 ? 'high' : ($sc >= 35 ? 'medium' : 'low');
                    ?>
                    <span>Total skor AI</span>
                    <span class="ai-total-score <?php echo e($cls); ?>"><?php echo e($sc); ?>/100</span>
                </div>

                <?php $__currentLoopData = $labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($meta['max'] === 0): ?> <?php continue; ?> <?php endif; ?>
                <?php
                $item = $breakdown[$key] ?? null;
                $score = $item['score'] ?? 0;
                $max = $meta['max'];
                $note = $item['note'] ?? null;
                $pct = $max > 0 ? round(($score / $max) * 100) : 0;
                $cls2 = $pct >= 60 ? 'high' : ($pct >= 35 ? 'medium' : 'low');
                ?>
                <div class="ai-breakdown-row">
                    <span class="ai-breakdown-label"><?php echo e($meta['label']); ?></span>
                    <div class="ai-breakdown-bar">
                        <div class="score-bar">
                            <div class="score-bar-fill <?php echo e($cls2); ?>" style="width:<?php echo e($pct); ?>%"></div>
                        </div>
                        <?php if($note): ?>
                        <div class="ai-breakdown-note"><?php echo e($note); ?></div>
                        <?php endif; ?>
                    </div>
                    <span class="ai-breakdown-score"><?php echo e($score); ?>/<?php echo e($max); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($verification->ai_score_notes): ?>
                <div class="ai-notes-box">
                    <i class="bi bi-info-circle"></i>
                    <?php echo nl2br(e($verification->ai_score_notes)); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    
    <div class="col-right">

        
        <?php if(in_array($verification->status, ['approved', 'rejected'])): ?>
        <div class="detail-card decision-card">
            <div class="detail-card-head">
                <i class="bi bi-flag-fill"></i> Keputusan
            </div>
            <div class="detail-card-body">
                <div class="decision-row">
                    <span class="badge badge-<?php echo e($verification->status); ?> badge-lg">
                        <?php if($verification->status === 'approved'): ?>
                        <i class="bi bi-check-lg"></i> Approved
                        <?php else: ?>
                        <i class="bi bi-x-lg"></i> Rejected
                        <?php endif; ?>
                    </span>
                    <span class="decision-date">
                        <?php echo e($verification->reviewed_at?->format('d M Y, H:i')); ?>

                    </span>
                </div>
                <?php if($verification->total_score !== null): ?>
                <div class="total-score-result">
                    Total skor: <strong><?php echo e($verification->total_score); ?>/100</strong>
                </div>
                <?php endif; ?>
                <?php if($verification->admin_notes_final): ?>
                <div class="final-notes-box">
                    <strong>Catatan final:</strong>
                    <p><?php echo e($verification->admin_notes_final); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($verification->status !== 'approved'): ?>
        <div class="detail-card">
            <div class="detail-card-head">
                <i class="bi bi-sliders"></i> Penilaian Admin
            </div>
            <div class="detail-card-body">

                
                <div class="total-score-display">
                    <div>
                        <div class="total-score-label">Total Skor</div>
                        <div class="score-threshold-hint">Minimum lulus: 60/100</div>
                    </div>
                    <div class="total-score-value" id="previewValue">—</div>
                </div>

                
                <div class="score-section-label">
                    Sosial Media
                    <span class="score-section-max">maks 40</span>
                </div>

                <?php
                $socialCriteria = [
                ['key' => 'score_social_style', 'label' => 'Style & konsistensi visual', 'max' => 10],
                ['key' => 'score_social_age', 'label' => 'Usia akun & riwayat posting', 'max' => 10],
                ['key' => 'score_social_wip', 'label' => 'Ada WIP/proses di sosmed', 'max' => 10],
                ['key' => 'score_social_comments', 'label' => 'Interaksi & komentar organik', 'max' => 10],
                ];
                ?>

                <?php $__currentLoopData = $socialCriteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="score-input-group">
                    <div class="score-input-label">
                        <?php echo e($c['label']); ?>

                        <span class="score-input-val" id="val_<?php echo e($c['key']); ?>">
                            <?php echo e($verification->{$c['key']} ?? 0); ?>

                        </span>
                        <span class="score-input-max">/<?php echo e($c['max']); ?></span>
                    </div>
                    <input type="range" min="0" max="<?php echo e($c['max']); ?>"
                        value="<?php echo e($verification->{$c['key']} ?? 0); ?>"
                        data-key="<?php echo e($c['key']); ?>"
                        oninput="updateScore(this)">
                    <div class="range-labels">
                        <span>0</span><span><?php echo e($c['max']); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <div class="score-section-label" style="margin-top:18px;">
                    Portfolio
                    <span class="score-section-max">maks 60</span>
                </div>
                <div class="score-input-group">
                    <div class="score-input-label">
                        Kualitas & keaslian portofolio
                        <span class="score-input-val" id="val_score_portfolio">
                            <?php echo e($verification->score_portfolio ?? 0); ?>

                        </span>
                        <span class="score-input-max">/60</span>
                    </div>
                    <input type="range" min="0" max="60"
                        value="<?php echo e($verification->score_portfolio ?? 0); ?>"
                        data-key="score_portfolio"
                        oninput="updateScore(this)">
                    <div class="range-labels"><span>0</span><span>60</span></div>
                </div>

                
                <div class="notes-group">
                    <label class="form-label">Catatan Sosmed <span class="optional">(opsional)</span></label>
                    <textarea class="admin-textarea" id="notes_social" rows="2"
                        placeholder="Komentar tentang akun sosmed artist..."><?php echo e($verification->admin_notes_social ?? ''); ?></textarea>
                </div>
                <div class="notes-group">
                    <label class="form-label">Catatan Portfolio <span class="optional">(opsional)</span></label>
                    <textarea class="admin-textarea" id="notes_portfolio" rows="2"
                        placeholder="Komentar tentang kualitas karya..."><?php echo e($verification->admin_notes_portfolio ?? ''); ?></textarea>
                </div>
                <div class="notes-group">
                    <label class="form-label">
                        Catatan Final untuk Artist
                        <span class="required-star">*</span>
                    </label>
                    <textarea class="admin-textarea" id="notes_final" rows="3" required
                        placeholder="Catatan ini ditampilkan ke artist. Jika reject, jelaskan alasan spesifik."><?php echo e($verification->admin_notes_final ?? ''); ?></textarea>
                    <div class="notes-hint">Wajib diisi sebelum kirim keputusan.</div>
                </div>

                
                <div class="action-buttons">
                    <form action="<?php echo e(route('admin.verification.decide', $verification->id)); ?>"
                        method="POST" id="approveForm">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="score_social_style" id="hid_score_social_style">
                        <input type="hidden" name="score_social_age" id="hid_score_social_age">
                        <input type="hidden" name="score_social_wip" id="hid_score_social_wip">
                        <input type="hidden" name="score_social_comments" id="hid_score_social_comments">
                        <input type="hidden" name="score_portfolio" id="hid_score_portfolio">
                        <input type="hidden" name="admin_notes_social" id="hid_notes_social">
                        <input type="hidden" name="admin_notes_portfolio" id="hid_notes_portfolio">
                        <input type="hidden" name="admin_notes_final" id="hid_notes_final">
                        <button type="submit" class="btn-approve"
                            onclick="return syncAndConfirm(this.form, 'Approve submisi ini? Artist akan langsung berstatus Verified.')">
                            <i class="bi bi-patch-check-fill"></i> Approve
                        </button>
                    </form>

                    <form action="<?php echo e(route('admin.verification.decide', $verification->id)); ?>"
                        method="POST" id="rejectForm">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="score_social_style" id="hid2_score_social_style">
                        <input type="hidden" name="score_social_age" id="hid2_score_social_age">
                        <input type="hidden" name="score_social_wip" id="hid2_score_social_wip">
                        <input type="hidden" name="score_social_comments" id="hid2_score_social_comments">
                        <input type="hidden" name="score_portfolio" id="hid2_score_portfolio">
                        <input type="hidden" name="admin_notes_social" id="hid2_notes_social">
                        <input type="hidden" name="admin_notes_portfolio" id="hid2_notes_portfolio">
                        <input type="hidden" name="admin_notes_final" id="hid2_notes_final">
                        <button type="submit" class="btn-reject"
                            onclick="return syncAndConfirm(this.form, 'Reject submisi ini? Artist akan dikunci 30 hari.')">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                    </form>
                </div>

            </div>
        </div>

        
        <?php if($verification->status === 'pending'): ?>
        <form action="<?php echo e(route('admin.verification.take', $verification->id)); ?>"
            method="POST" style="margin-top:10px;">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            <button type="submit" class="btn-secondary btn-full">
                <i class="bi bi-eye"></i> Tandai sebagai "In Review"
            </button>
        </form>
        <?php endif; ?>
        <?php endif; ?>

    </div>

</div>


<div class="lightbox" id="lightbox" onclick="if(event.target===this)closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">×</span>
    <img id="lightboxImg" src="" alt="">
    <div class="lightbox-name" id="lightboxName"></div>
</div>

<?php echo app('Illuminate\Foundation\Vite')('resources/js/pages/verification/show.js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/pages/verification/show.blade.php ENDPATH**/ ?>