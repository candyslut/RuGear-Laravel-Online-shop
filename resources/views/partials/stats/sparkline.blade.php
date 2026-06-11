{{--
    Компактная спарклайн-линия без осей — для виджета на дашборде.

    Параметры:
      $series  list<array{value:int}>|list<int>
      $accent  ?string
      $height  ?int (default 36)
--}}
@php
    $series = $series ?? [];
    $accent = $accent ?? 'var(--gs-accent, #a855f7)';
    $W = 120; $H = (int) ($height ?? 36);
    $pad = 3;
    $vals = array_map(fn ($p) => is_array($p) ? max(0, (int) ($p['value'] ?? 0)) : max(0, (int) $p), $series);
    $n = count($vals);
    $max = max(1, $vals ? max($vals) : 1);
    $plotW = $W - $pad * 2; $plotH = $H - $pad * 2; $baseY = $H - $pad;
    $pts = [];
    foreach ($vals as $i => $v) {
        $x = $n > 1 ? $pad + $i * ($plotW / ($n - 1)) : $W / 2;
        $y = $baseY - ($v / $max) * $plotH;
        $pts[] = round($x, 2) . ',' . round($y, 2);
    }
    $line = implode(' ', $pts);
@endphp
@if ($n > 0)
    <svg viewBox="0 0 {{ $W }} {{ $H }}" class="gs-spark" preserveAspectRatio="none">
        <polyline points="{{ $line }}" fill="none" stroke="{{ $accent }}" stroke-width="2"
                  stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
    </svg>
@endif
