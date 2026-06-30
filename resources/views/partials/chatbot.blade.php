<button class="chatbot-btn" id="chatbot-btn" aria-label="Asistente municipal" title="Asistente municipal">
    <i class="fa-solid fa-comments" id="chatbot-btn-icon"></i>
</button>

<div class="chatbot-ventana" id="chatbot-ventana" role="dialog" aria-label="Asistente municipal">
    <div class="chatbot-header">
        <i class="fa-solid fa-building-columns"></i>
        <div class="chatbot-header-info">
            <div class="chatbot-header-titulo">Asistente Municipal</div>
            <div class="chatbot-header-sub">Municipalidad de Chacabuco</div>
        </div>
        <button class="chatbot-cerrar" id="chatbot-cerrar" aria-label="Cerrar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="chatbot-mensajes" id="chatbot-mensajes"></div>

    <form class="chatbot-form" id="chatbot-form" autocomplete="off">
        <input type="text"
               class="chatbot-input"
               id="chatbot-input"
               placeholder="Escribí tu consulta..."
               maxlength="200"
               aria-label="Mensaje">
        <button type="submit" class="chatbot-enviar" aria-label="Enviar">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>

<script>
(function () {
    const ENDPOINT = '{{ route("chatbot.responder") }}';
    const TOKEN    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const btn      = document.getElementById('chatbot-btn');
    const ventana  = document.getElementById('chatbot-ventana');
    const cerrar   = document.getElementById('chatbot-cerrar');
    const mensajes = document.getElementById('chatbot-mensajes');
    const form     = document.getElementById('chatbot-form');
    const input    = document.getElementById('chatbot-input');
    const btnIcon  = document.getElementById('chatbot-btn-icon');

    let abierto = false;

    function abrir() {
        abierto = true;
        ventana.classList.add('is-open');
        btnIcon.className = 'fa-solid fa-xmark';
        input.focus();

        if (!mensajes.children.length) {
            agregarBot(
                'Hola! Soy el asistente de acceso rápido del Municipio de Chacabuco. ' +
                'No soy un bot inteligente, pero puedo ayudarte a encontrar teléfonos útiles y llevarte directo a las secciones del sitio. ' +
                'Probá escribiendo:',
                ['hospital', 'policía', 'bomberos', 'reclamo', 'expediente', 'turismo', 'licitaciones', 'proveedores']
            );
        }
    }

    function cerrarChat() {
        abierto = false;
        ventana.classList.remove('is-open');
        btnIcon.className = 'fa-solid fa-comments';
    }

    btn.addEventListener('click', function () { abierto ? cerrarChat() : abrir(); });
    cerrar.addEventListener('click', cerrarChat);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && abierto) cerrarChat();
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const texto = input.value.trim();
        if (!texto) return;

        agregarUsuario(texto);
        input.value = '';

        if (/^volver$/i.test(texto.trim())) {
            agregarBot(
                'Claro, ¿en qué puedo ayudarte?',
                ['hospital', 'policía', 'bomberos', 'reclamo', 'expediente', 'turismo', 'licitaciones', 'proveedores']
            );
            return;
        }

        mostrarTyping();

        fetch(ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ mensaje: texto }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            quitarTyping();
            manejarRespuesta(data);
        })
        .catch(function () {
            quitarTyping();
            agregarBot('Ocurrió un error. Por favor intentá nuevamente.');
        });
    });

    function manejarRespuesta(data) {
        if (data.tipo === 'telefonos') {
            var html = '<div class="chatbot-resultados">';
            data.telefonos.forEach(function (t) {
                html += '<div class="chatbot-resultado-item">';
                html += '<div class="chatbot-resultado-nombre">' + esc(t.nombre) + '</div>';
                html += '<div class="chatbot-resultado-detalle">';
                if (t.categoria) html += '<span>' + esc(t.categoria) + '</span>';
                if (t.telefono)  html += ' &nbsp;·&nbsp; <span class="chatbot-resultado-tel"><i class="fa-solid fa-phone fa-xs"></i> ' + esc(t.telefono) + '</span>';
                if (t.direccion) html += '<br><span>' + esc(t.direccion) + '</span>';
                html += '</div></div>';
            });
            html += '</div>';

            if (data.seccion) {
                html += '<a href="' + esc(data.seccion.url) + '" class="chatbot-link-seccion" target="_blank">' +
                        '<i class="fa-solid fa-arrow-right fa-xs"></i> ' + esc(data.seccion.label) + '</a>';
            }

            agregarBotHtml(data.texto, html);

        } else if (data.tipo === 'seccion') {
            var enlace = '<a href="' + esc(data.url) + '" class="chatbot-link-seccion">' +
                         '<i class="fa-solid fa-arrow-right fa-xs"></i> ' + esc(data.label) + '</a>';
            agregarBotHtml(data.texto, enlace);

        } else if (data.tipo === 'sin_resultados') {
            agregarBot(data.texto, data.sugerencias || []);

        } else {
            agregarBot('Escribí algo para que pueda ayudarte.');
        }
    }

    function agregarUsuario(texto) {
        var el = document.createElement('div');
        el.className = 'chatbot-burbuja usuario';
        el.textContent = texto;
        mensajes.appendChild(el);
        scroll();
    }

    function agregarBot(texto, sugerencias) {
        var el = document.createElement('div');
        el.className = 'chatbot-burbuja bot';
        el.textContent = texto;

        if (sugerencias && sugerencias.length) {
            var wrap = document.createElement('div');
            wrap.className = 'chatbot-sugerencias';
            sugerencias.forEach(function (s) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'chatbot-sugerencia';
                b.textContent = s;
                b.addEventListener('click', function () {
                    input.value = s;
                    form.dispatchEvent(new Event('submit'));
                });
                wrap.appendChild(b);
            });
            el.appendChild(wrap);
        }

        mensajes.appendChild(el);
        scroll();
    }

    function agregarBotHtml(texto, html) {
        var el = document.createElement('div');
        el.className = 'chatbot-burbuja bot';
        var p = document.createElement('p');
        p.style.margin = '0 0 6px';
        p.textContent = texto;
        el.appendChild(p);
        var extra = document.createElement('div');
        extra.innerHTML = html;
        el.appendChild(extra);
        mensajes.appendChild(el);
        scroll();
    }

    function mostrarTyping() {
        var el = document.createElement('div');
        el.className = 'chatbot-typing';
        el.id = 'chatbot-typing';
        el.innerHTML = '<span></span><span></span><span></span>';
        mensajes.appendChild(el);
        scroll();
    }

    function quitarTyping() {
        var el = document.getElementById('chatbot-typing');
        if (el) el.remove();
    }

    function scroll() {
        mensajes.scrollTop = mensajes.scrollHeight;
    }

    function esc(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
})();
</script>
