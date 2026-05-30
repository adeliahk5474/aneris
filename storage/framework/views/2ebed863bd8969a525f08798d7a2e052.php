<?php $__env->startSection('title', ($user->name ?? 'Profile') . ' — Aneris'); ?>

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/css/dashboards/profile.css'); ?>


<div class="profile-wrap">
    <div class="profile-top">

        
        <div class="profile-avatar-wrap">
            <img src="<?php echo e($user->avatar ?? asset('images/default-avatar.png')); ?>"
                class="profile-avatar" alt="<?php echo e($user->name); ?>">
            <?php if($user->isVerifiedArtist()): ?>
            <span class="verified-badge">
                <i class="bi bi-patch-check-fill"></i> Verified Non-AI
            </span>
            <?php endif; ?>
        </div>

        
        <div class="profile-info">

            <div class="profile-name-row">
                <span class="profile-username"><?php echo e($user->name); ?></span>
                <?php if($isArtist): ?>
                <span class="profile-badge artist">Digital Artist</span>
                <?php else: ?>
                <span class="profile-badge">Client</span>
                <?php endif; ?>
                <?php if($user->country): ?>
                <span style="font-size:13px; color:var(--muted);">📍 <?php echo e($user->country); ?></span>
                <?php endif; ?>
            </div>

            
            <div class="profile-stats">
                <?php if($isArtist): ?>
                <div class="stat-item">
                    <span class="stat-num"><?php echo e($postCount); ?></span>
                    <span class="stat-label">Posts</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num"><?php echo e(number_format($followerCount)); ?></span>
                    <span class="stat-label">Followers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num"><?php echo e(number_format($followingCount)); ?></span>
                    <span class="stat-label">Following</span>
                </div>
                <?php else: ?>
                <div class="stat-item">
                    <span class="stat-num"><?php echo e($user->ordersAsClient()->count()); ?></span>
                    <span class="stat-label">Commissions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num"><?php echo e(number_format($followingCount)); ?></span>
                    <span class="stat-label">Following</span>
                </div>
                <?php endif; ?>
            </div>

            
            <?php if($user->bio): ?>
            <div class="profile-bio"><?php echo e($user->bio); ?></div>
            <?php endif; ?>

            
            <?php if(!$isArtist && count($clientBadges)): ?>
            <div class="client-badges">
                <?php $__currentLoopData = $clientBadges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="client-badge" style="color:<?php echo e($badge['color']); ?>; border-color:<?php echo e($badge['color']); ?>22;">
                    <i class="bi <?php echo e($badge['icon']); ?>"></i>
                    <?php echo e($badge['label']); ?>

                </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <?php if($isArtist && $reviewStats && $reviewStats['count'] > 0): ?>
            <div class="rating-summary">
                <div class="rating-summary-title">
                    ⭐ <?php echo e(number_format($reviewStats['avg_overall'],1)); ?> — <?php echo e($reviewStats['count']); ?> reviews
                </div>
                <?php
                $bars = [
                'Quality' => $reviewStats['avg_quality'] ?? 0,
                'Timeliness' => $reviewStats['avg_timeliness'] ?? 0,
                'Communication' => $reviewStats['avg_communication'] ?? 0,
                ];
                ?>
                <?php $__currentLoopData = $bars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rating-row">
                    <span class="rating-row-label"><?php echo e($label); ?></span>
                    <div class="rating-bar-wrap">
                        <div class="rating-bar-fill" style="width:<?php echo e(($val/5)*100); ?>%"></div>
                    </div>
                    <span class="rating-row-val"><?php echo e(number_format($val,1)); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            
            <?php if($isOwner): ?>
            <div class="profile-actions">
                <button class="btn-edit-profile"
                    onclick="document.getElementById('editProfileModal').classList.add('open')">
                    Edit Profile
                </button>
                <button class="btn-edit-profile">Share Profile</button>
            </div>
            <?php if($isArtist): ?>
            <a href="<?php echo e(route('artist.dashboard')); ?>" class="btn-dashboard">
                <i class="bi bi-grid-1x2"></i> Artist Dashboard
            </a>
            <?php endif; ?>
            <?php else: ?>
            <div class="profile-actions">
                <form action="<?php echo e(route('follow.toggle', $user->user_id)); ?>" method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-follow <?php echo e($isFollowing ? 'following' : ''); ?>">
                        <?php echo e($isFollowing ? 'Following' : 'Follow'); ?>

                    </button>
                </form>
                <a href="<?php echo e(route('chat.index', ['user_id' => $user->user_id])); ?>" class="btn-message-profile">
                    <i class="bi bi-chat"></i> Message
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>


