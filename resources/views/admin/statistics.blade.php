<x-admin-layout>
<x-slot name="title">Статистика | RuGear Admin</x-slot>

@include('admin.partials.hud')

<style>
    .core { position: relative; background: var(--bg); border: 1px solid var(--line); }
    .readout-cell { background: var(--bg); transition: background .12s ease; }
    .readout-cell:hover { background: var(--bg-2); }
    .bar-col { position: relative; }
    .bar-col:hover .bar-fill { filter: brightness(1.2); }
    .bar-col:hover .bar-cap { opacity: 1; }
    .lead-row { transition: background .12s ease, box-shadow .12s ease; }
    .lead-row:hover { background: var(--bg-2); box-shadow: inset -3px 0 0 var(--accent); }
    .lead-row + .lead-row { border-top: 1px solid var(--line); }
</style>

@php
    $money = fn ($v) => number_format((float) $v, 0, '.', ' ') . ' ₽';

    $ic = [
        'rev'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M4 13l4-4 4 3 7-7"/>',
        'aov'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14v18l-2-1.5L15 21l-2-1.5L11 21l-2-1.5L7 21l-2-1.5V3z"/><path stroke-linecap="round" d="M8 8h8M8 12h8"/>',
        'box'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'usr'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 10a3 3 0 100-6 3 3 0 000 6zm11 10v-2a4 4 0 00-3-3.87M16 4.13A4 4 0 0118 8"/>',
        'chk'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
        'x'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>',
    ];
    $kpis = [
        ['ic' => 'rev', 'span' => 'sm:col-span-2', 'label' => 'Выручка · завершённые', 'value' => $money($totalRevenue), 'sub' => 'в работе: ' . $money($pipelineRevenue), 'tone' => 'var(--accent)'],
        ['ic' => 'aov', 'span' => '', 'label' => 'Средний чек',     'value' => $money($aov),                  'sub' => 'по активным',     'tone' => 'var(--text)'],
        ['ic' => 'box', 'span' => '', 'label' => 'Товаров продано', 'value' => number_format($itemsSold),     'sub' => 'в завершённых',   'tone' => 'var(--text)'],
        ['ic' => 'usr', 'span' => '', 'label' => 'Активных клиентов','value' => number_format($activeCustomers),'sub' => 'с покупкой',     'tone' => 'var(--text)'],
        ['ic' => 'chk', 'span' => '', 'label' => 'Завершаемость',   'value' => $completionRate . '%',         'sub' => $completed . '/' . $totalOrders, 'tone' => '#22c55e'],
        ['ic' => 'x',   'span' => '', 'label' => 'Отмены',          'value' => $cancelRate . '%',             'sub' => 'от всех',         'tone' => '#ef4444'],
    ];
@endphp

