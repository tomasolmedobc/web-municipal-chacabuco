document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-noticia');
    const estado = document.getElementById('estado');
    const alertaOculto = document.getElementById('estado-alerta-oculto');
    const alertaPublicado = document.getElementById('estado-alerta-publicado');
    
    function actualizarEstadoVisual() {
        if (!estado) return;

        if (alertaOculto) {
            alertaOculto.style.display = estado.value === 'oculto' ? 'block' : 'none';
        }

        if (alertaPublicado) {
            alertaPublicado.style.display = estado.value === 'publicado' ? 'block' : 'none';
        }
    }

    if (estado) {
        actualizarEstadoVisual();
        estado.addEventListener('change', actualizarEstadoVisual);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }

            const contenido = document.getElementById('contenido');
            if (contenido && !contenido.value.trim()) {
                alert('El contenido no puede estar vacío.');
                e.preventDefault();
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Guardando...';
            }
        });
    }
});