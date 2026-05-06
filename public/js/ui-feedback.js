function showToast(message, type = 'success') {
    const toast = document.getElementById('app-toast');
    const messageBox = document.getElementById('app-toast-message');

    if (!toast || !messageBox) return;

    toast.className = 'app-toast';
    toast.classList.add(type);

    messageBox.textContent = message;

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3500);
}

function showConfirm(message, onConfirm) {
    const modal = document.getElementById('app-confirm-modal');
    const messageBox = document.getElementById('app-confirm-message');
    const btnCancel = document.getElementById('app-confirm-cancel');
    const btnOk = document.getElementById('app-confirm-ok');
    const overlay = document.getElementById('app-confirm-overlay');

    if (!modal || !messageBox || !btnCancel || !btnOk || !overlay) {
        if (confirm(message)) onConfirm();
        return;
    }

    messageBox.textContent = message;
    modal.hidden = false;

    const close = () => {
        modal.hidden = true;
        btnOk.onclick = null;
        btnCancel.onclick = null;
        overlay.onclick = null;
    };

    btnOk.onclick = () => {
        close();
        onConfirm();
    };

    btnCancel.onclick = close;
    overlay.onclick = close;
}