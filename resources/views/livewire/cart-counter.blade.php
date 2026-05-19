<div>
    @if($product->quantity <= 0 && $quantity==0)
        <span class="text-xs font-mono text-red-500 bg-red-500/10 border border-red-500/20 px-3 py-2 rounded-xl uppercase tracking-wider font-bold">
        Закончился
        </span>

        {{-- Было: @elif($quantity > 0) --}}
        @elseif($quantity > 0)
        <div class="flex items-center bg-gray-900/80 border border-gray-800 rounded-xl p-1">
            <button wire:click="remove" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white transition-all active:scale-90 font-bold cursor-pointer">
                -
            </button>

            <span class="px-3 text-orange-500 font-black text-sm tabular-nums">{{ $quantity }}</span>

            @if($quantity >= $product->quantity)
            <button class="w-8 h-8 flex items-center justify-center text-gray-700 cursor-not-allowed font-bold" title="Достигнут лимит склада" disabled>
                +
            </button>
            @else
            <button wire:click="add" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white transition-all active:scale-90 font-bold cursor-pointer">
                +
            </button>
            @endif
        </div>

        @else
        <button wire:click="add" class="w-12 h-12 flex items-center justify-center bg-gray-800 hover:bg-orange-500 text-white hover:text-black rounded-xl transition-all duration-300 shadow-lg active:scale-90 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>
        @endif
</div>