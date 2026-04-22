document.addEventListener('DOMContentLoaded', function () {
    const orden = document.getElementById('orden');

    if (orden) {
        orden.addEventListener('change', function () {
            this.form.submit();
        });
    }
});