document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('app-toast');
    const toastMessage = document.getElementById('app-toast-message');

    const modal = document.getElementById('app-confirm-modal');
    const modalMessage = document.getElementById('app-confirm-message');
    const confirmBtn = document.getElementById('app-confirm-ok');
    const cancelBtn = document.getElementById('app-confirm-cancel');
    const overlay = document.getElementById('app-confirm-overlay');

    let confirmCallback = null;

    window.showToast = function (message, type = 'success') {
        if (!toast || !toastMessage) return;

        toastMessage.textContent = message;
        toast.className = `app-toast show ${type}`;

        clearTimeout(window.__toastTimeout);
        window.__toastTimeout = setTimeout(() => {
            toast.className = 'app-toast';
        }, 2600);
    };

    window.showConfirm = function (message, onConfirm) {
        if (!modal || !modalMessage) return;

        modalMessage.textContent = message;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        confirmCallback = onConfirm;
    };

    function closeConfirm() {
        if (!modal) return;
        modal.hidden = true;
        document.body.style.overflow = '';
        confirmCallback = null;
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (typeof confirmCallback === 'function') {
                confirmCallback();
            }
            closeConfirm();
        });
    }

    if (cancelBtn) cancelBtn.addEventListener('click', closeConfirm);
    if (overlay) overlay.addEventListener('click', closeConfirm);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && !modal.hidden) {
            closeConfirm();
        }
    });
});