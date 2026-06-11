{{--
    Карточка-плитка одной метрики: иконка + значение + подпись (+ доп. строка).

    Параметры:
      $icon   ?string  внутренняя разметка 24×24 stroke-иконки
      $label  string
      $value  string|int
      $sub    ?string
      $accent ?string
--}}
@php
    $accent = $accent ?? 'var(--gs-accent, #a855f7)';
    $icon   = $icon ?? null;
    $sub    = $sub ?? null;
@endphp
<div class="gs-tile">
    @if ($icon)
        <span class="gs-tile__ic" style="color: {{ $accent }}; background: color-mix(in srgb, {{ $accent }} 14%, transparent)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">{!! $icon !!}</svg>
        </span>
    @endif
    <div class="gs-tile__body">
        <span class="gs-tile__val">{{ $value }}</span>
        <span class="gs-tile__lbl">{{ $label }}</span>
        @if ($sub !== null)<span class="gs-tile__sub">{{ $sub }}</span>@endif
    </div>
</div>
