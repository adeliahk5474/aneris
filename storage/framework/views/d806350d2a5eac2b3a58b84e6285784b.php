

<style>
/* ================= SERVICE POPUP FIX ================= */
.service-popup-content {
    padding: 0 !important;
    overflow: hidden !important;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.service-popup-content img {
    display: block;
    width: 100%;
}

.service-popup-content .content-scroll {
    overflow-y: auto;
}

/* biar section bisa proporsional */
.service-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:10px 12px;
    border-bottom:1px solid #eee;
}

.service-user {
    display:flex;
    align-items:center;
    gap:8px;
}

.service-user img {
    width:32px;
    height:32px;
    border-radius:50%;
    object-fit:cover;
}

.service-image {
    width:100%;
    background:#000;
}

.service-image img {
    width:100%;
    max-height:300px;
    object-fit:contain;
}

.service-content {
    padding:12px;
    flex:1;
}

.service-actions {
    display:flex;
    gap:8px;
    padding:10px;
    border-top:1px solid #eee;
}
</style>

<div class="artwork-popup" id="servicePopup">
    <div class="artwork-popup-content service-popup-content">

        
        <div class="service-header">
            <div class="service-user">
                <img id="serviceAvatar">
                <span id="serviceUserName" style="font-weight:600;font-size:14px;"></span>
            </div>

            <span class="close-popup" style="cursor:pointer;font-size:20px;">&times;</span>
        </div>

        
        <div class="service-image">
            <img id="serviceImage">
        </div>

        
        <div class="content-scroll service-content">

            <h6 id="serviceTitle" style="font-weight:600;margin-bottom:5px;"></h6>

            <p id="servicePrice" style="font-weight:700;margin-bottom:8px;color:#000;"></p>

            <p id="serviceDescription"
                style="font-size:13px;color:#555;line-height:1.4;">
            </p>

        </div>

        
        <div class="service-actions">
            <button id="orderBtn"
                style="flex:1;background:#000;color:#fff;border:none;padding:10px;border-radius:6px;">
                Order
            </button>

            <a id="viewDetailBtn"
                href="#"
                style="flex:1;text-align:center;border:1px solid #000;padding:10px;border-radius:6px;text-decoration:none;color:#000;">
                View Detail
            </a>
        </div>

    </div>
</div>

<?php /**PATH C:\Users\User\Documents\ade\aneris\resources\views/commission/show.blade.php ENDPATH**/ ?>