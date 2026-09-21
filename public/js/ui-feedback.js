function showToast(message, type = 'success') {
    const toast      = document.getElementById('app-toast');
    const messageBox = document.getElementById('app-toast-message');

    if (!toast || !messageBox) return;

    toast.className = 'app-toast';
    toast.classList.add(type);
    messageBox.textContent = message;

    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => toast.classList.remove('show'), 3500);
}

function showConfirm(message, onConfirm) {
    const modal      = document.getElementById('app-confirm-modal');
    const dialog     = modal ? modal.querySelector('.app-confirm-dialog') : null;
    const messageBox = document.getElementById('app-confirm-message');
    const btnCancel  = document.getElementById('app-confirm-cancel');
    const btnOk      = document.getElementById('app-confirm-ok');
    const overlay    = document.getElementById('app-confirm-overlay');

    if (!modal || !dialog || !messageBox || !btnCancel || !btnOk || !overlay) {
        if (confirm(message)) onConfirm();
        return;
    }

    const FOCUSABLE  = 'a[href],button:not([disabled]),input:not([disabled]),[tabindex]:not([tabindex="-1"])';
    const lastFocus  = document.activeElement;

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

    function trapEscape(e) {
        if (e.key === 'Escape') close();
    }

    messageBox.textContent = message;
    modal.hidden = false;
    document.addEventListener('keydown', trapTab);
    document.addEventListener('keydown', trapEscape);
    btnCancel.focus();

    const close = () => {
        modal.hidden = true;
        btnOk.onclick     = null;
        btnCancel.onclick = null;
        overlay.onclick   = null;
        document.removeEventListener('keydown', trapTab);
        document.removeEventListener('keydown', trapEscape);
        if (lastFocus) lastFocus.focus();
    };

    btnOk.onclick     = () => { close(); onConfirm(); };
    btnCancel.onclick = close;
    overlay.onclick   = close;
}
