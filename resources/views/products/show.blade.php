<x-shop-layout>
    <x-slot name="title">{{ $product->name }} | RuGear</x-slot>

    <div class="mb-8">
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-orange-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Назад в каталог
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start mb-16">
        
        <div class="bg-[#161920] border border-gray-800 rounded-2xl aspect-square flex items-center justify-center p-8 shadow-xl">
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-[85%] h-[85%] object-contain bg-white rounded-xl p-4">
            @else
                <div class="text-center text-gray-700">
                    <svg class="w-20 h-20 mx-auto opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-xs font-mono uppercase tracking-wider mt-2 opacity-40">Нет изображения</p>
                </div>
            @endif
        </div>

        <div class="flex flex-col h-full justify-between py-2 space-y-8">
            <div class="space-y-4">
                <span class="text-xs font-mono text-gray-500 uppercase tracking-widest bg-gray-900 px-3 py-1 rounded border border-gray-800">
                    {{ $product->category->name ?? 'Девайс' }}
                </span>
                <h1 class="text-4xl lg:text-5xl font-black text-white uppercase tracking-tight">
                    {{ $product->name }}
                </h1>
                <div class="flex items-center gap-4 pt-2">
                    <span class="text-3xl font-bold text-orange-500 font-mono">
                        {{ number_format($product->price, 0, '.', ' ') }} ₽
                    </span>
                    <span class="text-base text-gray-600 line-through font-mono opacity-50">
                        {{ number_format($product->price * 1.2, 0, '.', ' ') }} ₽
                    </span>
                </div>
            </div>

            <div class="bg-[#161920]/50 border border-gray-800/80 rounded-xl p-5">
                <h3 class="text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">Описание</h3>
                <p class="text-gray-300 leading-relaxed text-sm italic">
                    {{ $product->description ?? 'Описание для этого товара временно отсутствует.' }}
                </p>
            </div>

            <div>
    <div class="mb-4 flex items-center gap-2 font-mono text-xs uppercase tracking-wider">
        <span class="text-gray-500">Наличие:</span>
        @if($product->quantity > 0)
            <span class="text-green-500 font-bold">В наличии ({{ $product->quantity }} шт.)</span>
        @else
            <span class="text-red-500 font-bold">Закончился</span>
        @endif
    </div>

    @livewire('comparison-manager', ['product' => $product])

    @auth
        @php 
            $itemInCart = auth()->user()->cartItems->where('product_id', $product->id)->first(); 
        @endphp

        @if($product->quantity <= 0)
            <button disabled class="w-full py-4 bg-gray-900 border border-gray-800 text-gray-600 font-extrabold uppercase text-xs tracking-widest rounded-xl cursor-not-allowed opacity-50">
                Товар закончился
            </button>
        @elseif($itemInCart)
            <div class="flex items-center justify-between bg-[#161920] border border-gray-800 rounded-xl p-2 shadow-lg">
                <form action="{{ route('cart.remove', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-12 h-12 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                </form>

                <div class="flex flex-col items-center">
                    <span class="text-xl font-bold text-orange-500 font-mono">{{ $itemInCart->quantity }}</span>
                    <span class="text-[9px] text-gray-600 font-mono uppercase">в корзине</span>
                </div>

                @if($itemInCart->quantity < $product->quantity)
                    <form action="{{ route('cart.add', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-12 h-12 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </form>
                @else
                    <button disabled class="w-12 h-12 flex items-center justify-center text-gray-700 bg-gray-950/40 rounded-lg cursor-not-allowed" title="Достигнут лимит доступного товара">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                @endif
            </div>
        @else
            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-black font-extrabold uppercase text-xs tracking-widest rounded-xl transition-all shadow-md cursor-pointer">
                    Добавить в корзину
                </button>
            </form>
        @endif
    @else
        <a href="{{ route('login') }}" class="block w-full py-4 bg-gray-800 text-white text-center font-bold uppercase text-xs tracking-widest rounded-xl transition-colors border border-gray-700">
            Войдите, чтобы купить
        </a>
    @endauth
