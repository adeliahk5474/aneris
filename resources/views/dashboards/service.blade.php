<style>
    /* ================= FIX POPUP SERVICE ================= */
.artwork-popup-content {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    padding: 0; /* biar rapi */
}

/* HEADER tetap */
.popup-header {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

/* BODY bisa scroll */
.popup-body {
    overflow-y: auto;
    flex: 1;
}

/* IMAGE biar kayak IG */
.popup-body img {
    width: 100%;
    max-height: 350px;
    object-fit: cover;
    display: block;
}
</style>
   SERVICE POPUP (ARTIST)
<div class="artwork-popup" id="servicePopup">
    <div class="artwork-popup-content">

        {{-- HEADER --}}
        <div class="popup-header">
            <div class="user">
                <div class="avatar">
                    <img src="" id="serviceAvatar">
                </div>
                <span id="serviceUserName"></span>
            </div>

            <div class="dropdown-container">
                <svg id="serviceDots" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    style="cursor:pointer;width:24px;height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 12h.01M12 12h.01M18 12h.01" />
                </svg>

                <div class="dropdown-menu" id="serviceDropdown">
                    <button id="editServiceBtn">Edit</button>
                    <button id="deleteServiceBtn">Delete</button>
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="popup-body">
            <img src="" id="serviceImage">

            <div style="padding:10px;">
                <h6 id="serviceTitle"></h6>
                <p id="servicePrice" style="font-weight:600;"></p>
                <p id="serviceDescription" style="font-size:13px;color:#555;"></p>
            </div>
        </div>

    </div>
</div>


{{-- =========================
   EDIT SERVICE MODAL
========================= --}}
<div id="editServiceModal" class="artwork-popup">
    <div class="artwork-popup-content">
        <span class="close-popup">&times;</span>

        <form id="editServiceForm" method="POST"
            action="{{ route('commission.update', ['id'=>0]) }}"
            enctype="multipart/form-data" style="padding:15px;">

            @csrf
            @method('PUT')

            <input type="hidden" name="service_id" id="editServiceId">

            <label>Title</label>
            <input type="text" name="title" id="editServiceTitle" class="form-control mb-2">

            <label>Price</label>
            <input type="number" name="price" id="editServicePrice" class="form-control mb-2">

            <label>Description</label>
            <textarea name="description" id="editServiceDescription"
                rows="3" class="form-control mb-2"></textarea>

            <label>Image</label>
            <input type="file" name="image" class="form-control mb-2">

            <button type="submit" class="btn btn-primary w-100">Update</button>
        </form>
    </div>
</div>


{{-- =========================
   DELETE SERVICE MODAL
========================= --}}
<div id="deleteServiceModal" class="artwork-popup">
    <div class="artwork-popup-content" style="padding:20px;">
        <p>Are you sure you want to delete this service?</p>

        <div style="display:flex;gap:10px;margin-top:10px;">
            <form id="deleteServiceForm" method="POST"
                action="{{ route('commission.delete', ['id'=>0]) }}">

                @csrf
                @method('DELETE')

                <input type="hidden" name="service_id" id="deleteServiceId">

                <button type="submit" class="btn btn-danger">Delete</button>
            </form>

            <button id="cancelServiceDelete" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>

