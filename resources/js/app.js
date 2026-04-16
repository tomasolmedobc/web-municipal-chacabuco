import './bootstrap';


document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const toggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
        body.classList.add('dark');
    }

    if (toggle) {
        toggle.addEventListener('click', () => {
            body.classList.toggle('dark');

            localStorage.setItem(
                'theme',
                body.classList.contains('dark') ? 'dark' : 'light'
            );
        });
    }
});