<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RuGear Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes tin  { from{opacity:0;transform:translateX(380px)} to{opacity:1;transform:translateX(0)} }
        @keyframes tout { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(380px)} }
        .toast { animation: tin .4s cubic-bezier(.34,1.56,.64,1); pointer-events:auto; }
        .toast.out { animation: tout .3s ease-in forwards; }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-bold text-gray-900 tracking-tight">
                        RU<span class="text-indigo-600">GEAR</span>
                    </a>
                </div>

                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-600 hover:text-indigo-600 font-medium transition">Каталог</a>
                    <a href="#" class="text-gray-600 hover:text-indigo-600 font-medium transition">О нас</a>
                </nav>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                            <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-black font-black text-sm flex-shrink-0">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition hidden sm:block leading-none">
                                {{ auth()->user()->name }}
                            </span>
                            <span class="flex items-center gap-1 text-sm font-black tabular-nums" style="color:#b45309;">
                                <svg class="w-[18px] h-[18px] flex-shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                    <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                    <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                                </svg>
                                {{ number_format(auth()->user()->coins) }}
                            </span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Выйти</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 font-medium hover:text-indigo-600">Вход</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Регистрация</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-300 mt-auto">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-white font-bold mb-4">RuGear</h3>
                <p class="text-sm">Твой надежный поставщик лучшего железа для разработки.</p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-4">Помощь</h3>
                <ul class="text-sm space-y-2">
                    <li><a href="#" class="hover:text-white">Доставка</a></li>
                    <li><a href="#" class="hover:text-white">Гарантия</a></li>
                </ul>
            </div>
            <div class="text-sm">
                <p>&copy; 2026 RuGear. Сделано с любовью на Laravel.</p>
            </div>
        </div>
    </footer>

    {{-- ─── Global toasts ──────────────────────────────────── --}}
    @if(session('achievement_awarded'))
    <div id="t-ach" class="fixed bottom-6 right-6 z-50 w-80 bg-[#1a1d24] border border-orange-500/30 rounded-2xl p-4 shadow-2xl toast">
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
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
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

    @if(session('level_up'))
    <div id="t-lvl" class="fixed right-6 z-50 w-80 bg-[#1a1d24] border border-cyan-500/30 rounded-2xl p-4 shadow-2xl toast"
         style="bottom: {{ session('achievement_awarded') ? 'calc(1.5rem + 88px + 0.75rem)' : '1.5rem' }};">
        <div class="flex gap-3 items-start">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold uppercase tracking-widest text-cyan-400">Новый уровень!</p>
                <p class="text-sm font-bold text-white mt-0.5">Уровень {{ session('level_up.level') }}</p>
                <span class="flex items-center gap-1 text-xs font-bold mt-1" style="color:#f59e0b;">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
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

    @if(session('success'))
    <div id="t-ok" class="fixed bottom-6 right-6 z-50 w-80 bg-[#1a1d24] border border-green-500/30 rounded-2xl p-4 shadow-2xl toast">
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

    <script>
        function closeToast(id){
            const t = document.getElementById(id);
            if (t) { t.classList.add('out'); setTimeout(() => t.remove(), 300); }
        }
        @if(session('achievement_awarded')) setTimeout(() => closeToast('t-ach'), 5000); @endif
        @if(session('level_up'))            setTimeout(() => closeToast('t-lvl'), 5500); @endif
        @if(session('success'))             setTimeout(() => closeToast('t-ok'),  4000); @endif
    </script>

</body>
</html>
