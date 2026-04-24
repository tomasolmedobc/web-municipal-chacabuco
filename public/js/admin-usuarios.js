document.addEventListener('DOMContentLoaded', function () {
    const formsEliminar = document.querySelectorAll('.form-eliminar-usuario');
    const formsReset = document.querySelectorAll('.form-reset-password');

    formsEliminar.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            showConfirm('¿Seguro que querés eliminar este usuario?', () => {
                form.submit();
            });
        });
    });

    formsReset.forEach((form) => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            showConfirm('¿Querés resetear la contraseña de este usuario?', () => {
                form.submit();
            });
        });
    });
});