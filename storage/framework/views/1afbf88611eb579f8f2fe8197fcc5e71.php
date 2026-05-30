<?php $__env->startSection('title', 'Dashboard Artist — Aneris'); ?>

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/css/dashboards/artist.css'); ?>


<div class="send-modal" id="sendModal">
    <div class="send-modal-card">
        <div class="send-modal-title">Kirim Hasil Kerja</div>
        <div class="send-modal-sub" id="sendModalSub">Upload file hasil untuk dikirim ke client</div>
        <form action="<?php echo e(route('order.send')); ?>" method="POST" enctype="multipart/form-data" id="sendForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="order_id" id="sendOrderId">
            <div class="send-upload-area" onclick="document.getElementById('resultFile').click()">
                <input type="file" name="result_file" id="resultFile"
                    accept="image/*,.pdf,.zip,.rar" required
                    onchange="showFileName(this)">
                <i class="bi bi-cloud-arrow-up"></i>
                <p id="uploadText">Klik untuk upload hasil kerja</p>
                <p style="font-size:11px; color:var(--muted); margin-top:4px;">JPG, PNG, PDF, ZIP — Maks 20MB</p>
            </div>
            <button type="submit" class="btn-send-submit"><i class="bi bi-send"></i> Kirim ke Client</button>
        </form>
        <button class="btn-cancel-modal" onclick="closeSendModal()">Batal</button>
    </div>
</div>


