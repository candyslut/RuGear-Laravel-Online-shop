{{--
    Круговая (кольцевая) диаграмма — inline-SVG + Alpine. Монохромные сегменты
    в цвете игры со ступенчатой прозрачностью; центр показывает итог либо данные
    наведённого сегмента, снизу — компактная легенда.

    Параметры:
      $series      list<array{label:string, value:int}>
      $accent      ?string  CSS-цвет (default var(--gs-accent))
      $size        ?int     диаметр SVG, px (default 150)
      $centerLabel ?string  подпись под итоговым числом (default 'всего')
--}}
@php
    $series = $series ?? [];
    $accent = $accent ?? 'var(--gs-accent, #a855f7)';
    $size   = (int) ($size ?? 150);
    $centerLabel = $centerLabel ?? 'всего';

    $r = 50; $cx = 60; $cy = 60;
    $circ = 2 * M_PI * $r;
    $total = 0;
    foreach ($series as $p) { $total += max(0, (int) ($p['value'] ?? 0)); }

    // Геометрия только для ненулевых сегментов; прозрачность ступенькой.
    $slices = [];
    $legend = [];
    $cum = 0;
    $nonZero = array_values(array_filter($series, fn ($p) => (int) ($p['value'] ?? 0) > 0));
    $cnt = max(1, count($nonZero));
    $i = 0;
    foreach ($series as $p) {
        $v = max(0, (int) ($p['value'] ?? 0));
        $op = $v > 0 ? round(1 - ($i / $cnt) * 0.62, 2) : 0.16;
        $legend[] = ['label' => (string) ($p['label'] ?? ''), 'value' => $v, 'op' => $v > 0 ? $op : 0.2];
        if ($v <= 0 || $total <= 0) { continue; }
        $frac = $v / $total;
        $slices[] = [
            'label'   => (string) ($p['label'] ?? ''),
            'value'   => $v,
            'pct'     => (int) round($frac * 100),
            'dash'    => round($frac * $circ, 2),
            'rest'    => round($circ - $frac * $circ, 2),
            'startDeg'=> round(-90 + $cum * 360, 2),
            'op'      => $op,
        ];
        $cum += $frac;
        $i++;
    }
@endphp

<div class="gs-donut" x-data="{ hi: null, slices: @js($slices), total: {{ $total }} }">
    <div class="gs-donut__ring" style="width: {{ $size }}px; height: {{ $size }}px">
        <svg viewBox="0 0 120 120" class="block w-full h-full">
            {{-- фоновое кольцо --}}
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                    stroke="var(--gs-line, rgba(125,134,148,.20))" stroke-width="18"/>
            @foreach ($slices as $idx => $s)
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                        stroke="{{ $accent }}" stroke-width="18"
                        stroke-dasharray="{{ $s['dash'] }} {{ $s['rest'] }}"
                        :opacity="hi === null ? {{ $s['op'] }} : (hi === {{ $idx }} ? 1 : {{ round($s['op'] * 0.35, 2) }})"
                        style="transform: rotate({{ $s['startDeg'] }}deg); transform-origin: 60px 60px; transition: opacity .12s"
                        @mouseenter="hi = {{ $idx }}" @mouseleave="hi = null"/>
            @endforeach
        </svg>
        <div class="gs-ring__c">
            <template x-if="hi === null">
                <div>
                    <span class="gs-ring__v">{{ \App\Support\GameStats::spaced($total) }}</span>
                    <span class="gs-ring__l">{{ $centerLabel }}</span>
                </div>
            </template>
            <template x-if="hi !== null">
                <div>
                    <span class="gs-ring__v" x-text="slices[hi].value.toLocaleString('ru-RU')"></span>
                    <span class="gs-ring__l" x-text="slices[hi].label + ' · ' + slices[hi].pct + '%'"></span>
                </div>
            </template>
        </div>
    </div>

    @if ($total > 0)
        <div class="gs-legend">
            @foreach ($legend as $l)
                <span class="gs-legend__i {{ $l['value'] <= 0 ? 'gs-legend__i--off' : '' }}">
                    <span class="gs-legend__dot" style="background: {{ $accent }}; opacity: {{ $l['op'] }}"></span>
                    {{ $l['label'] }}<span class="gs-legend__v">{{ $l['value'] }}</span>
                </span>
            @endforeach
        </div>
    @else
        <p class="gs-empty" style="padding:.5rem 0">Пока нет забегов</p>
    @endif
</div>
