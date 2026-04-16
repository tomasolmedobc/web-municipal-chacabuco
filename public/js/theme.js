document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('theme-toggle');
    const body = document.body;

    const temaGuardado = localStorage.getItem('theme');

    if (temaGuardado === 'dark') {
        body.classList.add('dark');
    }

    function actualizarTextoBoton() {
        if (!toggle) return;
        toggle.textContent = body.classList.contains('dark') ? '☀️ Claro' : '🌙 Oscuro';
    }

    actualizarTextoBoton();

    if (toggle) {
        toggle.addEventListener('click', function () {
            body.classList.toggle('dark');

            const nuevoTema = body.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', nuevoTema);

            actualizarTextoBoton();
        });
    }
});