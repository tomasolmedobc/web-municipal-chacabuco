document.addEventListener('DOMContentLoaded', function () {
    const formsEliminar = document.querySelectorAll('.form-eliminar-noticia');

    formsEliminar.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const mensaje = this.dataset.confirm || '¿Seguro que querés continuar?';

            if (typeof showConfirm !== 'function') {
                this.submit();
                return;
            }

            showConfirm(mensaje, () => {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                }
                this.submit();
            });
        });
    });

    const formsEstado = document.querySelectorAll('.form-toggle-estado');

    formsEstado.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            showConfirm('¿Querés cambiar el estado de esta noticia?', () => {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                }
                form.submit();
            });
        });
    });
});