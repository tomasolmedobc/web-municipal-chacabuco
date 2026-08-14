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

    // File upload zones
    (function () {
        function bindDragover(zona) {
            ['dragover', 'dragenter'].forEach(function (ev) {
                zona.addEventListener(ev, function (e) { e.preventDefault(); zona.classList.add('is-dragover'); });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                zona.addEventListener(ev, function () { zona.classList.remove('is-dragover'); });
            });
        }

        var zonaImagen = document.getElementById('zona-imagen');
        var inputImagen = document.getElementById('imagen_destacada');
        if (zonaImagen && inputImagen) {
            bindDragover(zonaImagen);
            inputImagen.addEventListener('change', function () {
                var file = this.files[0];
                if (!file) return;
                var preview = zonaImagen.querySelector('.file-upload-zone__preview');
                var label   = zonaImagen.querySelector('.file-upload-zone__label');
                var reader  = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.hidden = false;
                    zonaImagen.classList.add('has-preview');
                    label.textContent = file.name;
                };
                reader.readAsDataURL(file);
            });
        }

        var zonaArchivos = document.getElementById('zona-archivos');
        var inputArchivos = document.getElementById('archivos');
        if (zonaArchivos && inputArchivos) {
            bindDragover(zonaArchivos);
            var iconMap = { pdf: 'fa-file-pdf', doc: 'fa-file-word', docx: 'fa-file-word', xls: 'fa-file-excel', xlsx: 'fa-file-excel' };
            inputArchivos.addEventListener('change', function () {
                var list  = zonaArchivos.querySelector('.file-upload-zone__list');
                var label = zonaArchivos.querySelector('.file-upload-zone__label');
                list.innerHTML = '';
                Array.from(this.files).forEach(function (file) {
                    var ext  = file.name.split('.').pop().toLowerCase();
                    var icon = iconMap[ext] || 'fa-paperclip';
                    var li   = document.createElement('li');
                    li.innerHTML = '<i class="fa-solid ' + icon + '"></i> ' + file.name;
                    list.appendChild(li);
                });
                list.hidden = this.files.length === 0;
                label.textContent = this.files.length === 0
                    ? 'Seleccionar archivos'
                    : (this.files.length === 1 ? '1 archivo seleccionado' : this.files.length + ' archivos seleccionados');
            });
        }
    })();
});