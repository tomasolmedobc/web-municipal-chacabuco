document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('theme-toggle');
    const body = document.body;

    // El estado inicial ya fue aplicado por el script anti-FOUC al comienzo del <body>.
    // Aquí solo sincronizamos el texto del botón.
    function actualizarTextoBoton() {
        if (!toggle) return;
        toggle.textContent = body.classList.contains('dark') ? '☀️ Claro' : '🌙 Oscuro';
    }

    actualizarTextoBoton();

    if (toggle) {
        toggle.addEventListener('click', function () {
            const esDark = body.classList.contains('dark');

            body.classList.remove('dark', 'light');

            if (esDark) {
                // Pasamos a claro — si el OS prefiere oscuro, necesitamos .light para bloquearlo
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    body.classList.add('light');
                }
                localStorage.setItem('theme', 'light');
            } else {
                body.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }

            actualizarTextoBoton();
        });
    }
});
