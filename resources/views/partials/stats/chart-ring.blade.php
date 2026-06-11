{{--
    Радиальный индикатор-кольцо (inline-SVG). Доля value/max рисуется дугой.
    Подходит для перцентиля, прогресса к рекорду, доли уровней и т.п.

    Параметры:
      $value   int
      $max     int (>0)
      $center  ?string  крупный текст в центре (default — округлённый %)
      $label   ?string  подпись под числом
      $accent  ?string
      $size    ?int  размер SVG в px (default 120)
--}}
@php
    $value  = (int) ($value ?? 0);
    $max    = max(1, (int) ($max ?? 1));
    $accent = $accent ?? 'var(--gs-accent, #a855f7)';
    $size   = (int) ($size ?? 120);
    $frac   = max(0, min(1, $value / $max));
    $r = 50; $cx = 60; $cy = 60;
    $circ = 2 * M_PI * $r;
    $dash = round($circ * $frac, 2);
    $gap  = round($circ - $dash, 2);
    $center = $center ?? (round($frac * 100) . '%');
    $label  = $label ?? '';
@endphp

<div class="gs-ring" style="width: {{ $size }}px; height: {{ $size }}px">
    <svg viewBox="0 0 120 120" class="block w-full h-full" style="transform: rotate(-90deg)">
        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                stroke="var(--gs-line, rgba(125,134,148,.20))" stroke-width="9"/>
        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                stroke="{{ $accent }}" stroke-width="9" stroke-linecap="round"
                stroke-dasharray="{{ $dash }} {{ $gap }}"
                style="transition: stroke-dasharray .6s cubic-bezier(.4,0,.2,1)"/>
    </svg>
    <div class="gs-ring__c">
        <span class="gs-ring__v">{{ $center }}</span>
        @if ($label !== '')<span class="gs-ring__l">{{ $label }}</span>@endif
    </div>
</div>
