<!DOCTYPE html>
<html lang="ru">

<head>
    <script>
        window.livewire_app_url = "{{ config('app.env') === 'production' ? config('app.url') : '' }}";
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RuGear | Hardware Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes tin  { from{opacity:0;transform:translateX(400px)} to{opacity:1;transform:translateX(0)} }
        @keyframes tout { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(400px)} }
        .toast { animation: tin .4s cubic-bezier(.34,1.56,.64,1); }
        .toast.out { animation: tout .3s ease-in forwards; }
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
                            <p class="text-sm font-bold text-gray-200 leading-none">{{ auth()->user()->name }}</p>
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                    <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                    <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                                </svg>
                                <span class="text-xs font-black tabular-nums" style="color:#f59e0b;">{{ number_format(auth()->user()->coins) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('dashboard') }}" class="p-2 bg-gray-800 hover:bg-gray-700 rounded-full transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
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
        }

        @if(session('achievement_awarded')) setTimeout(() => closeToast('t-ach'), 5000); @endif
        @if(session('level_up'))            setTimeout(() => closeToast('t-lvl'), 5500); @endif
        @if(session('success'))             setTimeout(() => closeToast('t-ok'),  4000); @endif
    </script>

</body>

</html>
