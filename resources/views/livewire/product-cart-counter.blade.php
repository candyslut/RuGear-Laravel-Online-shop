<div>
    <div class="mb-4 flex items-center gap-2 font-mono text-xs uppercase tracking-wider">
        <span class="text-gray-500">Наличие:</span>
        @if($product->quantity > 0)
            <span class="text-green-500 font-bold">В наличии ({{ $product->quantity }} шт.)</span>
        @else
            <span class="text-red-500 font-bold">Закончился</span>
        @endif
    </div>

    @if($product->quantity <= 0)
        <button disabled class="w-full py-4 bg-gray-900 border border-gray-800 text-gray-600 font-extrabold uppercase text-xs tracking-widest rounded-xl cursor-not-allowed opacity-50">
            Товар закончился
        </button>
    @elseif($quantity > 0)
        <div class="flex items-center justify-between bg-[#161920] border border-gray-800 rounded-xl p-2 shadow-lg">
            <button wire:click="removeFromCart" class="w-12 h-12 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
            </button>

            <div class="flex flex-col items-center">
                <span class="text-xl font-bold text-orange-500 font-mono">{{ $quantity }}</span>
                <span class="text-[9px] text-gray-600 font-mono uppercase">в корзине</span>
            </div>

            @if($quantity < $product->quantity)
                <button wire:click="addToCart" class="w-12 h-12 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-800/50 rounded-lg transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            @else
                <button disabled class="w-12 h-12 flex items-center justify-center text-gray-700 bg-gray-950/40 rounded-lg cursor-not-allowed" title="Достигнут лимит доступного товара">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            @endif
        </div>
    @else
        <button wire:click="addToCart" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-black font-extrabold uppercase text-xs tracking-widest rounded-xl transition-all shadow-md cursor-pointer">
            Добавить в корзину
        </button>
    @endif
</div>
