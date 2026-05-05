<x-shop-layout>
    <x-slot name="title">{{ $product->name }} | RuGear</x-slot>

    <div class="mb-8">
        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-orange-500 transition-colors flex items-center gap-2 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Назад в каталог
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        <div class="bg-[#161920] border border-gray-800 rounded-3xl overflow-hidden aspect-square flex items-center justify-center relative shadow-2xl">
            <svg class="w-32 h-32 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <div class="absolute top-6 left-6">
                <span class="bg-orange-500 text-black text-xs font-black px-4 py-1 rounded-full uppercase tracking-widest">
                    {{ $product->category->name ?? 'Gear' }}
                </span>
            </div>
        </div>

        <div class="flex flex-col h-full">
            <h1 class="text-5xl font-black text-white uppercase tracking-tighter mb-4">
                {{ $product->name }}
            </h1>

            <div class="flex items-center gap-4 mb-8">
                <span class="text-3xl font-black text-orange-500 italic">
                    {{ number_format($product->price, 0, '.', ' ') }} ₽
                </span>
                <span class="text-gray-500 line-through text-xl opacity-50">
                    {{ number_format($product->price * 1.2, 0, '.', ' ') }} ₽
                </span>
            </div>

            <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6 mb-8">
                <h3 class="text-gray-400 uppercase text-xs font-bold tracking-widest mb-3">Описание девайса</h3>
                <p class="text-gray-300 leading-relaxed italic">
                    {{ $product->description ?? 'Этот девайс еще не получил официального описания, но мы-то знаем, что он разрывает кабины.' }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-10">
                <div class="border border-gray-800 rounded-xl p-4 bg-gray-900/30">
                    <span class="block text-[10px] text-gray-500 uppercase font-bold mb-1">Статус</span>
                    <span class="text-green-500 font-bold">В наличии</span>
                </div>
                <div class="border border-gray-800 rounded-xl p-4 bg-gray-900/30">
                    <span class="block text-[10px] text-gray-500 uppercase font-bold mb-1">Доставка</span>
                    <span class="text-white font-bold">От 2 часов</span>
                </div>
            </div>

            <div class="mt-auto">
                @auth
                @php
                // Проверяем, есть ли этот конкретный товар в корзине юзера
                $itemInCart = auth()->user()->cartItems->where('product_id', $product->id)->first();
                @endphp

                @if($itemInCart)
                {{-- Если товар уже в корзине — показываем счетчик --}}
                <div class="flex items-center justify-between bg-[#161920] border border-gray-800 rounded-2xl p-2 shadow-2xl">
                    <form action="{{ route('cart.remove', $product) }}" method="POST" class="inline">
                        @csrf
                        <button class="w-14 h-14 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all active:scale-90 text-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                    </form>

                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">В сетапе</span>
                        <span class="text-2xl font-black text-orange-500 tabular-nums">
                            {{ $itemInCart->quantity }}
                        </span>
                    </div>

                    <form action="{{ route('cart.add', $product) }}" method="POST" class="inline">
                        @csrf
                        <button class="w-14 h-14 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all active:scale-90 text-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                @else
                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <button class="w-full py-5 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase tracking-widest rounded-2xl transition-all shadow-[0_0_30px_rgba(249,115,22,0.3)] active:scale-[0.98]">
                        Добавить в сетап
                    </button>
                </form>
                @endif
                @else
                <a href="{{ route('login') }}" class="block w-full py-5 bg-gray-800 hover:bg-gray-700 text-white text-center font-black uppercase tracking-widest rounded-2xl transition-all">
                    Войди, чтобы купить
                </a>
                @endauth
            </div>
        </div>
    </div>
</x-shop-layout>