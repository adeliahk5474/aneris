// resources/js/pages/verification/index.js
// Halaman index hanya punya tabel + filter — tidak butuh banyak JS
// Row click sudah dihandle via onclick di blade

// Highlight row yang baru saja direview (via URL param ?highlight=id)
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const highlightId = params.get('highlight');
    if (highlightId) {
        const rows = document.querySelectorAll('.verif-row');
        rows.forEach(row => {
            if (row.textContent.includes('#' + highlightId)) {
                row.style.background = 'rgba(139,92,246,0.08)';
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
