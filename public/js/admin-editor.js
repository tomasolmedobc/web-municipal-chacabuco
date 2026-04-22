document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;

    const textarea = document.querySelector('#contenido');
    if (!textarea) return;

    tinymce.init({
        selector: '#contenido',
        height: 500,
        menubar: true,
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | alignleft aligncenter alignright | code',
        language: 'es',
        language_url: '/js/tinymce/langs/es.js',
        branding: false,
        promotion: false,
        license_key: 'gpl',
        setup: function (editor) {
            editor.on('change keyup', function () {
                tinymce.triggerSave();
            });
        }
    });
});