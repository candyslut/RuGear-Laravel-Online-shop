{{-- Renders a sticker inside a posted comment. Animated stickers are mounted by
     resources/js/stickers.js (mountPostedStickers) via plain data-* markup —
     NOT Alpine directives — because Livewire skips Alpine init inside wire:ignore
     subtrees on dynamic inserts, which left freshly-posted stickers blank until a
     reload. wire:ignore is still required so the morph never resets the canvas.
     Expects: $sticker (array, has 'file_path'), $size ('md'|'sm'), $wkey (unique). --}}
@php($px = ($size ?? 'md') === 'sm' ? 'w-24 h-24' : 'w-32 h-32')
@php($url = Storage::url($sticker['file_path']))

<div class="{{ ($size ?? 'md') === 'sm' ? 'pl-9' : 'pl-11' }} mt-1">
    @if(\Illuminate\Support\Str::endsWith($sticker['file_path'], '.webm'))
        {{-- Telegram video sticker (.webm): plays only while on screen --}}
        <div wire:ignore wire:key="sticker-{{ $wkey ?? $sticker['id'] }}">
            <video data-posted-sticker data-type="video" src="{{ $url }}#t=0.001"
                   class="{{ $px }} object-contain" muted loop playsinline preload="metadata"></video>
        </div>
    @elseif(\Illuminate\Support\Str::endsWith($sticker['file_path'], '.json'))
        {{-- Animated Lottie sticker (.json) --}}
        <div wire:ignore wire:key="sticker-{{ $wkey ?? $sticker['id'] }}">
            <canvas data-posted-sticker data-type="lottie" data-src="{{ $url }}"
                    class="{{ $px }}" width="256" height="256"></canvas>
        </div>
    @else
        <img src="{{ $url }}" alt="{{ $sticker['emoji'] ?? '' }}"
             class="{{ $px }} object-contain" loading="lazy">
    @endif
</div>
