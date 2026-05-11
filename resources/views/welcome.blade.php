<x-shop-layout>
    <x-slot name="title">Каталог товаров | RuGear</x-slot>

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tight text-white">На витрине</h1>
            <p class="text-gray-500 mt-2 font-medium italic">Твоё преимущество в катке. Сделано в России.</p>
        </div>
        <div class="hidden sm:block">
            <span class="text-[10px] font-mono bg-orange-500/10 text-orange-500 px-4 py-2 rounded-xl border border-orange-500/20 uppercase tracking-widest">
                Вам представленно: {{ $products->total() }} девайсов!
            </span>
        </div>
    </div>

    <div class="mb-12 bg-[#161920] border border-gray-800 p-6 rounded-3xl shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#f97316 1px, transparent 0); background-size: 20px 20px;"></div>

        <form action="{{ route('products.index') }}" method="GET" class="relative z-10 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 mb-2 block text-orange-500/70">Поиск по логам</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-5 py-3.5 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 transition-all shadow-inner" 
                        placeholder="Название...">
                    <svg class="w-4 h-4 text-gray-600 absolute right-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 mb-2 block">Класс объекта</label>
                <select name="category" class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-orange-500 transition-all appearance-none cursor-pointer">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1 mb-2 block">Бюджет (₽)</label>
                <div class="flex gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                        class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-4 py-3.5 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 transition-all" placeholder="Min">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                        class="w-full bg-gray-900/50 border border-gray-800 rounded-2xl px-4 py-3.5 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 transition-all" placeholder="Max">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-[10px] tracking-widest py-4 rounded-2xl transition-all active:scale-95 shadow-[0_5px_15px_rgba(249,115,22,0.2)]">
                    Применить
                </button>
                @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                    <a href="{{ route('products.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-4 rounded-2xl transition-all flex items-center justify-center border border-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($products as $product)
        <div class="group bg-[#161920] border border-gray-800 rounded-3xl overflow-hidden hover:border-orange-500/50 transition-all duration-300 flex flex-col shadow-xl">

            <div class="aspect-square bg-gradient-to-br from-gray-800 to-gray-900 relative flex items-center justify-center overflow-hidden">
                @if($product->image)
                <img src="{{ asset($product->image) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @else
                <svg class="w-16 h-16 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                @endif

                <div class="absolute top-4 left-4">
                    <span class="bg-black/50 backdrop-blur-md text-[10px] font-bold px-2 py-1 rounded border border-gray-700 uppercase tracking-widest text-orange-400">
                        {{ $product->category->name ?? 'Common' }}
                    </span>
                </div>
            </div>

            <div class="p-6 flex flex-col flex-grow">
                <a href="{{ route('products.show', $product) }}">
                    <h2 class="text-lg font-bold text-white hover:text-orange-500 transition-colors mb-4">
                        {{ $product->name }}
                    </h2>
                </a>

                <div class="mt-auto pt-6 flex justify-between items-center border-t border-gray-800/50">
                    <span class="text-xl font-black text-white italic">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>

                    @auth
                        @php
                        $itemInCart = auth()->user()->cartItems->where('product_id', $product->id)->first();
                        @endphp

                        @if($itemInCart)
                        <div class="flex items-center bg-gray-900/50 border border-gray-700 rounded-2xl p-1 shadow-inner">
                            <form action="{{ route('cart.remove', $product) }}" method="POST" class="inline">
                                @csrf
                                <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all active:scale-95 font-bold text-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                            </form>
                            <span class="px-4 text-orange-500 font-black text-lg min-w-[3rem] text-center tabular-nums">{{ $itemInCart->quantity }}</span>
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="inline">
                                @csrf
                                <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all active:scale-95 font-bold text-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </form>
                        </div>
                        @else
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <button class="p-3 bg-gray-800 hover:bg-orange-500 hover:text-black rounded-2xl transition-all duration-300 shadow-lg group-hover:shadow-orange-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="p-3 bg-gray-800 hover:bg-orange-500 rounded-2xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 flex flex-col items-center justify-center bg-[#161920] border border-gray-800 border-dashed rounded-3xl">
            <svg class="w-16 h-16 text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 italic font-medium">Объектов по данным координатам не обнаружено.</p>
            <a href="{{ route('products.index') }}" class="mt-4 text-orange-500 hover:underline text-xs uppercase font-black tracking-widest">Сбросить все фильтры</a>
        </div>
        @endforelse
    </div>

    <div class="mt-12 custom-pagination">
        {{ $products->onEachSide(1)->links() }}
    </div>

    <style>
        .custom-pagination p { color: #6b7280 !important; font-style: italic !important; font-size: 0.875rem !important; }
        .custom-pagination nav a, .custom-pagination nav span[aria-disabled="true"] span, .custom-pagination nav span[aria-current="page"] span {
            background-color: #161920 !important; border-color: #1f2937 !important; color: #9ca3af !important; border-radius: 0.75rem; margin: 0 2px;
        }
        .custom-pagination nav span[aria-current="page"] span {
            background-color: #f97316 !important; border-color: #f97316 !important; color: #000000 !important; font-weight: 900 !important;
        }
        .custom-pagination nav a:hover { background-color: #1f2937 !important; color: #f97316 !important; border-color: #f97316 !important; }

        /* Кастомный селект */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }
    </style>
</x-shop-layout>