<div class="profile-tabs">
    <?php if($isArtist): ?>
    <div class="profile-tab active" onclick="switchTab('artwork',this)">
        <i class="bi bi-grid-3x3"></i> Artwork
    </div>
    <div class="profile-tab" onclick="switchTab('commission',this)">
        <i class="bi bi-star"></i> Commission
    </div>
    <div class="profile-tab" onclick="switchTab('review',this)">
        <i class="bi bi-person-badge"></i> Reviews
    </div>
    <?php else: ?>
    <div class="profile-tab active" onclick="switchTab('artwork',this)">
        <i class="bi bi-image"></i> Artwork
    </div>
    <?php if($isOwner): ?>
    <div class="profile-tab" onclick="switchTab('dashboard',this)">
        <i class="bi bi-grid-1x2"></i> My Orders
    </div>
    <?php endif; ?>
    <div class="profile-tab" onclick="switchTab('review',this)">
        <i class="bi bi-star"></i> Reviews Given
    </div>
    <div class="profile-tab" onclick="switchTab('wishlist',this)">
        <i class="bi bi-bookmark"></i> Wishlist
    </div>
    <?php endif; ?>
</div>


<div class="tab-panel active" id="tab-artwork">
    <?php if($artworks->count()): ?>
    <div class="artwork-grid">
        <?php $__currentLoopData = $artworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="artwork-item">
            <img src="<?php echo e($art->image_url ?? asset('images/default-thumb.png')); ?>"
                alt="<?php echo e($art->caption ?? 'artwork'); ?>" loading="lazy">
            <div class="artwork-overlay">
                <span class="artwork-stat"><i class="bi bi-heart-fill"></i> 0</span>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="bi bi-image"></i>
        <p>Belum ada artwork.</p>
    </div>
    <?php endif; ?>
</div>


<?php if($isArtist): ?>
<div class="tab-panel" id="tab-commission">
    <?php if($commissionServices->count()): ?>
    <div class="commission-grid">
        <?php $__currentLoopData = $commissionServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('commission.show', $svc->service_id)); ?>" class="commission-card">
            <div class="commission-thumb">
                <img src="<?php echo e($svc->image_url ?? asset('images/default-thumb.png')); ?>"
                    alt="<?php echo e($svc->title); ?>" loading="lazy">
            </div>
            <div class="commission-info">
                <div class="commission-title"><?php echo e($svc->title); ?></div>
                <div class="commission-cat"><?php echo e($svc->category->name ?? ''); ?></div>
                <div class="commission-price">Rp <?php echo e(number_format($svc->base_price ?? 0, 0, ',', '.')); ?></div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="bi bi-star"></i>
        <p>Belum ada jasa commission.</p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>