<div class="review-modal" id="reviewModal">
    <div class="review-modal-card">
        <button class="modal-close" onclick="closeReviewModal()"><i class="bi bi-x"></i></button>
        <div class="review-modal-title">Review Client</div>
        <div class="review-modal-sub" id="reviewModalSub">Beri penilaian untuk client ini</div>

        <form action="<?php echo e(route('review.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="order_id" id="reviewOrderId">

            <label class="form-label">Overall Rating *</label>
            <div class="star-group" style="margin-bottom:14px;">
                <?php for($i=5;$i>=1;$i--): ?>
                <input type="radio" name="overall_rating" id="aor<?php echo e($i); ?>" value="<?php echo e($i); ?>" <?php echo e($i===5?'required':''); ?>>
                <label for="aor<?php echo e($i); ?>" title="<?php echo e($i); ?> bintang">★</label>
                <?php endfor; ?>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px;">
                <?php $__currentLoopData = ['brief'=>'Kejelasan Brief','attitude'=>'Sikap','revision'=>'Revisi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <label class="form-label"><?php echo e($lbl); ?></label>
                    <select name="rating_<?php echo e($key); ?>"
                        style="width:100%; background:var(--surface2); border:1px solid rgba(255,255,255,.1); border-radius:8px; padding:8px 10px; color:var(--text); font-family:'Outfit',sans-serif; font-size:13px; outline:none;">
                        <option value="">—</option>
                        <?php for($i=1;$i<=5;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option> <?php endfor; ?>
                    </select>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <label class="form-label">Komentar (opsional)</label>
            <textarea name="comment" class="form-textarea" placeholder="Pengalaman bekerja dengan client ini..."></textarea>

            <div class="blind-notice">
                <i class="bi bi-eye-slash"></i>
                Review tersembunyi sampai client juga submit atau 14 hari berlalu.
            </div>

            <button type="submit" class="btn-submit">Kirim Review</button>
        </form>
    </div>
</div>


<div class="dashboard-layout">

    
    <aside class="dash-sidebar">
        <div class="sidebar-artist-row">
            <img src="<?php echo e($artist->avatar ?? asset('images/default-avatar.png')); ?>"
                class="sidebar-avatar" alt="<?php echo e($artist->name); ?>">
            <div>
                <?php if($artist->isVerifiedArtist()): ?>
                <span class="verified-badge">
                    <i class="bi bi-patch-check-fill"></i> Verified Non-AI
                </span>
                <?php endif; ?>
                <div class="sidebar-artist-name">Artist Studio</div>
                <div class="sidebar-artist-role">Creative Pro</div>
            </div>
        </div>

        <a href="<?php echo e(route('upload.popup')); ?>" class="btn-new-service">
            <i class="bi bi-plus"></i> New Service
        </a>

        <nav class="sidebar-nav">
            <div class="nav-group-label">Studio</div>
            <a class="sidebar-nav-item active" onclick="switchDashTab('overview',this)" href="javascript:void(0)">
                <i class="bi bi-grid-1x2"></i> Overview
            </a>
            <a class="sidebar-nav-item" onclick="switchDashTab('orders',this)" href="javascript:void(0)">
                <i class="bi bi-bag-check"></i> Orders
                <?php if($pendingCommissions > 0): ?>
                <span style="margin-left:auto; background:var(--accent); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    <?php echo e($pendingCommissions); ?>

                </span>
                <?php endif; ?>
            </a>
            <a class="sidebar-nav-item" onclick="switchDashTab('listings',this)" href="javascript:void(0)">
                <i class="bi bi-grid-3x3-gap"></i> Listings
            </a>

            
            <a class="sidebar-nav-item" onclick="switchDashTab('portfolio',this)" href="javascript:void(0)">
                <i class="bi bi-patch-check"></i> Portfolio Verif
                <?php if(isset($verification) && $verification?->status === 'pending'): ?>
                <span style="margin-left:auto; background:var(--yellow); color:#000; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    Review
                </span>
                <?php elseif(isset($verification) && $verification?->status === 'rejected'): ?>
                <span style="margin-left:auto; background:var(--red); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:999px;">
                    Gagal
                </span>
                <?php endif; ?>
            </a>

            <div class="nav-group-label">Finance</div>
            <a class="sidebar-nav-item" href="javascript:void(0)">
                <i class="bi bi-wallet2"></i> Wallet
            </a>
        </nav>
    </aside>

    <main class="dash-main">

        
        <div id="dash-overview" class="dash-tab-panel">

            <div class="dash-page-header">
                <div>
                    <div class="dash-page-title">Dashboard Artist</div>
                    <div class="dash-page-sub">Welcome back, <span><?php echo e($artist->name); ?></span>. Studio is live.</div>
                </div>
                <a href="<?php echo e(route('upload.popup')); ?>" class="btn-add-service">
                    <i class="bi bi-plus"></i> Tambah Jasa
                </a>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon income"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <div class="stat-label">Total Pendapatan</div>
                        <div class="stat-value">Rp <?php echo e(number_format($totalEarnings,0,',','.')); ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orders"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="stat-label">Pesanan Aktif</div>
                        <div class="stat-value"><?php echo e($activeCommissions); ?></div>
                        <?php if($pendingCommissions > 0): ?>
                        <div class="stat-sub"><?php echo e($pendingCommissions); ?> menunggu konfirmasi</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rating"><i class="bi bi-star-fill"></i></div>
                    <div>
                        <div class="stat-label">Rating Rata-rata</div>
                        <div class="stat-value">
                            <?php echo e(number_format($averageRating ?? 0,1)); ?>

                            <span style="font-size:14px; color:var(--muted);">/5.0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-row">
                <div class="chart-card">
                    <div class="chart-header">
                        <div>
                            <div class="chart-title">Pertumbuhan Pendapatan</div>
                            <div class="chart-sub">Last 6 months</div>
                        </div>
                        <div class="chart-year"><?php echo e(now()->year); ?></div>
                    </div>
                    <div class="chart-canvas-wrap"><canvas id="revenueChart"></canvas></div>
                    <div class="chart-labels">
                        <?php $__currentLoopData = array_slice($monthlyLabels,-6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="chart-label"><?php echo e($label); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div class="actions-card">
                    <div class="actions-title">Quick Actions</div>
                    <a href="javascript:void(0)" class="action-item">
                        <div class="action-item-left"><i class="bi bi-wallet2"></i> Tarik Saldo</div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <?php if($recentNotifications > 0): ?>
                    <div class="tip-card">
                        <div class="tip-title">Perlu Perhatian</div>
                        <div class="tip-text">Ada <?php echo e($recentNotifications); ?> order yang butuh tindakan. Cek segera!</div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if(!$artist->isVerifiedArtist()): ?>
                    <div class="tip-card" style="border-color:rgba(139,92,246,.3); background:var(--accent-dim); margin-top:4px;">
                        <div class="tip-title" style="color:var(--accent);">
                            <i class="bi bi-patch-exclamation" style="margin-right:4px;"></i> Belum Terverifikasi
                        </div>
                        <div class="tip-text">Submit portofoliomu untuk mendapat badge Verified Non-AI dan bisa membuka commission.</div>
                        <a href="javascript:void(0)"
                            onclick="switchDashTab('portfolio', document.querySelectorAll('.sidebar-nav-item')[3])"
                            style="display:inline-block; margin-top:8px; font-size:12px; color:var(--accent); font-weight:600;">
                            Ajukan Verifikasi →
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="queue-header">
                <div class="queue-title">Active Queue</div>
                <span class="queue-view-all" onclick="switchDashTab('orders',document.querySelectorAll('.sidebar-nav-item')[1])">
                    View All Orders
                </span>
            </div>

            <div class="queue-list">
                <?php $__empty_1 = true; $__currentLoopData = $activeForOverview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                $maxRevisions = $order->service->max_revisions ?? 3;
                $usedRevisions = $order->revision_count ?? 0;
                ?>
                <div class="queue-item <?php echo e(in_array($order->status,['pending','paid']) ? 'pending-item' : ($order->status==='waiting_client' ? 'waiting-item' : ($order->status==='revision_requested' ? 'revision-requested-item' : ($order->status==='revision' ? 'revision-item' : '')))); ?>">
                    <img src="<?php echo e($order->service->image_url ?? asset('images/default-thumb.png')); ?>"
                        class="queue-thumb" alt="">
                    <div class="queue-info">
                        <div class="queue-service-name"><?php echo e($order->service->title ?? 'Commission'); ?></div>
                        <div class="queue-meta">
                            <span><?php echo e($order->client->name ?? '—'); ?></span>
                            <span class="status-badge badge-<?php echo e($order->status); ?>">
                                <?php echo e([
                                        'pending'=>'Pending','paid'=>'Paid','in_progress'=>'In Progress',
                                        'revision_requested'=>'Revision Requested','revision'=>'Revising',
                                        'waiting_client'=>'Waiting Client','completed'=>'Completed','canceled'=>'Canceled'
                                    ][$order->status] ?? ucfirst($order->status)); ?>

                            </span>
                        </div>
                        <?php if($usedRevisions > 0 || $maxRevisions > 0): ?>
                        <div class="revision-progress">
                            <?php for($ri=0;$ri<$maxRevisions;$ri++): ?>
                                <div class="revision-dot <?php echo e($ri < $usedRevisions ? 'used' : 'avail'); ?>">
                        </div>
                        <?php endfor; ?>
                        <span class="revision-label"><?php echo e($usedRevisions); ?>/<?php echo e($maxRevisions); ?> revisi</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="queue-actions">
                    <?php if($order->status === 'revision_requested'): ?>
                    <form action="<?php echo e(route('order.revision')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="qa-btn accept-revision">
                            <i class="bi bi-pencil"></i> Mulai Revisi
                        </button>
                    </form>
                    <?php elseif(in_array($order->status,['in_progress','revision'])): ?>
                    <button class="qa-btn send" onclick="openSendModal('<?php echo e($order->order_id); ?>','<?php echo e($order->phase ?? 'sketch'); ?>')">
                        <i class="bi bi-upload"></i> Kirim File
                    </button>
                    <?php elseif($order->status === 'waiting_client'): ?>
                    <span class="qa-btn waiting-state"><i class="bi bi-hourglass-split"></i> Menunggu</span>
                    <?php elseif(in_array($order->status,['pending','paid'])): ?>
                    <form action="<?php echo e(route('order.accept')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                        <button type="submit" class="qa-btn accept"><i class="bi bi-check-lg"></i> Terima</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
                <i class="bi bi-inbox" style="font-size:36px; opacity:.2; display:block; margin-bottom:12px;"></i>
                Tidak ada order aktif.
            </div>
            <?php endif; ?>
        </div>

</div>


<div id="dash-orders" class="dash-tab-panel" style="display:none;">

    <div class="dash-page-header">
        <div>
            <div class="dash-page-title">Semua Orders</div>
            <div class="dash-page-sub">Kelola semua pesanan masuk</div>
        </div>
    </div>

    <div class="filter-chips">
        <?php $__currentLoopData = ['all'=>'Semua','pending'=>'Pending','in_progress'=>'In Progress','revision_requested'=>'Revision Req','revision'=>'Revising','waiting_client'=>'Waiting Client','completed'=>'Completed','canceled'=>'Dibatalkan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="chip <?php echo e($st==='all'?'active':''); ?>" onclick="filterOrders('<?php echo e($st); ?>',this)">
            <?php echo e($lbl); ?>

            <?php if($st==='pending' && $pendingCommissions > 0): ?>
            <span style="background:var(--yellow);color:#000;border-radius:999px;padding:0 5px;font-size:10px;margin-left:3px;"><?php echo e($pendingCommissions); ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if(session('success')): ?>
    <div class="alert-success"><i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert-error"><i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="queue-list" id="orders-list">
        <?php $__empty_1 = true; $__currentLoopData = $incomingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
        $phases = ['Sketching','Waiting','Coloring','Finalizing'];
        $curPhase = match($order->phase ?? 'sketch') {
        'sketch' => 0, 'coloring' => 2, 'rendering' => 3, 'final' => 4, default => 0,
        };
        $itemClass = match($order->status) {
        'pending','paid' => 'pending-item',
        'waiting_client' => 'waiting-item',
        'revision' => 'revision-item',
        'revision_requested' => 'revision-requested-item',
        default => '',
        };
        $statusLabels = [
        'pending'=>'Pending','paid'=>'Paid','in_progress'=>'In Progress',
        'revision_requested'=>'Revision Requested','revision'=>'Revising',
        'waiting_client'=>'Waiting Client','completed'=>'Completed','canceled'=>'Canceled'
        ];
        $maxRevisions = $order->service->max_revisions ?? 3;
        $usedRevisions = $order->revision_count ?? 0;
        $remainRevisions = max(0, $maxRevisions - $usedRevisions);
        $artistReviewed = $order->reviews()->where('reviewer_type','artist')->exists();
        ?>

        <div class="queue-item <?php echo e($itemClass); ?>" data-status="<?php echo e($order->status); ?>">
            <img src="<?php echo e($order->service->image_url ?? asset('images/default-thumb.png')); ?>"
                class="queue-thumb" alt="">

            <div class="queue-info">
                <div class="queue-service-name"><?php echo e($order->service->title ?? 'Commission'); ?></div>
                <div class="queue-meta">
                    <span><?php echo e($order->client->name ?? '—'); ?></span>
                    <span class="status-badge badge-<?php echo e($order->status); ?>">
                        <?php echo e($statusLabels[$order->status] ?? ucfirst($order->status)); ?>

                        <?php if($order->phase && in_array($order->status,['in_progress','revision','waiting_client'])): ?>
                        — <?php echo e(ucfirst($order->phase)); ?>

                        <?php endif; ?>
                    </span>
                </div>

                <?php if($maxRevisions > 0): ?>
                <div class="revision-progress" style="margin-top:6px;">
                    <?php for($ri=0;$ri<$maxRevisions;$ri++): ?>
                        <div class="revision-dot <?php echo e($ri < $usedRevisions ? 'used' : 'avail'); ?>"
                        title="<?php echo e($ri < $usedRevisions ? 'Terpakai' : 'Tersisa'); ?>">
                </div>
                <?php endfor; ?>
                <span class="revision-label">
                    <?php echo e($usedRevisions); ?>/<?php echo e($maxRevisions); ?> revisi
                    <?php if($remainRevisions === 0): ?>
                    <span style="color:var(--red); font-weight:700;">· Habis</span>
                    <?php else: ?>
                    · <?php echo e($remainRevisions); ?> sisa
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>

            <div style="font-size:11px; color:var(--muted); margin-top:4px;">
                Rp <?php echo e(number_format($order->total_price,0,',','.')); ?> ·
                <?php echo e($order->created_at->format('d M Y')); ?>

            </div>
        </div>

        <div class="phase-track">
            <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pi => $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="phase-step <?php echo e($pi < $curPhase ? 'done' : ($pi === $curPhase ? 'active' : '')); ?>">
                <div class="phase-dot"></div>
                <div class="phase-label"><?php echo e($ph); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="queue-actions">
            <?php if(in_array($order->status,['pending','paid'])): ?>
            <form action="<?php echo e(route('order.accept')); ?>" method="POST">
                <?php echo csrf_field(); ?> <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                <button type="submit" class="qa-btn accept"><i class="bi bi-check-lg"></i> Terima</button>
            </form>
            <form action="<?php echo e(route('order.reject')); ?>" method="POST"
                onsubmit="return confirm('Tolak order ini?')">
                <?php echo csrf_field(); ?> <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                <button type="submit" class="qa-btn reject"><i class="bi bi-x-lg"></i> Tolak</button>
            </form>

            <?php elseif($order->status === 'revision_requested'): ?>
            <form action="<?php echo e(route('order.revision')); ?>" method="POST">
                <?php echo csrf_field(); ?> <input type="hidden" name="order_id" value="<?php echo e($order->order_id); ?>">
                <input type="hidden" name="action" value="accept">
                <button type="submit" class="qa-btn accept-revision">
                    <i class="bi bi-pencil-square"></i> Mulai Revisi
                </button>
            </form>
            <a href="<?php echo e(route('chat.index',['order_id'=>$order->order_id])); ?>"
                class="qa-btn chat"><i class="bi bi-chat-dots"></i> Chat</a>

            <?php elseif(in_array($order->status,['in_progress','revision'])): ?>
            <button class="qa-btn send"
                onclick="openSendModal('<?php echo e($order->order_id); ?>','<?php echo e($order->phase ?? 'sketch'); ?>')">
                <i class="bi bi-upload"></i> Kirim File
            </button>
            <a href="<?php echo e(route('chat.index',['order_id'=>$order->order_id])); ?>"
                class="qa-btn chat"><i class="bi bi-chat-dots"></i> Chat</a>

            <?php elseif($order->status === 'waiting_client'): ?>
            <span class="qa-btn waiting-state"><i class="bi bi-hourglass-split"></i> Menunggu</span>
            <a href="<?php echo e(route('chat.index',['order_id'=>$order->order_id])); ?>"
                class="qa-btn chat"><i class="bi bi-chat-dots"></i> Chat</a>

            <?php elseif($order->status === 'completed'): ?>
            <span class="qa-btn done-state"><i class="bi bi-check-circle"></i> Selesai</span>
            <?php if(!$artistReviewed): ?>
            <button class="qa-btn send"
                onclick="openReviewModal('<?php echo e($order->order_id); ?>','<?php echo e($order->client->name ?? 'Client'); ?>')">
                <i class="bi bi-star"></i> Review Client
            </button>
            <?php endif; ?>

            <?php elseif($order->status === 'canceled'): ?>
            <span class="qa-btn reject" style="pointer-events:none; opacity:.6;">Dibatalkan</span>
            <?php endif; ?>
        </div>

        <a href="<?php echo e(route('order.detail',$order->order_id)); ?>"
            class="queue-detail-link" title="Lihat Detail">
            <i class="bi bi-arrow-right" style="font-size:14px;"></i>
        </a>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center; padding:60px; color:var(--muted); font-size:14px;">
        <i class="bi bi-inbox" style="font-size:40px; opacity:.2; display:block; margin-bottom:12px;"></i>
        Belum ada orders.
    </div>
    <?php endif; ?>
</div>

</div>


<div id="dash-listings" class="dash-tab-panel" style="display:none;">
    <div class="dash-page-header">
        <div>
            <div class="dash-page-title">Jasa Saya</div>
            <div class="dash-page-sub">Kelola semua jasa commission kamu</div>
        </div>
        <a href="<?php echo e(route('upload.popup')); ?>" class="btn-add-service">
            <i class="bi bi-plus"></i> New Service
        </a>
    </div>

    <?php if($myServices->count()): ?>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
        <?php $__currentLoopData = $myServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
            <div style="aspect-ratio:16/9; overflow:hidden; background:var(--surface2);">
                <img src="<?php echo e($svc->image_url ?? asset('images/default-thumb.png')); ?>"
                    style="width:100%; height:100%; object-fit:cover;" alt="">
            </div>
            <div style="padding:14px;">
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo e($svc->title); ?></div>
                <div style="font-size:12px; color:var(--muted); margin-bottom:2px;">
                    <?php echo e($svc->category->name ?? ''); ?> · Rp <?php echo e(number_format($svc->base_price ?? 0,0,',','.')); ?>

                </div>
                <div style="font-size:11px; color:var(--muted); margin-bottom:8px;">
                    Max revisi: <strong style="color:var(--text);"><?php echo e($svc->max_revisions ?? 3); ?>x</strong>
                </div>
                <span style="font-size:10px; font-weight:700; padding:3px 8px; border-radius:999px; display:inline-block; margin-bottom:10px;
                            background:<?php echo e($svc->status==='active' ? 'var(--green-dim)' : 'var(--surface2)'); ?>;
                            color:<?php echo e($svc->status==='active' ? 'var(--green)' : 'var(--muted)'); ?>;">
                    <?php echo e(strtoupper($svc->status)); ?>

                </span>
                <div style="display:flex; gap:8px;">
                    <a href="<?php echo e(route('commission.show',$svc->service_id)); ?>"
                        style="flex:1; text-align:center; padding:7px; border-radius:7px; border:1px solid var(--border2); background:var(--surface2); color:var(--text); font-size:12px; font-weight:600; text-decoration:none;">
                        Lihat
                    </a>
                    <form action="<?php echo e(route('commission.delete',$svc->service_id)); ?>" method="POST" style="flex:1;">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" onclick="return confirm('Hapus jasa ini?')"
                            style="width:100%; padding:7px; border-radius:7px; border:1px solid rgba(239,68,68,.3); background:rgba(239,68,68,.1); color:#f87171; font-size:12px; font-weight:600; cursor:pointer; font-family:'Outfit',sans-serif;">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div style="text-align:center; padding:80px; color:var(--muted);">
        <i class="bi bi-grid-3x3-gap" style="font-size:40px; opacity:.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px;">Belum ada jasa. <a href="<?php echo e(route('upload.popup')); ?>" style="color:var(--accent);">Buat sekarang</a></p>
    </div>
    <?php endif; ?>
</div>


<div id="dash-portfolio" class="dash-tab-panel" style="display:none;">

    <div class="dash-page-header">
        <div>
            <div class="dash-page-title">Verifikasi Portfolio</div>
            <div class="dash-page-sub">Buktikan karyamu adalah karya manusia asli.</div>
        </div>
    </div>

    <?php
    $verif = $verification ?? null;
    $verifStatus = $verif?->status; // null | pending | in_review | approved | rejected
    $canResubmit = $verif?->status === 'rejected'
    && ($verif->next_eligible_at === null || now()->gte($verif->next_eligible_at));
    $daysLeft = $verif?->next_eligible_at
    ? (int) now()->diffInDays($verif->next_eligible_at, false)
    : 0;
    ?>

    
    <?php if($verif): ?>
    <div class="verif-status-card
                <?php if($verifStatus === 'approved'): ?> verif-approved
                <?php elseif($verifStatus === 'rejected'): ?> verif-rejected
                <?php else: ?> verif-pending
                <?php endif; ?>">

        <div class="verif-status-icon">
            <?php if($verifStatus === 'approved'): ?>
            <i class="bi bi-patch-check-fill"></i>
            <?php elseif($verifStatus === 'rejected'): ?>
            <i class="bi bi-x-circle-fill"></i>
            <?php elseif($verifStatus === 'in_review'): ?>
            <i class="bi bi-eye-fill"></i>
            <?php else: ?>
            <i class="bi bi-hourglass-split"></i>
            <?php endif; ?>
        </div>

        <div class="verif-status-body">
            <div class="verif-status-title">
                <?php if($verifStatus === 'approved'): ?> Terverifikasi ✓
                <?php elseif($verifStatus === 'rejected'): ?> Verifikasi Ditolak
                <?php elseif($verifStatus === 'in_review'): ?> Sedang Direview Admin
                <?php else: ?> Menunggu Review
                <?php endif; ?>
            </div>

            <?php if($verifStatus === 'approved'): ?>
            <div class="verif-status-sub">
                Kamu sudah mendapat badge <strong>Verified Non-AI</strong>. Commission kamu aktif dan bisa ditemukan client.
            </div>

            <?php elseif($verifStatus === 'rejected'): ?>
            <div class="verif-status-sub">
                Submisimu ditolak pada <?php echo e($verif->reviewed_at?->format('d M Y') ?? '-'); ?>.
            </div>
            <?php if($verif->admin_notes_final): ?>
            <div class="verif-admin-feedback">
                <div class="verif-feedback-label"><i class="bi bi-chat-left-text"></i> Feedback Admin</div>
                <div class="verif-feedback-text"><?php echo e($verif->admin_notes_final); ?></div>
            </div>
            <?php endif; ?>

            <?php if($verif->total_score !== null): ?>
            <div class="verif-score-row">
                <span class="verif-score-pill">Skor: <?php echo e($verif->total_score); ?>/100</span>
                <span style="font-size:12px; color:var(--muted);">Minimum lulus: 60</span>
            </div>
            <?php endif; ?>

            <?php if(!$canResubmit && $daysLeft > 0): ?>
            <div class="verif-cooldown">
                <i class="bi bi-clock"></i>
                Bisa submit ulang dalam <strong><?php echo e($daysLeft); ?> hari</strong>
                (<?php echo e($verif->next_eligible_at?->format('d M Y')); ?>)
            </div>
            <?php endif; ?>

            <?php else: ?> 
            <div class="verif-status-sub">
                Submisi diterima pada <?php echo e($verif->created_at->format('d M Y')); ?>.
                Tim kami akan mereview dalam 3–5 hari kerja.
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if(!$verif || $canResubmit): ?>

    <?php if($canResubmit): ?>
    <div style="margin-bottom:20px; padding:14px 18px; background:rgba(250,204,21,.08); border:1px solid rgba(250,204,21,.2); border-radius:10px; font-size:13px; color:var(--yellow);">
        <i class="bi bi-arrow-clockwise"></i>
        Kamu bisa submit ulang sekarang. Pastikan kamu memperbaiki poin yang disebutkan admin di atas.
    </div>
    <?php endif; ?>

    <form action="<?php echo e(route('verification.store')); ?>" method="POST" enctype="multipart/form-data"
        class="verif-form">
        <?php echo csrf_field(); ?>

        
        <?php if($errors->any()): ?>
        <div class="alert-error" style="margin-bottom:16px;">
            <i class="bi bi-exclamation-circle"></i>
            <ul style="margin:6px 0 0 16px; padding:0;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        
        <div class="verif-section">
            <div class="verif-section-title">
                <i class="bi bi-images"></i> File Portofolio
            </div>
            <div class="verif-section-desc">
                Upload 3–10 karya terbaikmu. Sertakan minimal 1 file WIP (work-in-progress) atau sketch layer.
                Format: JPG, PNG, PSD, PDF · Maks 20MB per file.
            </div>

            <div class="verif-upload-area" id="portfolioDropZone">
                <input type="file" name="portfolio_files[]" id="portfolioFiles"
                    accept="image/*,.pdf,.psd,.psb" multiple required
                    onchange="updatePortfolioPreview(this)">
                <i class="bi bi-cloud-arrow-up" style="font-size:28px; color:var(--muted); display:block; margin-bottom:8px;"></i>
                <div style="font-size:13px; color:var(--muted);">Klik atau drag file ke sini</div>
                <div style="font-size:11px; color:var(--muted); margin-top:4px;">Minimal 3 file, maksimal 10 file</div>
            </div>

            <div id="portfolioPreview" class="verif-file-preview"></div>
        </div>

        
        <div class="verif-section">
            <div class="verif-section-title">
                <i class="bi bi-globe2"></i> Link Sosial Media / Portfolio Online
            </div>
            <div class="verif-section-desc">
                Tambahkan minimal 1 link (Instagram, ArtStation, Behance, Twitter/X, dll) yang menampilkan karya dan aktivitas aslimu.
            </div>

            <div id="socialLinksContainer">
                <div class="verif-social-row">
                    <input type="url" name="social_media_links[]"
                        class="form-input" placeholder="https://instagram.com/username"
                        style="margin-bottom:0;" required>
                    <button type="button" class="verif-remove-link" onclick="removeSocialRow(this)" style="display:none;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <button type="button" class="verif-add-link" onclick="addSocialRow()">
                <i class="bi bi-plus"></i> Tambah Link
            </button>
        </div>

        
        <div class="verif-section">
            <div class="verif-section-title">
                <i class="bi bi-shield-check"></i> Pernyataan
            </div>
            <label class="verif-checkbox-row">
                <input type="checkbox" name="declaration" value="1" required>
                <span>
                    Saya menyatakan bahwa semua karya yang diupload adalah hasil kerja saya sendiri,
                    bukan hasil AI generatif, dan saya berhak atas karya tersebut.
                </span>
            </label>
        </div>

        <button type="submit" class="btn-verif-submit">
            <i class="bi bi-send-check"></i>
            <?php echo e($canResubmit ? 'Submit Ulang Verifikasi' : 'Ajukan Verifikasi'); ?>

        </button>

    </form>
    <?php endif; ?>

    
    <?php if($verif && $verifStatus === 'rejected' && !$canResubmit && $daysLeft > 0): ?>
    <div style="text-align:center; padding:40px; color:var(--muted); font-size:14px;">
        <i class="bi bi-clock" style="font-size:36px; opacity:.2; display:block; margin-bottom:12px;"></i>
        Perbaiki karyamu dan kembali dalam <strong style="color:var(--text);"><?php echo e($daysLeft); ?> hari</strong>.
    </div>
    <?php endif; ?>

</div>

</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    window.artistDashboard = {
        monthlyLabels: <?php echo json_encode(array_slice($monthlyLabels, -6), 512) ?>,
        monthlyEarnings: <?php echo json_encode(array_slice($monthlyEarnings, -6), 512) ?>,
    };
</script>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/dashboards/artist.js'); ?>

<script>
    /* ── Portfolio upload preview ── */
    function updatePortfolioPreview(input) {
        const preview = document.getElementById('portfolioPreview');
        preview.innerHTML = '';
        const files = Array.from(input.files);
        files.forEach(file => {
            const item = document.createElement('div');
            item.className = 'verif-file-item';
            const isImage = file.type.startsWith('image/');
            if (isImage) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                item.appendChild(img);
            } else {
                const icon = document.createElement('i');
                icon.className = 'bi bi-file-earmark-richtext';
                icon.style.cssText = 'font-size:24px; color:var(--accent);';
                item.appendChild(icon);
            }
            const name = document.createElement('span');
            name.textContent = file.name.length > 18 ? file.name.substring(0, 15) + '...' : file.name;
            item.appendChild(name);
            preview.appendChild(item);
        });
    }

    /* ── Social link rows ── */
    function addSocialRow() {
        const container = document.getElementById('socialLinksContainer');
        const row = document.createElement('div');
        row.className = 'verif-social-row';
        row.innerHTML = `
        <input type="url" name="social_media_links[]" class="form-input"
            placeholder="https://..." style="margin-bottom:0;">
        <button type="button" class="verif-remove-link" onclick="removeSocialRow(this)">
            <i class="bi bi-trash"></i>
        </button>`;
        container.appendChild(row);
        // Show remove button on first row if more than 1 row
        updateRemoveButtons();
    }

    function removeSocialRow(btn) {
        btn.closest('.verif-social-row').remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.verif-social-row');
        rows.forEach((row, i) => {
            const btn = row.querySelector('.verif-remove-link');
            if (btn) btn.style.display = rows.length > 1 ? 'flex' : 'none';
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/dashboards/artist.blade.php ENDPATH**/ ?>