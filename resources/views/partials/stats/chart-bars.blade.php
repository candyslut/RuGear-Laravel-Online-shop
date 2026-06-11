{{--
    Адаптивная столбчатая диаграмма (inline-SVG + Alpine).
    Используется для «забегов по дням» и гистограммы распределения счёта.

    Параметры:
      $series  list<array{label:string, value:int}>
      $accent  ?string
      $height  ?int (default 130)
      $unit    ?string
--}}
@php
    $series = $series ?? [];
    $accent = $accent ?? 'var(--gs-accent, #a855f7)';
    $unit   = $unit ?? '';
    $W = 320; $H = (int) ($height ?? 130);
    $padL = 6; $padR = 6; $padT = 12; $padB = 16;

    $n = count($series);
    $vals = array_map(fn ($p) => max(0, (int) ($p['value'] ?? 0)), $series);
    $max = max(1, $vals ? max($vals) : 1);
    $plotW = $W - $padL - $padR;
    $plotH = $H - $padT - $padB;
    $baseY = $H - $padB;

    $slot = $n > 0 ? $plotW / $n : $plotW;
    $bw   = min(28, max(3, $slot * 0.62));

    $bars = [];
    foreach ($series as $i => $p) {
        $v = max(0, (int) ($p['value'] ?? 0));
        $h = $v > 0 ? max(2, ($v / $max) * $plotH) : 0;
        $cx = $padL + $slot * $i + $slot / 2;
        $bars[] = [
            'x'     => round($cx - $bw / 2, 2),
            'y'     => round($baseY - $h, 2),
            'w'     => round($bw, 2),
            'h'     => round($h, 2),
            'cx'    => round($cx, 2),
            'label' => (string) ($p['label'] ?? ''),
            'value' => $v,
        ];
    }
@endphp

@if ($n === 0)
    <div class="gs-empty" style="height: {{ $H }}px">Пока нет данных</div>
@else
    <div class="gs-chart" x-data="{ hi: null, bars: @js($bars), W: {{ $W }}, H: {{ $H }}, unit: @js($unit) }">
        <svg viewBox="0 0 {{ $W }} {{ $H }}" class="gs-svg" @mouseleave="hi = null">
            @foreach ([0.0, 0.5, 1.0] as $g)
                @php $gy = $padT + $g * $plotH; @endphp
                <line x1="{{ $padL }}" y1="{{ round($gy, 1) }}" x2="{{ $W - $padR }}" y2="{{ round($gy, 1) }}"
                      stroke="var(--gs-line, rgba(125,134,148,.18))" stroke-width="1" vector-effect="non-scaling-stroke"/>
            @endforeach

            @foreach ($bars as $i => $b)
                <rect x="{{ $b['x'] }}" width="{{ $b['w'] }}" rx="2"
                      y="{{ $b['y'] }}" height="{{ $b['h'] }}"
                      fill="{{ $accent }}"
                      :opacity="hi === null || hi === {{ $i }} ? 1 : 0.4"
                      style="transition: opacity .12s ease"/>
                {{-- зона наведения на всю высоту слота --}}
                <rect x="{{ round($b['cx'] - $slot / 2, 2) }}" y="0" width="{{ round($slot, 2) }}" height="{{ $H }}"
                      fill="transparent" @mouseenter="hi = {{ $i }}"/>
            @endforeach
        </svg>

        <div class="gs-tip" x-show="hi !== null" x-cloak
             :style="`left:${(bars[hi]?.cx ?? 0) / W * 100}%; top:${(bars[hi]?.y ?? 0) / H * 100}%`">
            <span class="gs-tip__label" x-text="bars[hi]?.label"></span>
            <span class="gs-tip__val" x-text="(bars[hi]?.value ?? 0).toLocaleString('ru-RU') + (unit ? ' ' + unit : '')"></span>
        </div>

        <div class="gs-axis">
            <span>{{ $bars[0]['label'] }}</span>
            @if ($n > 2)<span>{{ $bars[intdiv($n, 2)]['label'] }}</span>@endif
            <span>{{ end($bars)['label'] }}</span>
        </div>
    </div>
@endif
