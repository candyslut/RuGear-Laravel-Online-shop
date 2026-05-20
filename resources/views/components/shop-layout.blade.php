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
                            <p class="text-xs text-gray-500 leading-none">Личный кабинет</p>
                            <p class="text-sm font-bold text-gray-200">{{ auth()->user()->name }}</p>
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

</body>

</html>