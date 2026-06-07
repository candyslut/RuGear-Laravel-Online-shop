@php
    $cu = $author;
    $borderCss = ($cu['cosmetic_border'] ?? null) && $cu['cosmetic_border'] !== 'rainbow' ? 'box-shadow:'.$cu['cosmetic_border'].';' : '';
    $isRainbow = ($cu['cosmetic_border'] ?? null) === 'rainbow';
    $small = ($size ?? 'md') === 'sm';
    $avatarBox = $small ? 'w-6 h-6 text-[10px]' : 'w-8 h-8 text-xs';
@endphp
<div class="flex items-center gap-3 mb-3">
    <div class="{{ $avatarBox }} rounded-lg bg-orange-500 flex items-center justify-center text-black font-black flex-shrink-0 overflow-hidden {{ $isRainbow ? 'avatar-rainbow' : '' }}"
         style="{{ $borderCss }}">
        @if(!empty($cu['avatar']))
            <img src="{{ Storage::url($cu['avatar']) }}" class="w-full h-full object-cover">
        @else
            {{ strtoupper(substr($cu['name'], 0, 2)) }}
        @endif
    </div>
    <div class="min-w-0 flex-1">
        <h4 class="{{ $small ? 'text-xs' : 'text-sm' }} font-semibold"
            style="color:{{ $cu['cosmetic_nickname_color'] ?? '#fff' }};{{ isset($cu['cosmetic_font']) && $cu['cosmetic_font'] ? 'font-family:'.$cu['cosmetic_font'].';' : '' }}">{{ $cu['name'] }}</h4>
        <span class="text-[10px] text-gray-600 font-mono">
            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
        </span>
    </div>
</div>
