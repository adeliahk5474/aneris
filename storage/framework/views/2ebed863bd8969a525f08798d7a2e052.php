<?php $__env->startSection('content'); ?>

<style>
    /* ================= HEADER PROFILE ================= */
    .profile-header {
        display: flex;
        align-items: center;
        padding: 15px 15px 5px;
    }

    .profile-photo {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        background: #ddd;
        overflow: hidden;
    }

    .profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info {
        margin-left: 15px;
    }

    .profile-info h5 {
        font-size: 18px;
        font-weight: 600;
    }

    .stats {
        display: flex;
        justify-content: space-around;
        text-align: center;
        padding: 10px 0;
    }

    .stats b {
        font-size: 15px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin: 10px;
    }

    .action-buttons a,
    .action-buttons button {
        flex: 1;
        padding: 7px 0;
        font-size: 14px;
    }

    /* ================= TABS ================= */
    .profile-tabs {
        display: flex;
        justify-content: space-around;
        border-bottom: 1px solid #ddd;
        margin-bottom: 10px;
    }

    .profile-tabs a {
        padding: 10px;
        flex: 1;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .profile-tabs a.active {
        border-bottom: 2px solid #000;
    }

    /* ================= ARTWORK GRID ================= */
    .grid-art {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 7px;
        padding: 10px 10px 70px 10px;
    }

    .grid-art img {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #000;
        cursor: pointer;
    }

    /* ================= TAB CONTENT GENERIC ================= */
    .tab-content {
        display: none;
        padding: 10px;
    }

    /* ================= SLIDE MENU ================= */
    .menu-container {
        margin-left: auto;
        position: relative;
    }

    .hamburger {
        width: 32px;
        height: 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        position: relative;
        z-index: 9999;
    }

    .hamburger span {
        height: 3px;
        background: #000;
        border-radius: 3px;
        transition: 0.3s;
    }

    .hamburger.active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 6px);
    }

    .hamburger.active span:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active span:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -7px);
    }

    .side-menu {
        position: fixed;
        top: 0;
        right: -30%;
        width: 30%;
        height: 100%;
        background: #fff;
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.15);
        transition: right 0.35s ease;
        padding-top: 60px;
        z-index: 9990;
    }

    .side-menu.active {
        right: 0;
    }

    .menu-item {
        width: 100%;
        text-align: left;
        border: none;
        background: transparent;
        padding: 15px 20px;
        font-size: 16px;
    }

    .menu-item:hover {
        background: #f3f3f3;
        cursor: pointer;
    }

    /* ================= POPUP MODAL ================= */
    .artwork-popup,
    #editProfilePopup,
    #shareProfilePopup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 10000;
    }

    .artwork-popup-content,
    .edit-share-popup-content {
        background: #fff;
        width: 90%;
        max-width: 450px;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        padding: 15px;
    }

    .close-popup {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
    }
</style>

<div class="container-fluid">

    
    <div class="profile-header">
        <div class="profile-photo">
            <img src="<?php echo e($user->avatar ? asset($user->avatar) : 'https://via.placeholder.com/120'); ?>" alt="Profile Photo">
        </div>
        <div class="profile-info">
            <h5 class="fw-bold mb-1"><?php echo e($user->name ?? 'Unknown'); ?></h5>
            <small class="text-muted"><?php echo e(ucfirst($user->role)); ?></small>
        </div>

        <?php if($isOwner): ?>
        <div class="menu-container">
            <div class="hamburger" id="hamburgerBtn">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="side-menu" id="sideMenu">
                <form action="<?php echo e(route('auth.logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="menu-item">Logout</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="stats">
        <div><b><?php echo e($artworks->count()); ?></b><br>Posts</div>
        <div><b>0</b><br>Followers</div>
        <div><b>0</b><br>Following</div>
    </div>

    
    <?php if($user->role === 'artist' && $isOwner): ?>
    <div class="px-3">
        <a href="<?php echo e(route('artist.dashboard')); ?>" class="btn btn-dark w-100">Dashboard Artist</a>
    </div>
    <?php endif; ?>

    
    <div class="action-buttons">
        <?php if($isOwner): ?>
        <button type="button" class="btn btn-outline-secondary btn-edit-profile">Edit Profile</button>
        <button type="button" class="btn btn-outline-secondary btn-share-profile">Share Profile</button>
        <?php else: ?>
        <button class="btn btn-primary">Follow</button>
        <button class="btn btn-dark">Message</button>
        <?php endif; ?>
    </div>

    
    <div class="profile-tabs">
        <a class="active" id="tab-artwork">Artwork</a>
        <?php if($user->role === 'artist'): ?>
        <a id="tab-commission">Commission</a>
        <a id="tab-reviews">Reviews</a>
        <?php else: ?>
        <?php if($isOwner): ?>
        <a id="tab-orders">Orders</a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <div id="tab-artwork-content" class="grid-art tab-content" style="display:grid;">
        <?php $__currentLoopData = $artworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="javascript:void(0)" class="artwork-trigger"
            data-id="<?php echo e($art->artwork_id); ?>"
            data-caption="<?php echo e($art->caption); ?>"
            data-image="<?php echo e($art->image_url); ?>"
            data-likes="<?php echo e($art->likes ?? 0); ?>"
            data-comments="<?php echo e($art->comments ?? 0); ?>"
            data-shares="<?php echo e($art->shares ?? 0); ?>"
            data-user-name="<?php echo e($art->user->name ?? 'Unknown Artist'); ?>"
            data-user-avatar="<?php echo e($art->user->avatar ?? '/default-avatar.png'); ?>">
            <img src="<?php echo e($art->image_url); ?>" alt="Artwork">
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>


    <div id="tab-commission-content" class="tab-content" style="display:none;">
        <div class="grid-art">
            <?php $__empty_1 = true; $__currentLoopData = $commissionServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div>
                <img src="<?php echo e($service->image_url); ?>" alt="Service">
                <p style="font-size:12px;margin-top:4px;">
                    <?php echo e($service->title); ?>

                </p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>No commissions yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php if($user->role !== 'artist' && $isOwner): ?>
    <div id="tab-reviews-content" class="tab-content">
        <p>No reviews yet.</p>
    </div>

    <div id="tab-orders-content" class="tab-content">
        <p>No orders yet.</p>
    </div>
    <?php endif; ?>

