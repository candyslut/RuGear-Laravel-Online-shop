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

    <div class="divide-y divide-gray-900/60 max-h-72 overflow-y-auto cs pb-3 pr-3">
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
    <div id="checkout-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/85 p-4 backdrop-blur-sm">
        <div class="bg-[#13151a] border border-gray-800/60 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="max-height:92vh;">

            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-800/60 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-500/15 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-orange-400 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-white uppercase tracking-tight">Оформление заказа</h3>
                        <p class="font-mono text-[10px] text-gray-500 mt-0.5">Шаг 1 из 2 — данные получателя</p>
                    </div>
                </div>
                <button onclick="closeCheckoutModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:bg-gray-800/60 transition text-xl leading-none">×</button>
            </div>

            {{-- Body: two columns --}}
            <div class="flex flex-1 overflow-hidden">

                {{-- Left column: form --}}
                <form action="{{ route('orders.store') }}" method="POST" class="flex-1 overflow-y-auto p-8 space-y-5" id="checkout-form">
                    @csrf

                    {{-- Recipient --}}
                    <div>
                        <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Получатель</label>
                        <input name="full_name" required placeholder="ФИО получателя"
                               class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition">
                    </div>

                    {{-- Phone + Email --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Телефон</label>
                            <input name="phone" required placeholder="+7 (999) 000-00-00"
                                   class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition">
                        </div>
                        <div>
                            <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Email <span class="text-gray-700 normal-case">(необязательно)</span></label>
                            <input name="email" type="email" placeholder="your@email.com"
                                   class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Адрес доставки</label>
                        <div class="relative" id="addr-wrap">
                            <input id="address-input" name="address" required placeholder="Начните вводить адрес…" autocomplete="off"
                                   class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition">
                            <div id="addr-dropdown"
                                 class="hidden absolute left-0 right-0 top-full mt-1 bg-[#1a1d24] border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden"
                                 style="max-height:240px;overflow-y:auto;">
                            </div>
                        </div>
                    </div>

                    {{-- Delivery --}}
                    <div>
                        <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Способ доставки</label>
                        <div class="grid grid-cols-3 gap-3" id="delivery-cards">
                            <label class="delivery-card cursor-pointer">
                                <input type="radio" name="delivery_type" value="courier" class="sr-only" checked>
                                <div class="delivery-card-inner border border-gray-800 bg-[#0d0f14] rounded-xl p-3 text-center transition hover:border-orange-500/40">
                                    <svg class="w-5 h-5 mx-auto mb-1.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                                    </svg>
                                    <p class="text-xs font-bold text-white">Курьер</p>
                                </div>
                            </label>
                            <label class="delivery-card cursor-pointer">
                                <input type="radio" name="delivery_type" value="pickup" class="sr-only">
                                <div class="delivery-card-inner border border-gray-800 bg-[#0d0f14] rounded-xl p-3 text-center transition hover:border-orange-500/40">
                                    <svg class="w-5 h-5 mx-auto mb-1.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                                    </svg>
                                    <p class="text-xs font-bold text-white">Самовывоз</p>
                                </div>
                            </label>
                            <label class="delivery-card cursor-pointer">
                                <input type="radio" name="delivery_type" value="post" class="sr-only">
                                <div class="delivery-card-inner border border-gray-800 bg-[#0d0f14] rounded-xl p-3 text-center transition hover:border-orange-500/40">
                                    <svg class="w-5 h-5 mx-auto mb-1.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                    </svg>
                                    <p class="text-xs font-bold text-white">Почта / ПВЗ</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Payment method --}}
                    <div>
                        <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Способ оплаты</label>
                        <div class="grid grid-cols-2 gap-3" id="payment-cards">
                            <label class="payment-card cursor-pointer">
                                <input type="radio" name="payment_method" value="card" class="sr-only" checked>
                                <div class="payment-card-inner border border-orange-500/40 bg-orange-500/5 rounded-xl p-4 transition hover:border-orange-500/60">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-orange-500/15 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white">Банковская карта</p>
                                            <p class="text-[10px] text-gray-500 mt-0.5 font-mono">Visa / MasterCard / МИР</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="payment-card cursor-pointer">
                                <input type="radio" name="payment_method" value="qr" class="sr-only">
                                <div class="payment-card-inner border border-gray-800 bg-[#0d0f14] rounded-xl p-4 transition hover:border-orange-500/40">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-800/60 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white">QR-код</p>
                                            <p class="text-[10px] text-gray-500 mt-0.5 font-mono">СБП / Сканировать и оплатить</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                </form>

                {{-- Right column: summary + submit --}}
                <div class="w-72 flex-shrink-0 border-l border-gray-800/60 flex flex-col bg-[#0f1115]">
                    <div class="p-6 flex-1 overflow-y-auto">
                        <p class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-4">Ваш заказ</p>

                        @if(count($cartItems) > 0)
                        <div class="space-y-3">
                            @foreach($cartItems as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-[#1a1d24] border border-gray-800/60 rounded-lg flex-shrink-0 overflow-hidden">
                                    <img src="{{ asset($item->product->image) }}" class="w-full h-full object-contain p-0.5">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-300 truncate font-medium">{{ $item->product->name }}</p>
                                    <p class="font-mono text-[10px] text-gray-600">{{ $item->quantity }} шт.</p>
                                </div>
                                <p class="font-mono text-xs text-gray-400 flex-shrink-0 tabular-nums">{{ number_format($item->product->price * $item->quantity, 0, '.', ' ') }} ₽</p>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-800/50">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-mono text-[10px] text-gray-600 uppercase">Товаров</span>
                                <span class="font-mono text-xs text-gray-400 tabular-nums">{{ $totalQuantity }} шт.</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[10px] text-gray-600 uppercase">Итого</span>
                                <span class="text-lg font-black text-white tabular-nums">{{ number_format($totalSum, 0, '.', ' ') }} <span class="text-orange-500">₽</span></span>
                            </div>
                        </div>
                        @endif

                        {{-- Info block --}}
                        <div class="mt-5 p-3 bg-[#161920] border border-gray-800/40 rounded-xl">
                            <p class="font-mono text-[10px] text-gray-600 leading-relaxed">После оформления вы будете перенаправлены на страницу оплаты.</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-800/60 flex-shrink-0">
                        <button type="submit" form="checkout-form"
                                class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black py-3.5 rounded-xl uppercase tracking-wider transition-all text-sm shadow-lg shadow-orange-500/20">
                            Перейти к оплате →
                        </button>
                        <button type="button" onclick="closeCheckoutModal()"
                                class="w-full mt-2.5 text-gray-600 hover:text-gray-400 text-xs py-2 transition">
                            Отмена
                        </button>
                    </div>
                </div>

            </div>
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

    // ── Delivery card toggle ──────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        function initCardToggle(groupId) {
            const group = document.getElementById(groupId);
            if (!group) return;
            const labels = group.querySelectorAll('label');
            labels.forEach(label => {
                const radio = label.querySelector('input[type=radio]');
                const inner = label.querySelector('[class*="-card-inner"]');
                function update() {
                    labels.forEach(l => {
                        const r = l.querySelector('input[type=radio]');
                        const d = l.querySelector('[class*="-card-inner"]');
                        if (!d) return;
                        if (r.checked) {
                            d.classList.add('border-orange-500/40', 'bg-orange-500/5');
                            d.classList.remove('border-gray-800', 'bg-[#0d0f14]');
                        } else {
                            d.classList.remove('border-orange-500/40', 'bg-orange-500/5');
                            d.classList.add('border-gray-800', 'bg-[#0d0f14]');
                        }
                    });
                }
                radio.addEventListener('change', update);
            });
        }
        initCardToggle('delivery-cards');
        initCardToggle('payment-cards');
    });

    // ── Address autocomplete ──────────────────────────────────
    (function () {
        const input = document.getElementById('address-input');
        const drop  = document.getElementById('addr-dropdown');
        if (!input) return;

        let timer, active = -1, items = [];

        input.addEventListener('input', () => {
            clearTimeout(timer);
            const q = input.value.trim();
            if (q.length < 2) { hideDrop(); return; }
            timer = setTimeout(() => fetchSuggest(q), 300);
        });

        input.addEventListener('keydown', e => {
            if (drop.classList.contains('hidden')) return;
            if (e.key === 'ArrowDown')  { e.preventDefault(); moveFocus(1);  }
            if (e.key === 'ArrowUp')    { e.preventDefault(); moveFocus(-1); }
            if (e.key === 'Enter' && active >= 0) { e.preventDefault(); pick(items[active]); }
            if (e.key === 'Escape')     { hideDrop(); }
        });

        document.addEventListener('click', e => {
            if (!document.getElementById('addr-wrap').contains(e.target)) hideDrop();
        });

        async function fetchSuggest(q) {
            try {
                const r = await fetch(`/address-suggest?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!r.ok) return;
                const data = await r.json();
                renderDrop(data);
            } catch { /* silently ignore network errors */ }
        }

        function renderDrop(data) {
            items  = data;
            active = -1;
            if (!data.length) { hideDrop(); return; }

            drop.innerHTML = data.map((s, i) => {
                const parts = [s.region, s.city, s.street, s.house].filter(Boolean);
                const sub   = parts.join(', ');
                return `<div class="addr-item px-4 py-3 cursor-pointer border-b border-gray-800/50 last:border-b-0 hover:bg-white/5 transition"
                             data-i="${i}">
                    <p class="text-sm text-white font-medium leading-tight">${escHtml(s.value)}</p>
                    ${sub !== s.value ? `<p class="text-[10px] text-gray-500 mt-0.5 leading-tight">${escHtml(sub)}</p>` : ''}
                </div>`;
            }).join('');

            drop.querySelectorAll('.addr-item').forEach(el => {
                el.addEventListener('mousedown', e => {
                    e.preventDefault();
                    pick(items[+el.dataset.i]);
                });
            });

            drop.classList.remove('hidden');
        }

        function pick(s) {
            input.value = s.value;
            hideDrop();
            input.dispatchEvent(new Event('change'));
        }

        function moveFocus(dir) {
            const els = drop.querySelectorAll('.addr-item');
            if (active >= 0) els[active].classList.remove('bg-white/5');
            active = Math.max(0, Math.min(items.length - 1, active + dir));
            els[active].classList.add('bg-white/5');
            els[active].scrollIntoView({ block: 'nearest' });
        }

        function hideDrop() { drop.classList.add('hidden'); drop.innerHTML = ''; active = -1; }

        function escHtml(s) {
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    })();
</script>
