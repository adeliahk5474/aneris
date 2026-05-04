document.addEventListener("DOMContentLoaded", function () {

    const serviceTriggers = document.querySelectorAll('.service-trigger');
    const servicePopup = document.getElementById('servicePopup');

    const serviceImage = document.getElementById('serviceImage');
    const serviceTitle = document.getElementById('serviceTitle');
    const servicePrice = document.getElementById('servicePrice');
    const serviceDescription = document.getElementById('serviceDescription');
    const serviceUserName = document.getElementById('serviceUserName');
    const serviceAvatar = document.getElementById('serviceAvatar');

    const viewDetailBtn = document.getElementById('viewDetailBtn');
    const orderBtn = document.getElementById('orderBtn');

    if (!servicePopup) return;

    serviceTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {

            const id = trigger.dataset.id;

            serviceImage.src = trigger.dataset.image;
            serviceTitle.innerText = trigger.dataset.title;
            servicePrice.innerText = "Rp " + trigger.dataset.price;
            serviceDescription.innerText = trigger.dataset.description;
            serviceUserName.innerText = trigger.dataset.userName;
            serviceAvatar.src = trigger.dataset.userAvatar;

            // tombol
            viewDetailBtn.href = "/commission/" + id;

            // kirim data ke tombol order
            orderBtn.dataset.id = id;
            orderBtn.dataset.title = trigger.dataset.title;
            orderBtn.dataset.price = trigger.dataset.price;

            servicePopup.style.display = 'flex';
        });
    });

    // close popup
    servicePopup.addEventListener('click', e => {
        if (e.target === servicePopup) {
            servicePopup.style.display = 'none';
        }
    });

    document.querySelectorAll('.close-popup').forEach(el => {
        el.addEventListener('click', () => {
            el.closest('.artwork-popup').style.display = 'none';
        });
    });

});
