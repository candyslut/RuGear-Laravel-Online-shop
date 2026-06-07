<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RuGear Admin' }}</title>
    <script>
        // Применяем тему до первой отрисовки, чтобы не было мигания тёмного фона.
        // Ключ 'theme' общий с витриной — тема синхронизирована между разделами.
        // Без явного выбора пользователя берём системную тему (prefers-color-scheme).
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'));
        function toggleTheme() {
            const cur = document.documentElement.getAttribute('data-theme') || 'dark';
            const next = cur === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ════════════════════════════════════════════════════════════
           СВЕТЛАЯ ТЕМА ДЛЯ АДМИН-ПАНЕЛИ
           Вёрстка захардкожена тёмными классами (bg-[#0b0c10],
           bg-gray-900/60, text-white, border-gray-800 …). Здесь они
           ремапятся на светлые значения, когда активна светлая тема.
           Селекторы вне @layer, поэтому уверенно перебивают утилиты Tailwind.
           ──────────────────────────────────────────────────────────── */

        /* ── Поверхности страницы / панели ── */
        [data-theme="light"] .bg-\[\#0b0c10\] { background-color: #eef1f5; }      /* фон страницы */
        [data-theme="light"] .bg-\[\#111318\],
        [data-theme="light"] .bg-\[\#161920\] { background-color: #ffffff; }      /* карточки/панели */

        /* ── Серые поверхности (карточки → белый, ячейки → светло-серый) ── */
        [data-theme="light"] .bg-gray-900,
        [data-theme="light"] .bg-gray-900\/60 { background-color: #ffffff; }
        [data-theme="light"] .bg-gray-950 { background-color: #f3f4f6; }
        [data-theme="light"] .bg-gray-950\/40 { background-color: #f4f5f7; }
        [data-theme="light"] .bg-gray-950\/20 { background-color: #f6f7f9; }
        [data-theme="light"] .bg-gray-800 { background-color: #e9ebef; }
        [data-theme="light"] .bg-gray-800\/60 { background-color: #eceef2; }
        [data-theme="light"] .bg-gray-700 { background-color: #dfe2e7; }
        [data-theme="light"] .bg-white\/10 { background-color: rgba(0, 0, 0, .05); }

        /* ── hover-фоны ── */
        [data-theme="light"] .hover\:bg-gray-900:hover { background-color: #eef0f3; }
        [data-theme="light"] .hover\:bg-gray-800:hover { background-color: #e9ebef; }

        /* ── Текст ── */
        [data-theme="light"] .text-white,
        [data-theme="light"] .text-gray-200 { color: #1f2937; }
        [data-theme="light"] .text-gray-300 { color: #374151; }
        [data-theme="light"] .text-gray-400 { color: #4b5563; }
        [data-theme="light"] .text-gray-500 { color: #6b7280; }
        [data-theme="light"] .text-gray-600 { color: #8a94a3; }
        [data-theme="light"] .text-gray-700 { color: #6b7280; }
        [data-theme="light"] .hover\:text-white:hover { color: #1f2937; }
        [data-theme="light"] .hover\:text-gray-400:hover { color: #4b5563; }

        /* ── Границы ── */
        [data-theme="light"] .border-gray-900,
        [data-theme="light"] .border-gray-800,
        [data-theme="light"] .border-gray-800\/50 { border-color: #e2e5ea; }
        [data-theme="light"] .border-gray-700,
        [data-theme="light"] .border-gray-700\/50,
        [data-theme="light"] .border-gray-700\/60 { border-color: #d8dce2; }
        [data-theme="light"] .border-gray-600 { border-color: #cbd0d8; }
        [data-theme="light"] .border-white\/20 { border-color: rgba(0, 0, 0, .12); }
        [data-theme="light"] .hover\:border-gray-700:hover,
        [data-theme="light"] .hover\:border-gray-700\/60:hover { border-color: #d8dce2; }
        [data-theme="light"] .hover\:border-gray-600:hover { border-color: #cbd0d8; }

        /* ── Разделители ── */
        [data-theme="light"] .divide-gray-800\/50 > :not([hidden]) ~ :not([hidden]) { border-color: #e2e5ea; }

        /* ── Плейсхолдеры ── */
        [data-theme="light"] .placeholder-gray-600::placeholder,
        [data-theme="light"] .placeholder-gray-700::placeholder { color: #9ca3af; }

        /* ── Пагинация (во вьюхах заданы тёмные !important стили) ── */
        [data-theme="light"] .custom-pagination p { color: #6b7280 !important; }
        [data-theme="light"] .custom-pagination nav a,
        [data-theme="light"] .custom-pagination nav span[aria-disabled="true"] span {
            background-color: #ffffff !important;
            border-color: #e2e5ea !important;
            color: #6b7280 !important;
        }
        [data-theme="light"] .custom-pagination nav a:hover {
            background-color: #f3f4f6 !important;
            color: #f97316 !important;
            border-color: #f97316 !important;
        }
        /* активная страница остаётся оранжевой — её не трогаем */

        /* ── Hover строк таблиц (был полупрозрачный белый — невидим на светлом) ── */
        [data-theme="light"] .usr-row:hover,
        [data-theme="light"] .tkt-row:hover,
        [data-theme="light"] .ord-row:hover { background: rgba(0, 0, 0, .035); }

        /* ── Переключатель тем: показываем одну иконку по активной теме ── */
        .ti-sun, .ti-moon { display: none; }
        [data-theme="dark"] .ti-sun { display: inline-block; }
        [data-theme="light"] .ti-moon { display: inline-block; }
    </style>
</head>

<body class="bg-[#0b0c10] text-gray-200 font-sans antialiased selection:bg-orange-500 selection:text-black">

    <div class="min-h-screen flex flex-col md:flex-row">

        <aside class="w-full md:w-64 bg-[#111318] border-b md:border-b-0 md:border-r border-gray-900 p-6 flex flex-col justify-between">
            <div class="space-y-8">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-7 bg-orange-500 rounded-full shadow-[0_0_15px_rgba(249,115,22,0.5)]"></div>
                        <span class="text-xl font-black uppercase tracking-wider text-white">Ru<span class="text-orange-500">Gear</span></span>
                        <span class="text-[9px] bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-md border border-orange-500/20 font-bold uppercase tracking-widest">Admin</span>
                    </div>

                    {{-- Переключатель темы (как в шапке витрины) --}}
                    <button onclick="toggleTheme()" type="button"
                        class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center border border-gray-800 bg-gray-950 text-gray-400 hover:text-orange-500 hover:border-orange-500/40 transition-colors"
                        aria-label="Переключить тему" title="Переключить тему">
                        {{-- Солнце — видно в тёмной теме (клик → светлая) --}}
                        <svg class="ti-sun w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="4.5" />
                            <path stroke-linecap="round" d="M12 2v2.5M12 19.5V22M4.22 4.22l1.77 1.77M18.01 18.01l1.77 1.77M2 12h2.5M19.5 12H22M4.22 19.78l1.77-1.77M18.01 5.99l1.77-1.77" />
                        </svg>
                        {{-- Луна — видно в светлой теме (клик → тёмная) --}}
                        <svg class="ti-moon w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.64 13a1 1 0 0 0-1.05-.14 8 8 0 0 1-9.45-9.45 1 1 0 0 0-1.19-1.19A10 10 0 1 0 22 14.05a1 1 0 0 0-.36-1.05z" />
                        </svg>
                    </button>
                </div>

                <nav class="space-y-2">

                    <a href="{{ route('admin.statistics') }}"
                        class="flex items-center gap-4 px-4 py-3
   {{ request()->routeIs('admin.statistics')
        ? 'bg-gradient-to-r from-orange-500/10 to-transparent border-l-2 border-orange-500 text-white font-black uppercase tracking-wider'
        : 'text-gray-500 hover:text-white font-semibold'
   }}
   rounded-r-xl transition-all text-sm">

                        <i class="fa-solid fa-chart-line text-lg
    {{ request()->routeIs('admin.statistics') ? 'text-orange-500' : '' }}"></i>

                        Статистика
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-4 px-4 py-3
   {{ request()->routeIs('admin.products.*')
        ? 'bg-gradient-to-r from-orange-500/10 to-transparent border-l-2 border-orange-500 text-white font-black uppercase tracking-wider'
        : 'text-gray-500 hover:text-white font-semibold'
   }}
   rounded-r-xl transition-all text-sm">

                        <i class="fa-solid fa-cubes text-lg
    {{ request()->routeIs('admin.products.*') ? 'text-orange-500' : '' }}"></i>

                        Товарооборот
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center gap-4 px-4 py-3
   {{ request()->routeIs('admin.orders.*')
        ? 'bg-gradient-to-r from-orange-500/10 to-transparent border-l-2 border-orange-500 text-white font-black uppercase tracking-wider'
        : 'text-gray-500 hover:text-white font-semibold'
   }}
   rounded-r-xl transition-all text-sm">

                        <i class="fa-solid fa-box text-lg
    {{ request()->routeIs('admin.orders.*') ? 'text-orange-500' : '' }}"></i>

                        Заказы
                    </a>

                    <a href="{{ route('admin.tickets.index') }}"
                        class="flex items-center gap-4 px-4 py-3
       {{ request()->routeIs('admin.tickets.*')
            ? 'bg-gradient-to-r from-orange-500/10 to-transparent border-l-2 border-orange-500 text-white font-black uppercase tracking-wider'
            : 'text-gray-500 hover:text-white font-semibold'
       }}
       rounded-r-xl transition-all text-sm">

                        <i class="fa-solid fa-headset text-lg
        {{ request()->routeIs('admin.tickets.*') ? 'text-orange-500' : '' }}"></i>

                        Заявки техподдержки
                    </a>


                    <a href="{{ route('admin.users') }}"
                        class="flex items-center gap-4 px-4 py-3
    {{ request()->routeIs('admin.users', 'admin.users.*')
        ? 'bg-gradient-to-r from-orange-500/10 to-transparent border-l-2 border-orange-500 text-white font-black uppercase tracking-wider'
        : 'text-gray-500 hover:text-white font-semibold'
    }}
    rounded-r-xl transition-all text-sm">

                        <i class="fa-solid fa-users text-lg
        {{ request()->routeIs('admin.users', 'admin.users.*') ? 'text-orange-500' : '' }}"></i>

                        Пользователи
                    </a>

                </nav>
            </div>

            <div class="space-y-4 pt-6 border-t border-gray-900">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full py-2.5 bg-gray-950 hover:bg-gray-900 text-xs font-bold text-gray-400 hover:text-orange-500 rounded-xl border border-gray-900 hover:border-orange-500/30 transition-all group">
                    <i class="fa-solid fa-arrow-left-long text-[10px] group-hover:-translate-x-1 transition-transform"></i>
                    Вернуться в профиль
                </a>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center font-black text-black shadow-lg">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="text-xs font-black text-white leading-none">{{ auth()->user()->name ?? 'Administrator' }}</h5>
                        <span class="text-[10px] text-gray-500 italic">root access</span>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-grow p-6 md:p-12 space-y-8">
            {{ $slot }}
        </main>

    </div>

</body>

</html>