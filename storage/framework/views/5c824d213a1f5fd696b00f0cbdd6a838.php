

<?php $__env->startSection('content'); ?>

<style>
/* POPUP OVERLAY */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1050;
}

/* POPUP CONTENT */
.popup-content {
    background: #fff;
    padding: 20px;
    width: 90%;
    max-width: 400px;
    border-radius: 10px;
    position: relative;
}

/* CLOSE BUTTON */
.popup-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
}

/* FORM */
.popup-content form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.popup-content label {
    font-weight: 600;
    font-size: 14px;
}

.popup-content input[type="file"] {
    padding: 5px;
}

.popup-content button {
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
}

/* SHARE BUTTONS */
.share-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.share-options button {
    padding: 10px;
    font-size: 16px;
    cursor: pointer;
}
</style>

<div class="container-fluid" style="padding-top:50px;">

    
    <div id="editProfilePopup" class="popup-overlay">
        <div class="popup-content">
            <span class="popup-close" id="closeEditProfile">&times;</span>
            <h5>Edit Profile</h5>
            <form action="<?php echo e(route('profile.update', $user->user_id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <label for="profile_picture">Change Profile Photo</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    
    <div id="shareProfilePopup" class="popup-overlay">
        <div class="popup-content">
            <span class="popup-close" id="closeShareProfile">&times;</span>
            <h5>Share Profile</h5>
            <div class="share-options">
                <button onclick="navigator.clipboard.writeText('<?php echo e(url()->current()); ?>')">Copy Profile Link</button>
                <button onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo e(url()->current()); ?>','_blank')">Share to Facebook</button>
                <button onclick="window.open('https://twitter.com/intent/tweet?url=<?php echo e(url()->current()); ?>','_blank')">Share to Twitter</button>
                <!-- Bisa tambah opsi lain -->
            </div>
        </div>
    </div>

</div>

<script>
// OPEN POPUP
document.querySelectorAll('.btn-edit-profile').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editProfilePopup').style.display = 'flex';
    });
});
document.querySelectorAll('.btn-share-profile').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('shareProfilePopup').style.display = 'flex';
    });
});

// CLOSE POPUP
document.getElementById('closeEditProfile').addEventListener('click', () => {
    document.getElementById('editProfilePopup').style.display = 'none';
});
document.getElementById('closeShareProfile').addEventListener('click', () => {
    document.getElementById('shareProfilePopup').style.display = 'none';
});

// TUTUP JIKA KLIK DI LUAR CONTENT
window.addEventListener('click', (e) => {
    if(e.target.classList.contains('popup-overlay')) {
        e.target.style.display = 'none';
    }
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/page/popup_profile.blade.php ENDPATH**/ ?>