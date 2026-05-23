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
            <button type="button" onclick="openCheckoutModal()" class="bg-orange-500 hover:bg-orange-600 text-black font-black px-10 py-4 rounded-2xl transition shadow-lg uppercase tracking-widest cursor-pointer">
                Оформить заказ
            </button>
        </form>
    </div>
    @endif

    <div id="checkout-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4">
        <div class="bg-[#161920] border border-gray-800 w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-800">
                <div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tight">Оформление заказа</h3>
                    <p class="text-[10px] text-gray-500 mt-0.5">Заполните данные для доставки</p>
                </div>
                <button type="button" onclick="closeCheckoutModal()" class="text-gray-500 hover:text-white transition text-2xl leading-none p-1">×</button>
            </div>

            {{-- Cart summary --}}
            @if(count($cartItems) > 0)
            <div class="mx-6 mt-4 bg-[#111318] border border-gray-800/60 rounded-2xl p-3 space-y-1.5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-2">Ваш заказ — {{ $totalQuantity }} поз.</p>
                @foreach($cartItems as $item)
                <div class="flex items-center justify-between text-xs gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-6 h-6 bg-gray-900 rounded-lg flex-shrink-0 overflow-hidden border border-gray-800">
                            <img src="{{ asset($item->product->image) }}" class="w-full h-full object-contain p-0.5">
                        </div>
                        <span class="text-gray-400 truncate">{{ $item->product->name }}</span>
                    </div>
                    <span class="text-gray-500 flex-shrink-0">{{ $item->quantity }}× {{ number_format($item->product->price, 0, '.', ' ') }} ₽</span>
                </div>
                @endforeach
                <div class="pt-1.5 border-t border-gray-800/60 flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Итого</span>
                    <span class="text-sm font-black text-white">{{ number_format($totalSum, 0, '.', ' ') }} <span class="text-orange-500">₽</span></span>
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-3">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <input name="full_name" required placeholder="ФИО получателя"
                           class="col-span-2 w-full bg-[#111318] border border-gray-800 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/50 transition">

                    <input name="phone" required placeholder="Телефон"
                           class="w-full bg-[#111318] border border-gray-800 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/50 transition">

                    <input name="email" type="email" placeholder="Email (необязательно)"
                           class="w-full bg-[#111318] border border-gray-800 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/50 transition">
                </div>

                <input id="address-input" name="address" required placeholder="Адрес доставки" autocomplete="off"
                       class="w-full bg-[#111318] border border-gray-800 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/50 transition">
                <input type="hidden" name="address_full" id="address_full">

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Доставка</label>
                        <select name="delivery_type"
                                class="w-full bg-[#111318] border border-gray-800 text-white text-sm px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/50 transition">
                            <option value="courier">🚚 Курьер</option>
                            <option value="pickup">🏪 Самовывоз</option>
                            <option value="post">📦 Почта / ПВЗ</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Оплата</label>
                        <select name="payment_method" required
                                class="w-full bg-[#111318] border border-gray-800 text-white text-sm px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/50 transition">
                            <option value="card">💳 Карта</option>
                            <option value="cash">💵 Наличные</option>
                        </select>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black py-3 rounded-xl uppercase tracking-wider transition-all shadow-lg shadow-orange-500/20 mt-1">
                    Подтвердить заказ →
                </button>
            </form>
        </div>
    </div>
</div>
<script>
    function openCheckoutModal() {
        const modal = document.getElementById('checkout-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeCheckoutModal() {
        const modal = document.getElementById('checkout-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
</script>