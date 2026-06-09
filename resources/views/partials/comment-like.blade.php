{{--
    Like control for a comment / reply.

    Authenticated users (other than the author) get an interactive heart that
    toggles optimistically on the client (instant fill + count change via Alpine)
    while wire:click persists it on the server. The author of the review — and
    guests — just see a static count (only when there is at least one like).

    Expects: $id, $likesCount, $liked (bool), $ownerId
--}}
@php $canLike = auth()->check() && auth()->id() !== $ownerId; @endphp

@if($canLike)
    <button
        type="button"
        wire:key="like-{{ $id }}"
        wire:click="toggleLike({{ $id }})"
        x-data="{ liked: @js((bool) $liked), count: {{ (int) $likesCount }} }"
        @click="liked = !liked; count = Math.max(0, count + (liked ? 1 : -1))"
        :class="liked ? 'text-orange-500' : 'text-gray-500 hover:text-orange-500'"
        class="inline-flex items-center gap-1.5 text-[11px] font-mono uppercase tracking-wider transition-colors active:scale-95"
        title="Нравится"
    >
        <svg class="w-4 h-4 transition-transform" :class="liked ? 'scale-110' : ''"
             :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 10-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/>
        </svg>
        <span x-text="count">{{ (int) $likesCount }}</span>
    </button>
@elseif($likesCount > 0)
    <span class="inline-flex items-center gap-1.5 text-[11px] font-mono uppercase tracking-wider text-gray-500" title="Лайки">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 21.23l-8.84-8.84a5.5 5.5 0 117.78-7.78L12 5.67l1.06-1.06a5.5 5.5 0 117.78 7.78z"/>
        </svg>
        {{ (int) $likesCount }}
    </span>
@endif
