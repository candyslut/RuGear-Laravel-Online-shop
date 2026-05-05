<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RuGear Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 font-semibold hover:underline">Кабинет</a>
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

</body>
</html>