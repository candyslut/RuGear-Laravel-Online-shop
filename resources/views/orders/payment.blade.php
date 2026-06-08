<x-shop-layout>
    <x-slot name="title">Оплата заказа ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} — RuGear</x-slot>

    <div class="min-h-[80vh] flex flex-col items-center justify-center py-10">

        {{-- Progress bar --}}
        <div class="w-full max-w-2xl mb-8">
            <div class="flex items-center gap-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-green-500/20 border border-green-500/40 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline text-xs font-bold text-green-400 uppercase tracking-wider">Заказ оформлен</span>
                </div>
                <div class="flex-1 h-px bg-orange-500/50 mx-2 sm:mx-4"></div>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-orange-500/20 border border-orange-500/50 flex items-center justify-center">
                        <span class="text-xs font-black text-orange-400">2</span>
                    </div>
                    <span class="hidden sm:inline text-xs font-bold text-orange-400 uppercase tracking-wider">Оплата</span>
                </div>
                <div class="flex-1 h-px bg-gray-800 mx-2 sm:mx-4"></div>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-gray-800 border border-gray-700 flex items-center justify-center">
                        <span class="text-xs font-black text-gray-600">3</span>
                    </div>
                    <span class="hidden sm:inline text-xs font-bold text-gray-600 uppercase tracking-wider">Доставка</span>
                </div>
            </div>
        </div>

        <div class="w-full max-w-2xl">
            <div class="bg-[#13151a] border border-gray-800/60 rounded-2xl overflow-hidden shadow-2xl">

                {{-- Header --}}
                <div class="px-5 sm:px-8 py-6 border-b border-gray-800/60">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Заказ</p>
                            <h1 class="text-2xl font-black text-white">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">К оплате</p>
                            <p class="text-3xl font-black text-white tabular-nums">{{ number_format($order->total_price, 0, '.', ' ') }}&nbsp;<span class="text-orange-500">₽</span></p>
                        </div>
                    </div>
                </div>

                {{-- Order summary --}}
                <div class="px-5 sm:px-8 py-5 border-b border-gray-800/40 bg-[#0f1115]">
                    <div class="grid grid-cols-3 gap-3 sm:gap-6 text-center">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-widest text-gray-600 mb-1">Получатель</p>
                            <p class="text-sm font-bold text-white">{{ $order->full_name }}</p>
                        </div>
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-widest text-gray-600 mb-1">Доставка</p>
                            <p class="text-sm font-bold text-white">
                                @if($order->delivery_type === 'courier') Курьер
                                @elseif($order->delivery_type === 'pickup') Самовывоз
                                @else Почта / ПВЗ
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-widest text-gray-600 mb-1">Товаров</p>
                            <p class="text-sm font-bold text-white">{{ $order->items->sum('quantity') }} шт.</p>
                        </div>
                    </div>
                </div>

                {{-- Payment section --}}
                <div class="px-5 sm:px-8 py-6 sm:py-8">

                    @if($order->payment_method === 'card')
                    {{-- Card payment --}}
                    <div>
                        <p class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-6">Оплата банковской картой</p>

                        {{-- Card visual --}}
                        <div class="relative w-full max-w-xs mx-auto mb-8 h-44 rounded-2xl overflow-hidden shadow-2xl"
                             style="background: linear-gradient(135deg, #1e2030 0%, #2d1f3d 50%, #1a2640 100%);">
                            <div class="absolute inset-0 opacity-10"
                                 style="background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.3) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(255,165,0,0.3) 0%, transparent 50%);"></div>
                            <div class="absolute top-5 right-5">
                                <svg viewBox="0 0 48 32" class="w-12 h-8" fill="none">
                                    <circle cx="18" cy="16" r="10" fill="#EB001B" opacity="0.9"/>
                                    <circle cx="30" cy="16" r="10" fill="#F79E1B" opacity="0.9"/>
                                    <path d="M24 8.5a10 10 0 010 15A10 10 0 0124 8.5z" fill="#FF5F00" opacity="0.8"/>
                                </svg>
                            </div>
                            <div class="absolute bottom-5 left-6 right-6">
                                <div class="flex gap-3 mb-3">
                                    <div class="w-8 h-6 bg-yellow-400/80 rounded-sm"></div>
                                </div>
                                <p class="font-mono text-white text-sm tracking-widest mb-2">•••• •••• •••• ••••</p>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="font-mono text-[9px] text-gray-400 uppercase tracking-wider">Владелец</p>
                                        <p class="font-mono text-xs text-white uppercase">{{ strtoupper($order->full_name) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-mono text-[9px] text-gray-400 uppercase tracking-wider">Срок</p>
                                        <p class="font-mono text-xs text-white">••/••</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card form --}}
                        <div class="space-y-4 max-w-sm mx-auto">
                            <div>
                                <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Номер карты</label>
                                <input type="text" placeholder="0000 0000 0000 0000" maxlength="19"
                                       class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition font-mono tracking-widest"
                                       oninput="this.value=this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim().slice(0,19)">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">Срок действия</label>
                                    <input type="text" placeholder="ММ / ГГ" maxlength="7"
                                           class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition font-mono"
                                           oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d)/,'$1 / $2').slice(0,7)">
                                </div>
                                <div>
                                    <label class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2 block">CVV / CVC</label>
                                    <input type="password" placeholder="•••" maxlength="3"
                                           class="w-full bg-[#0d0f14] border border-gray-800 text-white text-sm px-4 py-3 rounded-xl placeholder:text-gray-700 focus:outline-none focus:border-orange-500/50 focus:ring-1 focus:ring-orange-500/20 transition font-mono">
                                </div>
                            </div>

                            <button onclick="simulatePay(this)"
                                    class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black py-4 rounded-xl uppercase tracking-wider transition-all text-sm shadow-lg shadow-orange-500/20 mt-2">
                                Оплатить {{ number_format($order->total_price, 0, '.', ' ') }} ₽
                            </button>
                        </div>
                    </div>

                    @else
                    {{-- QR payment --}}
                    <div class="text-center">
                        <p class="font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-6">Оплата через СБП по QR-коду</p>

                        <div class="relative inline-block mb-6">
                            {{-- QR code SVG --}}
                            <div class="w-52 h-52 mx-auto bg-white rounded-2xl p-4 shadow-2xl shadow-orange-500/10 border-2 border-orange-500/20">
                                <svg viewBox="0 0 210 210" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Top-left finder pattern -->
                                    <rect x="10" y="10" width="60" height="60" fill="#111" rx="4"/>
                                    <rect x="18" y="18" width="44" height="44" fill="white" rx="2"/>
                                    <rect x="26" y="26" width="28" height="28" fill="#111" rx="2"/>
                                    <!-- Top-right finder pattern -->
                                    <rect x="140" y="10" width="60" height="60" fill="#111" rx="4"/>
                                    <rect x="148" y="18" width="44" height="44" fill="white" rx="2"/>
                                    <rect x="156" y="26" width="28" height="28" fill="#111" rx="2"/>
                                    <!-- Bottom-left finder pattern -->
                                    <rect x="10" y="140" width="60" height="60" fill="#111" rx="4"/>
                                    <rect x="18" y="148" width="44" height="44" fill="white" rx="2"/>
                                    <rect x="26" y="156" width="28" height="28" fill="#111" rx="2"/>
                                    <!-- Data modules (pseudo-random pattern) -->
                                    <rect x="82" y="10" width="8" height="8" fill="#111"/>
                                    <rect x="100" y="10" width="8" height="8" fill="#111"/>
                                    <rect x="118" y="10" width="8" height="8" fill="#111"/>
                                    <rect x="82" y="26" width="8" height="8" fill="#111"/>
                                    <rect x="118" y="26" width="8" height="8" fill="#111"/>
                                    <rect x="90" y="34" width="8" height="8" fill="#111"/>
                                    <rect x="108" y="34" width="8" height="8" fill="#111"/>
                                    <rect x="82" y="42" width="8" height="8" fill="#111"/>
                                    <rect x="100" y="42" width="8" height="8" fill="#111"/>
                                    <rect x="118" y="42" width="8" height="8" fill="#111"/>
                                    <rect x="82" y="58" width="8" height="8" fill="#111"/>
                                    <rect x="108" y="58" width="8" height="8" fill="#111"/>

                                    <rect x="10" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="26" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="50" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="82" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="100" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="118" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="140" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="164" y="82" width="8" height="8" fill="#111"/>
                                    <rect x="192" y="82" width="8" height="8" fill="#111"/>

                                    <rect x="18" y="90" width="8" height="8" fill="#111"/>
                                    <rect x="42" y="90" width="8" height="8" fill="#111"/>
                                    <rect x="66" y="90" width="8" height="8" fill="#111"/>
                                    <rect x="90" y="90" width="8" height="8" fill="#111"/>
                                    <rect x="130" y="90" width="8" height="8" fill="#111"/>
                                    <rect x="148" y="90" width="8" height="8" fill="#111"/>
                                    <rect x="172" y="90" width="8" height="8" fill="#111"/>

                                    <rect x="10" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="34" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="58" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="82" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="108" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="140" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="156" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="180" y="100" width="8" height="8" fill="#111"/>
                                    <rect x="200" y="100" width="8" height="8" fill="#111"/>

                                    <rect x="18" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="50" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="74" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="98" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="122" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="148" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="172" y="110" width="8" height="8" fill="#111"/>
                                    <rect x="192" y="110" width="8" height="8" fill="#111"/>

                                    <rect x="10" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="34" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="66" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="90" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="114" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="140" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="164" y="120" width="8" height="8" fill="#111"/>
                                    <rect x="192" y="120" width="8" height="8" fill="#111"/>

                                    <rect x="82" y="140" width="8" height="8" fill="#111"/>
                                    <rect x="100" y="140" width="8" height="8" fill="#111"/>
                                    <rect x="126" y="140" width="8" height="8" fill="#111"/>
                                    <rect x="148" y="140" width="8" height="8" fill="#111"/>
                                    <rect x="172" y="140" width="8" height="8" fill="#111"/>
                                    <rect x="200" y="140" width="8" height="8" fill="#111"/>

                                    <rect x="82" y="156" width="8" height="8" fill="#111"/>
                                    <rect x="108" y="156" width="8" height="8" fill="#111"/>
                                    <rect x="140" y="156" width="8" height="8" fill="#111"/>
                                    <rect x="164" y="156" width="8" height="8" fill="#111"/>
                                    <rect x="192" y="156" width="8" height="8" fill="#111"/>

                                    <rect x="82" y="172" width="8" height="8" fill="#111"/>
                                    <rect x="98" y="172" width="8" height="8" fill="#111"/>
                                    <rect x="122" y="172" width="8" height="8" fill="#111"/>
                                    <rect x="148" y="172" width="8" height="8" fill="#111"/>
                                    <rect x="172" y="172" width="8" height="8" fill="#111"/>
                                    <rect x="200" y="172" width="8" height="8" fill="#111"/>

                                    <rect x="82" y="192" width="8" height="8" fill="#111"/>
                                    <rect x="108" y="192" width="8" height="8" fill="#111"/>
                                    <rect x="130" y="192" width="8" height="8" fill="#111"/>
                                    <rect x="156" y="192" width="8" height="8" fill="#111"/>
                                    <rect x="180" y="192" width="8" height="8" fill="#111"/>

                                    <!-- SBP logo area (orange accent) -->
                                    <rect x="91" y="91" width="28" height="28" fill="white" rx="4"/>
                                    <text x="105" y="110" text-anchor="middle" font-size="16" font-weight="bold" fill="#FF5F00" font-family="Arial, sans-serif">СБП</text>
                                </svg>
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </div>

                        <p class="text-white font-black text-2xl tabular-nums mb-1">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</p>
                        <p class="text-gray-500 text-sm mb-6">Отсканируйте QR-код в приложении банка</p>

                        <div class="flex items-center justify-center gap-6 mb-8">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-5 bg-[#0d0f14] border border-gray-800 rounded flex items-center justify-center">
                                    <span class="text-[8px] font-black text-green-400">СБП</span>
                                </div>
                                <span class="text-xs text-gray-500">Система быстрых платежей</span>
                            </div>
                        </div>

                        <div class="bg-[#0d0f14] border border-gray-800/60 rounded-xl p-4 text-left max-w-xs mx-auto mb-6">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-5 h-5 rounded-full bg-orange-500/20 flex items-center justify-center">
                                    <span class="text-[10px] text-orange-400 font-bold">i</span>
                                </div>
                                <p class="font-mono text-[10px] uppercase tracking-wider text-gray-500">Инструкция</p>
                            </div>
                            <ol class="space-y-2 text-xs text-gray-400">
                                <li class="flex gap-2"><span class="text-orange-500 font-black flex-shrink-0">1.</span>Откройте приложение банка</li>
                                <li class="flex gap-2"><span class="text-orange-500 font-black flex-shrink-0">2.</span>Выберите оплату по QR-коду или СБП</li>
                                <li class="flex gap-2"><span class="text-orange-500 font-black flex-shrink-0">3.</span>Отсканируйте код выше</li>
                                <li class="flex gap-2"><span class="text-orange-500 font-black flex-shrink-0">4.</span>Подтвердите платёж на сумму {{ number_format($order->total_price, 0, '.', ' ') }} ₽</li>
                            </ol>
                        </div>

                        <button onclick="simulatePay(this)"
                                class="bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black py-3.5 px-10 rounded-xl uppercase tracking-wider transition-all text-sm shadow-lg shadow-orange-500/20">
                            Я оплатил
                        </button>
                    </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="px-5 sm:px-8 py-5 border-t border-gray-800/40 bg-[#0f1115] flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('dashboard') }}" class="text-xs text-gray-600 hover:text-gray-400 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                        В личный кабинет
                    </a>
                    <div class="flex items-center gap-2 text-xs text-gray-700">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                        Защищённое соединение
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Payment simulation overlay --}}
    <div id="pay-overlay" class="hidden fixed inset-0 z-50 bg-black/90 flex items-center justify-center backdrop-blur-sm">
        <div class="text-center">
            <div id="pay-spinner" class="w-16 h-16 rounded-full border-4 border-orange-500/20 border-t-orange-500 animate-spin mx-auto mb-6"></div>
            <p class="text-white font-black text-lg mb-2">Обработка платежа</p>
            <p class="text-gray-500 text-sm">Пожалуйста, не закрывайте страницу</p>
        </div>
    </div>

    <script>
        function simulatePay(btn) {
            btn.disabled = true;
            document.getElementById('pay-overlay').classList.remove('hidden');
            document.getElementById('pay-overlay').classList.add('flex');
            setTimeout(() => {
                window.location.href = '{{ route('orders.payment.confirm', $order) }}';
            }, 2500);
        }
    </script>
</x-shop-layout>
