<x-admin-layout>
<x-slot name="title">Статистика | RuGear Admin</x-slot>

<style>
    /* Theme-aware chart internals (defaults = dark, overridden for light) */
    .stats { --track: #1b1f27; --grid: rgba(148,163,184,.10); --donut-track: #1b1f27; }
    [data-theme="light"] .stats { --track: #eceef2; --grid: rgba(100,116,139,.14); --donut-track: #eceef2; }

    .stat-card { transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
    .stat-card:hover { transform: translateY(-3px); border-color: rgba(249,115,22,.35); box-shadow: 0 14px 30px rgba(0,0,0,.18); }

    .bar-fill { transition: filter .15s ease; }
    .bar-col:hover .bar-fill { filter: brightness(1.18); }

    @keyframes stat-rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .stat-rise { animation: stat-rise .45s cubic-bezier(.34,1.56,.64,1) both; }
</style>

@php
    $money = fn ($v) => number_format((float) $v, 0, '.', ' ') . ' ₽';
@endphp

<div class="stats space-y-8">

    <div class="mb-1">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            Вернуться в личный кабинет
        </a>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-wider text-white flex items-center gap-3">
                <span class="w-2.5 h-8 bg-orange-500 rounded-full shadow-[0_0_15px_rgba(249,115,22,.5)]"></span>
                Статистика заказов
            </h1>
            <p class="text-sm text-gray-500 mt-1">Аналитика продаж, спроса и аудитории</p>
        </div>
        <div class="flex items-center gap-2 bg-[#161920] border border-gray-800 px-4 py-2.5 rounded-xl">
            <i class="fa-solid fa-bolt text-orange-500"></i>
            <span class="text-[10px] text-gray-500 uppercase tracking-widest">Всего заказов</span>
            <span class="text-lg font-black text-white tabular-nums">{{ number_format($totalOrders) }}</span>
        </div>
    </div>

    {{-- ═══════════ KPI CARDS ═══════════ --}}
    @php
        $kpis = [
            ['icon' => 'fa-money-bill-trend-up', 'tone' => 'orange', 'label' => 'Выручка (завершённые)', 'value' => $money($totalRevenue),  'sub' => 'В ожидании: ' . $money($pipelineRevenue)],
            ['icon' => 'fa-receipt',             'tone' => 'cyan',   'label' => 'Средний чек',            'value' => $money($aov),           'sub' => 'по активным заказам'],
            ['icon' => 'fa-boxes-stacked',       'tone' => 'violet', 'label' => 'Товаров продано',        'value' => number_format($itemsSold), 'sub' => 'единиц в завершённых'],
            ['icon' => 'fa-users',               'tone' => 'blue',   'label' => 'Активных клиентов',      'value' => number_format($activeCustomers), 'sub' => 'совершили покупку'],
            ['icon' => 'fa-circle-check',        'tone' => 'green',  'label' => 'Доля завершённых',       'value' => $completionRate . '%',  'sub' => $completed . ' из ' . $totalOrders],
            ['icon' => 'fa-circle-xmark',        'tone' => 'red',    'label' => 'Доля отмен',             'value' => $cancelRate . '%',      'sub' => 'от всех заказов'],
        ];
        $tones = [
            'orange' => 'text-orange-400 bg-orange-500/10',
            'cyan'   => 'text-cyan-400 bg-cyan-500/10',
            'violet' => 'text-violet-400 bg-violet-500/10',
            'blue'   => 'text-blue-400 bg-blue-500/10',
            'green'  => 'text-emerald-400 bg-emerald-500/10',
            'red'    => 'text-red-400 bg-red-500/10',
        ];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach($kpis as $i => $k)
            <div class="stat-card stat-rise bg-[#161920] border border-gray-800 rounded-2xl p-5" style="animation-delay: {{ $i * 60 }}ms">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4 {{ $tones[$k['tone']] }}">
                    <i class="fa-solid {{ $k['icon'] }}"></i>
                </div>
                <p class="text-2xl font-black text-white tabular-nums leading-none">{{ $k['value'] }}</p>
                <p class="text-[11px] text-gray-500 font-semibold uppercase tracking-wider mt-2 leading-tight">{{ $k['label'] }}</p>
                <p class="text-[10px] text-gray-600 mt-1 truncate">{{ $k['sub'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ═══════════ MAIN GRID ═══════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ── Revenue chart (2/3) ── --}}
        <div class="xl:col-span-2 bg-[#161920] border border-gray-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-orange-500"></i> Выручка · 14 дней
                    </h2>
                    <p class="text-[11px] text-gray-500 mt-1">{{ $money($periodRevenue) }} · {{ $periodOrders }} заказов за период</p>
                </div>
                @if($bestDay && $bestDay['revenue'] > 0)
                    <div class="text-right">
                        <p class="text-[10px] text-gray-600 uppercase tracking-widest">Лучший день</p>
                        <p class="text-sm font-black text-emerald-400 tabular-nums">{{ $bestDay['label'] }} · {{ $money($bestDay['revenue']) }}</p>
                    </div>
                @endif
            </div>

            @if($periodRevenue > 0)
                <div class="relative h-52 flex items-end gap-1.5">
                    {{-- grid lines --}}
                    <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                        @for($g = 0; $g < 4; $g++)
                            <div class="w-full" style="border-top: 1px dashed var(--grid)"></div>
                        @endfor
                    </div>
                    @foreach($series as $d)
                        @php
                            $pct = $maxRevenue > 0 ? $d['revenue'] / $maxRevenue * 100 : 0;
                            if ($d['revenue'] > 0) { $pct = max($pct, 6); }
                        @endphp
                        <div class="bar-col relative flex-1 h-full flex items-end">
                            <div class="absolute inset-x-0 bottom-0 top-0 rounded-md" style="background: var(--track)"></div>
                            <div class="relative w-full rounded-md bar-fill"
                                 style="height: {{ $pct }}%; min-height: {{ $d['revenue'] > 0 ? '6px' : '0' }}; background: {{ $d['weekend'] ? '#fb923c' : '#f97316' }}"
                                 title="{{ $d['label'] }}: {{ $d['orders'] }} заказов · {{ $money($d['revenue']) }}"></div>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-1.5 mt-2">
                    @foreach($series as $d)
                        <span class="flex-1 text-center text-[9px] text-gray-600 tabular-nums">{{ $d['label'] }}</span>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-800">
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500"><span class="w-2.5 h-2.5 rounded-sm" style="background:#f97316"></span> Будни</span>
                    <span class="flex items-center gap-1.5 text-[10px] text-gray-500"><span class="w-2.5 h-2.5 rounded-sm" style="background:#fb923c"></span> Выходные</span>
                </div>
            @else
                <div class="h-52 flex flex-col items-center justify-center text-gray-600">
                    <i class="fa-solid fa-chart-column text-3xl mb-3 opacity-30"></i>
                    <p class="text-xs uppercase tracking-wider">Нет данных за период</p>
                </div>
            @endif
        </div>

        {{-- ── Status donut (1/3) ── --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6">
            <h2 class="text-sm font-black uppercase tracking-wider text-white flex items-center gap-2 mb-6">
                <i class="fa-solid fa-circle-half-stroke text-orange-500"></i> Статусы заказов
            </h2>

            @if($totalOrders > 0)
                @php $C = 2 * M_PI * 54; $acc = 0; @endphp
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-44 h-44">
                        <svg viewBox="0 0 140 140" class="w-full h-full -rotate-90">
                            <circle cx="70" cy="70" r="54" fill="none" stroke="var(--donut-track)" stroke-width="16"/>
                            @foreach($statusData as $seg)
                                @if($seg['count'] > 0)
                                    @php
                                        $len = $seg['count'] / $totalOrders * $C;
                                    @endphp
                                    <circle cx="70" cy="70" r="54" fill="none"
                                            stroke="{{ $seg['color'] }}" stroke-width="16"
                                            stroke-dasharray="{{ $len }} {{ $C - $len }}"
                                            stroke-dashoffset="{{ -$acc }}"/>
                                    @php $acc += $len; @endphp
                                @endif
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-black text-white tabular-nums leading-none">{{ $completionRate }}%</span>
                            <span class="text-[9px] text-gray-500 uppercase tracking-widest mt-1">завершено</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @foreach($statusData as $seg)
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $seg['color'] }}"></span>
                            <span class="text-xs text-gray-400 flex-1">{{ $seg['label'] }}</span>
                            <span class="text-xs font-bold text-white tabular-nums">{{ $seg['count'] }}</span>
                            <span class="text-[10px] text-gray-600 tabular-nums w-10 text-right">{{ $seg['pct'] }}%</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-44 flex flex-col items-center justify-center text-gray-600">
                    <i class="fa-solid fa-circle-half-stroke text-3xl mb-3 opacity-30"></i>
                    <p class="text-xs uppercase tracking-wider">Заказов пока нет</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════ SECONDARY GRID ═══════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- ── Top products ── --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6">
            <h2 class="text-sm font-black uppercase tracking-wider text-white flex items-center gap-2 mb-6">
                <i class="fa-solid fa-fire text-orange-500"></i> Хиты продаж
            </h2>

            @forelse($topProducts as $i => $tp)
                <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-800' : '' }}">
                    <span class="text-sm font-black tabular-nums w-5 text-center {{ $i === 0 ? 'text-orange-500' : 'text-gray-600' }}">{{ $i + 1 }}</span>
                    <div class="w-10 h-10 rounded-lg bg-gray-950 border border-gray-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($tp->product && $tp->product->image)
                            <img src="{{ asset($tp->product->image) }}" class="w-full h-full object-contain p-1" onerror="this.style.display='none'">
                        @else
                            <i class="fa-solid fa-microchip text-gray-700"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $tp->product->name ?? 'Товар #' . $tp->product_id }}</p>
                        <div class="mt-1.5 h-1.5 rounded-full overflow-hidden" style="background: var(--track)">
                            <div class="h-full rounded-full" style="width: {{ max(round($tp->qty / $topProductMax * 100), 4) }}%; background: #f97316"></div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black text-white tabular-nums">{{ $tp->qty }} шт</p>
                        <p class="text-[10px] text-gray-600 tabular-nums">{{ $money($tp->revenue) }}</p>
                    </div>
                </div>
            @empty
                <div class="h-32 flex flex-col items-center justify-center text-gray-600">
                    <i class="fa-solid fa-fire text-2xl mb-2 opacity-30"></i>
                    <p class="text-xs uppercase tracking-wider">Нет продаж</p>
                </div>
            @endforelse
        </div>

        {{-- ── Top customers (leaderboard) ── --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6">
            <h2 class="text-sm font-black uppercase tracking-wider text-white flex items-center gap-2 mb-6">
                <i class="fa-solid fa-trophy text-orange-500"></i> Топ покупателей
            </h2>

            @php
                $medal = ['#f59e0b', '#9ca3af', '#b45309']; // gold, silver, bronze
            @endphp
            @forelse($topCustomers as $i => $tc)
                <div class="flex items-center gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-800' : '' }}">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black flex-shrink-0 tabular-nums"
                          style="{{ $i < 3 ? 'color:#000;background:' . $medal[$i] : 'color:#9ca3af;background:var(--track)' }}">{{ $i + 1 }}</span>
                    <div class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center text-black font-black text-xs flex-shrink-0 overflow-hidden">
                        @if($tc->user && $tc->user->avatar)
                            <img src="{{ Storage::url($tc->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr($tc->user->name ?? '?', 0, 2)) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $tc->user->name ?? 'Пользователь #' . $tc->user_id }}</p>
                        <div class="mt-1.5 h-1.5 rounded-full overflow-hidden" style="background: var(--track)">
                            <div class="h-full rounded-full" style="width: {{ max(round($tc->total / $topCustomerMax * 100), 4) }}%; background: #f59e0b"></div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black text-white tabular-nums">{{ $money($tc->total) }}</p>
                        <p class="text-[10px] text-gray-600 tabular-nums">{{ $tc->orders }} заказов</p>
                    </div>
                </div>
            @empty
                <div class="h-32 flex flex-col items-center justify-center text-gray-600">
                    <i class="fa-solid fa-trophy text-2xl mb-2 opacity-30"></i>
                    <p class="text-xs uppercase tracking-wider">Нет покупателей</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════ BREAKDOWN: payment + delivery ═══════════ --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @php
            $breakdowns = [
                ['title' => 'Способы оплаты',   'icon' => 'fa-credit-card', 'color' => '#3b82f6', 'data' => $payments],
                ['title' => 'Способы доставки', 'icon' => 'fa-truck-fast',  'color' => '#a855f7', 'data' => $deliveries],
            ];
        @endphp
        @foreach($breakdowns as $bd)
            <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6">
                <h2 class="text-sm font-black uppercase tracking-wider text-white flex items-center gap-2 mb-6">
                    <i class="fa-solid {{ $bd['icon'] }}" style="color: {{ $bd['color'] }}"></i> {{ $bd['title'] }}
                </h2>
                @forelse($bd['data'] as $row)
                    <div class="flex items-center gap-4 py-2.5">
                        <span class="text-xs text-gray-400 w-24 flex-shrink-0">{{ $row['label'] }}</span>
                        <div class="flex-1 h-2 rounded-full overflow-hidden" style="background: var(--track)">
                            <div class="h-full rounded-full" style="width: {{ max(round($row['count'] / $nonCancelled * 100), 3) }}%; background: {{ $bd['color'] }}"></div>
                        </div>
                        <span class="text-xs font-bold text-white tabular-nums w-16 text-right">{{ $row['count'] }} <span class="text-gray-600 font-normal">({{ round($row['count'] / $nonCancelled * 100) }}%)</span></span>
                    </div>
                @empty
                    <p class="text-xs text-gray-600 uppercase tracking-wider py-6 text-center">Нет данных</p>
                @endforelse
            </div>
        @endforeach
    </div>

</div>
</x-admin-layout>
