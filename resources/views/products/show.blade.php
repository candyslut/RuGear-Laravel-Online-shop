<x-shop-layout>
    <x-slot name="title">{{ $product->name }} | RuGear</x-slot>

    {{-- Кнопка назад --}}
    <div class="mb-8">
        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-orange-500 transition-colors flex items-center gap-2 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Назад в каталог
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start mb-20">
        {{-- Левая колонка: Картинка --}}
        <div class="relative group bg-[#161920] border border-gray-800 rounded-3xl overflow-hidden aspect-square flex items-center justify-center shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 via-transparent to-purple-500/5"></div>
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="relative w-[85%] h-[85%] object-contain bg-white rounded-3xl shadow-inner transition-transform duration-500 group-hover:scale-105">
            @else
                <svg class="w-32 h-32 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            @endif
        </div>

        <div class="flex flex-col h-full">
            <h1 class="text-5xl font-black text-white uppercase tracking-tighter mb-4">{{ $product->name }}</h1>
            <div class="flex items-center gap-4 mb-8">
                <span class="text-3xl font-black text-orange-500 italic">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                <span class="text-gray-500 line-through text-xl opacity-50">{{ number_format($product->price * 1.2, 0, '.', ' ') }} ₽</span>
            </div>

            <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6 mb-4">
                <h3 class="text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-3">Описание девайса</h3>
                <p class="text-gray-300 leading-relaxed italic">{{ $product->description ?? 'Описание скоро будет.' }}</p>
            </div>

            <details class="group bg-[#161920] border border-gray-800 rounded-2xl mb-8 overflow-hidden transition-all duration-300">
                <summary class="flex items-center justify-between p-6 cursor-pointer list-none">
                    <h3 class="text-gray-400 uppercase text-[10px] font-bold tracking-widest">Характеристики</h3>
                    <svg class="w-4 h-4 text-orange-500 transform transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <div class="px-6 pb-6 space-y-3">
                    <div class="flex justify-between border-b border-gray-800 pb-2"><span class="text-gray-500 text-sm">Тип</span><span class="text-gray-200 text-sm font-medium">Original Gear</span></div>
                    <div class="flex justify-between border-b border-gray-800 pb-2"><span class="text-gray-500 text-sm">Гарантия</span><span class="text-gray-200 text-sm font-medium">12 месяцев</span></div>
                </div>
            </details>

            <div class="mt-auto">
                @auth
                    @php $itemInCart = auth()->user()->cartItems->where('product_id', $product->id)->first(); @endphp
                    @if($itemInCart)
                        <div class="flex items-center justify-between bg-[#161920] border border-gray-800 rounded-2xl p-2 shadow-2xl">
                            <form action="{{ route('cart.remove', $product) }}" method="POST">@csrf<button class="w-14 h-14 flex items-center justify-center text-gray-400 hover:text-white rounded-xl"> <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button></form>
                            <span class="text-2xl font-black text-orange-500">{{ $itemInCart->quantity }}</span>
                            <form action="{{ route('cart.add', $product) }}" method="POST">@csrf<button class="w-14 h-14 flex items-center justify-center text-gray-400 hover:text-white rounded-xl"> <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button></form>
                        </div>
                    @else
                        <form action="{{ route('cart.add', $product) }}" method="POST">@csrf<button class="w-full py-5 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase tracking-widest rounded-2xl transition-all">Добавить в сетап</button></form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full py-5 bg-gray-800 text-white text-center font-black uppercase tracking-widest rounded-2xl">Войди, чтобы купить</a>
                @endauth
            </div>
        </div>
    </div>

    <hr class="border-gray-800 mb-12">

    <div class="w-full pb-20">
        <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-8 italic">
            Отзывы о продукте <span class="text-orange-500">/</span> {{ $product->commentaries->count() }}
        </h2>

        @auth
            <div class="bg-[#161920] border border-gray-800 rounded-3xl p-8 mb-12 shadow-xl">
                <form action="{{ route('product.commentary', $product) }}" method="POST">
                    @csrf
                    <label class="block text-gray-400 uppercase text-[10px] font-bold tracking-widest mb-4 text-center">Оставь свой след в истории</label>
                    <textarea 
                        name="content" 
                        rows="3" 
                        required
                        class="w-full bg-[#0f1117] border border-gray-800 rounded-2xl p-4 text-gray-200 placeholder-gray-600 focus:border-orange-500 focus:ring-0 transition-colors resize-none mb-4"
                        placeholder="Напиши, как тебе этот девайс..."></textarea>
                    <button type="submit" class="w-full py-4 bg-white hover:bg-orange-500 hover:text-black text-black font-black uppercase tracking-widest rounded-xl transition-all active:scale-[0.99]">
                        Отправить комментарий
                    </button>
                </form>
            </div>
        @else
            <div class="bg-gray-900/30 border border-dashed border-gray-800 rounded-3xl p-8 mb-12 text-center text-gray-500">
                Только авторизованные пользователи могут оставлять отзывы. <a href="{{ route('login') }}" class="text-orange-500 hover:underline">Войти?</a>
            </div>
        @endauth

        <div class="space-y-6">
            @forelse($product->commentaries->sortByDesc('created_at') as $comment)
                <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6 transition-all hover:border-gray-700 group shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-500 to-purple-500 flex items-center justify-center text-black font-black text-xs shadow-lg">
                                {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="text-white font-bold leading-none mb-1">{{ $comment->user->name }}</h4>
                                <span class="text-[10px] text-gray-600 uppercase font-bold tracking-widest">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-400 italic leading-relaxed">
                        « {{ $comment->content }} »
                    </p>
                </div>
            @empty
                <p class="text-gray-600 italic text-center py-10 uppercase tracking-widest text-sm font-bold opacity-50">Тут пока тишина... Будь первым, кто протестит этот девайс!</p>
            @endforelse
        </div>
    </div>
</x-shop-layout>