</div>


<div class="artwork-popup" id="artworkPopup">
    <div class="artwork-popup-content">
        <div class="popup-header">
            <div class="user">
                <div class="avatar">
                    <img src="" id="popupAvatar">
                </div>
                <span id="popupUserName"></span>
            </div>
            <?php if($isOwner): ?>
            <div class="dropdown-container">
                <svg id="popupDots" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6" style="cursor:pointer;width:24px;height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M12 12h.01M18 12h.01" />
                </svg>
                <div class="dropdown-menu" id="popupDropdown">
                    <button id="editBtn">Edit</button>
                    <button id="deleteBtn">Delete</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="popup-body">
            <img src="" id="popupImage">
            <p id="popupCaption" style="padding:10px 12px;"></p>
        </div>
        <div class="popup-footer">
            <div class="popup-buttons">
                <div class="icon-group">
                    <?php echo e(svg('heroicon-o-heart')); ?>
                    <span id="popupLikes">0</span>
                </div>
                <div class="icon-group">
                    <?php echo e(svg('heroicon-o-chat-bubble-left')); ?>
                    <span id="popupComments">0</span>
                </div>
                <div class="icon-group">
                    <?php echo e(svg('heroicon-o-paper-airplane')); ?>
                    <span id="popupShares">0</span>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="editModal" class="artwork-popup">
    <div class="artwork-popup-content">
        <span class="close-edit" class="close-popup">&times;</span>
        <form id="editForm" method="POST" action="<?php echo e(route('artwork.update', ['id'=>0])); ?>" enctype="multipart/form-data" style="padding:15px;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="artwork_id" id="editArtworkId">
            <label>Caption</label>
            <textarea name="caption" id="editCaption" rows="3" class="form-control"></textarea>
            <label>Image</label>
            <input type="file" name="image" class="form-control mb-2">
            <button type="submit" class="btn btn-primary w-100">Simpan</button>
        </form>
    </div>
</div>


<div id="deleteModal" class="artwork-popup">
    <div class="artwork-popup-content" style="padding:20px;">
        <p>Are you sure you want to delete this artwork?</p>
        <div style="display:flex;gap:10px;margin-top:10px;">
            <form id="deleteForm" method="POST" action="<?php echo e(route('artwork.delete', ['id'=>0])); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <input type="hidden" name="artwork_id" id="deleteArtworkId">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <button id="cancelDelete" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>


