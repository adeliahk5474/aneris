document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // ELEMENT
    // =========================
    const serviceTriggers = document.querySelectorAll('.service-trigger');
    const servicePopup = document.getElementById('servicePopup');

    const serviceImage = document.getElementById('serviceImage');
    const serviceTitle = document.getElementById('serviceTitle');
    const servicePrice = document.getElementById('servicePrice');
    const serviceDescription = document.getElementById('serviceDescription');
    const serviceUserName = document.getElementById('serviceUserName');
    const serviceAvatar = document.getElementById('serviceAvatar');

    const serviceDots = document.getElementById('serviceDots');
    const serviceDropdown = document.getElementById('serviceDropdown');

    const editServiceModal = document.getElementById('editServiceModal');
    const editServiceBtn = document.getElementById('editServiceBtn');

    const deleteServiceModal = document.getElementById('deleteServiceModal');
    const deleteServiceBtn = document.getElementById('deleteServiceBtn');

    const editServiceId = document.getElementById('editServiceId');
    const editServiceTitle = document.getElementById('editServiceTitle');
    const editServicePrice = document.getElementById('editServicePrice');
    const editServiceDescription = document.getElementById('editServiceDescription');

    const deleteServiceId = document.getElementById('deleteServiceId');
    const cancelServiceDelete = document.getElementById('cancelServiceDelete');

    // =========================
    // OPEN POPUP
    // =========================
    serviceTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {

            serviceImage.src = trigger.dataset.image;
            serviceTitle.innerText = trigger.dataset.title;
            servicePrice.innerText = "Rp " + trigger.dataset.price;
            serviceDescription.innerText = trigger.dataset.description;
            serviceUserName.innerText = trigger.dataset.userName;
            serviceAvatar.src = trigger.dataset.userAvatar;

            serviceImage.dataset.id = trigger.dataset.id;

            servicePopup.style.display = 'flex';
        });
    });

    // =========================
    // CLOSE POPUP
    // =========================
    servicePopup.addEventListener('click', (e) => {
        if (e.target === servicePopup) {
            servicePopup.style.display = 'none';
        }
    });

    document.querySelectorAll('.close-popup').forEach(el => {
        el.addEventListener('click', () => {
            el.closest('.artwork-popup').style.display = 'none';
        });
    });

    // =========================
    // DROPDOWN
    // =========================
    if (serviceDots) {
        serviceDots.addEventListener('click', () => {
            serviceDropdown.style.display =
                (serviceDropdown.style.display === 'flex') ? 'none' : 'flex';
        });
    }

    // =========================
    // EDIT
    // =========================
    if (editServiceBtn) {
        editServiceBtn.addEventListener('click', () => {

            editServiceId.value = serviceImage.dataset.id;
            editServiceTitle.value = serviceTitle.innerText;
            editServicePrice.value = servicePrice.innerText.replace('Rp ', '');
            editServiceDescription.value = serviceDescription.innerText;

            editServiceModal.style.display = 'flex';
            servicePopup.style.display = 'none';
        });
    }

    // =========================
    // DELETE
    // =========================
    if (deleteServiceBtn) {
        deleteServiceBtn.addEventListener('click', () => {

            deleteServiceId.value = serviceImage.dataset.id;

            deleteServiceModal.style.display = 'flex';
            servicePopup.style.display = 'none';
        });
    }

    if (cancelServiceDelete) {
        cancelServiceDelete.addEventListener('click', () => {
            deleteServiceModal.style.display = 'none';
        });
    }

});
