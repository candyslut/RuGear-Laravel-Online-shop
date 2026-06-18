<!DOCTYPE html>
<html lang="ru">

<head>
    <script>
        window.livewire_app_url = "{{ config('app.env') === 'production' ? config('app.url') : '' }}";
        // Купленная в мини-маркете тема сайта (слаг для <html data-theme>),
        // null — обычные dark/light. Имеет приоритет над переключателем.
        window.__siteTheme = @json(auth()->user()?->cosmetic_theme);
        // Названия покупных тем для бейджа в меню «Оформление».
        window.SITE_THEMES = { dark: 'Тёмная', light: 'Светлая', cosmic: 'Космос', pink: 'Няшная' };
        // Применяем тему до первой отрисовки, чтобы не было мигания тёмного фона.
        // Без явного выбора пользователя берём системную тему (prefers-color-scheme).
        document.documentElement.setAttribute('data-theme', window.__siteTheme || localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'));
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RuGear | Hardware Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
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
           ТЕМА «КОСМОС» (покупается в мини-маркете, слаг `cosmic`)
           Глубокий индиго-космос: звёздное небо + дрейфующие туманности
           с аврора-переливами. Панели непрозрачные, границы усилены —
           контент читается на любом экране и при любом освещении.
           ──────────────────────────────────────────────────────────── */
        :root[data-theme="cosmic"] {
            --bg-primary: #070a1c;
            --bg-secondary: #11173a;
            --bg-tertiary: #1a2150;
            --text-primary: #eef1ff;
            --text-secondary: #aab4e8;
            --text-tertiary: #7681bd;
            --border-color: #3d4a99;
            --border-color-light: #283273;
            --lf-brand: #8b5cf6;
        }

        /* ════════════════════════════════════════════════════════════
           ТЕМА «НЯШНЫЙ РОЗОВЫЙ» (покупается в мини-маркете, слаг `pink`)
           Нежная пастель: розовый градиент с парящими сердечками,
           ленточка-перелив в шапке и курсор-лапка котика. Светлая
           палитра, поэтому тёмные захардкоженные классы ремапятся
           в розовые пастельные тона (блок ниже, после cosmic).
           ──────────────────────────────────────────────────────────── */
        :root[data-theme="pink"] {
            --bg-primary: #fdf0f7;
            --bg-secondary: #fff7fb;
            --bg-tertiary: #fbe7f2;
            --text-primary: #4a2338;
            --text-secondary: #8a4f6e;
            --text-tertiary: #bd8aa3;
            --border-color: #f2c4dc;
            --border-color-light: #f8dcea;
            --lf-brand: #ec4899;
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

        /* — Стикер-пикер (всплывающее меню в комментариях) — */
        /* Корпус поповера: тёмный градиент → белый с лёгким понижением книзу */
        [data-theme="light"] .from-\[\#13161d\] {
            --tw-gradient-from: #ffffff var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(255 255 255 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="light"] .to-\[\#0d0f15\] { --tw-gradient-to: #f3f4f6 var(--tw-gradient-to-position); }
        /* Панель вкладок сверху */
        [data-theme="light"] .bg-\[\#0b0d12\] { background-color: #f3f4f6; }
        /* Липкие заголовки секций (полупрозрачные поверх грида) */
        [data-theme="light"] .bg-\[\#0d0f15\]\/95 { background-color: rgba(255, 255, 255, .95); }
        /* hover-фон ячеек/вкладок, ещё не покрытый общими правилами */
        [data-theme="light"] .hover\:bg-gray-800\/80:hover { background-color: #e9ebef; }
        [data-theme="light"] .hover\:bg-gray-800\/70:hover { background-color: #ebedf1; }

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

        /* ════════════════════════════════════════════════════════════
           COSMIC THEME OVERRIDES
           1) Живой фон: базовый градиент + туманности (медленный дрейф
              и hue-переливы, transform/filter — композитор, не репейнт)
              и два слоя мерцающих звёзд.
           2) Аврора-линия в шапке.
           3) Ремап захардкоженных тёмных классов (как для светлой темы),
              чтобы карточки/модалки/тосты сели в индиго-палитру.
           ──────────────────────────────────────────────────────────── */

        [data-theme="cosmic"] body { position: relative; z-index: 0; }

        /* — Базовый космос + туманности (низ z-стека body) — */
        [data-theme="cosmic"] body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -2;
            pointer-events: none;
            background:
                radial-gradient(58rem 42rem at 82% -12%, rgba(139, 92, 246, .34), transparent 62%),
                radial-gradient(50rem 38rem at -8% 30%, rgba(34, 211, 238, .20), transparent 60%),
                radial-gradient(46rem 34rem at 70% 80%, rgba(236, 72, 153, .17), transparent 62%),
                radial-gradient(34rem 26rem at 25% 105%, rgba(99, 102, 241, .25), transparent 65%),
                linear-gradient(180deg, #05071a 0%, #0a0f30 48%, #0e0a2e 100%);
            animation: cosmic-nebula 46s ease-in-out infinite alternate;
        }
        @keyframes cosmic-nebula {
            0%   { filter: hue-rotate(0deg) saturate(1); }
            50%  { filter: hue-rotate(28deg) saturate(1.18); }
            100% { filter: hue-rotate(-22deg) saturate(1.05); }
        }

        /* — Звёзды: два слоя точек разного шага, мягкое мерцание — */
        [data-theme="cosmic"] body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background-image:
                radial-gradient(1px 1px at 25px 35px, rgba(255, 255, 255, .9), transparent 55%),
                radial-gradient(1px 1px at 160px 120px, rgba(255, 255, 255, .7), transparent 55%),
                radial-gradient(1.5px 1.5px at 90px 210px, rgba(186, 230, 253, .85), transparent 55%),
                radial-gradient(1px 1px at 230px 60px, rgba(255, 255, 255, .55), transparent 55%),
                radial-gradient(2px 2px at 310px 170px, rgba(221, 214, 254, .8), transparent 55%),
                radial-gradient(1px 1px at 55px 300px, rgba(255, 255, 255, .65), transparent 55%),
                radial-gradient(1.5px 1.5px at 280px 280px, rgba(165, 243, 252, .7), transparent 55%);
            background-size: 357px 343px;
            animation: cosmic-twinkle 5.5s ease-in-out infinite alternate;
        }
        @keyframes cosmic-twinkle {
            0%   { opacity: .45; }
            55%  { opacity: .95; }
            100% { opacity: .6; }
        }

        /* — Аврора-перелив по нижней кромке шапки — */
        [data-theme="cosmic"] header::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 2px;
            background: linear-gradient(90deg, #22d3ee, #a78bfa, #f472b6, #818cf8, #22d3ee);
            background-size: 300% 100%;
            animation: cosmic-aurora 9s linear infinite;
            opacity: .85;
            pointer-events: none;
        }
        @keyframes cosmic-aurora { from { background-position: 0% 0; } to { background-position: 300% 0; } }

        /* — Шапка/футер: лёгкая прозрачность поверх звёзд, но с blur — */
        [data-theme="cosmic"] header { background-color: rgba(13, 18, 48, .82); }

        @media (prefers-reduced-motion: reduce) {
            [data-theme="cosmic"] body::before,
            [data-theme="cosmic"] body::after,
            [data-theme="cosmic"] header::after { animation: none; }
        }

        /* — Акцент: фирменный оранжевый → космический фиолет —
              Бренд-акцент (кнопки, ссылки, фокусы, рамки, логотип) в космосе
              фиолетовый #8b5cf6. Семантика не трогается: монеты — золото,
              огонь серии входов — оранжевый, цвета редкости — как были. — */
        [data-theme="cosmic"] .bg-orange-500,
        [data-theme="cosmic"] .bg-orange-400,
        [data-theme="cosmic"] .hover\:bg-orange-500:hover,
        [data-theme="cosmic"] .group:hover .group-hover\:bg-orange-500 { background-color: #8b5cf6; }
        [data-theme="cosmic"] .hover\:bg-orange-600:hover { background-color: #7c3aed; }
        [data-theme="cosmic"] .hover\:bg-orange-400:hover { background-color: #a78bfa; }
        [data-theme="cosmic"] .bg-orange-500\/50 { background-color: rgba(139, 92, 246, .5); }
        [data-theme="cosmic"] .bg-orange-500\/20,
        [data-theme="cosmic"] .hover\:bg-orange-500\/20:hover { background-color: rgba(139, 92, 246, .2); }
        [data-theme="cosmic"] .bg-orange-500\/15 { background-color: rgba(139, 92, 246, .15); }
        [data-theme="cosmic"] .bg-orange-500\/10 { background-color: rgba(139, 92, 246, .12); }
        [data-theme="cosmic"] .bg-orange-500\/5 { background-color: rgba(139, 92, 246, .07); }

        [data-theme="cosmic"] .text-orange-500,
        [data-theme="cosmic"] .hover\:text-orange-500:hover { color: #a78bfa; }
        [data-theme="cosmic"] .text-orange-400,
        [data-theme="cosmic"] .hover\:text-orange-400:hover { color: #b5a6fb; }
        [data-theme="cosmic"] .text-orange-400\/80 { color: rgba(181, 166, 251, .8); }
        [data-theme="cosmic"] .hover\:text-orange-300:hover { color: #c4b5fd; }

        [data-theme="cosmic"] .border-orange-500,
        [data-theme="cosmic"] .hover\:border-orange-500:hover,
        [data-theme="cosmic"] .focus\:border-orange-500:focus { border-color: #8b5cf6; }
        [data-theme="cosmic"] .border-orange-500\/50,
        [data-theme="cosmic"] .hover\:border-orange-500\/50:hover,
        [data-theme="cosmic"] .focus\:border-orange-500\/50:focus,
        [data-theme="cosmic"] .hover\:border-orange-500\/60:hover { border-color: rgba(139, 92, 246, .55); }
        [data-theme="cosmic"] .border-orange-500\/40,
        [data-theme="cosmic"] .hover\:border-orange-500\/40:hover { border-color: rgba(139, 92, 246, .45); }
        [data-theme="cosmic"] .border-orange-500\/30,
        [data-theme="cosmic"] .hover\:border-orange-500\/30:hover { border-color: rgba(139, 92, 246, .38); }
        [data-theme="cosmic"] .border-orange-500\/20 { border-color: rgba(139, 92, 246, .28); }
        [data-theme="cosmic"] .border-orange-500\/10 { border-color: rgba(139, 92, 246, .16); }

        [data-theme="cosmic"] .ring-orange-500\/50 { --tw-ring-color: rgba(139, 92, 246, .5); }
        [data-theme="cosmic"] .ring-orange-500\/40,
        [data-theme="cosmic"] .hover\:ring-orange-500\/40:hover { --tw-ring-color: rgba(139, 92, 246, .45); }
        [data-theme="cosmic"] .focus\:ring-orange-500:focus { --tw-ring-color: #8b5cf6; }
        [data-theme="cosmic"] .focus\:ring-orange-500\/20:focus { --tw-ring-color: rgba(139, 92, 246, .25); }

        [data-theme="cosmic"] .from-orange-500\/10 {
            --tw-gradient-from: rgb(139 92 246 / .12) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(139 92 246 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="cosmic"] .from-orange-500\/20 {
            --tw-gradient-from: rgb(139 92 246 / .22) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(139 92 246 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }

        /* Произвольные оранжевые glow-тени (shadow-[...rgba(249,115,22...]) —
           ловим по подстроке класса, заменяем фиолетовым свечением. */
        [data-theme="cosmic"] [class*="shadow-["][class*="rgba(249"] { box-shadow: 0 0 22px rgba(139, 92, 246, .42); }

        /* Инлайновые «тёплые» акценты (тосты, колокольчик) идут через var
           с оранжевым фолбэком — здесь даём им космические значения. */
        :root[data-theme="cosmic"] {
            --warm-acc: #b5a6fb;
            --warm-acc-bg: rgba(139, 92, 246, .16);
            --tier-daily: #60a5fa; /* «дневной» тир колеса наград */
        }

        /* — Поверхности: карточки/модалки/тосты → панельный индиго — */
        [data-theme="cosmic"] .bg-\[\#161920\],
        [data-theme="cosmic"] .bg-\[\#14171d\],
        [data-theme="cosmic"] .bg-\[\#1a1d24\],
        [data-theme="cosmic"] .bg-\[\#111318\] { background-color: #11173a; }
        [data-theme="cosmic"] .bg-\[\#161920\]\/50,
        [data-theme="cosmic"] .bg-\[\#161920\]\/40 { background-color: rgba(17, 23, 58, .82); }

        /* — Вложенные/утопленные поверхности (инпуты, ячейки) — */
        [data-theme="cosmic"] .bg-\[\#0f1115\],
        [data-theme="cosmic"] .bg-\[\#0f1117\],
        [data-theme="cosmic"] .bg-\[\#0d0f14\],
        [data-theme="cosmic"] .bg-\[\#13151a\],
        [data-theme="cosmic"] .bg-\[\#0f1115\]\/50 { background-color: #0b1029; }

        /* — Серые фоны — */
        [data-theme="cosmic"] .bg-gray-900,
        [data-theme="cosmic"] .bg-gray-950 { background-color: #0d1233; }
        [data-theme="cosmic"] .bg-gray-800 { background-color: #1a2150; }
        [data-theme="cosmic"] .bg-gray-700 { background-color: #27306b; }
        [data-theme="cosmic"] .bg-gray-500 { background-color: #3d4a99; }
        [data-theme="cosmic"] .bg-gray-900\/80 { background-color: rgba(13, 18, 51, .85); }
        [data-theme="cosmic"] .bg-gray-900\/60 { background-color: rgba(13, 18, 51, .65); }
        [data-theme="cosmic"] .bg-gray-900\/50 { background-color: rgba(13, 18, 51, .55); }
        [data-theme="cosmic"] .bg-gray-900\/40 { background-color: rgba(13, 18, 51, .45); }
        [data-theme="cosmic"] .bg-gray-900\/30 { background-color: rgba(13, 18, 51, .35); }
        [data-theme="cosmic"] .bg-gray-900\/20 { background-color: rgba(13, 18, 51, .25); }
        [data-theme="cosmic"] .bg-gray-800\/70 { background-color: rgba(26, 33, 80, .75); }
        [data-theme="cosmic"] .bg-gray-800\/60 { background-color: rgba(26, 33, 80, .65); }
        [data-theme="cosmic"] .bg-gray-800\/50 { background-color: rgba(26, 33, 80, .55); }
        [data-theme="cosmic"] .bg-gray-800\/30 { background-color: rgba(26, 33, 80, .35); }
        [data-theme="cosmic"] .bg-gray-800\/10 { background-color: rgba(26, 33, 80, .14); }
        [data-theme="cosmic"] .bg-gray-950\/40 { background-color: rgba(9, 13, 38, .5); }
        [data-theme="cosmic"] .bg-gray-950\/20 { background-color: rgba(9, 13, 38, .28); }

        /* — hover-фоны — */
        [data-theme="cosmic"] .hover\:bg-gray-800:hover { background-color: #212a63; }
        [data-theme="cosmic"] .hover\:bg-gray-700:hover { background-color: #2d377a; }
        [data-theme="cosmic"] .hover\:bg-gray-900\/30:hover { background-color: rgba(13, 18, 51, .4); }
        [data-theme="cosmic"] .hover\:bg-gray-900\/50:hover { background-color: rgba(13, 18, 51, .6); }
        [data-theme="cosmic"] .hover\:bg-gray-800\/60:hover { background-color: rgba(26, 33, 80, .7); }
        [data-theme="cosmic"] .hover\:bg-gray-800\/50:hover { background-color: rgba(26, 33, 80, .6); }
        [data-theme="cosmic"] .hover\:bg-gray-800\/80:hover { background-color: rgba(26, 33, 80, .85); }
        [data-theme="cosmic"] .hover\:bg-gray-800\/70:hover { background-color: rgba(26, 33, 80, .78); }

        /* — Текст: серые оттенки → звёздно-сиреневые — */
        [data-theme="cosmic"] .text-gray-300 { color: #c5cdf4; }
        [data-theme="cosmic"] .text-gray-400 { color: #aab4e8; }
        [data-theme="cosmic"] .text-gray-500 { color: #8a95cf; }
        [data-theme="cosmic"] .text-gray-600 { color: #6a76b5; }
        [data-theme="cosmic"] .hover\:text-gray-400:hover { color: #aab4e8; }
        [data-theme="cosmic"] .hover\:text-gray-300:hover { color: #c5cdf4; }
        [data-theme="cosmic"] .group:hover .group-hover\:text-gray-400 { color: #aab4e8; }

        /* — Плейсхолдеры — */
        [data-theme="cosmic"] .placeholder-gray-600::placeholder,
        [data-theme="cosmic"] .placeholder-gray-700::placeholder,
        [data-theme="cosmic"] .placeholder\:text-gray-600::placeholder,
        [data-theme="cosmic"] .placeholder\:text-gray-700::placeholder { color: #6a76b5; }

        /* — Границы: всегда различимы на панелях — */
        [data-theme="cosmic"] .border-gray-900,
        [data-theme="cosmic"] .border-gray-900\/80,
        [data-theme="cosmic"] .border-gray-900\/50,
        [data-theme="cosmic"] .border-gray-800,
        [data-theme="cosmic"] .border-gray-800\/80,
        [data-theme="cosmic"] .border-gray-800\/60,
        [data-theme="cosmic"] .border-gray-800\/50,
        [data-theme="cosmic"] .border-gray-800\/40 { border-color: #323d80; }
        [data-theme="cosmic"] .border-gray-700,
        [data-theme="cosmic"] .border-gray-700\/60,
        [data-theme="cosmic"] .border-gray-700\/50,
        [data-theme="cosmic"] .border-gray-700\/30 { border-color: #3d4a99; }
        [data-theme="cosmic"] .border-gray-600 { border-color: #4d5bb0; }
        [data-theme="cosmic"] .hover\:border-gray-700:hover,
        [data-theme="cosmic"] .hover\:border-gray-700\/60:hover { border-color: #4d5bb0; }
        [data-theme="cosmic"] .hover\:border-gray-600:hover { border-color: #5e6cc4; }

        /* — Разделители — */
        [data-theme="cosmic"] .divide-gray-800 > :not([hidden]) ~ :not([hidden]),
        [data-theme="cosmic"] .divide-gray-800\/60 > :not([hidden]) ~ :not([hidden]),
        [data-theme="cosmic"] .divide-gray-800\/50 > :not([hidden]) ~ :not([hidden]),
        [data-theme="cosmic"] .divide-gray-900\/60 > :not([hidden]) ~ :not([hidden]) { border-color: #2a3470; }

        /* — Градиенты: серые и тёмные hex-стопы → индиго — */
        [data-theme="cosmic"] .from-gray-800 {
            --tw-gradient-from: #1a2150 var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(26 33 80 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="cosmic"] .to-gray-900 { --tw-gradient-to: #0d1233 var(--tw-gradient-to-position); }
        [data-theme="cosmic"] .from-gray-800\/40 {
            --tw-gradient-from: rgb(26 33 80 / .45) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(26 33 80 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="cosmic"] .to-gray-900\/60 { --tw-gradient-to: rgb(13 18 51 / .65) var(--tw-gradient-to-position); }
        [data-theme="cosmic"] .from-\[\#1a1d24\],
        [data-theme="cosmic"] .from-\[\#161920\] {
            --tw-gradient-from: #141b45 var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(20 27 69 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="cosmic"] .via-\[\#161920\] {
            --tw-gradient-to: rgb(17 23 58 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), #11173a var(--tw-gradient-via-position), var(--tw-gradient-to);
        }
        [data-theme="cosmic"] .to-\[\#0f1115\] { --tw-gradient-to: #0b1029 var(--tw-gradient-to-position); }

        /* — Стикер-пикер — */
        [data-theme="cosmic"] .from-\[\#13161d\] {
            --tw-gradient-from: #11173a var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(17 23 58 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="cosmic"] .to-\[\#0d0f15\] { --tw-gradient-to: #0b1029 var(--tw-gradient-to-position); }
        [data-theme="cosmic"] .bg-\[\#0b0d12\] { background-color: #0b1029; }
        [data-theme="cosmic"] .bg-\[\#0d0f15\]\/95 { background-color: rgba(13, 18, 48, .95); }

        /* — Пагинация (постраничные стили задают тёмный фон через !important) — */
        [data-theme="cosmic"] .custom-pagination a,
        [data-theme="cosmic"] .custom-pagination nav a,
        [data-theme="cosmic"] .custom-pagination span[aria-disabled="true"] span {
            background-color: #11173a !important;
            border-color: #3d4a99 !important;
            color: #aab4e8 !important;
        }
        [data-theme="cosmic"] .custom-pagination a:hover,
        [data-theme="cosmic"] .custom-pagination nav a:hover {
            background-color: #1a2150 !important;
            color: #eef1ff !important;
        }

        /* ════════════════════════════════════════════════════════════
           PINK THEME OVERRIDES («Няшный розовый»)
           1) Пастельный фон: розовый градиент + парящие сердечки.
           2) Ленточка-перелив в шапке и курсор-лапка котика.
           3) Ремап захардкоженных тёмных классов в розовую пастель
              (по образцу light/cosmic-оверрайдов выше).
           ──────────────────────────────────────────────────────────── */

        [data-theme="pink"] body { position: relative; z-index: 0; }

        /* — Пастельная база + мягкие цветные пятна — */
        [data-theme="pink"] body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -2;
            pointer-events: none;
            background:
                radial-gradient(46rem 34rem at 85% -10%, rgba(244, 114, 182, .22), transparent 65%),
                radial-gradient(40rem 30rem at -8% 35%, rgba(216, 180, 254, .20), transparent 62%),
                radial-gradient(36rem 28rem at 75% 88%, rgba(253, 186, 116, .14), transparent 65%),
                linear-gradient(180deg, #fff3f9 0%, #fdeaf4 52%, #fce3f0 100%);
        }

        /* — Парящие сердечки и блёстки (тайл медленно плывёт вверх) — */
        [data-theme="pink"] body::after {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180' viewBox='0 0 180 180'%3E%3Cg fill='%23ec4899'%3E%3Cpath opacity='.13' transform='translate(20,24) rotate(-14 12 12)' d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z'/%3E%3Cpath opacity='.09' transform='translate(116,84) rotate(12 12 12) scale(.8)' d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z'/%3E%3Cpath opacity='.11' transform='translate(58,134) rotate(-7 12 12) scale(.55)' d='M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z'/%3E%3Ccircle opacity='.28' cx='142' cy='34' r='1.6'/%3E%3Ccircle opacity='.22' cx='38' cy='98' r='1.3'/%3E%3Ccircle opacity='.24' cx='160' cy='150' r='1.5'/%3E%3Ccircle opacity='.2' cx='14' cy='160' r='1.2'/%3E%3Ccircle opacity='.22' cx='92' cy='12' r='1.3'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 180px 180px;
            animation: pink-hearts-float 38s linear infinite;
        }
        @keyframes pink-hearts-float { from { background-position: 0 0; } to { background-position: 0 -180px; } }

        /* — Ленточка-перелив по нижней кромке шапки — */
        [data-theme="pink"] header { background-color: rgba(255, 247, 251, .85); }
        [data-theme="pink"] header::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 2px;
            background: linear-gradient(90deg, #f9a8d4, #f472b6, #fbcfe8, #fda4af, #f9a8d4);
            background-size: 300% 100%;
            animation: pink-ribbon 12s linear infinite;
            opacity: .9;
            pointer-events: none;
        }
        @keyframes pink-ribbon { from { background-position: 0% 0; } to { background-position: 300% 0; } }

        @media (prefers-reduced-motion: reduce) {
            [data-theme="pink"] body::after,
            [data-theme="pink"] header::after { animation: none; }
        }

        /* — Курсор-лапка котика: обычная (светлая) и кликабельная (ярче).
              Текстовые поля не трогаем — у них остаётся курсор `text`. — */
        [data-theme="pink"],
        [data-theme="pink"] body {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='26' height='26' viewBox='0 0 26 26'%3E%3Cg fill='%23f472b6' stroke='%23ffffff' stroke-width='1.2'%3E%3Cellipse cx='9.4' cy='6.8' rx='2.4' ry='3.3' transform='rotate(-8 9.4 6.8)'/%3E%3Cellipse cx='16.6' cy='6.8' rx='2.4' ry='3.3' transform='rotate(8 16.6 6.8)'/%3E%3Cellipse cx='3.9' cy='11.6' rx='2.2' ry='3' transform='rotate(-28 3.9 11.6)'/%3E%3Cellipse cx='22.1' cy='11.6' rx='2.2' ry='3' transform='rotate(28 22.1 11.6)'/%3E%3Cpath d='M13 11.8c3.8 0 6.6 2.6 6.6 5.8 0 3-2.5 4.9-6.6 4.9s-6.6-1.9-6.6-4.9c0-3.2 2.8-5.8 6.6-5.8z'/%3E%3C/g%3E%3C/svg%3E") 13 13, auto;
        }
        [data-theme="pink"] a,
        [data-theme="pink"] button,
        [data-theme="pink"] [role="button"],
        [data-theme="pink"] label,
        [data-theme="pink"] select,
        [data-theme="pink"] summary,
        [data-theme="pink"] input[type="checkbox"],
        [data-theme="pink"] input[type="radio"],
        [data-theme="pink"] input[type="range"],
        [data-theme="pink"] input[type="submit"],
        [data-theme="pink"] .cursor-pointer {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='26' height='26' viewBox='0 0 26 26'%3E%3Cg fill='%23ec4899' stroke='%23ffffff' stroke-width='1.2'%3E%3Cellipse cx='9.4' cy='6.8' rx='2.4' ry='3.3' transform='rotate(-8 9.4 6.8)'/%3E%3Cellipse cx='16.6' cy='6.8' rx='2.4' ry='3.3' transform='rotate(8 16.6 6.8)'/%3E%3Cellipse cx='3.9' cy='11.6' rx='2.2' ry='3' transform='rotate(-28 3.9 11.6)'/%3E%3Cellipse cx='22.1' cy='11.6' rx='2.2' ry='3' transform='rotate(28 22.1 11.6)'/%3E%3Cpath d='M13 11.8c3.8 0 6.6 2.6 6.6 5.8 0 3-2.5 4.9-6.6 4.9s-6.6-1.9-6.6-4.9c0-3.2 2.8-5.8 6.6-5.8z'/%3E%3C/g%3E%3C/svg%3E") 13 13, pointer;
        }

        /* — Акцент: фирменный оранжевый → нежный розовый —
              Бренд-акцент (кнопки, ссылки, фокусы, рамки, логотип) в розовой
              теме #ec4899. Семантика не трогается: монеты — золото,
              огонь серии входов — оранжевый, цвета редкости — как были. — */
        [data-theme="pink"] .bg-orange-500,
        [data-theme="pink"] .bg-orange-400,
        [data-theme="pink"] .hover\:bg-orange-500:hover,
        [data-theme="pink"] .group:hover .group-hover\:bg-orange-500 { background-color: #ec4899; }
        [data-theme="pink"] .hover\:bg-orange-600:hover { background-color: #db2777; }
        [data-theme="pink"] .hover\:bg-orange-400:hover { background-color: #f472b6; }
        [data-theme="pink"] .bg-orange-500\/50 { background-color: rgba(236, 72, 153, .5); }
        [data-theme="pink"] .bg-orange-500\/20,
        [data-theme="pink"] .hover\:bg-orange-500\/20:hover { background-color: rgba(236, 72, 153, .2); }
        [data-theme="pink"] .bg-orange-500\/15 { background-color: rgba(236, 72, 153, .15); }
        [data-theme="pink"] .bg-orange-500\/10 { background-color: rgba(236, 72, 153, .12); }
        [data-theme="pink"] .bg-orange-500\/5 { background-color: rgba(236, 72, 153, .07); }

        [data-theme="pink"] .text-orange-500,
        [data-theme="pink"] .hover\:text-orange-500:hover { color: #db2777; }
        [data-theme="pink"] .text-orange-400,
        [data-theme="pink"] .hover\:text-orange-400:hover { color: #ec4899; }
        [data-theme="pink"] .text-orange-400\/80 { color: rgba(236, 72, 153, .8); }
        [data-theme="pink"] .hover\:text-orange-300:hover { color: #f472b6; }

        [data-theme="pink"] .border-orange-500,
        [data-theme="pink"] .hover\:border-orange-500:hover,
        [data-theme="pink"] .focus\:border-orange-500:focus { border-color: #ec4899; }
        [data-theme="pink"] .border-orange-500\/50,
        [data-theme="pink"] .hover\:border-orange-500\/50:hover,
        [data-theme="pink"] .focus\:border-orange-500\/50:focus,
        [data-theme="pink"] .hover\:border-orange-500\/60:hover { border-color: rgba(236, 72, 153, .55); }
        [data-theme="pink"] .border-orange-500\/40,
        [data-theme="pink"] .hover\:border-orange-500\/40:hover { border-color: rgba(236, 72, 153, .45); }
        [data-theme="pink"] .border-orange-500\/30,
        [data-theme="pink"] .hover\:border-orange-500\/30:hover { border-color: rgba(236, 72, 153, .38); }
        [data-theme="pink"] .border-orange-500\/20 { border-color: rgba(236, 72, 153, .28); }
        [data-theme="pink"] .border-orange-500\/10 { border-color: rgba(236, 72, 153, .16); }

        [data-theme="pink"] .ring-orange-500\/50 { --tw-ring-color: rgba(236, 72, 153, .5); }
        [data-theme="pink"] .ring-orange-500\/40,
        [data-theme="pink"] .hover\:ring-orange-500\/40:hover { --tw-ring-color: rgba(236, 72, 153, .45); }
        [data-theme="pink"] .focus\:ring-orange-500:focus { --tw-ring-color: #ec4899; }
        [data-theme="pink"] .focus\:ring-orange-500\/20:focus { --tw-ring-color: rgba(236, 72, 153, .25); }

        [data-theme="pink"] .from-orange-500\/10 {
            --tw-gradient-from: rgb(236 72 153 / .12) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(236 72 153 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="pink"] .from-orange-500\/20 {
            --tw-gradient-from: rgb(236 72 153 / .22) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(236 72 153 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }

        /* Произвольные оранжевые glow-тени → мягкое розовое свечение. */
        [data-theme="pink"] [class*="shadow-["][class*="rgba(249"] { box-shadow: 0 0 22px rgba(236, 72, 153, .35); }

        /* Инлайновые «тёплые» акценты (тосты, колокольчик) — розовые значения. */
        :root[data-theme="pink"] {
            --warm-acc: #db2777;
            --warm-acc-bg: rgba(236, 72, 153, .14);
        }

        /* — Поверхности: карточки/модалки/тосты → розово-белые — */
        [data-theme="pink"] .bg-\[\#161920\],
        [data-theme="pink"] .bg-\[\#14171d\],
        [data-theme="pink"] .bg-\[\#1a1d24\],
        [data-theme="pink"] .bg-\[\#111318\] { background-color: #fff7fb; }
        [data-theme="pink"] .bg-\[\#161920\]\/50,
        [data-theme="pink"] .bg-\[\#161920\]\/40 { background-color: #fff7fb; }

        /* — Вложенные/утопленные поверхности (инпуты, ячейки) — */
        [data-theme="pink"] .bg-\[\#0f1115\],
        [data-theme="pink"] .bg-\[\#0f1117\],
        [data-theme="pink"] .bg-\[\#0d0f14\],
        [data-theme="pink"] .bg-\[\#13151a\],
        [data-theme="pink"] .bg-\[\#0f1115\]\/50 { background-color: #fbe7f2; }

        /* — Серые фоны → розовая пастель — */
        [data-theme="pink"] .bg-gray-900,
        [data-theme="pink"] .bg-gray-950 { background-color: #fceef6; }
        [data-theme="pink"] .bg-gray-800 { background-color: #f9dfee; }
        [data-theme="pink"] .bg-gray-700 { background-color: #f3cfe3; }
        [data-theme="pink"] .bg-gray-500 { background-color: #e9b6d2; }
        [data-theme="pink"] .bg-gray-900\/80 { background-color: #fceef6; }
        [data-theme="pink"] .bg-gray-900\/60 { background-color: #fcf0f7; }
        [data-theme="pink"] .bg-gray-900\/50 { background-color: #fdf1f8; }
        [data-theme="pink"] .bg-gray-900\/40 { background-color: #fdf2f8; }
        [data-theme="pink"] .bg-gray-900\/30 { background-color: #fdf3f9; }
        [data-theme="pink"] .bg-gray-900\/20 { background-color: #fef5fa; }
        [data-theme="pink"] .bg-gray-800\/70 { background-color: #f9e1ee; }
        [data-theme="pink"] .bg-gray-800\/60 { background-color: #fae3f0; }
        [data-theme="pink"] .bg-gray-800\/50 { background-color: #fae5f1; }
        [data-theme="pink"] .bg-gray-800\/30 { background-color: #fbeaf4; }
        [data-theme="pink"] .bg-gray-800\/10 { background-color: #fdf2f8; }
        [data-theme="pink"] .bg-gray-950\/40 { background-color: #fdf1f8; }
        [data-theme="pink"] .bg-gray-950\/20 { background-color: #fef5fa; }

        /* — hover-фоны — */
        [data-theme="pink"] .hover\:bg-gray-800:hover { background-color: #f6d9e9; }
        [data-theme="pink"] .hover\:bg-gray-700:hover { background-color: #f0c9df; }
        [data-theme="pink"] .hover\:bg-gray-900\/30:hover { background-color: #fdf3f9; }
        [data-theme="pink"] .hover\:bg-gray-900\/50:hover { background-color: #fdf1f8; }
        [data-theme="pink"] .hover\:bg-gray-800\/60:hover { background-color: #fae3f0; }
        [data-theme="pink"] .hover\:bg-gray-800\/50:hover { background-color: #fae5f1; }
        [data-theme="pink"] .hover\:bg-gray-800\/80:hover { background-color: #f8dfec; }
        [data-theme="pink"] .hover\:bg-gray-800\/70:hover { background-color: #f9e1ee; }

        /* — Текст: белый/серый → тёплый розово-коричневый — */
        [data-theme="pink"] .text-white,
        [data-theme="pink"] .text-gray-200 { color: #4a2338; }
        [data-theme="pink"] .text-gray-300 { color: #5e2f49; }
        [data-theme="pink"] .text-gray-400 { color: #8a4f6e; }
        [data-theme="pink"] .text-gray-500 { color: #a96f8e; }
        [data-theme="pink"] .text-gray-600 { color: #c694ad; }
        [data-theme="pink"] .hover\:text-white:hover,
        [data-theme="pink"] .group:hover .group-hover\:text-white { color: #4a2338; }
        [data-theme="pink"] .hover\:text-gray-400:hover { color: #8a4f6e; }
        [data-theme="pink"] .hover\:text-gray-300:hover { color: #5e2f49; }
        [data-theme="pink"] .group:hover .group-hover\:text-gray-400 { color: #8a4f6e; }

        /* — Плейсхолдеры — */
        [data-theme="pink"] .placeholder-gray-600::placeholder,
        [data-theme="pink"] .placeholder-gray-700::placeholder,
        [data-theme="pink"] .placeholder\:text-gray-600::placeholder,
        [data-theme="pink"] .placeholder\:text-gray-700::placeholder { color: #c694ad; }

        /* — Границы — */
        [data-theme="pink"] .border-gray-900,
        [data-theme="pink"] .border-gray-900\/80,
        [data-theme="pink"] .border-gray-900\/50,
        [data-theme="pink"] .border-gray-800,
        [data-theme="pink"] .border-gray-800\/80,
        [data-theme="pink"] .border-gray-800\/60,
        [data-theme="pink"] .border-gray-800\/50,
        [data-theme="pink"] .border-gray-800\/40 { border-color: #f4cce1; }
        [data-theme="pink"] .border-gray-700,
        [data-theme="pink"] .border-gray-700\/60,
        [data-theme="pink"] .border-gray-700\/50,
        [data-theme="pink"] .border-gray-700\/30 { border-color: #ecb9d5; }
        [data-theme="pink"] .border-gray-600 { border-color: #e2a3c7; }
        [data-theme="pink"] .hover\:border-gray-700:hover,
        [data-theme="pink"] .hover\:border-gray-700\/60:hover { border-color: #ecb9d5; }
        [data-theme="pink"] .hover\:border-gray-600:hover { border-color: #e2a3c7; }

        /* — Разделители — */
        [data-theme="pink"] .divide-gray-800 > :not([hidden]) ~ :not([hidden]),
        [data-theme="pink"] .divide-gray-800\/60 > :not([hidden]) ~ :not([hidden]),
        [data-theme="pink"] .divide-gray-800\/50 > :not([hidden]) ~ :not([hidden]),
        [data-theme="pink"] .divide-gray-900\/60 > :not([hidden]) ~ :not([hidden]) { border-color: #f4cce1; }

        /* — Градиенты: серые и тёмные hex-стопы → розовая пастель — */
        [data-theme="pink"] .from-gray-800 {
            --tw-gradient-from: #f9dfee var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(249 223 238 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="pink"] .to-gray-900 { --tw-gradient-to: #fceef6 var(--tw-gradient-to-position); }
        [data-theme="pink"] .from-gray-800\/40 {
            --tw-gradient-from: rgb(249 223 238 / .45) var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(249 223 238 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="pink"] .to-gray-900\/60 { --tw-gradient-to: rgb(252 238 246 / .65) var(--tw-gradient-to-position); }
        [data-theme="pink"] .from-\[\#1a1d24\],
        [data-theme="pink"] .from-\[\#161920\] {
            --tw-gradient-from: #fff7fb var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(255 247 251 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="pink"] .via-\[\#161920\] {
            --tw-gradient-to: rgb(254 244 249 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), #fef4f9 var(--tw-gradient-via-position), var(--tw-gradient-to);
        }
        [data-theme="pink"] .to-\[\#0f1115\] { --tw-gradient-to: #fbe7f2 var(--tw-gradient-to-position); }

        /* — Стикер-пикер — */
        [data-theme="pink"] .from-\[\#13161d\] {
            --tw-gradient-from: #fff7fb var(--tw-gradient-from-position);
            --tw-gradient-to: rgb(255 247 251 / 0) var(--tw-gradient-to-position);
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
        }
        [data-theme="pink"] .to-\[\#0d0f15\] { --tw-gradient-to: #fbe7f2 var(--tw-gradient-to-position); }
        [data-theme="pink"] .bg-\[\#0b0d12\] { background-color: #fbe7f2; }
        [data-theme="pink"] .bg-\[\#0d0f15\]\/95 { background-color: rgba(255, 247, 251, .95); }

        /* — Пагинация — */
        [data-theme="pink"] .custom-pagination a,
        [data-theme="pink"] .custom-pagination nav a,
        [data-theme="pink"] .custom-pagination span[aria-disabled="true"] span {
            background-color: #fff7fb !important;
            border-color: #f2c4dc !important;
            color: #a96f8e !important;
        }
        [data-theme="pink"] .custom-pagination a:hover,
        [data-theme="pink"] .custom-pagination nav a:hover {
            background-color: #fbe7f2 !important;
            color: #4a2338 !important;
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

        /* ─── Live Feed strip — editorial, restrained palette ──────────────── */
        /* Accent (--lf-accent) is a desaturated, cool, per-type hue set inline.
           Colour is used sparingly: a 5px node, the avatar ring and hovers —
           the cards themselves stay neutral, so the look reads premium not loud.
           The feed fades softly at its right edge (alpha mask, not a gradient). */
        /* The strip itself — a clearly framed band (crisp hairlines that read in
           the light theme too) with its own surface. */
        #live-feed-bar {
            background: var(--bg-secondary);
            border-top: 1px solid color-mix(in srgb, var(--text-primary) 22%, var(--border-color));
            border-bottom: 1px solid color-mix(in srgb, var(--text-primary) 22%, var(--border-color));
        }
        .lf-bar-label {
            flex-shrink: 0; display: flex; align-items: center; gap: 7px;
            padding-right: 1rem; margin-right: 1rem;
            border-right: 1px solid var(--border-color);
            font-size: 10px; font-weight: 800; letter-spacing: .22em;
            text-transform: uppercase; color: var(--text-secondary);
        }
        .lf-bar-label::before {
            content: ""; width: 6px; height: 6px; border-radius: 2px;
            background: var(--text-tertiary); transform: rotate(45deg);
        }
        #live-feed-track {
            -webkit-mask-image: linear-gradient(to right, #000 calc(100% - 64px), transparent);
                    mask-image: linear-gradient(to right, #000 calc(100% - 64px), transparent);
        }

        /* avatar — neutral circle with a hairline ring */
        .lf-ava {
            flex-shrink: 0; border-radius: 50%; object-fit: cover;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: var(--text-secondary);
            background: var(--bg-tertiary);
            box-shadow: inset 0 0 0 1px var(--border-color);
        }
        .lf-ava-sm { width: 28px; height: 28px; font-size: 11px; }
        .lf-ava-lg { width: 38px; height: 38px; font-size: 13px; }

        /* eyebrow — uppercase micro-label with a small accent node */
        .lf-eyebrow {
            display: flex; align-items: center; gap: 6px; min-width: 0;
            font-size: 9px; font-weight: 800; letter-spacing: .15em;
            text-transform: uppercase; line-height: 1; color: var(--lf-accent);
        }
        .lf-eyebrow-txt { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .lf-node { width: 5px; height: 5px; border-radius: 50%; background: var(--lf-accent); flex-shrink: 0; }
        .lf-strong { color: var(--lf-accent); font-weight: 800; }

        /* primary line — name · value, single line with ellipsis */
        .lf-body { min-width: 0; display: flex; flex-direction: column; gap: 5px; }
        .lf-row  { max-width: 210px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lf-name { font-size: 12.5px; font-weight: 600; color: var(--text-primary); }
        .lf-dot  { color: var(--text-tertiary); margin: 0 .3rem; }
        .lf-val  { font-size: 12px; font-weight: 500; color: var(--text-secondary); }

        /* recent chip — flat with a neutral hairline + a brand decorative stripe */
        .lf-block {
            display: flex; align-items: center; gap: .6rem; flex-shrink: 0;
            height: 46px; padding: 0 .9rem 0 .7rem;
            border: 1px solid color-mix(in srgb, var(--text-primary) 18%, var(--border-color));
            border-left: 3px solid var(--lf-brand, #f97316);
            border-radius: 9px;
            background: transparent;
            transition: opacity .5s ease, transform .5s cubic-bezier(.34,1.4,.64,1),
                        background-color .25s ease;
        }
        .lf-block:hover { background: color-mix(in srgb, var(--text-primary) 5%, transparent); }
        .lf-block.enter { opacity: 0; transform: translateX(-22px); }

        /* event of the day — filled, elevated, divider-separated */
        .lf-eod-zone {
            display: flex; align-items: center; flex-shrink: 0;
            padding-right: 1.1rem; margin-right: 1.1rem;
            border-right: 1px solid var(--border-color);
        }
        .lf-eod {
            display: flex; align-items: center; gap: .7rem; flex-shrink: 0;
            height: 52px; padding: 0 1.05rem 0 .8rem;
            border: 1px solid color-mix(in srgb, var(--lf-brand, #f97316) 45%, var(--border-color));
            border-left: 4px solid var(--lf-brand, #f97316);
            border-radius: 11px;
            background: color-mix(in srgb, var(--lf-brand, #f97316) 10%, var(--bg-secondary));
        }
        .lf-eod .lf-ava { box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--lf-brand, #f97316) 50%, var(--border-color)); }
        .lf-eod .lf-row { max-width: 240px; }
        .lf-eod .lf-name { font-size: 13px; font-weight: 700; }
        /* «Событие дня» — a filled brand badge so it clearly reads as featured */
        .lf-eod-head { display: flex; align-items: center; gap: 7px; min-width: 0; }
        .lf-eod-badge {
            flex-shrink: 0;
            padding: 2px 7px; border-radius: 5px;
            background: var(--lf-brand, #f97316); color: #fff;
            font-size: 8.5px; font-weight: 800; letter-spacing: .12em;
            text-transform: uppercase; line-height: 1.5;
        }
        .lf-eod-head .lf-strong {
            font-size: 9px; font-weight: 800; letter-spacing: .12em;
            text-transform: uppercase; color: var(--lf-accent);
        }

        @media (prefers-reduced-motion: reduce) {
            .lf-block { transition: border-color .25s ease, background-color .25s ease; }
            .lf-block.enter { opacity: 1; transform: none; }
        }

        /* cursor tooltip — the full, untruncated card shown on hover */
        .lf-tip {
            position: fixed; z-index: 1000; pointer-events: none;
            opacity: 0; transform: translateY(4px);
            transition: opacity .13s ease, transform .13s ease;
            max-width: 300px; padding: .7rem .8rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-left: 3px solid var(--lf-brand, #f97316);
            border-radius: 10px;
        }
        .lf-tip.show { opacity: 1; transform: translateY(0); }
        .lf-tip .lf-eyebrow { margin-bottom: 8px; }
        .lf-tip .lf-tip-name { font-size: 13.5px; font-weight: 700; color: var(--text-primary); line-height: 1.3; white-space: normal; }
        .lf-tip .lf-tip-val  { font-size: 12.5px; font-weight: 500; color: var(--text-secondary); margin-top: 3px; line-height: 1.3; white-space: normal; }
        .lf-tip .lf-tip-time { font-size: 10.5px; color: var(--text-tertiary); margin-top: 8px; letter-spacing: .04em; }

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
                    <div
                        class="relative"
                        x-data="{
                            open: false,
                            unread: false,
                            syncUnread() { this.unread = !!document.querySelector('[data-bell-badge]'); },
                        }"
                        x-init="
                            syncUnread();
                            $nextTick(() => syncUnread());
                            const host = document.getElementById('notif-bell-host');
                            if (host) {
                                const mo = new MutationObserver(() => syncUnread());
                                mo.observe(host, { childList: true, subtree: true });
                            }
                        "
                        @keydown.escape.window="open = false"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open.toString()"
                            class="relative w-9 h-9 rounded-lg flex items-center justify-center border border-[var(--border-color)] bg-[var(--bg-tertiary)] text-[var(--text-secondary)] hover:text-orange-500 hover:border-orange-500/40 transition-colors"
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
                            {{-- Индикатор непрочитанных уведомлений: дублирует бейдж
                                 колокольчика (который спрятан внутри меню), чтобы он был
                                 виден и при закрытом меню. Видимостью управляет JS,
                                 синхронизирующий его с бейджем колокольчика. --}}
                            @auth
                            <span x-show="unread && !open" x-cloak style="display: none;" class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full rounded-full bg-orange-500 opacity-60 animate-ping"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-orange-500 ring-2 ring-[var(--bg-secondary)]"></span>
                            </span>
                            @endauth
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
                                @auth
                                <button type="button"
                                        @click="open = false; Livewire.dispatch('open-quests')"
                                        class="w-full text-left block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-tertiary)] transition">
                                    Ежедневные задания
                                </button>
                                @endauth
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

                            {{-- Уведомления — на своей строке (это не «оформление») --}}
                            @auth
                            <div class="flex items-center justify-between gap-3 px-4 py-3 border-t border-[var(--border-color-light)]">
                                <span class="text-sm text-[var(--text-secondary)]">Уведомления</span>
                                <div id="notif-bell-host" class="flex items-center">
                                    @livewire('notification-bell')
                                </div>
                            </div>
                            @endauth

                            {{-- Переключатель темы --}}
                            <div class="flex items-center justify-between gap-3 px-4 py-3 border-t border-[var(--border-color-light)]">
                                <span class="text-sm text-[var(--text-secondary)]">Оформление</span>
                                <div class="flex items-center gap-3">
                                    {{-- Бейдж купленной темы: пока она надета, dark/light
                                         переключатель спрятан (темой управляет мини-маркет).
                                         Видимостью рулит syncSiteThemeUi(). --}}
                                    <a id="site-theme-badge" href="{{ route('market.index') }}"
                                       class="hidden items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-lg border border-[var(--border-color)] bg-[var(--bg-tertiary)] text-[var(--text-primary)] hover:border-violet-400/60 transition-colors"
                                       title="Тема из мини-маркета — управление там">
                                        <span id="site-theme-badge-name"></span>
                                    </a>
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
                        </div>
                        <div class="relative" id="avatar-menu-wrap">
                            <button
                                id="avatar-btn"
                                data-user-avatar
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
                                    <p class="text-xs text-[var(--text-secondary)] font-semibold truncate">{{ auth()->user()->name }}</p>
                                    @if(auth()->user()->full_name)
                                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5 truncate">{{ auth()->user()->full_name }}</p>
                                    @endif
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

    {{-- ─── Живая лента: полоска событий под шапкой ──────────────────────── --}}
    <x-live-feed />

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

        {{-- ─── Streak toast — same reward-toast style ────────────────────── --}}
        @if(session('streak_awarded'))
        @php $st = session('streak_awarded'); @endphp
        <div id="t-streak" class="w-80 bg-[#1a1d24] border border-orange-500/30 rounded-2xl p-4 shadow-2xl toast pointer-events-auto">
            <div class="flex gap-3 items-start">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--warm-acc-bg, rgba(249,115,22,0.15))">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" style="color:var(--warm-acc, #fb923c)"><path d="M12.5 2c.4 2.8 2.3 4.4 3.6 6 1.2 1.5 1.9 3 1.9 4.8a6 6 0 11-12 0c0-1.6.6-3 1.6-4.2.2.9.9 1.5 1.8 1.5 1.2 0 1.8-1 1.3-2.5C11.8 5.4 12 3.6 12.5 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--warm-acc, #fb923c)">Серия входов</p>
                    <p class="text-sm font-bold text-white mt-0.5">День {{ $st['streak'] }} подряд!</p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-gray-400">+{{ $st['xp'] }} XP</span>
                        <span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b;">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                            </svg>
                            +{{ $st['coins'] }} монет
                        </span>
                    </div>
                </div>
                <button onclick="closeToast('t-streak')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>
            </div>
        </div>
        @endif

        {{-- ─── Rank-up toast ──────────────────────────────────────────────── --}}
        @if(session('rank_up'))
        @php $rk = session('rank_up'); @endphp
        <div id="t-rank" class="w-80 bg-[#1a1d24] rounded-2xl p-4 shadow-2xl toast pointer-events-auto" style="border:1px solid {{ $rk['color'] ?? '#fbbf24' }}66;">
            <div class="flex gap-3 items-start">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $rk['color'] ?? '#fbbf24' }}26;color:{{ $rk['color'] ?? '#fbbf24' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5 3.4 7.7 8 9 4.6-1.3 8-4 8-9V5l-8-3z"/><polyline points="8.5 11.5 11 14 15.5 9.5"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest" style="color:{{ $rk['color'] ?? '#fbbf24' }}">Новый ранг!</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $rk['name'] ?? '' }}</p>
                    @if(($rk['coins'] ?? 0) > 0)
                    <span class="flex items-center gap-1 text-xs font-bold mt-1" style="color:#f59e0b;">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                            <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                        </svg>
                        +{{ $rk['coins'] }} монет
                    </span>
                    @endif
                </div>
                <button onclick="closeToast('t-rank')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>
            </div>
        </div>
        @endif

        {{-- ─── Quest-completed queue (replayed by JS like achievements) ───── --}}
        @if(session('quests_completed'))
        <div id="quests-queue" data-quests="{{ json_encode(session('quests_completed')) }}" class="hidden"></div>
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

        function showToast(type, data, updateCoins = true) {
            const wrap = document.getElementById('toast-wrap');
            if (!wrap) return;
            const id = 'dyn-toast-' + Date.now();
            const el = document.createElement('div');
            el.id = id;
            el.className = 'w-80 bg-[#1a1d24] rounded-2xl p-4 shadow-2xl toast pointer-events-auto';

            if (type === 'achievement') {
                el.style.border = '1px solid var(--warm-acc-bg, rgba(249,115,22,0.3))';
                el.innerHTML =
                    `<div class="flex gap-3 items-start">` +
                        `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:var(--warm-acc-bg, rgba(249,115,22,0.15))">` +
                            `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" style="color:var(--warm-acc, #fb923c)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>` +
                        `</div>` +
                        `<div class="flex-1 min-w-0">` +
                            `<p class="text-xs font-bold uppercase tracking-widest" style="color:var(--warm-acc, #fb923c)">Достижение разблокировано</p>` +
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
            } else if (type === 'rankup') {
                const c = data.color || '#fbbf24';
                el.style.border = '1px solid ' + c + '66';
                const coinLine = data.coins > 0
                    ? `<span class="flex items-center gap-1 text-xs font-bold mt-1" style="color:#f59e0b">${_coinSvg(14)} +${data.coins} монет</span>`
                    : '';
                el.innerHTML =
                    `<div class="flex gap-3 items-start">` +
                        `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:${c}26;color:${c}">` +
                            `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5 3.4 7.7 8 9 4.6-1.3 8-4 8-9V5l-8-3z"/><polyline points="8.5 11.5 11 14 15.5 9.5"/></svg>` +
                        `</div>` +
                        `<div class="flex-1 min-w-0">` +
                            `<p class="text-xs font-bold uppercase tracking-widest" style="color:${c}">Новый ранг!</p>` +
                            `<p class="text-sm font-bold text-white mt-0.5">${data.name || ''}</p>` +
                            coinLine +
                        `</div>` +
                        `<button onclick="closeToast('${id}')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>` +
                    `</div>`;
            } else if (type === 'quest') {
                el.style.border = '1px solid rgba(16,185,129,0.4)';
                el.innerHTML =
                    `<div class="flex gap-3 items-start">` +
                        `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.15);color:#34d399">` +
                            `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg>` +
                        `</div>` +
                        `<div class="flex-1 min-w-0">` +
                            `<p class="text-xs font-bold uppercase tracking-widest" style="color:#34d399">Задание выполнено</p>` +
                            `<p class="text-sm font-bold text-white mt-0.5">${data.title || ''}</p>` +
                            `<div class="flex items-center gap-3 mt-1">` +
                                `<span class="text-xs text-gray-400">+${data.xp} XP</span>` +
                                `<span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b">${_coinSvg(14)} +${data.coins} монет</span>` +
                            `</div>` +
                        `</div>` +
                        `<button onclick="closeToast('${id}')" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>` +
                    `</div>`;
            }

            // prepend = первый в DOM = внизу контейнера (flex-col-reverse)
            wrap.prepend(el);
            setTimeout(() => closeToast(id), 5000);

            // Update header coin counter — only for live AJAX awards (comment form),
            // where the balance hasn't been re-rendered yet. The achievements queue
            // replays awards that were already persisted *and* already reflected in the
            // server-rendered counter, so it must NOT increment again (double-count).
            const coinEl = document.getElementById('coin-count');
            if (updateCoins && coinEl && data.coins) {
                const cur = parseInt(coinEl.textContent.replace(/\D/g, ''), 10) || 0;
                coinEl.textContent = (cur + data.coins).toLocaleString('ru-RU');
            }
        }

        @if(session('achievement_awarded')) setTimeout(() => closeToast('t-ach'), 5000); @endif
        @if(session('level_up'))            setTimeout(() => closeToast('t-lvl'), 5500); @endif
        @if(session('streak_awarded'))      setTimeout(() => closeToast('t-streak'), 6000); @endif
        @if(session('rank_up'))             setTimeout(() => closeToast('t-rank'), 6500); @endif
        @if(session('success'))             setTimeout(() => closeToast('t-ok'),  4000); @endif

        // ── Replay quest-completed toasts queued on a full page load ──────────
        const questQueue = document.getElementById('quests-queue');
        if (questQueue) {
            const quests = JSON.parse(questQueue.getAttribute('data-quests') || '[]');
            quests.forEach((q, idx) => {
                setTimeout(() => showToast('quest', q, false), idx * 600);
            });
        }

        // ── Live rank-up / quest toasts (Livewire-driven, no page reload) ─────
        document.addEventListener('livewire:init', () => {
            Livewire.on('hud-rankup', (payload) => {
                const d = Array.isArray(payload) ? payload[0] : payload;
                if (d) showToast('rankup', d, false);
            });
            Livewire.on('quests-completed', (payload) => {
                const d = Array.isArray(payload) ? payload[0] : payload;
                const list = d && d.quests ? d.quests : (Array.isArray(d) ? d : []);
                list.forEach((q, idx) => setTimeout(() => showToast('quest', q, false), idx * 600));
            });
            // Live achievement toasts from Livewire components (likes etc.).
            Livewire.on('achievements-awarded', (payload) => {
                const d = Array.isArray(payload) ? payload[0] : payload;
                const list = d && d.achievements ? d.achievements : (Array.isArray(d) ? d : []);
                list.forEach((a, idx) => setTimeout(() => showToast('achievement', a, false), idx * 600));
            });
            // Live coin balance (quest reroll etc.) — updates every counter at
            // once: the header chip and the dashboard HUD stat share #coin-count.
            Livewire.on('coins-changed', (payload) => {
                const d = Array.isArray(payload) ? payload[0] : payload;
                const v = d && typeof d.coins === 'number' ? d.coins : null;
                if (v === null) return;
                document.querySelectorAll('#coin-count').forEach(el => {
                    el.textContent = Number(v).toLocaleString('ru-RU');
                    el.setAttribute('data-count-to', v);
                });
            });
            // Body scroll-lock for overlay modals (e.g. the global quests modal),
            // so it works on pages other than the dashboard too.
            Livewire.on('lock-body',   () => { document.body.style.overflow = 'hidden'; });
            Livewire.on('unlock-body', () => { document.body.style.overflow = ''; });
        });

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
                    }, false);
                }, idx * 600);
            });
        }

        // ── Live cosmetic border (avatar icon) ───────────────────
        // Applies a newly equipped/unequipped border cosmetic to EVERY avatar
        // on the page (header, dashboard, etc.) without a reload. Any avatar
        // that should track the current user's border carries [data-user-avatar].
        // The market dispatches `cosmetic-border-changed` on equip/buy/unequip.
        window.applyUserBorder = function (value) {
            document.querySelectorAll('[data-user-avatar]').forEach(function (el) {
                el.classList.remove('avatar-rainbow');
                el.style.boxShadow = '';
                if (value === 'rainbow') el.classList.add('avatar-rainbow');
                else if (value) el.style.boxShadow = value;
            });
        };
        window.addEventListener('cosmetic-border-changed', function (e) {
            window.applyUserBorder(e.detail ? e.detail.value : null);
        });

        // ── Live site theme (куплена в мини-маркете) ─────────────
        // Пока надета покупная тема, dark/light переключатель спрятан,
        // вместо него — бейдж с названием темы. Маркет диспатчит
        // `site-theme-changed` на покупку/экипировку/снятие — тема
        // применяется ко всей странице мгновенно, без перезагрузки.
        window.syncSiteThemeUi = function () {
            const t      = window.__siteTheme;
            const badge  = document.getElementById('site-theme-badge');
            const name   = document.getElementById('site-theme-badge-name');
            const toggle = document.getElementById('theme-toggle');
            if (badge) {
                badge.classList.toggle('hidden', !t);
                badge.classList.toggle('inline-flex', !!t);
            }
            if (name && t) name.textContent = (window.SITE_THEMES || {})[t] || t;
            if (toggle) toggle.style.display = t ? 'none' : '';
        };
        window.applySiteTheme = function (value) {
            window.__siteTheme = value || null;
            const fallback = localStorage.getItem('theme')
                || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', value || fallback);
            window.syncSiteThemeUi();
        };
        window.addEventListener('site-theme-changed', function (e) {
            window.applySiteTheme(e.detail ? e.detail.value : null);
        });
        window.syncSiteThemeUi();

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

    {{-- Global daily-quests modal (opened from the burger menu on any page) --}}
    @livewire('quests-modal')

    {{-- Global mini-game statistics modal (own profile + other players) --}}
    @livewire('game-stats')

    {{-- Global game leaderboards modal --}}
    @livewire('game-leaderboards')

    {{-- Мгновенный отклик при открытии тяжёлых модалок (рейтинг, достижения,
         игровая статистика, лидерборды). Их оверлей появляется только после
         round-trip к Livewire-компоненту, из-за чего клик «как будто залипает».
         Спиннер показывается сразу по клику и снимается в тот момент, когда
         реальная модалка смонтировалась (её x-init вызывает hideModalLoader). --}}
    <div id="modal-loader" role="status" aria-label="Загрузка" aria-hidden="true">
        <span class="ml-spinner"></span>
    </div>
    <style>
        #modal-loader { display:none; position:fixed; inset:0; z-index:200; align-items:center; justify-content:center; background:rgba(0,0,0,.55); }
        #modal-loader.is-on { display:flex; }
        .ml-spinner { width:46px; height:46px; border-radius:50%; border:3px solid rgba(255,255,255,.18); border-top-color:#f97316; animation:mlspin .7s linear infinite; }
        [data-theme="cosmic"] .ml-spinner { border-top-color:#a78bfa; }
        [data-theme="pink"] .ml-spinner   { border-top-color:#ec4899; }
        @keyframes mlspin { to { transform:rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) { .ml-spinner { animation-duration:1.4s; } }
    </style>
    <script>
        (function () {
            var el = document.getElementById('modal-loader');
            var timer = null;
            window.showModalLoader = function () {
                if (!el) return;
                el.classList.add('is-on');
                clearTimeout(timer);
                // Подстраховка: спиннер не должен зависнуть, если модалка не смонтировалась.
                timer = setTimeout(window.hideModalLoader, 6000);
            };
            window.hideModalLoader = function () {
                if (!el) return;
                el.classList.remove('is-on');
                clearTimeout(timer);
            };
            // Показать спиннер мгновенно, затем попросить Livewire открыть модалку.
            window.openModal = function (event, params) {
                window.showModalLoader();
                if (window.Livewire) Livewire.dispatch(event, params || {});
            };
            // Мгновенное закрытие модалки: проигрываем анимацию ухода сразу и
            // снимаем блокировку прокрутки, а серверный round-trip Livewire
            // (close() и его побочную обработку) оставляем работать в фоне.
            window.dismissModal = function (node) {
                var overlay = node && node.closest
                    ? node.closest('.lb-overlay, .qm-overlay')
                    : null;
                if (!overlay || overlay.classList.contains('is-closing')) return;
                overlay.classList.add('is-closing');
                document.body.style.overflow = '';
                if (window.hideModalLoader) window.hideModalLoader();
            };
        })();
    </script>
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

    {{-- Принудительно: бургер-меню работает на Alpine, который приезжает в
         составе livewire.js. Без явной директивы Livewire подключает скрипты
         только на страницах с компонентами — на гостевых (вход/регистрация)
         их нет, и меню не открывалось. --}}
    @livewireScripts

</body>

</html>
