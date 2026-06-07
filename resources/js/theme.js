// Theme Management
(function () {
    // Системная тема, когда пользователь ещё не выбрал вручную.
    function systemTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    }

    // Применяем тему к документу. persist=true — это явный выбор пользователя.
    function applyTheme(theme, persist) {
        document.documentElement.setAttribute('data-theme', theme);
        if (persist) {
            localStorage.setItem('theme', theme);
        }
    }

    // Toggle between light and dark theme.
    // Иконки переключаются чисто через CSS по data-theme — здесь только флип атрибута.
    window.toggleTheme = function () {
        const current = document.documentElement.getAttribute('data-theme') || systemTheme();
        applyTheme(current === 'dark' ? 'light' : 'dark', true);
    };

    // На старте: сохранённый выбор пользователя, иначе системная тема (без записи в localStorage,
    // чтобы продолжать следовать за системой). В layout атрибут уже выставлен inline-скриптом.
    const saved = localStorage.getItem('theme');
    applyTheme(saved || systemTheme(), false);

    // Пока пользователь не зафиксировал выбор, следуем за системной темой вживую.
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', function (e) {
            if (!localStorage.getItem('theme')) {
                document.documentElement.setAttribute('data-theme', e.matches ? 'light' : 'dark');
            }
        });
    }
})();
