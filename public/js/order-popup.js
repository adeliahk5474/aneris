document.addEventListener("DOMContentLoaded", function () {

    const orderPopup = document.getElementById('orderPopup');
    const openOrderBtn = document.getElementById('openOrderBtn');

    const orderTitle = document.getElementById('orderTitle');
    const orderPrice = document.getElementById('orderPrice');
    const orderServiceId = document.getElementById('orderServiceId');

    // ambil data dari button
    if (openOrderBtn) {
        openOrderBtn.addEventListener('click', () => {

            orderTitle.innerText = openOrderBtn.dataset.title;
            orderPrice.innerText = "Rp " + openOrderBtn.dataset.price;
            orderServiceId.value = openOrderBtn.dataset.id;

            orderPopup.style.display = 'flex';
        });
    }

    // close popup
    document.querySelectorAll('.close-popup').forEach(el => {
        el.addEventListener('click', () => {
            orderPopup.style.display = 'none';
        });
    });

    // klik luar
    if (orderPopup) {
        orderPopup.addEventListener('click', e => {
            if (e.target === orderPopup) {
                orderPopup.style.display = 'none';
            }
        });
    }

});
