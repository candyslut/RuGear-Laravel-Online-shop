<x-shop-layout>
    <x-slot name="title">Каталог товаров | RuGear</x-slot>

    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tight">На витрине</h1>
            <p class="text-gray-500 mt-2 font-medium italic">Твоё преимущество в катке. Сделано в России.</p>
        </div>
        <div class="hidden sm:block">
            <span class="text-xs bg-gray-800 text-gray-400 px-3 py-1 rounded-full border border-gray-700">Найдено: {{ $products->count() }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($products as $product)
        <div class="group bg-[#161920] border border-gray-800 rounded-3xl overflow-hidden hover:border-orange-500/50 transition-all duration-300 flex flex-col shadow-xl">

            <div class="aspect-square bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-700 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <div class="absolute top-4 left-4">
                    <span class="bg-black/50 backdrop-blur-md text-[10px] font-bold px-2 py-1 rounded border border-gray-700 uppercase tracking-widest text-orange-400">
                        {{ $product->category->name ?? 'Common' }}
                    </span>
                </div>
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <h2 class="text-lg font-bold text-white group-hover:text-orange-500 transition-colors mb-4">{{ $product->name }}</h2>

                <div class="mt-auto pt-6 flex justify-between items-center border-t border-gray-800/50">
                    <span class="text-xl font-black text-white italic">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>

                    @auth
                    @php
                    $itemInCart = auth()->user()->cartItems->where('product_id', $product->id)->first();
                    @endphp

                    @if($itemInCart)
                    <div class="flex items-center bg-gray-900/50 border border-gray-700 rounded-2xl p-1 shadow-inner">
                        {{-- Кнопка Минус --}}
                        <form action="{{ route('cart.remove', $product) }}" method="POST" class="inline">
                            @csrf
                            <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all active:scale-95 font-bold text-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                        </form>

                        <span class="px-4 text-orange-500 font-black text-lg min-w-[3rem] text-center tabular-nums">
                            {{ $itemInCart->quantity }}
                        </span>

                        <form action="{{ route('cart.add', $product) }}" method="POST" class="inline">
                            @csrf
                            <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all active:scale-95 font-bold text-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf
                        <button class="p-3 bg-gray-800 hover:bg-orange-500 hover:text-black rounded-2xl transition-all duration-300 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </form>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="p-3 bg-gray-800 hover:bg-orange-500 rounded-2xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-shop-layout>