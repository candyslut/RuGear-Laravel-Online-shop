<!DOCTYPE html>
<html lang="ru">

<head>
    <script>
        window.livewire_app_url = "{{ config('app.env') === 'production' ? config('app.url') : '' }}";
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RuGear | Hardware Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
    </style>
</head>

<body class="bg-[#0f1115] text-gray-100 flex flex-col min-h-screen antialiased">
    <header class="border-b border-gray-800 bg-[#161920]/80 backdrop-blur-md sticky top-0 z-50">
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

                <nav class="hidden md:flex items-center space-x-10 text-sm font-medium text-gray-400">
                    <a href="/" class="hover:text-orange-500 transition">Каталог</a>
                    <a href="{{route('support')}}" class="hover:text-orange-500 transition">Поддержка</a>
                    @if(auth()->user() && auth()->user()->role === 'admin')
                    <a href="{{route('admin.tickets.index')}}" class="hover:text-orange-500 transition">Админ панель</a>
                    @endif
                </nav>

                <div class="flex items-center space-x-6">
                    @auth
                    <div class="flex items-center space-x-4 border-l border-gray-800 pl-6">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs text-gray-500 leading-none mb-0.5">Личный кабинет</p>
                            <p class="text-sm font-bold leading-none"
                               style="color:{{ auth()->user()->cosmetic_nickname_color ?? '#e5e7eb' }};{{ auth()->user()->cosmetic_font ? 'font-family:'.auth()->user()->cosmetic_font.';' : '' }}">{{ auth()->user()->name }}</p>
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
                                class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center text-black font-black text-sm flex-shrink-0 hover:opacity-90 transition focus:outline-none overflow-hidden {{ auth()->user()->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
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
                                class="hidden absolute right-0 mt-2 w-52 bg-[#1a1d24] border border-gray-700 rounded-2xl shadow-2xl py-2 z-50"
                            >
                                <div class="px-4 py-2 border-b border-gray-700 mb-1">
                                    <p class="text-xs text-gray-500 leading-none">Вы вошли как</p>
                                    <p class="text-sm font-bold text-white mt-0.5 truncate">{{ auth()->user()->name }}</p>
                                </div>
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Личный кабинет
                                </a>
                                <a href="{{ route('market.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    Мини-маркет
                                </a>
                                <a href="{{ route('settings.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Настройки
                                </a>
                                <div class="border-t border-gray-700 mt-1 pt-1">
                                    <button onclick="openLogoutModal()" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-white/5 transition text-left">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Выйти
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-bold hover:text-orange-500 transition">Вход</a>
                    <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 text-black px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-[0_0_20px_rgba(249,115,22,0.3)]">
                        Регистрация
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow py-12">
        <div class="max-w-7xl mx-auto px-6">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-gray-800 bg-[#0a0c10] py-10">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-600 text-sm italic">«Железо с характером для тех, кто создает будущее»</p>
            <p class="text-gray-700 text-xs mt-4 uppercase tracking-widest">&copy; 2026 RUGEAR Ecosystem</p>
        </div>
    </footer>

    {{-- ─── Toast container (все тосты внутри, flex стакает снизу вверх) ── --}}
    {{-- flex-col-reverse: первый в DOM = внизу экрана, следующие выше      --}}
    <div id="toast-wrap"
         class="fixed bottom-6 right-6 z-[999] flex flex-col-reverse gap-3 items-end pointer-events-none">

        {{-- achievement — первый в DOM = ближайший к низу экрана --}}
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
    </script>

    @auth
    {{-- Logout confirmation modal --}}
    <dialog id="logout-modal" class="rounded-2xl border border-gray-700 bg-[#1a1d24] p-0 shadow-2xl w-full max-w-sm backdrop:bg-black/70">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-500/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-white">Выход из аккаунта</h2>
            </div>
            <p class="text-sm text-gray-400 mb-6">Вы точно хотите выйти из личного кабинета?</p>
            <div class="flex gap-3">
                <button
                    onclick="closeLogoutModal()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-300 bg-gray-800 hover:bg-gray-700 transition"
                >
                    Отмена
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button
                        type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-500 transition"
                    >
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </dialog>
    @endauth

</body>

</html>
