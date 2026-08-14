function copiarPassword(btn) {
    var texto = document.getElementById('nueva-password').innerText.trim();

    function exito() {
        btn.textContent = '¡Copiado!';
        setTimeout(function () { btn.textContent = 'Copiar'; }, 2000);
    }

    function fallback() {
        var ta = document.createElement('textarea');
        ta.value = texto;
        ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy');
            exito();
        } catch (e) {
            btn.textContent = 'Error';
        }
        document.body.removeChild(ta);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(texto).then(exito).catch(fallback);
    } else {
        fallback();
    }
}

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