<?php if(!$isArtist && $isOwner): ?>
<div class="tab-panel" id="tab-dashboard">
    <div class="client-dashboard">

        
        <div class="cl-stat-grid">
            <div class="cl-stat-card">
                <div class="cl-stat-icon orders"><i class="bi bi-bag-check"></i></div>
                <div>
                    <div class="cl-stat-label">Total Orders</div>
                    <div class="cl-stat-value"><?php echo e($clientOrders->count()); ?></div>
                </div>
            </div>
            <div class="cl-stat-card">
                <div class="cl-stat-icon spent"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <div class="cl-stat-label">Total Spent</div>
                    <div class="cl-stat-value">Rp <?php echo e(number_format($totalSpent/1000,0,'.','.')); ?>k</div>
                </div>
            </div>
            <div class="cl-stat-card">
                <div class="cl-stat-icon artists"><i class="bi bi-people"></i></div>
                <div>
                    <div class="cl-stat-label">Artists Hired</div>
                    <div class="cl-stat-value"><?php echo e($artistsHired); ?></div>
                </div>
            </div>
        </div>

        
        <div>
            <div class="cl-section-title">Semua Order</div>

            
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;">
                <?php $__currentLoopData = ['all'=>'Semua','pending'=>'Pending','in_progress'=>'In Progress','revision'=>'Revision','waiting_client'=>'Waiting','completed'=>'Selesai','canceled'=>'Dibatalkan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="chip <?php echo e($st === 'all' ? 'active' : ''); ?>"
                    onclick="filterClientOrders('<?php echo e($st); ?>', this)"
                    style="padding:5px 14px; border-radius:999px; font-size:12px; font-weight:500;
                                 border:1px solid var(--border2); background:var(--surface2); color:var(--muted);
                                 cursor:pointer; user-select:none;">
                    <?php echo e($label); ?>

                </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="cl-order-list" id="cl-orders-list">
                <?php $__empty_1 = true; $__currentLoopData = $clientOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('order.detail', $order->order_id)); ?>"
                    class="cl-order-item" data-status="<?php echo e($order->status); ?>">
                    <?php if($order->service && $order->service->image_url): ?>
                    <img src="<?php echo e($order->service->image_url); ?>" class="cl-order-thumb" alt="">
                    <?php else: ?>
                    <div class="cl-order-thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:22px;opacity:.3;">
                        <i class="bi bi-image"></i>
                    </div>
                    <?php endif; ?>
                    <div class="cl-order-info">
                        <div class="cl-order-title"><?php echo e($order->service->title ?? 'Commission'); ?></div>
                        <div class="cl-order-meta">
                            <span><?php echo e($order->artist->name ?? '—'); ?></span>
                            <span class="status-badge s-<?php echo e($order->status); ?>">
                                <?php
                                $statusLabel = [
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'in_progress' => 'In Progress',
                                'revision' => 'Revising',
                                'revision_requested' => 'Revision Requested',
                                'waiting_client' => 'Waiting Review',
                                'completed' => 'Completed',
                                'canceled' => 'Canceled',
                                ];
                                ?>
                                <?php echo e($statusLabel[$order->status] ?? ucfirst($order->status)); ?>

                            </span>
                            <?php if($order->revision_count > 0): ?>
                            <span style="font-size:10px; color:var(--red);">
                                Revisi <?php echo e($order->revision_count); ?>/<?php echo e($order->service->max_revisions ?? 3); ?>

                            </span>
                            <?php endif; ?>
                            <span><?php echo e($order->created_at->format('d M Y')); ?></span>
                        </div>
                    </div>
                    <div class="cl-order-price">Rp <?php echo e(number_format($order->total_price, 0, ',', '.')); ?></div>
                </a>

                
                <?php if($order->status === 'completed' && $isOwner): ?>
                <?php
                $alreadyReviewed = $order->reviews()
                ->where('reviewer_id', auth()->user()->user_id)
                ->exists();
                ?>
                <?php if(!$alreadyReviewed): ?>
                <div style="margin-top:-6px; margin-bottom:4px; padding:10px 14px;
                                        background:var(--accent-dim); border:1px dashed var(--accent);
                                        border-radius:0 0 10px 10px; font-size:12px; color:var(--accent);
                                        display:flex; align-items:center; justify-content:space-between;">
                    <span><i class="bi bi-star"></i> Beri rating untuk order ini</span>
                    <button class="btn-submit"
                        style="padding:6px 14px; font-size:12px; border-radius:6px;"
                        onclick="openReviewModal('<?php echo e($order->order_id); ?>', '<?php echo e($order->artist->name ?? 'Artist'); ?>')">
                        Review
                    </button>
                </div>
                <?php else: ?>
                <div class="review-blind-notice" style="margin-top:-6px; margin-bottom:4px; border-radius:0 0 10px 10px;">
                    <i class="bi bi-eye-slash"></i>
                    Review terkirim. Akan terlihat setelah artist juga submit atau 14 hari berlalu.
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state"><i class="bi bi-bag"></i>
                    <p>Belum ada order.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="tab-panel" id="tab-review">
    <?php if($reviews->count()): ?>
    <div class="review-list">
        <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="review-item">
            <div class="review-header">
                <img src="<?php echo e($review->reviewer->avatar ?? asset('images/default-avatar.png')); ?>"
                    class="review-avatar" alt="">
                <div class="review-meta">
                    <div class="review-name"><?php echo e($review->reviewer->name ?? 'Anonymous'); ?></div>
                    <div class="review-date">
                        <?php echo e($review->created_at->format('d M Y')); ?>

                        <?php if($review->order && $review->order->service): ?>
                        · <?php echo e($review->order->service->title); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <div class="review-stars">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <i class="bi <?php echo e($i <= ($review->rating ?? 0) ? 'bi-star-fill' : 'bi-star'); ?>"></i>
                        <?php endfor; ?>
                </div>
            </div>

            
            <?php if($review->rating_quality || $review->rating_timeliness || $review->rating_communication): ?>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:8px;">
                <?php if($review->rating_quality): ?>
                <span style="font-size:11px; color:var(--muted); background:var(--surface2); padding:3px 8px; border-radius:6px;">
                    Quality <?php echo e($review->rating_quality); ?>/5
                </span>
                <?php endif; ?>
                <?php if($review->rating_timeliness): ?>
                <span style="font-size:11px; color:var(--muted); background:var(--surface2); padding:3px 8px; border-radius:6px;">
                    Timeliness <?php echo e($review->rating_timeliness); ?>/5
                </span>
                <?php endif; ?>
                <?php if($review->rating_communication): ?>
                <span style="font-size:11px; color:var(--muted); background:var(--surface2); padding:3px 8px; border-radius:6px;">
                    Communication <?php echo e($review->rating_communication); ?>/5
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($review->comment): ?>
            <div class="review-text"><?php echo e($review->comment); ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="bi bi-star"></i>
        <p>Belum ada review.</p>
    </div>
    <?php endif; ?>
