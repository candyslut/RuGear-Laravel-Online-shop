<x-admin-layout>
<x-slot name="title">Заказы | RuGear Admin</x-slot>

@include('admin.partials.hud')

<style>
    .led-row { position: relative; transition: background .12s ease, box-shadow .12s ease; }
    .led-row:hover { background: var(--bg-2); box-shadow: inset -3px 0 0 var(--accent); }
    .led-row + .led-row { border-top: 1px solid var(--line); }
    .led-rail { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }
    .led-thumb { background: var(--inset); border: 1px solid var(--line); }

    .custom-pagination p { color: var(--dim) !important; font-size: .8rem !important; }
    .custom-pagination nav a, .custom-pagination nav span[aria-disabled="true"] span, .custom-pagination nav span[aria-current="page"] span {
        background-color: var(--bg) !important; border: 1px solid var(--line) !important; color: var(--dim) !important; border-radius: 0; margin: 0 2px;
    }
    .custom-pagination nav span[aria-current="page"] span { background-color: var(--accent) !important; border-color: var(--accent) !important; color: #0a0a0a !important; font-weight: 900 !important; }
    .custom-pagination nav a:hover { border-color: var(--accent) !important; color: var(--accent) !important; }
</style>

@php
    $statusMeta = [
        'pending'    => ['label' => 'Ожидает',     'c' => '#f59e0b'],
        'processing' => ['label' => 'В обработке', 'c' => '#3b82f6'],
        'completed'  => ['label' => 'Завершён',    'c' => '#22c55e'],
        'cancelled'  => ['label' => 'Отменён',     'c' => '#ef4444'],
    ];
    $railOf = fn ($s) => $statusMeta[$s]['c'] ?? '#6b7280';
@endphp

<div class="hud space-y-5">

    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold t-dim hover:t-acc transition-colors group">
        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        НАЗАД В ПРОФИЛЬ
    </a>

    {{-- ══ Command header ══ --}}
    <div class="hud-panel hud-corner hud-grid-bg">
        <div class="flex flex-col lg:flex-row lg:items-stretch">
            <div class="flex-1 p-6">
                <p class="hud-mono text-[10px] tracking-[0.3em] t-dim2">RUGEAR // ЗАКАЗЫ</p>
                <h1 class="text-3xl font-black uppercase tracking-tight t-text mt-2">Заказы</h1>
                <p class="text-sm t-dim mt-1">Всего заказов: {{ $orders->total() }}</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:flex border-t lg:border-t-0 lg:border-l" style="border-color: var(--line)">
                @foreach($statusMeta as $key => $m)
                    <div class="flex flex-col justify-center px-5 py-4 lg:py-0 border-r last:border-r-0" style="border-color: var(--line); min-width: 6.5rem">
                        <div class="flex items-center gap-2">
                            <span class="hud-state" style="background: {{ $m['c'] }}"></span>
                            <span class="hud-colhead">{{ $m['label'] }}</span>
                        </div>
                        <span class="text-2xl font-black hud-tnum t-text mt-1.5">{{ $counts[$key] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="hud-panel flex items-center gap-3 px-5 py-3.5" style="border-color: #22c55e">
        <span class="hud-state" style="background: #22c55e"></span>
        <span class="text-sm t-text">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ══ Control bar ══ --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" id="filter-form" class="space-y-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 t-dim2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" oninput="debounceSubmit()"
                       placeholder="ПОИСК: ID / ИМЯ / ТЕЛЕФОН / EMAIL"
                       class="hud-input hud-mono w-full pl-10 pr-9 py-2.5 text-xs tracking-wider">
                @if($search)
                <a href="{{ route('admin.orders.index', ['sort' => $sort, 'status' => $status]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 t-dim2 hover:t-text transition text-lg leading-none">×</a>
                @endif
            </div>

            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
            <input type="hidden" name="status" id="status-input" value="{{ $status }}">
            <div class="hud-seg flex-shrink-0">
                @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'amount_desc' => 'Дорогие', 'amount_asc' => 'Дешёвые'] as $key => $label)
                <button type="button" onclick="setSort('{{ $key }}')" class="hud-seg__btn {{ $sort === $key ? 'hud-seg__btn--on' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        {{-- status filters --}}
        <div class="flex items-center flex-wrap gap-2">
            @php
                $tabMeta = ['all' => ['label' => 'Все', 'c' => 'var(--accent)']] + array_map(fn ($m) => ['label' => $m['label'], 'c' => $m['c']], $statusMeta);
            @endphp
            @foreach($tabMeta as $key => $tab)
            @php $on = $status === $key; @endphp
            <button type="button" onclick="setStatus('{{ $key }}')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-[11px] font-bold uppercase tracking-wider transition"
                    style="border: 1px solid {{ $on ? $tab['c'] : 'var(--line)' }}; color: {{ $on ? $tab['c'] : 'var(--dim)' }}; background: {{ $on ? 'color-mix(in srgb, '.$tab['c'].' 12%, transparent)' : 'transparent' }}">
                @if($key !== 'all')<span class="hud-state" style="background: {{ $tab['c'] }}"></span>@endif
                {{ $tab['label'] }}
                <span class="hud-mono t-dim2">{{ $counts[$key] }}</span>
            </button>
            @endforeach
        </div>
    </form>

    {{-- ══ Ledger ══ --}}
    @if($orders->isEmpty())
        <div class="hud-panel py-20 flex flex-col items-center justify-center">
            <svg class="w-10 h-10 t-dim2 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="text-xs uppercase tracking-widest t-dim">Заказы не найдены</p>
        </div>
    @else
        <div class="hud-panel">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Список заказов</span>
                <span class="hud-head__code hud-mono">{{ $orders->firstItem() }}–{{ $orders->lastItem() }} / {{ $orders->total() }}</span>
            </div>

            {{-- column header --}}
            <div class="hidden md:grid gap-4 px-5 py-2.5 border-b items-center hud-colhead" style="border-color: var(--line); grid-template-columns: 7rem 1fr 6.5rem 8rem 9rem 5.5rem 5.5rem">
                <span>ID</span>
                <span>Покупатель</span>
                <span>Товары</span>
                <span class="text-right">Сумма</span>
                <span>Статус</span>
                <span class="text-center">Дата</span>
                <span class="text-right">—</span>
            </div>

            @foreach($orders as $order)
            @php $rail = $railOf($order->status); $m = $statusMeta[$order->status] ?? ['label' => $order->status_label, 'c' => '#6b7280']; @endphp
            <div class="led-row flex flex-col gap-3 px-5 py-4 md:grid md:gap-4 md:py-3.5 md:items-center" style="grid-template-columns: 7rem 1fr 6.5rem 8rem 9rem 5.5rem 5.5rem">
                <span class="led-rail" style="background: {{ $rail }}"></span>

                {{-- code --}}
                <span class="hud-mono text-xs t-dim">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>

                {{-- operator --}}
                <div class="min-w-0">
                    <p class="text-sm font-bold t-text truncate">{{ $order->full_name ?: $order->user->name }}</p>
                    <p class="text-xs t-dim2 truncate hud-mono">{{ $order->user->email }}</p>
                </div>

                {{-- secondary: на мобильном — сетка 2×2 с подписями; на desktop — отдельные колонки --}}
                <div class="grid grid-cols-2 gap-3 border-t border-b py-3 md:contents" style="border-color: var(--line)">

                    {{-- items --}}
                    <div>
                        <span class="md:hidden hud-colhead block mb-1.5">Товары</span>
                        <div class="flex items-center gap-1.5">
                            @foreach($order->items->take(3) as $item)
                            <div class="led-thumb w-8 h-8 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                <img src="{{ asset($item->product->image ?? '') }}" class="w-full h-full object-contain p-0.5" onerror="this.style.display='none'">
                            </div>
                            @endforeach
                            @if($order->items->count() > 3)
                            <span class="text-[10px] t-dim2 hud-mono">+{{ $order->items->count() - 3 }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- amount --}}
                    <div class="md:text-right">
                        <span class="md:hidden hud-colhead block mb-1.5">Сумма</span>
                        <p class="text-sm font-black t-text hud-tnum">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</p>
                        <p class="text-[10px] t-dim2 hud-mono">{{ $order->items->count() }} ПОЗ</p>
                    </div>

                    {{-- status --}}
                    <div>
                        <span class="md:hidden hud-colhead block mb-1.5">Статус</span>
                        <div class="flex items-center gap-2">
                            <span class="hud-state" style="background: {{ $m['c'] }}"></span>
                            <span class="text-[11px] font-bold uppercase tracking-wider" style="color: {{ $m['c'] }}">{{ $order->status_label }}</span>
                        </div>
                    </div>

                    {{-- date --}}
                    <div class="md:text-center">
                        <span class="md:hidden hud-colhead block mb-1.5">Дата</span>
                        <p class="text-xs t-dim hud-mono">{{ $order->created_at->format('d.m.y') }}</p>
                        <p class="text-[10px] t-dim2 hud-mono">{{ $order->created_at->format('H:i') }}</p>
                    </div>
                </div>

                {{-- action --}}
                <div class="flex md:justify-end">
                    <a href="{{ route('admin.orders.show', $order) }}" class="hud-btn w-full md:w-auto">Открыть</a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="custom-pagination">{{ $orders->onEachSide(1)->links() }}</div>
    @endif
</div>

<script>
let _searchTimer;
function debounceSubmit() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}
function setSort(val) {
    document.getElementById('sort-input').value = val;
    document.getElementById('filter-form').submit();
}
function setStatus(val) {
    document.getElementById('status-input').value = val;
    document.getElementById('filter-form').submit();
}
</script>

</x-admin-layout>
