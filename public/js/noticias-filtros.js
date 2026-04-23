document.addEventListener('DOMContentLoaded', function () {
    const orden = document.getElementById('orden');
    const categoria = document.getElementById('categoria');

    if (orden) {
        orden.addEventListener('change', function () {
            this.form.submit();
        });
    }

    if (categoria) {
        categoria.addEventListener('change', function () {
            this.form.submit();
        });
    }
});