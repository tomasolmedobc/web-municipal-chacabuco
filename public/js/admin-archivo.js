document.addEventListener('DOMContentLoaded', function () {
    const botonesEliminar = document.querySelectorAll('.btn-eliminar-archivo');

    if (!botonesEliminar.length) return;

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : null;

    botonesEliminar.forEach((boton) => {
        boton.addEventListener('click', function () {
            const url = this.dataset.url;
            const id = this.dataset.id;
            const botonActual = this;

            if (!url || !id) return;

            showConfirm('¿Seguro que querés eliminar este archivo adjunto?', async function () {
                botonActual.disabled = true;
                botonActual.textContent = 'Quitando...';

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    if (!response.ok) {
                        throw new Error('No se pudo eliminar el archivo.');
                    }

                    const card = document.getElementById(`archivo-${id}`);
                    const grid = document.getElementById('archivos-grid');

                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';

                        setTimeout(() => {
                            card.remove();

                            if (grid && grid.children.length === 0) {
                                const bloqueArchivos = grid.closest('.admin-form-group');
                                if (bloqueArchivos) {
                                    bloqueArchivos.remove();
                                }
                            }
                        }, 300);
                    }

                    showToast('Archivo eliminado correctamente.', 'success');

                } catch (error) {
                    botonActual.disabled = false;
                    botonActual.textContent = 'Quitar';
                    showToast('Hubo un problema al eliminar el archivo.', 'error');
                }
            });
        });
    });
});