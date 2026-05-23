<div>
    @if(count($cartItems) === 0)
    <div class="py-16 flex flex-col items-center gap-3 text-center">
        <div class="w-10 h-10 border border-dashed border-gray-800 rounded-xl flex items-center justify-center text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <p class="text-sm text-gray-600">Корзина пуста</p>
        <a href="/" class="text-orange-500 hover:text-orange-400 text-sm font-bold transition">В каталог →</a>
    </div>
    @else

    <div class="divide-y divide-gray-900/60 max-h-72 overflow-y-auto cs">
        @foreach($cartItems as $item)
        <div class="flex items-center gap-4 py-4" wire:key="profile-cart-item-{{ $item->id }}">

            <div class="w-11 h-11 bg-[#0f1115] border border-gray-900/80 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                <img src="{{ asset($item->product->image) }}"
                     alt="{{ $item->product->name }}"
                     class="w-full h-full object-contain p-1">
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white line-clamp-1">{{ $item->product->name }}</p>
                <p class="font-mono text-[10px] uppercase tracking-wider text-gray-600 mt-0.5">{{ $item->product->category->name ?? '—' }}</p>
            </div>

            <div class="flex items-center border border-gray-800 rounded-lg overflow-hidden flex-shrink-0">
                <button wire:click="decrement({{ $item->product->id }})"
                        class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-white hover:bg-gray-800/60 transition cursor-pointer text-sm select-none">
                    −
                </button>
                <span class="w-9 text-center text-sm font-black text-orange-500 tabular-nums select-none">{{ $item->quantity }}</span>
                @if($item->quantity >= $item->product->quantity)
                <button disabled
                        class="w-8 h-8 flex items-center justify-center text-gray-800 cursor-not-allowed text-sm bg-gray-900/20 select-none">
                    +
                </button>
                @else
                <button wire:click="increment({{ $item->product->id }})"
                        class="w-8 h-8 flex items-center justify-center text-gray-600 hover:text-white hover:bg-gray-800/60 transition cursor-pointer text-sm select-none">
                    +
                </button>
                @endif
            </div>

            <div class="text-right flex-shrink-0 w-28">
                <p class="text-sm font-black text-white tabular-nums">{{ number_format($item->product->price * $item->quantity, 0, '.', ' ') }} ₽</p>
                <p class="font-mono text-[10px] text-gray-700 mt-0.5 tabular-nums">{{ number_format($item->product->price, 0, '.', ' ') }} ₽/шт.</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="flex items-center justify-between pt-7 mt-3 border-t border-gray-900/50">
        <div>
            <p class="font-mono text-[10px] uppercase tracking-widest text-gray-600 mb-1.5">Итого</p>
            <p class="text-3xl font-black text-white leading-none tabular-nums">
                {{ number_format($totalSum, 0, '.', ' ') }}&nbsp;<span class="text-orange-500">₽</span>
            </p>
        </div>
        <button type="button" onclick="openCheckoutModal()"
                class="bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black text-sm px-8 py-3.5 rounded-xl transition-all uppercase tracking-wider shadow-lg shadow-orange-500/10">
            Оформить заказ
        </button>
    </div>
    @endif

    {{-- ─── Checkout modal ─────────────────────────────────── --}}
    <div id="checkout-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4">
        <div class="bg-[#161920] border border-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">

            <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-800/60">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-tight">Оформление заказа</h3>
                    <p class="font-mono text-[10px] text-gray-600 mt-0.5">Заполните данные для доставки</p>
                </div>
                <button onclick="closeCheckoutModal()" class="text-gray-600 hover:text-white transition text-xl leading-none p-1">×</button>
            </div>

            @if(count($cartItems) > 0)
            <div class="mx-6 mt-4 bg-[#0f1115] border border-gray-900/70 rounded-xl p-3 space-y-1.5">
                <p class="font-mono text-[10px] uppercase tracking-wider text-gray-600 mb-2">{{ $totalQuantity }} позиций</p>
                @foreach($cartItems as $item)
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-5 h-5 bg-gray-900 rounded flex-shrink-0 overflow-hidden border border-gray-800/60">
                            <img src="{{ asset($item->product->image) }}" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xs text-gray-500 truncate">{{ $item->product->name }}</span>
                    </div>
                    <span class="font-mono text-[11px] text-gray-600 flex-shrink-0 tabular-nums">{{ $item->quantity }}× {{ number_format($item->product->price, 0, '.', ' ') }} ₽</span>
                </div>
                @endforeach
                <div class="pt-2 border-t border-gray-900/50 flex justify-between items-center">
                    <span class="font-mono text-[10px] text-gray-600 uppercase">Итого</span>
                    <span class="text-sm font-black text-white tabular-nums">{{ number_format($totalSum, 0, '.', ' ') }} <span class="text-orange-500">₽</span></span>
                </div>
            </div>
            @endif

            <form action="{{ route('orders.store') }}" method="POST" class="p-6 space-y-3">
                @csrf

                <input name="full_name" required placeholder="ФИО получателя"
                       class="w-full bg-[#0f1115] border border-gray-900 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-gray-700 focus:ring-1 focus:ring-gray-700 transition">

                <div class="grid grid-cols-2 gap-3">
                    <input name="phone" required placeholder="Телефон"
                           class="w-full bg-[#0f1115] border border-gray-900 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-gray-700 focus:ring-1 focus:ring-gray-700 transition">
                    <input name="email" type="email" placeholder="Email"
                           class="w-full bg-[#0f1115] border border-gray-900 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-gray-700 focus:ring-1 focus:ring-gray-700 transition">
                </div>

                <input id="address-input" name="address" required placeholder="Адрес доставки" autocomplete="off"
                       class="w-full bg-[#0f1115] border border-gray-900 text-white text-sm px-4 py-2.5 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-gray-700 focus:ring-1 focus:ring-gray-700 transition">
                <input type="hidden" name="address_full" id="address_full">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="font-mono text-[10px] uppercase tracking-wider text-gray-600 mb-1.5">Доставка</p>
                        <select name="delivery_type"
                                class="w-full bg-[#0f1115] border border-gray-900 text-white text-sm px-4 py-2.5 rounded-xl focus:outline-none focus:border-gray-700 transition">
                            <option value="courier">Курьер</option>
                            <option value="pickup">Самовывоз</option>
                            <option value="post">Почта / ПВЗ</option>
                        </select>
                    </div>
                    <div>
                        <p class="font-mono text-[10px] uppercase tracking-wider text-gray-600 mb-1.5">Оплата</p>
                        <select name="payment_method" required
                                class="w-full bg-[#0f1115] border border-gray-900 text-white text-sm px-4 py-2.5 rounded-xl focus:outline-none focus:border-gray-700 transition">
                            <option value="card">Банковская карта</option>
                            <option value="cash">Наличные</option>
                        </select>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black py-3 rounded-xl uppercase tracking-wider transition-all mt-1">
                    Подтвердить заказ
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openCheckoutModal() {
        const m = document.getElementById('checkout-modal');
        m.classList.remove('hidden'); m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeCheckoutModal() {
        const m = document.getElementById('checkout-modal');
        m.classList.add('hidden'); m.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
</script>
