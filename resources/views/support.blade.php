<x-shop-layout>
    <x-slot name="title">Центр связи | RuGear Support</x-slot>

    <div class="mb-8 md:mb-12">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tighter text-white">
            Техподдержка <span class="text-orange-500">2.0</span>
        </h1>
        <p class="text-gray-500 mt-2 font-medium italic text-base md:text-lg">
            Если девайс подвел в решающем раунде — мы здесь.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">

        <div class="space-y-6 md:space-y-8">

            <div class="bg-[#161920] border border-gray-800 p-6 md:p-8 rounded-3xl shadow-xl">
                <h3 class="text-orange-500 font-bold uppercase tracking-widest text-sm mb-6">Прямая связь</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center group-hover:bg-orange-500 group-hover:text-black transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Горячая линия</p>
                            <p class="text-white font-bold">8 800 555 35 35</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 bg-gray-800 rounded-xl flex items-center justify-center group-hover:bg-orange-500 group-hover:text-black transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">E-mail</p>
                            <p class="text-white font-bold">base@rugear.tech</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#161920] border border-gray-800 rounded-3xl shadow-xl overflow-hidden relative group">
                <div class="p-8 pb-6">
                    <h3 class="text-orange-500 font-bold uppercase tracking-widest text-[10px] mb-1">Где нас найти?</h3>
                    <p class="text-white font-black text-2xl uppercase tracking-tighter">Sector 7 <span class="text-gray-600">/ Moscow</span></p>
                </div>

                <div class="relative h-64 bg-[#0d0f14] overflow-hidden border-t border-gray-800">
                    <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(#f97316 1px, transparent 1px), linear-gradient(90deg, #f97316 1px, transparent 1px); background-size: 30px 30px;"></div>

                    <svg class="absolute inset-0 w-full h-full opacity-10 text-orange-500" viewBox="0 0 800 400">
                        <circle cx="400" cy="200" r="150" stroke="currentColor" stroke-width="1" stroke-dasharray="10 5" />
                        <path d="M100 100l600 200M100 300l600-200" stroke="currentColor" stroke-width="0.5" />
                    </svg>

                    <div class="absolute inset-0 pointer-events-none">
                        <div class="w-full h-20 bg-gradient-to-b from-orange-500/20 to-transparent absolute top-0 animate-scan"></div>
                    </div>

                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <div class="relative flex items-center justify-center">
                            <span class="animate-ping absolute inline-flex h-12 w-12 rounded-full bg-orange-500 opacity-30"></span>
                            <div class="relative bg-orange-500 text-black p-2 rounded-xl shadow-[0_0_30px_rgba(249,115,22,0.6)]">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" />
                                </svg>
                            </div>
                            <div class="absolute left-14 top-0 bg-black/90 border border-orange-500/40 backdrop-blur-md px-3 py-1.5 rounded-lg text-[10px] font-mono text-orange-400 whitespace-nowrap shadow-2xl">
                                LAT: 55.7558<br>LNG: 37.6173
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-black/40 flex justify-between items-center border-t border-gray-800">
                    <span class="text-[9px] font-mono text-gray-500 uppercase tracking-[0.2em]">Signal: Secure</span>
                    <a href="https://yandex.ru/maps" target="_blank" class="text-[10px] font-black text-orange-500 uppercase hover:text-white transition-colors">Маршрут →</a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl animate-fade-in">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl animate-fade-in">
        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

        <div class="lg:col-span-2">
            <div class="bg-[#161920] border border-gray-800 p-6 sm:p-8 md:p-10 rounded-3xl shadow-2xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 opacity-[0.03] pointer-events-none">
                    <svg class="w-80 h-80" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-white uppercase mb-6 md:mb-8 tracking-tighter">Открыть тикет</h2>

                <form action="{{route('ticket.store')}}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Позывной (Имя)</label>
                            <input type="text" name="name" required
                                class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-4 md:px-6 py-3.5 md:py-4 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/20 transition-all"
                                placeholder="Ghost_99">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Категория лога</label>
                            <select name="category"
                                class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-4 md:px-6 py-3.5 md:py-4 text-white focus:outline-none focus:border-orange-500 transition-all appearance-none cursor-pointer">
                                <option>Технический сбой</option>
                                <option>Проблема с заказом</option>
                                <option>Сотрудничество</option>
                                <option>Пасхалки и идеи</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Детали инцидента</label>
                        <textarea name="content" rows="5" required
                            class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-4 md:px-6 py-3.5 md:py-4 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 transition-all resize-none"
                            placeholder="Опиши, что пошло не так..."></textarea>
                    </div>

                    <button type="submit"
                        class="group relative w-full py-4 md:py-5 bg-orange-500 text-black font-black uppercase tracking-[0.15em] md:tracking-[0.2em] rounded-2xl hover:bg-orange-400 transition-all active:scale-[0.99] shadow-[0_10px_20px_rgba(249,115,22,0.2)]">
                        Отправить данные
                    </button>
                </form>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 bg-orange-500/5 border border-orange-500/10 rounded-2xl">
                    <div class="flex items-center gap-3 mb-2 text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h4 class="text-white font-bold text-xs uppercase tracking-wider">Факт дня</h4>
                    </div>
                    <p class="text-gray-500 text-[11px] leading-relaxed italic">
                        Наши инженеры не спят, пока твоя мышь не начнет летать. Среднее время фикса бага — 1.5 чашки крепкого кофе.
                    </p>
                </div>
                <div class="p-6 bg-gray-800/10 border border-gray-800 rounded-2xl">
                    <div class="flex items-center gap-3 mb-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h4 class="text-white font-bold text-xs uppercase tracking-wider">Система</h4>
                    </div>
                    <p class="text-green-500 text-[10px] font-mono">STATUS: STABLE [ONLINE]</p>
                    <p class="text-gray-600 text-[9px] mt-1 uppercase tracking-tighter italic">Все службы RuGear работают в штатном режиме.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(300%);
            }
        }

        .animate-scan {
            animation: scan 3s linear infinite;
        }

        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.75rem;
        }

        @media (min-width: 768px) {
            select {
                background-position: right 1.5rem center;
            }
        }
    </style>
</x-shop-layout>