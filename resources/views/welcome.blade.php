<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    .custom-pagination p {
        color: #6b7280 !important;
        font-style: italic !important;
        font-size: 0.875rem !important;
    }

    .custom-pagination nav a,
    .custom-pagination nav span[aria-disabled="true"] span,
    .custom-pagination nav span[aria-current="page"] span {
        background-color: #161920 !important;
        border-color: #1f2937 !important;
        color: #9ca3af !important;
        border-radius: 0.75rem;
        margin: 0 2px;
    }

    .custom-pagination nav span[aria-current="page"] span {
        background-color: #f97316 !important;
        border-color: #f97316 !important;
        color: #000000 !important;
        font-weight: 900 !important;
    }

    .custom-pagination nav a:hover {
        background-color: #1f2937 !important;
        color: #f97316 !important;
        border-color: #f97316 !important;
    }

    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }

    #filters-menu {
        max-height: 0;
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }

    #filters-menu.active {
        max-height: 500px;
        opacity: 1;
        margin-top: 1rem;
        pointer-events: auto;
    }
</style>

<x-shop-layout>
    <x-slot name="title">Каталог товаров | RuGear</x-slot>

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tight text-white font-serif">На витрине</h1>
            <p class="text-gray-500 mt-2 font-medium italic">Твоё преимущество в катке. Сделано в России.</p>
        </div>
        <div class="hidden sm:block">
            <span class="text-[10px] font-mono bg-orange-500/10 text-orange-500 px-4 py-2 rounded-xl border border-orange-500/20 uppercase tracking-widest">
                Вам представлено: {{ $products->total() }} девайсов!
            </span>
        </div>
    </div>

    <div class="mb-12">
        <form action="{{ route('products.index') }}" method="GET" id="search-form">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow group">
                    <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-gray-500 group-focus-within:text-orange-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full bg-[#161920] border border-gray-800 rounded-2xl pl-14 pr-32 py-5 text-white placeholder-gray-600 focus:outline-none focus:border-orange-500/50 focus:ring-4 focus:ring-orange-500/5 transition-all shadow-xl"
                        placeholder="Найти снаряжение...">

                    <button type="button" onclick="toggleFilters()" id="filter-btn"
                        class="absolute inset-y-2.5 right-2.5 px-4 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white rounded-xl flex items-center gap-2 transition-all border border-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Параметры</span>
                    </button>
                </div>

                <button type="submit" class="bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-[0.2em] px-10 py-5 rounded-2xl transition-all active:scale-95 shadow-[0_10px_20px_rgba(249,115,22,0.2)]">
                    Поиск
                </button>
            </div>

            <div id="filters-menu" class="overflow-hidden">
                <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl grid grid-cols-1 md:grid-cols-3 gap-8 border-t-orange-500/20">

                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Категория</label>
                        </div>
                        <div class="relative">
                            <select name="category" class="w-full bg-gray-900/50 border border-gray-800 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-orange-500 transition-all appearance-none cursor-pointer text-sm">
                                <option value="">Все категории</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-1 h-4 bg-orange-500 rounded-full"></div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Стоимость (₽)</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-grow">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] text-gray-600 font-black">ОТ</span>
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    class="w-full bg-gray-900/50 border border-gray-800 rounded-xl pl-10 pr-4 py-4 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 transition-all text-sm" placeholder="0">
                            </div>
                            <div class="relative flex-grow">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] text-gray-600 font-black">ДО</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    class="w-full bg-gray-900/50 border border-gray-800 rounded-xl pl-10 pr-4 py-4 text-white placeholder-gray-700 focus:outline-none focus:border-orange-500 transition-all text-sm" placeholder="∞">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col justify-end">
                        <div class="bg-gray-900/30 border border-gray-800 p-4 rounded-2xl flex items-center justify-between">
                            <div class="space-y-1">
                                <h4 class="text-[10px] font-black text-orange-500 uppercase tracking-widest">Смарт-фильтр</h4>
                                <p class="text-[9px] text-gray-500 leading-tight italic">Найдено в этой выборке: {{ $products->total() }}</p>
                            </div>
                            @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                            <a href="{{ route('products.index') }}" class="p-2.5 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-all" title="Сбросить всё">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($products as $product)
        <div class="group bg-[#161920] border border-gray-800 rounded-[2rem] overflow-hidden hover:border-orange-500/50 transition-all duration-500 flex flex-col shadow-xl">

            <div class="aspect-square bg-gradient-to-br from-gray-800 to-gray-900 relative overflow-hidden">
                @if($product->image)
                <img
                    src="{{ asset($product->image) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                @endif

                <div class="absolute top-5 left-5">
                    <span class="bg-black/60 backdrop-blur-md text-[9px] font-black px-3 py-1.5 rounded-lg border border-gray-700 uppercase tracking-[0.2em] text-orange-400">
                        {{ $product->category->name ?? 'Базовый' }}
                    </span>
                </div>

                @if($product->quantity <= 0)
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center">
                    <span class="bg-red-950/90 text-red-400 border border-red-900/50 text-[10px] font-mono font-black uppercase tracking-widest px-4 py-2 rounded-xl">
                        Нет в наличии
                    </span>
            </div>
            @endif
        </div>

        <div class="p-6 flex-grow flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-white font-black text-lg line-clamp-2 hover:text-orange-500 transition-colors">
                    <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                </h3>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-800/50">
                <span class="text-2xl font-black text-white italic">
                    {{ number_format($product->price, 0, '.', ' ') }} ₽
                </span>

                <livewire:cart-counter
                    :product="$product"
                    :key="'cart-counter-' . $product->id" />
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-32 flex flex-col items-center justify-center bg-[#161920] border border-gray-800 border-dashed rounded-[3rem]">
        <div class="relative mb-6">
            <svg class="w-20 h-20 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <div class="absolute inset-0 animate-ping opacity-20">
                <svg class="w-20 h-20 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        <p class="text-gray-500 italic font-medium text-lg">Объектов по данным координатам не обнаружено.</p>
        <a href="{{ route('products.index') }}" class="mt-6 px-8 py-3 border border-orange-500/30 text-orange-500 hover:bg-orange-500 hover:text-black transition-all rounded-xl text-[10px] uppercase font-black tracking-widest">Сбросить параметры</a>
    </div>
    @endforelse
    </div>

    <div class="mt-16 custom-pagination">
        {{ $products->onEachSide(1)->links() }}
    </div>

    <script>
        function toggleFilters() {
            const menu = document.getElementById('filters-menu');
            const btn = document.getElementById('filter-btn');

            menu.classList.toggle('active');

            if (menu.classList.contains('active')) {
                btn.classList.add('bg-orange-500', 'text-black', 'border-orange-500');
                btn.classList.remove('bg-gray-800', 'text-gray-400', 'border-gray-700');
            } else {
                btn.classList.remove('bg-orange-500', 'text-black', 'border-orange-500');
                btn.classList.add('bg-gray-800', 'text-gray-400', 'border-gray-700');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('category') || urlParams.has('min_price') || urlParams.has('max_price');

            if (hasFilters) {
                const menu = document.getElementById('filters-menu');
                const btn = document.getElementById('filter-btn');
                menu.classList.add('active');
                btn.classList.add('bg-orange-500', 'text-black', 'border-orange-500');
                btn.classList.remove('bg-gray-800', 'text-gray-400', 'border-gray-700');
            }
        });
    </script>
</x-shop-layout>

