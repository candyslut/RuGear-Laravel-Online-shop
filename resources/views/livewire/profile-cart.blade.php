<div class="bg-[#161920] border border-gray-800 rounded-3xl p-8 shadow-xl">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-black uppercase tracking-tighter flex items-center">
            <svg class="w-6 h-6 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            Моё <span class="text-orange-500 ml-2">снаряжение</span>
        </h2>

        <span class="text-xs bg-gray-800 text-gray-400 px-3 py-1 rounded-full border border-gray-700 uppercase">
            Предметов: {{ $totalQuantity }}
        </span>
    </div>

    @if(count($cartItems) === 0)
    <div class="py-12 text-center border-2 border-dashed border-gray-800 rounded-3xl">
        <p class="text-gray-500 italic mb-6">В корзине пока пусто</p>
        <a href="/" class="bg-orange-500 hover:bg-orange-600 text-black font-bold py-3 px-8 rounded-2xl uppercase text-sm">
            На витрину
        </a>
    </div>
    @else

    <div class="space-y-4">
        @foreach($cartItems as $item)
        <div class="flex items-center justify-between px-5 py-4 bg-gray-900/40 border border-gray-800 rounded-2xl" wire:key="profile-cart-item-{{ $item->id }}">
            <div class="flex items-center gap-4">
                {{-- Блок картинки товара --}}
                <div class="w-12 h-12 bg-gray-950 border border-gray-800 rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                    <img src="{{ asset($item->product->image) }}"
                        alt="{{ $item->product->name }}"
                        class="w-full h-full object-contain p-1">
                </div>

                <div>
                    <h3 class="text-white font-bold line-clamp-1">
                        {{ $item->product->name }}
                    </h3>
                    <p class="text-gray-500 text-[10px] uppercase tracking-wider mt-0.5">
                        {{ $item->product->category->name ?? 'Девайс' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-10">
                <div class="w-[140px] flex justify-center">
                    <div class="flex items-center bg-black/30 border border-gray-800 rounded-xl overflow-hidden">
                        <button wire:click="decrement({{ $item->product->id }})" class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition cursor-pointer">
                            -
                        </button>

                        <span class="w-12 text-center text-orange-500 font-bold tabular-nums">
                            {{ $item->quantity }}
                        </span>

                        @if($item->quantity >= $item->product->quantity)
                        <button class="w-9 h-9 flex items-center justify-center text-gray-700 bg-gray-950/20 cursor-not-allowed" title="Максимальное количество" disabled>
                            +
                        </button>
                        @else
                        <button wire:click="increment({{ $item->product->id }})" class="w-9 h-9 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/5 transition cursor-pointer">
                            +
                        </button>
                        @endif
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
                {{ number_format($totalSum, 0, '.', ' ') }}
                <span class="text-orange-500">₽</span>
            </p>
        </div>

        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-black font-black px-10 py-4 rounded-2xl transition shadow-lg uppercase tracking-widest cursor-pointer">
                Оформить заказ
            </button>
        </form>
    </div>
    @endif
</div>