</div>


<?php if(!$isArtist): ?>
<div class="tab-panel" id="tab-wishlist">
    <div class="empty-state"><i class="bi bi-bookmark"></i>
        <p>Wishlist kosong.</p>
    </div>
</div>
<?php endif; ?>


<?php if(auth()->guard()->check()): ?>
<a href="<?php echo e(route('upload.popup')); ?>" class="fab" aria-label="Upload">
    <i class="bi bi-plus-lg"></i>
</a>
<?php endif; ?>


<div id="editProfileModal" class="modal-overlay">
    <div class="modal-card">
        <button class="modal-close"
            onclick="document.getElementById('editProfileModal').classList.remove('open')">
            <i class="bi bi-x"></i>
        </button>
        <div class="modal-title">Edit Profile</div>
        <form action="<?php echo e(route('profile.update-popup', $user->user_id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <label class="form-label">Nama</label>
            <input type="text" name="name" value="<?php echo e($user->name); ?>" class="form-input">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-textarea"><?php echo e($user->bio); ?></textarea>
            <label class="form-label">Avatar</label>
            <input type="file" name="profile_picture" accept="image/*"
                style="font-size:13px; color:var(--muted); margin-bottom:20px; display:block;">
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
        </form>
    </div>
</div>


<div id="reviewModal" class="modal-overlay">
    <div class="modal-card" style="max-width:500px;">
        <button class="modal-close"
            onclick="document.getElementById('reviewModal').classList.remove('open')">
            <i class="bi bi-x"></i>
        </button>
        <div class="modal-title">Review untuk <span id="reviewArtistName"></span></div>

        <form action="<?php echo e(route('review.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="order_id" id="reviewOrderId">

            
            <label class="form-label">Overall Rating *</label>
            <div class="star-group" style="margin-bottom:14px;">
                <?php for($i=5;$i>=1;$i--): ?>
                <input type="radio" name="overall_rating" id="or<?php echo e($i); ?>" value="<?php echo e($i); ?>" <?php echo e($i===5?'required':''); ?>>
                <label for="or<?php echo e($i); ?>" title="<?php echo e($i); ?> bintang">★</label>
                <?php endfor; ?>
            </div>

            
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px;">
                <?php $__currentLoopData = ['quality'=>'Kualitas','timeliness'=>'Ketepatan Waktu','communication'=>'Komunikasi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <label class="form-label"><?php echo e($lbl); ?></label>
                    <select name="rating_<?php echo e($key); ?>"
                        style="width:100%; background:var(--surface2); border:1px solid rgba(255,255,255,.1);
                                       border-radius:8px; padding:8px 10px; color:var(--text);
                                       font-family:'Outfit',sans-serif; font-size:13px; outline:none;">
                        <option value="">—</option>
                        <?php for($i=1;$i<=5;$i++): ?>
                            <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                            <?php endfor; ?>
                    </select>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <label class="form-label">Komentar (opsional)</label>
            <textarea name="comment" class="form-textarea" placeholder="Ceritakan pengalamanmu..."></textarea>

            <div class="review-blind-notice" style="margin-bottom:14px;">
                <i class="bi bi-eye-slash"></i>
                Review kamu tersembunyi dulu. Akan tampil setelah artist juga submit atau 14 hari berlalu.
            </div>

            <button type="submit" class="btn-submit">Kirim Review</button>
        </form>
    </div>
</div>

<?php echo app('Illuminate\Foundation\Vite')('resources/js/dashboards/profile.js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/dashboards/profile.blade.php ENDPATH**/ ?>