<div class="space-y-4">
    @forelse($comments as $comment)
        <div 
            wire:key="comment-{{ $comment['id'] }}"
            class="bg-[#161920]/40 border border-gray-800 rounded-xl p-5 shadow-sm comment-item"
            @if($loop->first) style="animation: comment-slide-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);" @endif
        >
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg bg-orange-500 flex items-center justify-center text-black font-black text-xs flex-shrink-0">
                    {{ strtoupper(substr($comment['user']['name'], 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-white text-sm font-semibold">{{ $comment['user']['name'] }}</h4>
                    <span class="text-[10px] text-gray-600 font-mono">
                        @php
                            $date = \Carbon\Carbon::parse($comment['created_at']);
                            echo $date->diffForHumans();
                        @endphp
                    </span>
                </div>
            </div>
            <p class="text-gray-400 font-light text-sm italic pl-11">
                « {{ $comment['content'] }} »
            </p>
        </div>
    @empty
        <div class="text-center py-8 text-gray-600 font-mono text-xs uppercase tracking-wider opacity-50">
            Отзывов пока нет.
        </div>
    @endforelse
</div>

<style>
    @keyframes comment-slide-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .comment-item {
        transition: all 0.3s ease;
    }
</style>
