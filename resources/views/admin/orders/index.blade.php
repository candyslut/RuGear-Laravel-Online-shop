<x-admin-layout>
<x-slot name="title">Заказы | RuGear Admin</x-slot>

<style>
    .ord-row { transition: background .15s; }
    .ord-row:hover { background: rgba(255,255,255,.02); }
    .custom-pagination p { color:#6b7280!important;font-style:italic!important;font-size:.875rem!important; }
    .custom-pagination nav a,.custom-pagination nav span[aria-disabled="true"] span,.custom-pagination nav span[aria-current="page"] span { background-color:#161920!important;border-color:#1f2937!important;color:#9ca3af!important;border-radius:.75rem;margin:0 2px; }
    .custom-pagination nav span[aria-current="page"] span { background-color:#f97316!important;border-color:#f97316!important;color:#000!important;font-weight:900!important; }
    .custom-pagination nav a:hover { background-color:#1f2937!important;color:#f97316!important;border-color:#f97316!important; }
</style>

<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        Вернуться в личный кабинет
    </a>
</div>

{{-- Header --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black uppercase tracking-wider text-white">Заказы</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $orders->total() }} найдено</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $statBadges = [
                'pending'    => ['label' => 'Ожидает',     'cls' => 'text-gray-400'],
                'processing' => ['label' => 'В обработке', 'cls' => 'text-blue-400'],
                'completed'  => ['label' => 'Завершён',    'cls' => 'text-green-400'],
                'cancelled'  => ['label' => 'Отменён',     'cls' => 'text-red-400'],
            ];
        @endphp
        @foreach($statBadges as $key => $b)
        <div class="bg-[#161920] border border-gray-800 px-4 py-2 rounded-xl flex items-center gap-2">
            <span class="text-[9px] text-gray-600 uppercase tracking-widest">{{ $b['label'] }}</span>
            <span class="text-sm font-black {{ $b['cls'] }}">{{ $counts[$key] }}</span>
        </div>
        @endforeach
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3.5 rounded-2xl text-sm flex items-center gap-3 mb-5">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

{{-- Search + Sort + Filter --}}
<form method="GET" action="{{ route('admin.orders.index') }}" id="filter-form" class="flex flex-col gap-3 mb-5">

    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Search --}}
        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Поиск по ID, имени, телефону или email..."
                   oninput="debounceSubmit()"
                   class="w-full bg-[#161920] border border-gray-800 rounded-xl pl-10 pr-9 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-orange-500 transition">
            @if($search)
            <a href="{{ route('admin.orders.index', ['sort' => $sort, 'status' => $status]) }}"
               class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-white transition text-lg leading-none">×</a>
            @endif
        </div>

        {{-- Sort --}}
        <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
        <input type="hidden" name="status" id="status-input" value="{{ $status }}">
        <div class="flex items-center gap-1.5 bg-[#161920] border border-gray-800 rounded-xl p-1 flex-shrink-0">
            @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'amount_desc' => 'Дорогие', 'amount_asc' => 'Дешёвые'] as $key => $label)
            <button type="button" onclick="setSort('{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap sort-btn {{ $sort === $key ? 'bg-orange-500 text-black' : 'text-gray-500 hover:text-white' }}"
                    data-sort="{{ $key }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Status tabs --}}
    <div class="flex items-center gap-1.5 flex-wrap">
        @php
            $tabs = [
                'all'        => ['label' => 'Все',          'active' => 'bg-white/10 text-white border-white/20'],
                'pending'    => ['label' => 'Ожидает',      'active' => 'bg-gray-700 text-gray-300 border-gray-600'],
                'processing' => ['label' => 'В обработке',  'active' => 'bg-blue-500/20 text-blue-400 border-blue-500/30'],
                'completed'  => ['label' => 'Завершён',     'active' => 'bg-green-500/20 text-green-400 border-green-500/30'],
                'cancelled'  => ['label' => 'Отменён',      'active' => 'bg-red-500/20 text-red-400 border-red-500/30'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
        <button type="button" onclick="setStatus('{{ $key }}')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap border status-btn
                       {{ $status === $key ? $tab['active'].' border' : 'text-gray-600 hover:text-gray-400 border-transparent' }}"
                data-status="{{ $key }}">
            {{ $tab['label'] }} ({{ $counts[$key] }})
        </button>
        @endforeach
    </div>
</form>

{{-- Table --}}
@if($orders->isEmpty())
<div class="bg-[#161920] border border-gray-800 rounded-2xl p-16 text-center">
    <i class="fa-solid fa-box-open text-3xl text-gray-700 mb-3 block"></i>
    <p class="text-sm text-gray-500">Заказы не найдены</p>
</div>
@else

<div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">

    {{-- Head --}}
    <div class="grid gap-4 px-5 py-3 border-b border-gray-800 text-[10px] font-bold uppercase tracking-widest text-gray-600"
         style="grid-template-columns:5rem 1fr 10rem 8rem 7rem 7rem 6rem">
        <span>ID</span>
        <span>Покупатель</span>
        <span>Товары</span>
        <span class="text-right">Сумма</span>
        <span class="text-center">Статус</span>
        <span class="text-center">Дата</span>
        <span class="text-right">Действие</span>
    </div>

    @foreach($orders as $order)
    @php
        $statusCls = match($order->status) {
            'pending'    => 'bg-gray-800 text-gray-400 border-gray-700',
            'processing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            'completed'  => 'bg-green-500/10 text-green-400 border-green-500/20',
            'cancelled'  => 'bg-red-500/10 text-red-400 border-red-500/20',
            default      => 'bg-gray-800 text-gray-400 border-gray-700',
        };
    @endphp
    <div class="ord-row grid gap-4 px-5 py-3.5 border-b border-gray-800/50 items-center last:border-b-0"
         style="grid-template-columns:5rem 1fr 10rem 8rem 7rem 7rem 6rem">

        {{-- ID --}}
        <span class="text-xs font-mono text-gray-600">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>

        {{-- Buyer --}}
        <div class="min-w-0">
            <p class="text-sm font-bold text-white truncate">{{ $order->full_name ?: $order->user->name }}</p>
            <p class="text-xs text-gray-600 truncate">{{ $order->user->email }}</p>
        </div>

        {{-- Items preview --}}
        <div class="flex items-center gap-1.5">
            @foreach($order->items->take(3) as $item)
            <div class="w-8 h-8 rounded-lg bg-gray-900 border border-gray-800 overflow-hidden flex-shrink-0 flex items-center justify-center">
                <img src="{{ asset($item->product->image ?? '') }}" class="w-full h-full object-contain p-0.5" onerror="this.style.display='none'">
            </div>
            @endforeach
            @if($order->items->count() > 3)
            <span class="text-[9px] text-gray-600">+{{ $order->items->count() - 3 }}</span>
            @endif
        </div>

        {{-- Amount --}}
        <div class="text-right">
            <p class="text-sm font-black text-white">{{ number_format($order->total_price, 0, '.', ' ') }} ₽</p>
            <p class="text-[9px] text-gray-600 mt-0.5">{{ $order->items->count() }} поз.</p>
        </div>

        {{-- Status --}}
        <div class="text-center">
            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg border {{ $statusCls }}">
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Date --}}
        <div class="text-center">
            <p class="text-xs text-gray-400">{{ $order->created_at->format('d.m.Y') }}</p>
            <p class="text-[9px] text-gray-600 mt-0.5">{{ $order->created_at->format('H:i') }}</p>
        </div>

        {{-- Action --}}
        <div class="flex justify-end">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-300 hover:text-white border border-gray-700 hover:border-gray-600 transition">
                Открыть
            </a>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6 custom-pagination">
    {{ $orders->onEachSide(1)->links() }}
</div>
@endif

<script>
let _searchTimer;
function debounceSubmit() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}
function setSort(val) {
    document.getElementById('sort-input').value = val;
    document.querySelectorAll('.sort-btn').forEach(btn => {
        const active = btn.dataset.sort === val;
        btn.className = btn.className.replace('bg-orange-500 text-black','').replace('text-gray-500 hover:text-white','').trim();
        btn.classList.add(...(active ? ['bg-orange-500','text-black'] : ['text-gray-500','hover:text-white']));
    });
    document.getElementById('filter-form').submit();
}
function setStatus(val) {
    document.getElementById('status-input').value = val;
    document.getElementById('filter-form').submit();
}
</script>

</x-admin-layout>