</div>
        </div>
    </div>

    <div class="bg-[#161920] border border-gray-800 rounded-2xl p-8 mb-16 shadow-xl">
        <h2 class="text-lg font-bold text-white uppercase tracking-wider border-b border-gray-800 pb-4 mb-6">
            Технические характеристики
        </h2>

        @if($product->specification)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-4">
                
                <div class="flex justify-between border-b border-gray-800/40 pb-2">
                    <span class="text-gray-500 text-sm">Категория</span>
                    <span class="text-gray-200 text-sm font-medium">{{ $product->category->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-800/40 pb-2">
                    <span class="text-gray-500 text-sm">Гарантия</span>
                    <span class="text-gray-200 text-sm font-medium">12 месяцев</span>
                </div>

                @switch(get_class($product->specification))
                    @case('App\Models\Spec\MouseSpecification')
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Сенсор</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->sensor ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Максимальный DPI</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->max_dpi ? number_format($product->specification->max_dpi, 0, '.', ' ') : '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Частота опроса</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->polling_rate ? $product->specification->polling_rate . ' Гц' : '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Переключатели</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->switches ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Подключение</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->connection ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Вес</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->weight ? $product->specification->weight . ' г' : '—' }}</span>
                        </div>
                        @if($product->specification->battery_life)
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Время работы</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->battery_life }} ч.</span>
                        </div>
                        @endif
                        @break

                    @case('App\Models\Spec\KeyboardSpecification')
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Тип переключателей</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->switch_type ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Форм-фактор</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->form_factor ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Материал кейкапов</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->keycap_material ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Hot-Swap</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->hotswap ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Подсветка</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->illumination ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Строение корпуса</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->construction ?? '—' }}</span>
                        </div>
                        @break

                    @case('App\Models\Spec\HeadphoneSpecification')
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Тип звука</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->sound_type ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Диаметр динамиков</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->drivers ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Частотный диапазон</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->frequency ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Сопротивление</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->impedance ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Микрофон</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->microphone ?? '—' }}</span>
                        </div>
                        @if($product->specification->battery_life)
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Автономность</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->battery_life }} ч.</span>
                        </div>
                        @endif
                        @break

                    @case('App\Models\Spec\PadSpecification')
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Тип поверхности</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->surface ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Материал покрытия</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->material ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Материал основания</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->base_material ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Размеры</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->dimensions ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Толщина</span>
                            <span class="text-gray-200 text-sm font-medium font-mono">{{ $product->specification->thickness ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800/40 pb-2">
                            <span class="text-gray-500 text-sm">Обработка краев</span>
                            <span class="text-gray-200 text-sm font-medium">{{ $product->specification->edges ?? '—' }}</span>
                        </div>
                        @break
                @endswitch
            </div>
        @else
            <div class="text-center py-4 text-xs font-mono text-gray-600 uppercase tracking-wider">
                Характеристики не заполнены
            </div>
        @endif
    </div>

    <div class="w-full pb-20 max-w-4xl mx-auto space-y-8">
        <h2 class="text-2xl font-black text-white uppercase tracking-tight">
            Отзывы о девайсе <span class="text-orange-500 font-normal">/</span> <span class="text-gray-500 font-normal">{{ $product->commentaries->count() }}</span>
        </h2>

        @auth
            <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6 shadow-md">
                @livewire('comment-form', ['product' => $product])
            </div>
        @else
            <div class="border border-dashed border-gray-800 rounded-2xl p-6 text-center text-sm text-gray-500">
                Только авторизованные пользователи могут оставлять отзывы. <a href="{{ route('login') }}" class="text-orange-500 hover:underline">Войти</a>
            </div>
        @endauth

        <div class="space-y-4">
            @forelse($product->commentaries->sortByDesc('created_at') as $comment)
                <div class="bg-[#161920]/40 border border-gray-800 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-black font-black text-xs">
                            {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">{{ $comment->user->name }}</h4>
                            <span class="text-[10px] text-gray-600 font-mono">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <p class="text-gray-400 font-light text-sm italic pl-11">
                        « {{ $comment->content }} »
                    </p>
                </div>
            @empty
                <div class="text-center py-8 text-gray-600 font-mono text-xs uppercase tracking-wider opacity-50">
                    Отзывов пока нет.
                </div>
            @endforelse
        </div>
    </div>
</x-shop-layout>