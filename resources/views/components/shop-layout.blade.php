<!DOCTYPE html>
<html lang="ru">

<head>
    <script>
        window.livewire_app_url = "{{ config('app.env') === 'production' ? config('app.url') : '' }}";
        // Применяем тему до первой отрисовки, чтобы не было мигания тёмного фона.
        // Без явного выбора пользователя берём системную тему (prefers-color-scheme).
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'));
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RuGear | Hardware Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Theme Variables */
        :root[data-theme="dark"] {
            --bg-primary: #0f1115;
            --bg-secondary: #161920;
            --bg-tertiary: #1a1d24;
            --text-primary: #e5e7eb;
            --text-secondary: #9ca3af;
            --text-tertiary: #6b7280;
            --border-color: #374151;
            --border-color-light: #1f2937;
        }

        :root[data-theme="light"] {
            --bg-primary: #f4f5f7;
            --bg-secondary: #ffffff;
            --bg-tertiary: #eceef2;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --text-tertiary: #9ca3af;
            --border-color: #e2e5ea;
            --border-color-light: #eef0f3;
        }

        /* ════════════════════════════════════════════════════════════
           LIGHT THEME OVERRIDES
           Большинство блоков свёрстано захардкоженными тёмными классами
           (bg-[#161920], bg-gray-900/80, border-gray-800, text-white …),
           которые не управляются CSS-переменными. Здесь они ремапятся
           на светлые значения, когда активна светлая тема.
           ──────────────────────────────────────────────────────────── */

        /* — Поверхности: карточки/модалки → белый — */
        [data-theme="light"] .bg-\[\#161920\],
        [data-theme="light"] .bg-\[\#14171d\],
        [data-theme="light"] .bg-\[\#1a1d24\],
        [data-theme="light"] .bg-\[\#111318\] { background-color: #ffffff; }

        /* — Те же поверхности с прозрачностью — */
        [data-theme="light"] .bg-\[\#161920\]\/50,
        [data-theme="light"] .bg-\[\#161920\]\/40 { background-color: #ffffff; }

        /* — Вложенные/утопленные поверхности (инпуты, ячейки) → светло-серый — */
        [data-theme="light"] .bg-\[\#0f1115\],
        [data-theme="light"] .bg-\[\#0f1117\],
        [data-theme="light"] .bg-\[\#0d0f14\],
        [data-theme="light"] .bg-\[\#13151a\],
        [data-theme="light"] .bg-\[\#0f1115\]\/50 { background-color: #f3f4f6; }

        /* — Серые фоны — */
        [data-theme="light"] .bg-gray-900,
        [data-theme="light"] .bg-gray-950 { background-color: #eef0f3; }
        [data-theme="light"] .bg-gray-800 { background-color: #e9ebef; }
        [data-theme="light"] .bg-gray-700 { background-color: #dfe2e7; }
        [data-theme="light"] .bg-gray-500 { background-color: #d1d5db; }

        /* — Серые фоны с прозрачностью → сплошной светлый (стабильный контраст) — */
        [data-theme="light"] .bg-gray-900\/80 { background-color: #eef0f3; }
        [data-theme="light"] .bg-gray-900\/60 { background-color: #f0f2f5; }
        [data-theme="light"] .bg-gray-900\/50 { background-color: #f1f3f6; }
        [data-theme="light"] .bg-gray-900\/40 { background-color: #f2f4f7; }
        [data-theme="light"] .bg-gray-900\/30 { background-color: #f3f5f8; }
        [data-theme="light"] .bg-gray-900\/20 { background-color: #f5f6f9; }
        [data-theme="light"] .bg-gray-800\/70 { background-color: #ebedf1; }
        [data-theme="light"] .bg-gray-800\/60 { background-color: #eceef2; }
        [data-theme="light"] .bg-gray-800\/50 { background-color: #eef0f3; }
        [data-theme="light"] .bg-gray-800\/30 { background-color: #f2f4f7; }
        [data-theme="light"] .bg-gray-800\/10 { background-color: #f6f7f9; }
        [data-theme="light"] .bg-gray-950\/40 { background-color: #f1f3f6; }
        [data-theme="light"] .bg-gray-950\/20 { background-color: #f5f6f9; }

        /* — hover-фоны — */
        [data-theme="light"] .hover\:bg-gray-800:hover { background-color: #e9ebef; }
        [data-theme="light"] .hover\:bg-gray-700:hover { background-color: #dfe2e7; }
        [data-theme="light"] .hover\:bg-gray-900\/30:hover { background-color: #f3f5f8; }
        [data-theme="light"] .hover\:bg-gray-900\/50:hover { background-color: #f1f3f6; }
        [data-theme="light"] .hover\:bg-gray-800\/60:hover { background-color: #eceef2; }
        [data-theme="light"] .hover\:bg-gray-800\/50:hover { background-color: #eef0f3; }

        /* — Текст — */
        [data-theme="light"] .text-white,
        [data-theme="light"] .text-gray-200 { color: #1f2937; }
        [data-theme="light"] .text-gray-300 { color: #374151; }
        [data-theme="light"] .text-gray-400 { color: #4b5563; }
        [data-theme="light"] .text-gray-500 { color: #6b7280; }
        [data-theme="light"] .text-gray-600 { color: #9ca3af; }

        /* — hover/group-hover текст (иначе белый текст исчезает на светлом) — */
        [data-theme="light"] .hover\:text-white:hover,
        [data-theme="light"] .group:hover .group-hover\:text-white { color: #1f2937; }
        [data-theme="light"] .hover\:text-gray-400:hover { color: #4b5563; }
        [data-theme="light"] .hover\:text-gray-300:hover { color: #374151; }
        [data-theme="light"] .group:hover .group-hover\:text-gray-400 { color: #4b5563; }

        /* — Плейсхолдеры — */
        [data-theme="light"] .placeholder-gray-600::placeholder,
        [data-theme="light"] .placeholder-gray-700::placeholder,
        [data-theme="light"] .placeholder\:text-gray-600::placeholder,
        [data-theme="light"] .placeholder\:text-gray-700::placeholder { color: #9ca3af; }

        /* — Границы — */
        [data-theme="light"] .border-gray-900,
        [data-theme="light"] .border-gray-900\/80,
        [data-theme="light"] .border-gray-900\/50,
        [data-theme="light"] .border-gray-800,
        [data-theme="light"] .border-gray-800\/80,
        [data-theme="light"] .border-gray-800\/60,
        [data-theme="light"] .border-gray-800\/50,
        [data-theme="light"] .border-gray-800\/40 { border-color: #e2e5ea; }
        [data-theme="light"] .border-gray-700,
        [data-theme="light"] .border-gray-700\/60,
        [data-theme="light"] .border-gray-700\/50,
        [data-theme="light"] .border-gray-700\/30 { border-color: #d8dce2; }
        [data-theme="light"] .border-gray-600 { border-color: #cbd0d8; }

        [data-theme="light"] .hover\:border-gray-700:hover,
        [data-theme="light"] .hover\:border-gray-700\/60:hover { border-color: #d8dce2; }
        [data-theme="light"] .hover\:border-gray-600:hover { border-color: #cbd0d8; }

        /* — Разделители (divide-*) — */
        [data-theme="light"] .divide-gray-800 > :not([hidden]) ~ :not([hidden]),
        [data-theme="light"] .divide-gray-800\/60 > :not([hidden]) ~ :not([hidden]),
        [data-theme="light"] .divide-gray-800\/50 > :not([hidden]) ~ :not([hidden]),
        [data-theme="light"] .divide-gray-900\/60 > :not([hidden]) ~ :not([hidden]) { border-color: #e2e5ea; }

        /* — Градиенты: серые стопы — */
        [data-theme="light"] .from-gray-800 {
            --tw-gradient-from: #eef0f3 var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(238 240 243 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="light"] .to-gray-900 { --tw-gradient-to: #eef0f3 var(--tw-gradient-to-position); }
        [data-theme="light"] .from-gray-800\/40 {
            --tw-gradient-from: #f2f4f7 var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(242 244 247 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="light"] .to-gray-900\/60 { --tw-gradient-to: #f0f2f5 var(--tw-gradient-to-position); }

        /* — Градиенты: произвольные тёмные hex-стопы (hero-панели) — */
        [data-theme="light"] .from-\[\#1a1d24\],
        [data-theme="light"] .from-\[\#161920\] {
            --tw-gradient-from: #ffffff var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(255 255 255 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="light"] .via-\[\#161920\] {
            --tw-gradient-to: rgb(248 249 251 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), #f8f9fb var(--tw-gradient-via-position), var(--tw-gradient-to);
        }
        [data-theme="light"] .to-\[\#0f1115\] { --tw-gradient-to: #f3f4f6 var(--tw-gradient-to-position); }

        /* — Пагинация (постраничные стили задают тёмный фон через !important) — */
        [data-theme="light"] .custom-pagination a,
        [data-theme="light"] .custom-pagination nav a,
        [data-theme="light"] .custom-pagination span[aria-disabled="true"] span {
            background-color: #ffffff !important;
            border-color: #e2e5ea !important;
            color: #6b7280 !important;
        }
        [data-theme="light"] .custom-pagination a:hover,
        [data-theme="light"] .custom-pagination nav a:hover {
            background-color: #f3f4f6 !important;
            color: #1f2937 !important;
        }

        /* — Переключатель тем: показываем одну иконку по активной теме — */
        .ti-sun, .ti-moon { display: none; }
        :root[data-theme="dark"] .ti-sun { display: block; }
        :root[data-theme="light"] .ti-moon { display: block; }

        :root {
            --bg-primary: #0f1115;
            --bg-secondary: #161920;
            --bg-tertiary: #1a1d24;
            --text-primary: #e5e7eb;
            --text-secondary: #9ca3af;
            --text-tertiary: #6b7280;
            --border-color: #374151;
            --border-color-light: #1f2937;
        }

        @keyframes tin  { from{opacity:0;transform:translateX(400px)} to{opacity:1;transform:translateX(0)} }
        @keyframes tout { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(400px)} }
        .toast { animation: tin .4s cubic-bezier(.34,1.56,.64,1); }
        .toast.out { animation: tout .3s ease-in forwards; }

        @keyframes rainbow-shadow {
            0%   { box-shadow: 0 0 0 3px #ff4444, 0 0 14px rgba(255,68,68,.5); }
            16%  { box-shadow: 0 0 0 3px #ff8800, 0 0 14px rgba(255,136,0,.5); }
            33%  { box-shadow: 0 0 0 3px #ffee00, 0 0 14px rgba(255,238,0,.5); }
            50%  { box-shadow: 0 0 0 3px #44ff44, 0 0 14px rgba(68,255,68,.5); }
            66%  { box-shadow: 0 0 0 3px #0099ff, 0 0 14px rgba(0,153,255,.5); }
            83%  { box-shadow: 0 0 0 3px #aa00ff, 0 0 14px rgba(170,0,255,.5); }
            100% { box-shadow: 0 0 0 3px #ff4444, 0 0 14px rgba(255,68,68,.5); }
        }
        .avatar-rainbow { animation: rainbow-shadow 2.5s linear infinite; }

        /* ─── Logout modal (theme-aware) ─────────────────────────── */
        #logout-modal {
            border: none;
            padding: 0;
            background: transparent;
            width: calc(100% - 2rem);
            max-width: 25rem;
            border-radius: 1.25rem;
            color: var(--text-primary);
            overflow: visible;
        }
        #logout-modal::backdrop {
            background: rgba(0, 0, 0, .55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        #logout-modal[open]          { animation: lm-pop .32s cubic-bezier(.34, 1.56, .64, 1); }
        #logout-modal[open]::backdrop { animation: lm-fade .25s ease both; }
        @keyframes lm-fade { from { opacity: 0 } to { opacity: 1 } }
        @keyframes lm-pop {
            from { opacity: 0; transform: translateY(14px) scale(.94); }
            to   { opacity: 1; transform: translateY(0)    scale(1);   }
        }

        .lm-card {
            position: relative;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.75rem;
            text-align: center;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .4);
            overflow: hidden;
        }
        .lm-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #ef4444, transparent);
        }
        .lm-icon {
            width: 3.5rem;
            height: 3.5rem;
            margin: 0 auto 1rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f87171;
            background: rgba(239, 68, 68, .12);
            box-shadow: 0 0 0 6px rgba(239, 68, 68, .06);
        }
        .lm-title {
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: .4rem;
        }
        .lm-text {
            font-size: .875rem;
            line-height: 1.5;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }
        .lm-actions { display: flex; align-items: stretch; gap: .75rem; }
        /* Форма-обёртка кнопки «Выйти» убирается из раскладки,
           чтобы обе кнопки были прямыми flex-элементами и были
           одинакового размера и на одном уровне. */
        .lm-actions > form { display: contents; }
        .lm-btn {
            flex: 1 1 0;
            min-width: 0;
            width: 100%;
            min-height: 2.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            margin: 0;
            padding: .7rem 1rem;
            border-radius: .85rem;
            font-family: inherit;
            font-size: .875rem;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            transition: all .2s ease;
            border: 1px solid transparent;
        }
        .lm-btn-cancel {
            color: var(--text-secondary);
            background: var(--bg-tertiary);
            border-color: var(--border-color);
        }
        .lm-btn-cancel:hover {
            color: var(--text-primary);
            border-color: var(--text-tertiary);
        }
        .lm-btn-confirm {
            color: #fff;
            background: #dc2626;
            box-shadow: 0 6px 18px rgba(220, 38, 38, .35);
        }
        .lm-btn-confirm:hover {
            background: #ef4444;
            box-shadow: 0 8px 22px rgba(239, 68, 68, .45);
        }
    </style>
</head>

<body class="bg-[var(--bg-primary)] text-[var(--text-primary)] flex flex-col min-h-screen antialiased transition-colors duration-300">
    <header class="border-b border-[var(--border-color)] bg-[var(--bg-secondary)]/80 backdrop-blur-md sticky top-0 z-50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-20 items-center">
                <a href="/" class="group flex items-center space-x-2">
                    <div class="bg-orange-500 p-1.5 rounded-lg group-hover:rotate-12 transition-transform">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-xl font-black uppercase tracking-tighter">RU<span class="text-orange-500">GEAR</span></span>
                </a>

                <div class="flex items-center space-x-4">
                    {{-- Бургер-меню — строго между логотипом и аватаром.
                         Сюда свёрнуты: Каталог, Поддержка, Админ панель,
                         колокольчик уведомлений и переключатель темы. --}}
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open.toString()"
                            class="w-9 h-9 rounded-lg flex items-center justify-center border border-[var(--border-color)] bg-[var(--bg-tertiary)] text-[var(--text-secondary)] hover:text-orange-500 hover:border-orange-500/40 transition-colors"
                            aria-label="Меню"
                            title="Меню"
                        >
                            {{-- Иконка «бургер» --}}
                            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            {{-- Иконка «крестик» при открытом меню --}}
                            <svg x-show="open" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition.opacity
                            @click.outside="open = false"
                            class="fixed sm:absolute top-20 sm:top-auto left-3 right-3 sm:left-auto sm:right-0 sm:mt-3 w-auto sm:w-64 max-w-[calc(100vw-1.5rem)] bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-xl shadow-lg z-50"
                            style="display: none;"
                        >
                            {{-- Навигация --}}
                            <div class="py-1.5">
                                <a href="/" class="block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">Каталог</a>
                                <a href="{{ route('support') }}" class="block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">Поддержка</a>
                                @if(auth()->user() && auth()->user()->role === 'admin')
                                <a href="{{ route('admin.tickets.index') }}" class="block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">Админ панель</a>
                                @endif
                            </div>

                            {{-- Вход / Регистрация — только на узких экранах, где кнопки шапки скрыты --}}
                            @guest
                            <div class="sm:hidden py-1.5 border-t border-[var(--border-color-light)]">
                                <a href="{{ route('login') }}" class="block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">Вход</a>
                                <a href="{{ route('register') }}" class="block px-4 py-2.5 text-sm font-bold text-orange-500 hover:bg-[var(--bg-tertiary)] transition">Регистрация</a>
                            </div>
                            @endguest

                            {{-- Уведомления и переключатель темы --}}
                            <div class="flex items-center justify-between gap-3 px-4 py-3 border-t border-[var(--border-color-light)]">
                                <span class="text-sm text-[var(--text-secondary)]">Оформление</span>
                                <div class="flex items-center gap-3">
                                    @auth
                                    {{-- Notification bell --}}
                                    @livewire('notification-bell')
                                    @endauth
                                    {{-- Theme Toggle Button --}}
                                    <button
                                        id="theme-toggle"
                                        onclick="toggleTheme()"
                                        class="w-9 h-9 rounded-lg flex items-center justify-center border border-[var(--border-color)] bg-[var(--bg-tertiary)] text-[var(--text-secondary)] hover:text-orange-500 hover:border-orange-500/40 transition-colors"
                                        aria-label="Переключить тему"
                                        title="Переключить тему"
                                    >
                                        {{-- Солнце — видно в тёмной теме (клик → светлая) --}}
                                        <svg class="ti-sun w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="4.5"/>
                                            <path stroke-linecap="round" d="M12 2v2.5M12 19.5V22M4.22 4.22l1.77 1.77M18.01 18.01l1.77 1.77M2 12h2.5M19.5 12H22M4.22 19.78l1.77-1.77M18.01 5.99l1.77-1.77"/>
                                        </svg>
                                        {{-- Луна — видно в светлой теме (клик → тёмная) --}}
                                        <svg class="ti-moon w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21.64 13a1 1 0 0 0-1.05-.14 8 8 0 0 1-9.45-9.45 1 1 0 0 0-1.19-1.19A10 10 0 1 0 22 14.05a1 1 0 0 0-.36-1.05z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                    <div class="flex items-center space-x-4 border-l border-[var(--border-color)] pl-6">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs text-[var(--text-tertiary)] leading-none mb-0.5">Личный кабинет</p>
                            <p class="text-sm font-bold leading-none"
                               style="color:{{ auth()->user()->cosmetic_nickname_color ?? 'var(--text-primary)' }};{{ auth()->user()->cosmetic_font ? 'font-family:'.auth()->user()->cosmetic_font.';' : '' }}">{{ auth()->user()->name }}</p>
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                    <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                    <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                                </svg>
                                <span id="coin-count" class="text-xs font-black tabular-nums" style="color:#f59e0b;">{{ number_format(auth()->user()->coins) }}</span>
                            </div>
                        </div>
                        <div class="relative" id="avatar-menu-wrap">
                            <button
                                id="avatar-btn"
                                onclick="toggleAvatarMenu()"
                                class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center text-black font-black text-sm flex-shrink-0 hover:opacity-90 transition focus:outline-none overflow-hidden {{ auth()->user()->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }} hover:shadow-[0_0_20px_rgba(249,115,22,0.6)]"
                                style="{{ auth()->user()->cosmetic_border && auth()->user()->cosmetic_border !== 'rainbow' ? 'box-shadow:'.auth()->user()->cosmetic_border.';' : '' }}"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Аватар" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </button>

                            <div
                                id="avatar-dropdown"
                                class="hidden absolute right-0 mt-3 w-56 bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-xl shadow-lg overflow-hidden z-50"
                            >
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-[var(--border-color-light)] bg-[var(--bg-tertiary)]">
                                    <p class="text-xs text-[var(--text-secondary)] font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5">Уровень {{ auth()->user()->level }}</p>
                                </div>

                                <!-- Menu Items -->
                                <div class="divide-y divide-[var(--border-color-light)] py-1.5">
                                    <!-- Личный кабинет -->
                                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">
                                        <svg class="w-4 h-4 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        <span>Личный кабинет</span>
                                    </a>

                                    <!-- Мини-маркет -->
                                    <a href="{{ route('market.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">
                                        <svg class="w-4 h-4 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>Мини-маркет</span>
                                    </a>

                                    <!-- Настройки -->
                                    <a href="{{ route('settings.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">
                                        <svg class="w-4 h-4 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>Настройки</span>
                                    </a>

                                    <!-- Выход -->
                                    <button onclick="openLogoutModal()" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition text-left">
                                        <svg class="w-4 h-4 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>Выход</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="hidden sm:flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-sm font-bold hover:text-orange-500 transition">Вход</a>
                        <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-[0_0_20px_rgba(249,115,22,0.3)]">
                            Регистрация
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow py-12 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-[var(--border-color)] bg-[var(--bg-secondary)] py-10 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[var(--text-tertiary)] text-sm italic">«Железо с характером для тех, кто создает будущее»</p>
            <p class="text-[var(--text-tertiary)] text-xs mt-4 uppercase tracking-widest">&copy; 2026 RUGEAR Ecosystem</p>
        </div>
    </footer>

    {{-- ─── Toast container (все тосты внутри, flex стакает снизу вверх) ── --}}
    {{-- flex-col-reverse: первый в DOM = внизу экрана, следующие выше      --}}
    <div id="toast-wrap"
         class="fixed bottom-6 right-6 z-[999] flex flex-col-reverse gap-3 items-end pointer-events-none">

        {{-- achievement — первый в DOM = ближайший к низу экрана --}}
        @if(session('achievements_awarded'))
        <div id="achievements-queue" data-achievements="{{ json_encode(session('achievements_awarded')) }}" class="hidden"></div>
        @endif

        @if(session('achievement_awarded'))
        <div id="t-ach" class="w-80 bg-[#1a1d24] border border-orange-500/30 rounded-2xl p-4 shadow-2xl toast pointer-events-auto">
            <div class="flex gap-3 items-start">
                <div class="w-10 h-10 rounded-xl bg-orange-500/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-orange-400">Достижение разблокировано</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ session('achievement_awarded.title') }}</p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-gray-400">+{{ session('achievement_awarded.experience') }} XP</span>
                        <span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                            </svg>
                            +{{ session('achievement_awarded.coins') }} монет
                        </span>
                    </div>
                </div>
                <button onclick="closeToast('t-ach')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>
            </div>
        </div>
        @endif

        {{-- level_up — второй в DOM = выше achievement --}}
        @if(session('level_up'))
        <div id="t-lvl" class="w-80 bg-[#1a1d24] border border-cyan-500/30 rounded-2xl p-4 shadow-2xl toast pointer-events-auto">
            <div class="flex gap-3 items-start">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-cyan-400">Новый уровень!</p>
                    <p class="text-sm font-bold text-white mt-0.5">Уровень {{ session('level_up.level') }}</p>
                    <span class="flex items-center gap-1 text-xs font-bold mt-1" style="color:#f59e0b;">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                            <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                        </svg>
                        +{{ session('level_up.coins') }} монет
                    </span>
                </div>
                <button onclick="closeToast('t-lvl')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>
            </div>
        </div>
        @endif

        {{-- success — третий в DOM = выше остальных --}}
        @if(session('success'))
        <div id="t-ok" class="w-80 bg-[#1a1d24] border border-green-500/30 rounded-2xl p-4 shadow-2xl toast pointer-events-auto">
            <div class="flex gap-3 items-start">
                <div class="w-10 h-10 rounded-xl bg-green-500/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-green-400">Готово</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ session('success') }}</p>
                </div>
                <button onclick="closeToast('t-ok')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>
            </div>
        </div>
        @endif

    </div>

    <script>
        function closeToast(id) {
            const t = document.getElementById(id);
            if (!t) return;
            t.classList.add('out');
            setTimeout(() => t.remove(), 300);
        }

        function _coinSvg(size) {
            return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">` +
                `<circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>` +
                `<circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>` +
                `<text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>` +
                `</svg>`;
        }

        function showToast(type, data) {
            const wrap = document.getElementById('toast-wrap');
            if (!wrap) return;
            const id = 'dyn-toast-' + Date.now();
            const el = document.createElement('div');
            el.id = id;
            el.className = 'w-80 bg-[#1a1d24] rounded-2xl p-4 shadow-2xl toast pointer-events-auto';

            if (type === 'achievement') {
                el.style.border = '1px solid rgba(249,115,22,0.3)';
                el.innerHTML =
                    `<div class="flex gap-3 items-start">` +
                        `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(249,115,22,0.15)">` +
                            `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" style="color:#fb923c"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>` +
                        `</div>` +
                        `<div class="flex-1 min-w-0">` +
                            `<p class="text-xs font-bold uppercase tracking-widest" style="color:#fb923c">Достижение разблокировано</p>` +
                            `<p class="text-sm font-bold text-white mt-0.5">${data.title}</p>` +
                            `<div class="flex items-center gap-3 mt-1">` +
                                `<span class="text-xs text-gray-400">+${data.experience} XP</span>` +
                                `<span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b">${_coinSvg(14)} +${data.coins} монет</span>` +
                            `</div>` +
                        `</div>` +
                        `<button onclick="closeToast('${id}')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>` +
                    `</div>`;
            } else if (type === 'game_reward') {
                el.style.border = '1px solid rgba(168,85,247,0.35)';
                el.innerHTML =
                    `<div class="flex gap-3 items-start">` +
                        `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(168,85,247,0.15)">` +
                            `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#c084fc"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>` +
                        `</div>` +
                        `<div class="flex-1 min-w-0">` +
                            `<p class="text-xs font-bold uppercase tracking-widest" style="color:#c084fc">Buzzword Blast</p>` +
                            `<p class="text-sm font-bold text-white mt-0.5">Уровень пройден!</p>` +
                            `<div class="flex items-center gap-3 mt-1">` +
                                `<span class="text-xs text-gray-400">+${data.xp} XP</span>` +
                                `<span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b">${_coinSvg(14)} +${data.coins} монета</span>` +
                            `</div>` +
                        `</div>` +
                        `<button onclick="closeToast('${id}')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>` +
                    `</div>`;
            } else if (type === 'levelup') {
                el.style.border = '1px solid rgba(6,182,212,0.3)';
                el.innerHTML =
                    `<div class="flex gap-3 items-start">` +
                        `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(6,182,212,0.15)">` +
                            `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:#22d3ee"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>` +
                        `</div>` +
                        `<div class="flex-1 min-w-0">` +
                            `<p class="text-xs font-bold uppercase tracking-widest" style="color:#22d3ee">Новый уровень!</p>` +
                            `<p class="text-sm font-bold text-white mt-0.5">Уровень ${data.level}</p>` +
                            `<span class="flex items-center gap-1 text-xs font-bold mt-1" style="color:#f59e0b">${_coinSvg(14)} +${data.coins} монет</span>` +
                        `</div>` +
                        `<button onclick="closeToast('${id}')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>` +
                    `</div>`;
            }

            // prepend = первый в DOM = внизу контейнера (flex-col-reverse)
            wrap.prepend(el);
            setTimeout(() => closeToast(id), 5000);

            // Update header coin counter
            const coinEl = document.getElementById('coin-count');
            if (coinEl && data.coins) {
                const cur = parseInt(coinEl.textContent.replace(/\D/g, ''), 10) || 0;
                coinEl.textContent = (cur + data.coins).toLocaleString('ru-RU');
            }
        }

        @if(session('achievement_awarded')) setTimeout(() => closeToast('t-ach'), 5000); @endif
        @if(session('level_up'))            setTimeout(() => closeToast('t-lvl'), 5500); @endif
        @if(session('success'))             setTimeout(() => closeToast('t-ok'),  4000); @endif

        // ── Показываем несколько достижений поочередно ──────────────────────
        const achQueue = document.getElementById('achievements-queue');
        if (achQueue) {
            const achievements = JSON.parse(achQueue.getAttribute('data-achievements'));
            achievements.forEach((ach, idx) => {
                setTimeout(() => {
                    showToast('achievement', {
                        title: ach.title,
                        experience: ach.experience,
                        coins: ach.coins
                    });
                }, idx * 600);
            });
        }

        // ── Avatar dropdown ──────────────────────────────────────
        function toggleAvatarMenu() {
            const btn  = document.getElementById('avatar-btn');
            const menu = document.getElementById('avatar-dropdown');
            const open = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', open);
            btn.setAttribute('aria-expanded', String(!open));
        }

        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('avatar-menu-wrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('avatar-dropdown').classList.add('hidden');
                document.getElementById('avatar-btn').setAttribute('aria-expanded', 'false');
            }
        });

        // ── Logout modal ─────────────────────────────────────────
        function openLogoutModal() {
            document.getElementById('avatar-dropdown').classList.add('hidden');
            document.getElementById('logout-modal').showModal();
        }
        function closeLogoutModal() {
            document.getElementById('logout-modal').close();
        }

        // ── Toggle password visibility ───────────────────────────
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const eyeOpen = document.getElementById(fieldId + '-eye-open');
            const eyeClosed = document.getElementById(fieldId + '-eye-closed');
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            // Toggle between eye icons
            if (eyeOpen && eyeClosed) {
                if (isPassword) {
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            }
        }
    </script>

    @auth
    {{-- Logout confirmation modal --}}
    <dialog id="logout-modal" onclick="if(event.target===this)closeLogoutModal()">
        <div class="lm-card">
            <div class="lm-icon">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h2 class="lm-title">Выход из аккаунта</h2>
            <p class="lm-text">Вы точно хотите выйти из личного кабинета?</p>
            <div class="lm-actions">
                <button type="button" onclick="closeLogoutModal()" class="lm-btn lm-btn-cancel">
                    Отмена
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="lm-btn lm-btn-confirm">
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </dialog>
    @endauth

    {{-- ─── Image lightbox (фото в отзывах и т.п.) ──────────────────────
         Любая ссылка с атрибутом data-lightbox открывает изображение
         поверх страницы. Закрытие: крестик, клик по фону или Esc. --}}
    <div id="lightbox"
         class="hidden fixed inset-0 z-[1000] items-center justify-center bg-black/85 backdrop-blur-sm p-4"
         role="dialog" aria-modal="true" aria-label="Просмотр изображения">
        <button type="button"
                onclick="closeLightbox()"
                class="absolute top-4 right-4 w-11 h-11 flex items-center justify-center rounded-full bg-black/50 text-white text-3xl leading-none hover:bg-black/80 transition-colors"
                aria-label="Закрыть">&times;</button>
        <img id="lightbox-img" src="" alt=""
             class="max-w-full max-h-full object-contain rounded-lg shadow-2xl select-none">
    </div>

    <script>
        (function () {
            const box = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');

            window.closeLightbox = function () {
                box.classList.add('hidden');
                box.classList.remove('flex');
                img.src = '';
                document.body.style.overflow = '';
            };

            function openLightbox(src) {
                img.src = src;
                box.classList.remove('hidden');
                box.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            // Делегирование: работает и для фото, добавленных Livewire после загрузки.
            document.addEventListener('click', function (e) {
                const link = e.target.closest('a[data-lightbox]');
                if (link) {
                    e.preventDefault();
                    openLightbox(link.getAttribute('href'));
                    return;
                }
                // Клик по фону (но не по самому изображению) — закрыть.
                if (e.target === box) closeLightbox();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !box.classList.contains('hidden')) closeLightbox();
            });
        })();
    </script>

</body>

</html>
