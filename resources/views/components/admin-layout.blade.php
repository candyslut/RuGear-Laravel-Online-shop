<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RuGear Admin' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-[#0b0c10] text-gray-200 font-sans antialiased selection:bg-orange-500 selection:text-black">

    <div class="min-h-screen flex flex-col md:flex-row">

        <aside class="w-full md:w-64 bg-[#111318] border-b md:border-b-0 md:border-r border-gray-900 p-6 flex flex-col justify-between">
            <div class="space-y-8">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-7 bg-orange-500 rounded-full shadow-[0_0_15px_rgba(249,115,22,0.5)]"></div>
                    <span class="text-xl font-black uppercase tracking-wider text-white">Ru<span class="text-orange-500">Gear</span></span>
                    <span class="text-[9px] bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-md border border-orange-500/20 font-bold uppercase tracking-widest">Admin</span>
                </div>

                <nav class="space-y-2">

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