<div class="hud space-y-5">

    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold t-dim hover:t-acc transition-colors group">
        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        НАЗАД В ПРОФИЛЬ
    </a>

    {{-- ══ Command header ══ --}}
    <div class="core hud-corner hud-grid-bg flex flex-col lg:flex-row lg:items-stretch">
        <div class="flex-1 p-6">
            <p class="hud-mono text-[10px] tracking-[0.3em] t-dim2">RUGEAR // СТАТИСТИКА</p>
            <h1 class="text-3xl font-black uppercase tracking-tight t-text mt-2">Статистика заказов</h1>
            <p class="text-sm t-dim mt-1">Сводка продаж, спроса и аудитории в реальном времени</p>

            {{-- ── Экспорт в Excel (CSV) за выбранный период ── --}}
            <form method="GET" action="{{ route('admin.statistics.export') }}"
                  class="flex flex-wrap items-end gap-3 mt-5">
                <label class="flex flex-col gap-1">
                    <span class="hud-colhead">Период с</span>
                    <input type="date" name="from" max="{{ now()->format('Y-m-d') }}"
                           class="bg-gray-950 border border-gray-800 rounded-lg px-3 py-2 text-xs t-text focus:border-orange-500/50 focus:outline-none">
                </label>
                <label class="flex flex-col gap-1">
                    <span class="hud-colhead">по</span>
                    <input type="date" name="to" max="{{ now()->format('Y-m-d') }}"
                           class="bg-gray-950 border border-gray-800 rounded-lg px-3 py-2 text-xs t-text focus:border-orange-500/50 focus:outline-none">
                </label>
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-400 text-black text-xs font-black uppercase tracking-wider rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                    </svg>
                    Экспорт в Excel
                </button>
                <span class="text-[10px] t-dim2 hud-mono pb-2">пусто = за всё время</span>
            </form>
        </div>
        <div class="flex border-t lg:border-t-0 lg:border-l" style="border-color: var(--line)">
            <div class="flex flex-col justify-center px-6 py-4 lg:py-0 border-r" style="border-color: var(--line); min-width: 8rem">
                <span class="hud-colhead">Заказов всего</span>
                <span class="text-3xl font-black hud-tnum t-text mt-1">{{ number_format($totalOrders) }}</span>
            </div>
            <div class="flex flex-col justify-center px-6 py-4 lg:py-0" style="min-width: 9rem">
                <span class="hud-colhead">Выручка 14д</span>
                <span class="text-3xl font-black hud-tnum t-acc mt-1">{{ $money($periodRevenue) }}</span>
            </div>
        </div>
    </div>

    {{-- ══ Primary readouts (one sblocked instrument panel, asymmetric cells) ══ --}}
    <div class="core hud-corner">
        <div class="hud-head"><span class="hud-head__bar"></span><span class="hud-head__title">Ключевые показатели</span><span class="hud-head__code hud-mono">01 / 03</span></div>
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-7 gap-px" style="background: var(--line)">
            @foreach($kpis as $k)
            <div class="readout-cell p-5 {{ $k['ic'] === 'rev' ? 'xl:col-span-2' : '' }}">
                <svg class="w-5 h-5 mb-3" fill="none" stroke="{{ $k['tone'] }}" stroke-width="1.8" viewBox="0 0 24 24">{!! $ic[$k['ic']] !!}</svg>
                <p class="text-2xl font-black hud-tnum leading-none" style="color: {{ $k['tone'] }}">{{ $k['value'] }}</p>
                <p class="hud-colhead mt-2.5 leading-tight">{{ $k['label'] }}</p>
                <p class="text-[10px] t-dim2 mt-1 hud-mono truncate">{{ $k['sub'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══ Asymmetric: revenue flux (wide) + status matrix (narrow) ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1.75fr_1fr] gap-5">

        {{-- Revenue flux --}}
        <div class="core hud-corner">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Выручка · 14 дней</span>
                @if($bestDay && $bestDay['revenue'] > 0)
                    <span class="hud-head__code hud-mono">ПИК {{ $bestDay['label'] }} · {{ $money($bestDay['revenue']) }}</span>
                @endif
            </div>
            <div class="p-6">
                @if($periodRevenue > 0)
                    <div class="flex items-end justify-between mb-4">
                        <div>
                            <p class="text-2xl font-black hud-tnum t-text">{{ $money($periodRevenue) }}</p>
                            <p class="hud-colhead mt-1">{{ $periodOrders }} заказов за период</p>
                        </div>
                        <p class="hud-mono text-[10px] t-dim2">MAX {{ $money($maxRevenue) }}</p>
                    </div>
                    <div class="relative h-52 flex items-end gap-1.5">
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                            @for($g = 0; $g < 4; $g++)<div class="w-full" style="border-top: 1px solid var(--grid)"></div>@endfor
                        </div>
                        @foreach($series as $d)
                            @php $pct = $maxRevenue > 0 ? $d['revenue'] / $maxRevenue * 100 : 0; if ($d['revenue'] > 0) $pct = max($pct, 4); @endphp
                            <div class="bar-col flex-1 h-full flex items-end" title="{{ $d['label'] }} — {{ $d['orders'] }} зак. · {{ $money($d['revenue']) }}">
                                <div class="absolute inset-x-0 bottom-0 top-0" style="background: var(--track)"></div>
                                <div class="relative w-full bar-fill" style="height: {{ $pct }}%; min-height: {{ $d['revenue'] > 0 ? '4px' : '0' }}; background: var(--accent); opacity: {{ $d['weekend'] ? '.5' : '1' }}">
                                    <span class="bar-cap absolute -top-px inset-x-0 h-0.5 opacity-0 transition-opacity" style="background: var(--text)"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-1.5 mt-2">
                        @foreach($series as $d)<span class="flex-1 text-center text-[9px] t-dim2 hud-mono">{{ $d['label'] }}</span>@endforeach
                    </div>
                    <div class="flex items-center gap-5 mt-4 pt-4 border-t" style="border-color: var(--line)">
                        <span class="flex items-center gap-2 text-[10px] t-dim"><span class="hud-state" style="background: var(--accent)"></span>Будни</span>
                        <span class="flex items-center gap-2 text-[10px] t-dim"><span class="hud-state" style="background: var(--accent); opacity: .5"></span>Выходные</span>
                    </div>
                @else
                    <div class="h-52 flex flex-col items-center justify-center t-dim2">
                        <svg class="w-9 h-9 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 15l3-3 3 2 4-5"/></svg>
                        <p class="text-xs uppercase tracking-widest">Нет данных за период</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Status matrix donut --}}
        <div class="core">
            <div class="hud-head"><span class="hud-head__bar"></span><span class="hud-head__title">Статусы заказов</span><span class="hud-head__code hud-mono">02 / 03</span></div>
            <div class="p-6">
                @if($totalOrders > 0)
                    @php $C = 2 * M_PI * 54; $acc = 0; @endphp
                    <div class="flex items-center justify-center mb-6">
                        <div class="relative w-40 h-40">
                            <svg viewBox="0 0 140 140" class="w-full h-full -rotate-90">
                                <circle cx="70" cy="70" r="54" fill="none" stroke="var(--track)" stroke-width="14"/>
                                @foreach($statusData as $seg)
                                    @if($seg['count'] > 0)
                                        @php $len = $seg['count'] / $totalOrders * $C; @endphp
                                        <circle cx="70" cy="70" r="54" fill="none" stroke="{{ $seg['color'] }}" stroke-width="14" stroke-dasharray="{{ $len }} {{ $C - $len }}" stroke-dashoffset="{{ -$acc }}"/>
                                        @php $acc += $len; @endphp
                                    @endif
                                @endforeach
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl font-black hud-tnum t-text leading-none">{{ $completionRate }}%</span>
                                <span class="hud-colhead mt-1">завершено</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-px" style="background: var(--line); border: 1px solid var(--line)">
                        @foreach($statusData as $seg)
                        <div class="flex items-center gap-3 px-3 py-2" style="background: var(--bg)">
                            <span class="hud-state" style="background: {{ $seg['color'] }}"></span>
                            <span class="text-xs t-dim flex-1">{{ $seg['label'] }}</span>
                            <span class="text-xs font-bold t-text hud-tnum">{{ $seg['count'] }}</span>
                            <span class="text-[10px] t-dim2 hud-tnum w-10 text-right hud-mono">{{ $seg['pct'] }}%</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-40 flex flex-col items-center justify-center t-dim2">
                        <svg class="w-9 h-9 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 3v9l6 3"/></svg>
                        <p class="text-xs uppercase tracking-widest">Заказов нет</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ Leaderboards ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Top units --}}
        <div class="core">
            <div class="hud-head"><span class="hud-head__bar"></span><span class="hud-head__title">Хиты продаж</span><span class="hud-head__code hud-mono">ТОП-5</span></div>
            <div>
                @forelse($topProducts as $i => $tp)
                <div class="lead-row flex items-center gap-4 px-5 py-3">
                    <span class="w-7 h-7 flex items-center justify-center text-xs font-black hud-tnum flex-shrink-0" style="{{ $i === 0 ? 'background:var(--accent);color:#0a0a0a' : 'border:1px solid var(--line);color:var(--dim)' }}">{{ $i + 1 }}</span>
                    <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center overflow-hidden" style="background: var(--inset); border: 1px solid var(--line)">
                        @if($tp->product && $tp->product->image)
                            <img src="{{ asset($tp->product->image) }}" class="w-full h-full object-contain p-1" onerror="this.style.display='none'">
                        @else
                            <svg class="w-4 h-4 t-dim2" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="1"/><path d="M9 9h6v6H9z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold t-text truncate">{{ $tp->product->name ?? 'Товар #' . $tp->product_id }}</p>
                        <div class="hud-meter mt-1.5"><span style="width: {{ max(round($tp->qty / $topProductMax * 100), 4) }}%; background: var(--accent)"></span></div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black t-text hud-tnum">{{ $tp->qty }} <span class="t-dim2 text-[10px]">шт</span></p>
                        <p class="text-[10px] t-dim2 hud-mono">{{ $money($tp->revenue) }}</p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-xs uppercase tracking-widest t-dim2">Нет продаж</div>
                @endforelse
            </div>
        </div>

        {{-- Top operators --}}
        <div class="core">
            <div class="hud-head"><span class="hud-head__bar"></span><span class="hud-head__title">Топ покупателей</span><span class="hud-head__code hud-mono">ТОП-5</span></div>
            <div>
                @php $medal = ['var(--accent)', '#9ca3af', '#b45309']; @endphp
                @forelse($topCustomers as $i => $tc)
                <div class="lead-row flex items-center gap-4 px-5 py-3">
                    <span class="w-7 h-7 flex items-center justify-center text-xs font-black hud-tnum flex-shrink-0" style="{{ $i < 3 ? 'background:'.$medal[$i].';color:#0a0a0a' : 'border:1px solid var(--line);color:var(--dim)' }}">{{ $i + 1 }}</span>
                    <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center overflow-hidden font-black text-xs text-black" style="background: var(--accent)">
                        @if($tc->user && $tc->user->avatar)
                            <img src="{{ Storage::url($tc->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr($tc->user->name ?? '?', 0, 2)) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold t-text truncate">{{ $tc->user->name ?? 'Пользователь #' . $tc->user_id }}</p>
                        <div class="hud-meter mt-1.5"><span style="width: {{ max(round($tc->total / $topCustomerMax * 100), 4) }}%; background: #f59e0b"></span></div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black t-text hud-tnum">{{ $money($tc->total) }}</p>
                        <p class="text-[10px] t-dim2 hud-mono">{{ $tc->orders }} зак.</p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-xs uppercase tracking-widest t-dim2">Нет покупателей</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ Channels (one core split in two) ══ --}}
    <div class="core">
        <div class="hud-head"><span class="hud-head__bar"></span><span class="hud-head__title">Каналы оплаты и доставки</span><span class="hud-head__code hud-mono">CORE // 03</span></div>
        <div class="grid grid-cols-1 md:grid-cols-2">
            @php
                $channels = [
                    ['title' => 'Оплата',   'data' => $payments],
                    ['title' => 'Доставка', 'data' => $deliveries],
                ];
            @endphp
            @foreach($channels as $idx => $ch)
            <div class="p-6 {{ $idx === 0 ? 'md:border-r' : 'border-t md:border-t-0' }}" style="border-color: var(--line)">
                <p class="hud-colhead mb-4">{{ $ch['title'] }}</p>
                @forelse($ch['data'] as $row)
                <div class="flex items-center gap-4 py-2">
                    <span class="text-xs t-dim w-24 flex-shrink-0 truncate">{{ $row['label'] }}</span>
                    <div class="flex-1 hud-meter"><span style="width: {{ max(round($row['count'] / $nonCancelled * 100), 3) }}%; background: var(--accent)"></span></div>
                    <span class="text-xs font-bold t-text hud-tnum w-16 text-right">{{ $row['count'] }} <span class="t-dim2 font-normal hud-mono">{{ round($row['count'] / $nonCancelled * 100) }}%</span></span>
                </div>
                @empty
                <p class="text-xs uppercase tracking-widest t-dim2 py-4">Нет данных</p>
                @endforelse
            </div>
            @endforeach
        </div>
    </div>

</div>
</x-admin-layout>
