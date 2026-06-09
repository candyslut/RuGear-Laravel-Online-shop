{{-- One sticker cell in the picker grid. Clicking it STAGES the sticker
     client-side (no server round-trip); it is sent on comment submit.

     Animated/video cells render a cheap placeholder — the x-sticker-grid
     directive (resources/js/stickers.js) injects a paused player only while the
     cell is on screen, and plays it on hover. Static stickers are a plain <img>.
     Expects: $sticker, $ctx (section key, keeps wire:key unique per section). --}}
@php
    $url  = Storage::url($sticker->file_path);
    $type = \Illuminate\Support\Str::endsWith($sticker->file_path, '.json') ? 'lottie'
          : (\Illuminate\Support\Str::endsWith($sticker->file_path, '.webm') ? 'video' : 'image');
@endphp
<button
    type="button"
    @click="select({{ $sticker->id }}, '{{ $url }}', '{{ $type }}')"
    wire:key="pick-{{ $ctx }}-{{ $sticker->id }}"
    data-sticker-cell data-sticker-id="{{ $sticker->id }}" data-type="{{ $type }}" data-src="{{ $url }}"
    title="{{ $sticker->emoji }}"
    class="sticker-cell aspect-square rounded-xl p-1 flex items-center justify-center
           hover:bg-gray-800/80 ring-1 ring-transparent hover:ring-orange-500/40
           transition-all active:scale-90"
>
    @if($type === 'image')
        <img src="{{ $url }}" alt="{{ $sticker->emoji }}" loading="lazy"
             class="w-full h-full object-contain transition-transform group-hover:scale-110">
    @else
        <span class="sticker-ph w-full h-full flex items-center justify-center"></span>
    @endif
</button>
