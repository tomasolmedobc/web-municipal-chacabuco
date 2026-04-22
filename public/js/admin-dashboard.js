document.addEventListener('DOMContentLoaded', function () {
    const formsEliminar = document.querySelectorAll('.form-eliminar-noticia');

    if (!formsEliminar.length) return;

    formsEliminar.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const mensaje = this.dataset.confirm || '¿Seguro que querés continuar?';

            if (typeof showConfirm !== 'function') {
                this.submit();
                return;
            }

            showConfirm(mensaje, () => {
                this.submit();
            });
        });
    });
    const formsEstado = document.querySelectorAll('.form-toggle-estado');

    formsEstado.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            showConfirm('¿Querés cambiar el estado de esta noticia?', () => {
                form.submit();
            });
        });
    });
});