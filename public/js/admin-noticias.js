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

    const checkboxDestacada = document.querySelector('input[name="destacada"]');
    const configDestacada = document.getElementById('destacada-config');

    function actualizarDestacadaVisual() {
        if (!checkboxDestacada || !configDestacada) return;

        configDestacada.style.display = checkboxDestacada.checked ? 'block' : 'none';
    }

    if (checkboxDestacada && configDestacada) {
        actualizarDestacadaVisual();
        checkboxDestacada.addEventListener('change', actualizarDestacadaVisual);
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
                return;
            }

            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Guardando...';
            }
        });
    }
});