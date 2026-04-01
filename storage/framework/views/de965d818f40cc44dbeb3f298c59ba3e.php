<div class="artwork-popup" id="editProfilePopup">
    <div class="artwork-popup-content">
        <span class="close-edit" style="position:absolute;top:10px;right:15px;font-size:24px;cursor:pointer;">&times;</span>
        <h5>Edit Profile</h5>
        <form action="<?php echo e(route('profile.update-popup', $user->id)); ?>" method="POST" enctype="multipart/form-data" style="padding:15px;">
            <?php echo csrf_field(); ?>
            <label for="name">Name</label>
            <input type="text" name="name" value="<?php echo e($user->name); ?>" class="form-control mb-2">

            <label for="profile_picture">Profile Photo</label>
            <input type="file" name="profile_picture" accept="image/*" class="form-control mb-2">

            <button type="submit" class="btn btn-primary w-100">Save</button>
        </form>

        <hr>

        <h5>Share Profile</h5>
        <input type="text" value="<?php echo e(url('/profile/'.$user->id)); ?>" readonly class="form-control mb-2">
        <button onclick="navigator.clipboard.writeText('<?php echo e(url('/profile/'.$user->id)); ?>')" class="btn btn-secondary w-100">Copy Link</button>
    </div>
</div>

<script>
const editProfilePopup = document.getElementById('editProfilePopup');
const closeEditProfile = document.querySelector('#editProfilePopup .close-edit');

closeEditProfile.addEventListener('click', ()=>{
    editProfilePopup.style.display='none';
});

// Tombol di profile page untuk buka popup
const editProfileBtn = document.querySelector('.btn-edit-profile');
const shareProfileBtn = document.querySelector('.btn-share-profile');

if(editProfileBtn){
    editProfileBtn.addEventListener('click', ()=>{
        editProfilePopup.style.display='flex';
    });
}

if(shareProfileBtn){
    shareProfileBtn.addEventListener('click', ()=>{
        editProfilePopup.style.display='flex';
    });
});
</script>

<style>
.artwork-popup {
    display: none;
    position: fixed;
    top:0; left:0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    justify-content: center;
    align-items: center;
    z-index: 10000;
}
.artwork-popup-content {
    background: #fff;
    width: 90%;
    max-width: 400px;
    border-radius: 10px;
    padding: 20px;
    position: relative;
}
</style>
<?php /**PATH C:\Users\hana fathia\Documents\aneris\resources\views/page/editprofile.blade.php ENDPATH**/ ?>