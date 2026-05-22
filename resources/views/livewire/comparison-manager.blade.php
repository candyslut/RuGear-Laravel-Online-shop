<div class="space-y-2 mb-4">
    @if($isInComparison)
        <button wire:click="removeFromComparison" class="w-full py-3 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 hover:border-red-500/50 text-red-400 font-semibold text-xs uppercase tracking-wider rounded-lg transition-all">
            Удалить из сравнения
        </button>
    @elseif(!$isComparisonFull)
        <button wire:click="addToComparison" class="w-full py-3 bg-orange-500/10 hover:bg-orange-500/20 border border-orange-500/30 hover:border-orange-500/50 text-orange-400 font-semibold text-xs uppercase tracking-wider rounded-lg transition-all">
            Добавить в сравнение
        </button>
    @else
        <button disabled class="w-full py-3 bg-gray-800 border border-gray-700 text-gray-500 font-semibold text-xs uppercase tracking-wider rounded-lg cursor-not-allowed opacity-50" title="Максимум 2 устройства">
            Сравнение полное
        </button>
    @endif

    @if(!empty($comparisonItems))
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 text-xs text-blue-300">
            <div class="font-semibold mb-1">В сравнении: {{ count($comparisonItems) }}/2</div>
            @if(count($comparisonItems) >= 1)
                <a href="{{ route('comparison.show') }}" class="text-blue-400 hover:text-blue-300 underline">
                    Посмотреть сравнение →
                </a>
            @endif
        </div>
    @endif
</div>
