<x-shop-layout>
    <x-slot name="title">Личный кабинет | RuGear</x-slot>

    <div class="space-y-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tight">
                Личный <span class="text-orange-500">кабинет</span>
            </h1>
            <p class="text-gray-500 mt-2 font-medium italic">
                Вы авторизованы как: {{ auth()->user()->name }}
            </p>
        </div>

        <div class="bg-[#161920] border border-gray-800 rounded-3xl p-8 shadow-xl">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black uppercase tracking-tighter flex items-center">
                    <svg class="w-6 h-6 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Моё <span class="text-orange-500 ml-2">снаряжение</span>
                </h2>

                <span class="text-xs bg-gray-800 text-gray-400 px-3 py-1 rounded-full border border-gray-700 uppercase">
                    Предметов: {{ $cartItems->sum('quantity') }}
                </span>
            </div>

            @if($cartItems->isEmpty())
            <div class="py-12 text-center border-2 border-dashed border-gray-800 rounded-3xl">
                <p class="text-gray-500 italic mb-6">В корзине пока пусто</p>
                <a href="/" class="bg-orange-500 hover:bg-orange-600 text-black font-bold py-3 px-8 rounded-2xl uppercase text-sm">
                    На витрину
                </a>
            </div>
            @else

            <div class="space-y-4">
                @foreach($cartItems as $item)
                <div class="flex items-center justify-between px-5 py-4 bg-gray-900/40 border border-gray-800 rounded-2xl">

                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center text-orange-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-white font-bold">
                                {{ $item->product->name }}
                            </h3>
                            <p class="text-gray-500 text-xs uppercase">
                                {{ $item->product->category->name ?? 'Девайс' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-10">

                        <div class="w-[140px] flex justify-center">
                            <div class="flex items-center bg-black/30 border border-gray-800 rounded-xl overflow-hidden">

                                <form action="{{ route('cart.remove', $item->product) }}" method="POST">
                                    @csrf
                                    <button class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition">
                                        -
                                    </button>
                                </form>

                                <span class="w-12 text-center text-orange-500 font-bold tabular-nums">
                                    {{ $item->quantity }}
                                </span>

                                <form action="{{ route('cart.add', $item->product) }}" method="POST">
                                    @csrf
                                    <button class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition">
                                        +
                                    </button>
                                </form>

                            </div>
                        </div>

                        <div class="text-right w-[120px]">
                            <p class="text-white font-black">
                                {{ number_format($item->product->price * $item->quantity, 0, '.', ' ') }} ₽
                            </p>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10 pt-8 border-t border-gray-800 flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-widest mb-2">Итого</p>
                    <p class="text-3xl font-black text-white">
                        {{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity), 0, '.', ' ') }}
                        <span class="text-orange-500">₽</span>
                    </p>
                </div>

                <button class="bg-orange-500 hover:bg-orange-600 text-black font-black px-10 py-4 rounded-2xl transition shadow-lg uppercase tracking-widest">
                    Оформить заказ
                </button>
            </div>

            @endif
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="toggleModal('logout-modal', true)" class="text-xs text-red-600 hover:text-red-500 uppercase font-bold">
                Выход
            </button>

            <div id="logout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                <div class="bg-[#161920] border border-gray-800 w-full max-w-md rounded-3xl p-8 shadow-2xl transform transition-all">
                    <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-4 text-center">
                        Уже <span class="text-orange-500">уходишь?</span>
                    </h3>
                    <p class="text-gray-400 text-center mb-8 italic">Ты уверен, что хочешь покинуть систему RuGear?</p>

                    <div class="grid grid-cols-2 gap-4">
                        <button onclick="toggleModal('logout-modal', false)" class="py-4 bg-gray-800 hover:bg-gray-700 text-white font-bold uppercase tracking-widest rounded-xl transition-all">
                            Остаться
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-black font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-orange-500/20">
                                Выйти
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-shop-layout>

<script>
    function toggleModal(modalID, show) {
        const modal = document.getElementById(modalID);
        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('logout-modal');
        if (event.target == modal) {
            toggleModal('logout-modal', false);
        }
    }
</script>