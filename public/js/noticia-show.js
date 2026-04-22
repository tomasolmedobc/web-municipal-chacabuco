document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('pdfModal');
    const overlay = document.getElementById('pdfModalOverlay');
    const closeBtn = document.getElementById('pdfModalClose');
    const viewer = document.getElementById('pdfViewer');
    const title = document.getElementById('pdfModalTitle');
    const buttons = document.querySelectorAll('.btn-preview-pdf');

    if (!modal || !viewer || buttons.length === 0) return;

    function abrirModal(pdfUrl, pdfTitle) {
        viewer.src = pdfUrl;
        title.textContent = pdfTitle || 'Vista previa PDF';
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function cerrarModal() {
        modal.hidden = true;
        viewer.src = '';
        document.body.style.overflow = '';
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            abrirModal(this.dataset.pdf, this.dataset.title);
        });
    });

    if (overlay) overlay.addEventListener('click', cerrarModal);
    if (closeBtn) closeBtn.addEventListener('click', cerrarModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) {
            cerrarModal();
        }
    });
});