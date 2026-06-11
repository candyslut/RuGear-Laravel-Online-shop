{{--
    Адаптивный линейный/площадной график (inline-SVG + Alpine).
    Тема — через CSS-переменные родителя (--gs-accent/--gs-line/--gs-text-dim).

    Параметры (@include):
      $series  list<array{label:string, value:int}>  — точки слева направо
      $accent  ?string  CSS-цвет линии (default var(--gs-accent))
      $height  ?int      высота viewBox (default 120)
      $unit    ?string   подпись к значению в тултипе
      $fill    ?bool      заливка площади под линией (default true)
--}}
@php
    $series = $series ?? [];
    $accent = $accent ?? 'var(--gs-accent, #a855f7)';
    $unit   = $unit ?? '';
    $fill   = $fill ?? true;
    $W = 320; $H = (int) ($height ?? 120);
    $padL = 8; $padR = 8; $padT = 14; $padB = 16;
    $uid  = 'ln' . substr(md5(uniqid('', true)), 0, 6);

    $n = count($series);
    $vals = array_map(fn ($p) => max(0, (int) ($p['value'] ?? 0)), $series);
    $max = max(1, $vals ? max($vals) : 1);
    $plotW = $W - $padL - $padR;
    $plotH = $H - $padT - $padB;
    $baseY = $H - $padB;

    $pts = [];
    foreach ($series as $i => $p) {
        $x = $n > 1 ? $padL + $i * ($plotW / ($n - 1)) : $padL + $plotW / 2;
        $v = max(0, (int) ($p['value'] ?? 0));
        $y = $baseY - ($v / $max) * $plotH;
        $pts[] = ['x' => round($x, 2), 'y' => round($y, 2), 'label' => (string) ($p['label'] ?? ''), 'value' => $v];
    }
    $line = implode(' ', array_map(fn ($p) => $p['x'] . ',' . $p['y'], $pts));
    $area = $pts
        ? ('M ' . $pts[0]['x'] . ',' . $baseY . ' L ' . $line . ' L ' . end($pts)['x'] . ',' . $baseY . ' Z')
        : '';
    $band = $n > 1 ? $plotW / ($n - 1) : $plotW;
@endphp

@if ($n === 0)
    <div class="gs-empty" style="height: {{ $H }}px">Пока нет данных</div>
@else
    <div class="gs-chart" x-data="{ hi: null, pts: @js($pts), W: {{ $W }}, H: {{ $H }}, unit: @js($unit) }">
        <svg viewBox="0 0 {{ $W }} {{ $H }}" class="gs-svg" @mouseleave="hi = null">
            <defs>
                <linearGradient id="{{ $uid }}" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%"   stop-color="{{ $accent }}" stop-opacity="0.30"/>
                    <stop offset="100%" stop-color="{{ $accent }}" stop-opacity="0"/>
                </linearGradient>
            </defs>

            {{-- базовые горизонтали --}}
            @foreach ([0.0, 0.5, 1.0] as $g)
                @php $gy = $padT + $g * $plotH; @endphp
                <line x1="{{ $padL }}" y1="{{ round($gy, 1) }}" x2="{{ $W - $padR }}" y2="{{ round($gy, 1) }}"
                      stroke="var(--gs-line, rgba(125,134,148,.18))" stroke-width="1" vector-effect="non-scaling-stroke"/>
            @endforeach

            @if ($fill)
                <path d="{{ $area }}" fill="url(#{{ $uid }})"/>
            @endif
            <polyline points="{{ $line }}" fill="none" stroke="{{ $accent }}" stroke-width="2"
                      stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>

            {{-- вертикальный курсор + активная точка --}}
            <template x-if="hi !== null">
                <g>
                    <line :x1="pts[hi].x" :x2="pts[hi].x" y1="{{ $padT }}" y2="{{ $baseY }}"
                          stroke="{{ $accent }}" stroke-width="1" stroke-dasharray="2 3"
                          opacity="0.7" vector-effect="non-scaling-stroke"/>
                    <circle :cx="pts[hi].x" :cy="pts[hi].y" r="3.2" fill="{{ $accent }}"
                            stroke="var(--gs-bg, #14171c)" stroke-width="1.5"/>
                </g>
            </template>

            {{-- невидимые зоны наведения --}}
            @foreach ($pts as $i => $p)
                <rect x="{{ round($p['x'] - $band / 2, 2) }}" y="0" width="{{ round($band, 2) }}" height="{{ $H }}"
                      fill="transparent" @mouseenter="hi = {{ $i }}"/>
            @endforeach
        </svg>

        {{-- тултип --}}
        <div class="gs-tip" x-show="hi !== null" x-cloak
             :style="`left:${(pts[hi]?.x ?? 0) / W * 100}%; top:${(pts[hi]?.y ?? 0) / H * 100}%`">
            <span class="gs-tip__label" x-text="pts[hi]?.label"></span>
            <span class="gs-tip__val" x-text="(pts[hi]?.value ?? 0).toLocaleString('ru-RU') + (unit ? ' ' + unit : '')"></span>
        </div>

        {{-- разреженные подписи оси X --}}
        <div class="gs-axis">
            <span>{{ $pts[0]['label'] }}</span>
            @if ($n > 2)<span>{{ $pts[intdiv($n, 2)]['label'] }}</span>@endif
            <span>{{ end($pts)['label'] }}</span>
        </div>
    </div>
@endif