<div id="editProfilePopup" class="artwork-popup">
    <div class="edit-share-popup-content">
        <span class="close-popup">&times;</span>
        <h5>Edit Profile</h5>
        <form method="POST" action="<?php echo e(route('profile.update-popup', $user->user_id)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <label>Name</label>
            <input type="text" name="name" value="<?php echo e($user->name); ?>" class="form-control mb-2">
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" accept="image/*" class="form-control mb-2">
            <button type="submit" class="btn btn-primary w-100">Save</button>
        </form>
    </div>
</div>


<div id="shareProfilePopup" class="artwork-popup">
    <div class="edit-share-popup-content">
        <span class="close-popup">&times;</span>
        <h5>Share Profile</h5>
        <p>Copy link to share your profile:</p>
        <input type="text" readonly class="form-control mb-2" value="<?php echo e(url()->current()); ?>">
        <button class="btn btn-primary w-100" onclick="copyProfileLink()">Copy Link</button>
    </div>
</div>

<?php echo $__env->make('layouts.botnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
    const hamb = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('sideMenu');
    hamb.addEventListener('click', () => {
        hamb.classList.toggle('active');
        menu.classList.toggle('active');
    });

    // TABS INTERAKTIF
    const tabs = document.querySelectorAll('.profile-tabs a');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const contents = ['tab-artwork-content', 'tab-commission-content', 'tab-reviews-content', 'tab-orders-content'];
            contents.forEach(c => {
                const el = document.getElementById(c);
                if (el) el.style.display = 'none';
            });
            const el = document.getElementById(tab.id + '-content');
            if (el) el.style.display = (tab.id === 'tab-artwork') ? 'grid' : 'block';
        });
    });

    // POPUP ARTWORK
    const artworkTriggers = document.querySelectorAll('.artwork-trigger');
    const artworkPopup = document.getElementById('artworkPopup');
    const popupImage = document.getElementById('popupImage');
    const popupCaption = document.getElementById('popupCaption');
    const popupUserName = document.getElementById('popupUserName');
    const popupAvatar = document.getElementById('popupAvatar');
    const popupLikes = document.getElementById('popupLikes');
    const popupComments = document.getElementById('popupComments');
    const popupShares = document.getElementById('popupShares');

    artworkTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            popupImage.src = trigger.dataset.image;
            popupCaption.innerText = trigger.dataset.caption;
            popupUserName.innerText = trigger.dataset.userName;
            popupAvatar.src = trigger.dataset.userAvatar;
            popupLikes.innerText = trigger.dataset.likes;
            popupComments.innerText = trigger.dataset.comments;
            popupShares.innerText = trigger.dataset.shares;
            artworkPopup.style.display = 'flex';
        });
    });

    artworkPopup.addEventListener('click', e => {
        if (e.target === artworkPopup) artworkPopup.style.display = 'none';
    });

    // DROPDOWN TITIK 3
    const popupDots = document.getElementById('popupDots');
    const popupDropdown = document.getElementById('popupDropdown');
    if (popupDots) {
        popupDots.addEventListener('click', () => {
            popupDropdown.style.display = (popupDropdown.style.display === 'flex') ? 'none' : 'flex';
            popupDropdown.style.flexDirection = 'column';
        });
    }

    // EDIT ARTWORK
    const editModal = document.getElementById('editModal');
    const editBtn = document.getElementById('editBtn');
    const editArtworkId = document.getElementById('editArtworkId');
    const editCaption = document.getElementById('editCaption');
    if (editBtn) {
        editBtn.addEventListener('click', () => {
            editArtworkId.value = popupImage.dataset.id;
            editCaption.value = popupCaption.innerText;
            editModal.style.display = 'flex';
            artworkPopup.style.display = 'none';
        });
    }
    document.querySelectorAll('.close-edit, .close-popup').forEach(el => {
        el.addEventListener('click', () => {
            el.closest('.artwork-popup').style.display = 'none';
        });
    });

    // DELETE ARTWORK
    const deleteModal = document.getElementById('deleteModal');
    const deleteBtn = document.getElementById('deleteBtn');
    const deleteArtworkId = document.getElementById('deleteArtworkId');
    const cancelDelete = document.getElementById('cancelDelete');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            deleteArtworkId.value = popupImage.dataset.id;
            deleteModal.style.display = 'flex';
            artworkPopup.style.display = 'none';
        });
    }
    cancelDelete.addEventListener('click', () => deleteModal.style.display = 'none');

    // EDIT PROFILE POPUP
    const btnEditProfile = document.querySelector('.btn-edit-profile');
    const editProfilePopup = document.getElementById('editProfilePopup');
    btnEditProfile.addEventListener('click', () => {
        editProfilePopup.style.display = 'flex';
    });

    // SHARE PROFILE POPUP
    const btnShareProfile = document.querySelector('.btn-share-profile');
    const shareProfilePopup = document.getElementById('shareProfilePopup');
    btnShareProfile.addEventListener('click', () => {
        shareProfilePopup.style.display = 'flex';
    });

    // COPY LINK
    function copyProfileLink() {
        const input = shareProfilePopup.querySelector('input');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value);
        alert('Profile link copied!');
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/dashboards/profile.blade.php ENDPATH**/ ?>