document.addEventListener("DOMContentLoaded", function () {

    const orderPopup = document.getElementById('orderPopup');
    const orderServiceId = document.getElementById('orderServiceId');
    const orderTitle = document.getElementById('orderTitle');
    const orderPrice = document.getElementById('orderPrice');

    if (!orderPopup) return;

    // =========================
    // ORDER DARI SHOW POPUP
    // =========================
    document.addEventListener('click', function (e) {

        // dari show popup
        if (e.target.id === 'orderBtn') {

            const btn = e.target;

            orderServiceId.value = btn.dataset.id;
            orderTitle.innerText = btn.dataset.title;
            orderPrice.innerText = "Rp " + btn.dataset.price;

            document.getElementById('servicePopup').style.display = 'none';
            orderPopup.style.display = 'flex';
        }

        // dari detail page (kalau ada tombol)
        if (e.target.id === 'orderNowBtn') {

            const btn = e.target;

            orderServiceId.value = btn.dataset.id;
            orderTitle.innerText = btn.dataset.title;
            orderPrice.innerText = "Rp " + btn.dataset.price;

            orderPopup.style.display = 'flex';
        }

    });

    // =========================
    // CLOSE
    // =========================
    document.querySelectorAll('.close-popup').forEach(el => {
        el.addEventListener('click', () => {
            el.closest('.artwork-popup').style.display = 'none';
        });
    });

    // klik background
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('artwork-popup')) {
            e.target.style.display = 'none';
        }
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const orderPopup = document.getElementById("orderPopup");
    const paymentPopup = document.getElementById("paymentPopup");

    const orderNowBtn = document.getElementById("orderNowBtn");
    const continueBtn = document.getElementById("continueToPayment");

    // input order
    const orderServiceId = document.getElementById("orderServiceId");
    const orderTitle = document.getElementById("orderTitle");
    const orderPrice = document.getElementById("orderPrice");

    // payment display
    const payServiceId = document.getElementById("payServiceId");
    const payTitle = document.getElementById("payTitle");
    const payPrice = document.getElementById("payPrice");
    const payNote = document.getElementById("payNote");
    const payMethod = document.getElementById("payMethod");

    // buka order popup dari DETAIL
    if (orderNowBtn) {
        orderNowBtn.addEventListener("click", () => {
            orderServiceId.value = orderNowBtn.dataset.id;
            orderTitle.innerText = orderNowBtn.dataset.title;
            orderPrice.innerText = "Rp " + orderNowBtn.dataset.price;

            orderPopup.style.display = "flex";
        });
    }

    // lanjut ke payment popup
    if (continueBtn) {
        continueBtn.addEventListener("click", () => {

            const note = document.querySelector('textarea[name="note"]').value;
            const method = document.querySelector('select[name="payment_method"]').value;

            payServiceId.value = orderServiceId.value;
            payTitle.innerText = orderTitle.innerText;
            payPrice.innerText = orderPrice.innerText;
            payNote.innerText = note || "-";
            payMethod.innerText = method || "-";

            orderPopup.style.display = "none";
            paymentPopup.style.display = "flex";
        });
    }

    // close semua popup
    document.querySelectorAll(".close-popup").forEach(btn => {
        btn.addEventListener("click", () => {
            btn.closest(".artwork-popup").style.display = "none";
        });
    });

    // klik background
    document.querySelectorAll(".artwork-popup").forEach(popup => {
        popup.addEventListener("click", e => {
            if (e.target === popup) {
                popup.style.display = "none";
            }
        });
    });

});
