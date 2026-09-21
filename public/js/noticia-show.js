document.addEventListener('DOMContentLoaded', function () {
    const modal   = document.getElementById('pdfModal');
    const overlay = document.getElementById('pdfModalOverlay');
    const closeBtn = document.getElementById('pdfModalClose');
    const viewer  = document.getElementById('pdfViewer');
    const title   = document.getElementById('pdfModalTitle');
    const buttons = document.querySelectorAll('.btn-preview-pdf');

    if (!modal || !viewer || buttons.length === 0) return;

    const dialog   = modal.querySelector('.pdf-modal__dialog');
    const FOCUSABLE = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';
    let lastFocus  = null;

    function trapTab(e) {
        if (e.key !== 'Tab') return;
        const els = Array.from(dialog.querySelectorAll(FOCUSABLE));
        if (!els.length) return;
        const first = els[0], last = els[els.length - 1];
        if (e.shiftKey) {
            if (document.activeElement === first) { e.preventDefault(); last.focus(); }
        } else {
            if (document.activeElement === last)  { e.preventDefault(); first.focus(); }
        }
    }

    function abrirModal(pdfUrl, pdfTitle) {
        lastFocus   = document.activeElement;
        viewer.src  = pdfUrl;
        title.textContent = pdfTitle || 'Vista previa PDF';
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', trapTab);
        const first = dialog.querySelector(FOCUSABLE);
        if (first) first.focus();
    }

    function cerrarModal() {
        modal.hidden = true;
        viewer.src   = '';
        document.body.style.overflow = '';
        document.removeEventListener('keydown', trapTab);
        if (lastFocus) lastFocus.focus();
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            abrirModal(this.dataset.pdf, this.dataset.title);
        });
    });

    if (overlay)  overlay.addEventListener('click', cerrarModal);
    if (closeBtn) closeBtn.addEventListener('click', cerrarModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) cerrarModal();
    });
});
