<x-admin-layout>
<x-slot name="title">Заказ #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} | RuGear Admin</x-slot>

<div class="mb-5">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        Все заказы
    </a>
</div>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3.5 rounded-2xl text-sm flex items-center gap-3 mb-5">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

@php
    $statusCls = match($order->status) {
        'pending'    => 'bg-gray-800 text-gray-400 border-gray-700',
        'processing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        'completed'  => 'bg-green-500/10 text-green-400 border-green-500/20',
        'cancelled'  => 'bg-red-500/10 text-red-400 border-red-500/20',
        default      => 'bg-gray-800 text-gray-400 border-gray-700',
    };
    $statusRadio = [
        'pending'    => ['label' => 'Ожидает',     'checked' => 'bg-gray-700 text-gray-300 border-gray-600'],
        'processing' => ['label' => 'В обработке', 'checked' => 'bg-blue-500/20 text-blue-400 border-blue-500/40'],
        'completed'  => ['label' => 'Завершён',    'checked' => 'bg-green-500/20 text-green-400 border-green-500/40'],
        'cancelled'  => ['label' => 'Отменён',     'checked' => 'bg-red-500/20 text-red-400 border-red-500/40'],
    ];
@endphp

{{-- Page header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-black text-white">Заказ <span class="text-orange-500">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span></h1>
            <span class="text-[9px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg border {{ $statusCls }}">{{ $order->status_label }}</span>
        </div>
        <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d.m.Y') }} в {{ $order->created_at->format('H:i') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: items + notes --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Items --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-800">
                <h2 class="text-sm font-black text-white uppercase tracking-wider">Товары · {{ $order->items->count() }} поз.</h2>
            </div>
            <div class="divide-y divide-gray-800/50">
                @forelse($order->items as $item)
                <div class="flex items-center gap-4 px-5 py-3.5">
                    <div class="w-14 h-14 bg-gray-900 border border-gray-800 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                        <img src="{{ asset($item->product->image ?? '') }}" class="w-full h-full object-contain p-1" onerror="this.style.display='none'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $item->product->name }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $item->product->category->name ?? '—' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black text-white">{{ number_format($item->price * $item->quantity, 0, '.', ' ') }} ₽</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $item->quantity }} шт · {{ number_format($item->price, 0, '.', ' ') }} ₽/шт</p>
                    </div>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-600">Нет товаров</p>
                @endforelse
            </div>
            <div class="px-5 py-3.5 border-t border-gray-800 flex items-center justify-between">
                <span class="text-xs text-gray-600 uppercase tracking-widest">Итого</span>
                <span class="text-xl font-black text-white">{{ number_format($order->total_price, 0, '.', ' ') }} <span class="text-orange-500">₽</span></span>
            </div>
        </div>

        {{-- Notes --}}
        @if($order->notes)
        <div class="bg-[#161920] border border-gray-800 rounded-2xl px-5 py-4">
            <p class="text-[9px] text-gray-600 uppercase tracking-widest mb-2">Примечания</p>
            <p class="text-sm text-gray-400 leading-relaxed">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Right: info + status --}}
    <div class="space-y-5">

        {{-- Customer info --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-800">
                <h2 class="text-sm font-black text-white uppercase tracking-wider">Покупатель</h2>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 flex-shrink-0 flex items-center justify-center font-black text-black overflow-hidden">
                        @if($order->user->avatar)
                            <img src="{{ Storage::url($order->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr($order->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold truncate" style="{{ $order->user->cosmetic_nickname_color ? 'color:' . $order->user->cosmetic_nickname_color . ';' : '' }}{{ $order->user->cosmetic_font ? 'font-family:' . $order->user->cosmetic_font . ';' : '' }}">{{ $order->user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $order->user->email }}</p>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-gray-800">
                    @foreach([
                        'Получатель' => $order->full_name,
                        'Телефон'    => $order->phone,
                        'Адрес'      => $order->address,
                        'Доставка'   => $order->delivery_type,
                        'Оплата'     => $order->payment_method,
                    ] as $lbl => $val)
                    @if($val)
                    <div class="flex items-start gap-2">
                        <span class="text-[9px] text-gray-600 uppercase tracking-widest w-20 flex-shrink-0 mt-0.5">{{ $lbl }}</span>
                        <span class="text-xs text-gray-300 leading-relaxed">{{ $val }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Order meta --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl px-5 py-4 space-y-2">
            <p class="text-[9px] text-gray-600 uppercase tracking-widest mb-3">Детали заказа</p>
            @foreach([
                'ID'          => '#'.str_pad($order->id, 4, '0', STR_PAD_LEFT),
                'Создан'      => $order->created_at->format('d.m.Y H:i'),
                'Обновлён'    => $order->updated_at->format('d.m.Y H:i'),
                'Позиций'     => $order->items->count().' шт.',
            ] as $lbl => $val)
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">{{ $lbl }}</span>
                <span class="text-xs font-semibold text-gray-300 font-mono">{{ $val }}</span>
            </div>
            @endforeach
        </div>

        {{-- Status management --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-800">
                <h2 class="text-sm font-black text-white uppercase tracking-wider">Изменить статус</h2>
            </div>
            <div class="px-5 py-4">
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($statusRadio as $val => $s)
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="{{ $val }}" class="sr-only peer" {{ $order->status === $val ? 'checked' : '' }}>
                            <div class="text-center py-2.5 rounded-xl text-xs font-semibold border transition
                                        border-gray-800 text-gray-600 hover:border-gray-700 hover:text-gray-400
                                        peer-checked:{{ $s['checked'] }} peer-checked:border">
                                {{ $s['label'] }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-black text-xs font-black uppercase tracking-wider rounded-xl transition">
                        Применить
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

</x-admin-layout>
