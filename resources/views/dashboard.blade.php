<x-shop-layout>
    <x-slot name="title">Личный кабинет | RuGear</x-slot>

    <style>
        dialog[open] {
            position: fixed;
            top: 50% !important; left: 50% !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
        }
        dialog::backdrop { background: rgba(0,0,0,0.8); }
        dialog { animation: dlgIn 0.2s ease-out; }
        @keyframes dlgIn { from { opacity:0; transform:translate(-50%,-48%) scale(.97); } to { opacity:1; transform:translate(-50%,-50%) scale(1); } }

        .cs::-webkit-scrollbar { width: 4px; }
        .cs::-webkit-scrollbar-track { background: transparent; }
        .cs::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }

        .custom-pagination nav { background:transparent!important; box-shadow:none!important; border:none!important; padding:0!important; display:flex!important; justify-content:flex-end!important; }
        .custom-pagination nav>div:first-child,.custom-pagination nav p { display:none!important; }
        .custom-pagination nav>div:last-child { display:flex!important; background:transparent!important; }
        .custom-pagination a,.custom-pagination span[aria-current="page"] span { background:#161920!important; color:#9ca3af!important; border:1px solid #374151!important; font-size:12px!important; padding:7px 13px!important; margin:0 2px!important; border-radius:10px!important; transition:all .15s!important; }
        .custom-pagination span[aria-disabled="true"] span { background:#161920!important; color:#4b5563!important; border:1px solid #374151!important; font-size:12px!important; padding:7px 13px!important; margin:0 2px!important; border-radius:10px!important; cursor:not-allowed!important; }
        .custom-pagination span[aria-current="page"] span { background:#f97316!important; color:#000!important; border-color:#f97316!important; font-weight:900!important; }
        .custom-pagination a:hover { background:#1f2937!important; color:#fff!important; }

        #ach-track { scrollbar-width:none; -ms-overflow-style:none; }
        #ach-track::-webkit-scrollbar { display:none; }
    </style>

    <div class="space-y-5">

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ПРОФИЛЬ                                           --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-orange-500 flex items-center justify-center text-black text-2xl font-black flex-shrink-0">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white leading-tight">{{ auth()->user()->name }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-6 flex-wrap">
                <div class="text-center">
                    <p class="text-3xl font-black text-orange-500 leading-none">{{ auth()->user()->level }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Уровень</p>
                </div>
                <div class="w-px h-12 bg-gray-800"></div>
                <div class="text-center">
                    <p class="text-3xl font-black text-white leading-none">{{ auth()->user()->experience }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Опыт (XP)</p>
                </div>
                <div class="w-px h-12 bg-gray-800"></div>
                <div class="text-center">
                    <p class="text-3xl font-black leading-none" style="color:#f59e0b;">{{ number_format(auth()->user()->coins) }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Коины</p>
                </div>
                <div class="w-px h-12 bg-gray-800"></div>
                <div class="text-center">
                    <p class="text-3xl font-black text-white leading-none">{{ auth()->user()->achievements->count() }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Достижений</p>
                </div>
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <div class="flex-1 h-2 bg-gray-900 rounded-full overflow-hidden">
                <div class="h-full bg-orange-500 rounded-full transition-all duration-700"
                     style="width: {{ min(100, auth()->user()->experience > 0 ? (int) round(auth()->user()->experience / auth()->user()->next_level_experience * 100) : 0) }}%">
                </div>
            </div>
            <p class="text-sm text-gray-500 flex-shrink-0 whitespace-nowrap">
                {{ auth()->user()->next_level_experience - auth()->user()->experienceProgress }} XP до уровня {{ auth()->user()->level + 1 }}
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- КОРЗИНА                                           --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Корзина</h2>
            <span class="text-sm text-gray-500">{{ collect($cartItems)->sum('quantity') }} товаров</span>
        </div>
        <div class="p-6">
            <livewire:profile-cart />
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ЗАКАЗЫ                                            --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Мои заказы</h2>
            @if(!$userOrders->isEmpty())
            <span class="text-sm font-medium text-gray-500">{{ $userOrders->total() }} заказов</span>
            @endif
        </div>

        @if($userOrders->isEmpty())
        <div class="p-10 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-800 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-base font-semibold text-gray-400">Заказов пока нет</p>
            <p class="text-sm text-gray-600 mt-1">Добавьте товары в корзину и оформите первый заказ</p>
            <a href="/" class="inline-block mt-4 bg-orange-500 hover:bg-orange-600 text-black font-bold text-sm px-6 py-2.5 rounded-xl transition">
                В каталог
            </a>
        </div>
        @else
        <div class="divide-y divide-gray-800">
            @foreach($userOrders as $order)
            @php
                $dlabel = ['courier'=>'Курьер','pickup'=>'Самовывоз','post'=>'Почта / ПВЗ'];
                $plabel = ['card'=>'Карта','cash'=>'Наличные'];
                $steps  = ['pending'=>0,'processing'=>1,'completed'=>2];
                $step   = $steps[$order->status] ?? 0;
                $cnt    = $order->items->count();
                $word   = ($cnt%10===1&&$cnt%100!==11)?'позиция':(in_array($cnt%10,[2,3,4])&&!in_array($cnt%100,[12,13,14])?'позиции':'позиций');
                $oid    = str_pad($order->id,4,'0',STR_PAD_LEFT);
                $accent = match($order->status){
                    'processing'=>'bg-blue-500','completed'=>'bg-green-500',
                    'cancelled'=>'bg-red-500',default=>'bg-gray-700'};
            @endphp
            <div class="relative pl-8 pr-5 py-5 hover:bg-white/[0.02] transition-colors">

                {{-- Full-height left accent --}}
                <div class="absolute left-0 top-0 bottom-0 w-[3px] rounded-r-sm {{ $accent }}"></div>

                {{-- Header: ID + badge + date/meta + actions --}}
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap mb-1">
                            <span class="font-mono text-sm font-black text-white tracking-wider">ORD-{{ $oid }}</span>
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide
                                @switch($order->status)
                                    @case('pending')    bg-gray-800 text-gray-300 @break
                                    @case('processing') bg-blue-500/20 text-blue-300 border border-blue-500/30 @break
                                    @case('completed')  bg-green-500/20 text-green-300 border border-green-500/30 @break
                                    @case('cancelled')  bg-red-500/20 text-red-300 border border-red-500/30 @break
                                @endswitch">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600">
                            {{ $order->created_at->format('d.m.Y · H:i') }}
                            @if($order->delivery_type) <span class="mx-1 text-gray-800">·</span> {{ $dlabel[$order->delivery_type] ?? '' }} @endif
                            @if($order->payment_method) <span class="mx-1 text-gray-800">·</span> {{ $plabel[$order->payment_method] ?? '' }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        @if($order->status === 'pending')
                        <form action="{{ route('orders.cancel', $order) }}" method="POST"
                              onsubmit="return confirm('Отменить заказ ORD-{{ $oid }}?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-xs font-semibold text-gray-600 hover:text-red-400 transition px-1">
                                Отменить
                            </button>
                        </form>
                        @endif
                        <button onclick="toggleDetails({{ $order->id }})"
                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-800 text-gray-500 hover:border-gray-600 hover:text-white transition">
                            <svg id="btn-icon-{{ $order->id }}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Product thumbnails + price --}}
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex -space-x-2.5 flex-shrink-0">
                            @foreach($order->items->take(3) as $item)
                            <div class="w-11 h-11 rounded-xl bg-[#0f1115] border-2 border-[#161920] overflow-hidden flex items-center justify-center">
                                <img src="{{ asset($item->product->image) }}" class="w-full h-full object-contain p-1">
                            </div>
                            @endforeach
                            @if($cnt > 3)
                            <div class="w-11 h-11 rounded-xl bg-gray-800/70 border-2 border-[#161920] flex items-center justify-center">
                                <span class="text-[11px] font-bold text-gray-400">+{{ $cnt - 3 }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-2xl font-black text-white tabular-nums leading-none">
                            {{ number_format($order->total_price, 0, '.', ' ') }}&nbsp;<span class="text-orange-500">₽</span>
                        </p>
                        <p class="text-xs text-gray-600 mt-1">{{ $cnt }} {{ $word }}</p>
                    </div>
                </div>

                {{-- Status timeline --}}
                @if($order->status !== 'cancelled')
                <div class="flex items-center gap-2">
                    @for($s = 0; $s < 3; $s++)
                    <div class="w-5 h-5 rounded-full flex-shrink-0 flex items-center justify-center border-2
                         {{ $step > $s ? 'bg-orange-500 border-orange-500' : ($step === $s ? 'border-orange-500 bg-transparent' : 'border-gray-800 bg-transparent') }}">
                        @if($step > $s)
                        <svg class="w-2.5 h-2.5 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        @elseif($step === $s)
                        <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                        @endif
                    </div>
                    @if($s < 2)
                    <div class="w-10 h-px {{ $step > $s ? 'bg-orange-500' : 'bg-gray-800' }}"></div>
                    @endif
                    @endfor
                    <div class="flex gap-5 ml-2">
                        @foreach(['Ожидание','Обработка','Доставлен'] as $si => $sl)
                        <span class="text-xs font-medium {{ $step >= $si ? 'text-orange-400' : 'text-gray-600' }}">{{ $sl }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Expandable details --}}
                <div id="det-{{ $order->id }}" class="hidden mt-4 pt-4 border-t border-gray-800/60 space-y-2.5">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-[#0f1115] border border-gray-800 rounded-xl flex-shrink-0 overflow-hidden flex items-center justify-center">
                            <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-contain p-1">
                        </div>
                        <span class="text-sm text-gray-400 flex-1 truncate">{{ $item->product->name }}</span>
                        <span class="text-xs text-gray-600 flex-shrink-0 tabular-nums">{{ $item->quantity }}&times;</span>
                        <span class="text-sm font-bold text-white flex-shrink-0 tabular-nums">{{ number_format($item->price * $item->quantity, 0, '.', ' ') }} ₽</span>
                    </div>
                    @endforeach
                    @if($order->address)
                    <div class="flex items-start gap-2 pt-2.5 border-t border-gray-800/60">
                        <svg class="w-3.5 h-3.5 text-gray-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-xs text-gray-500">{{ $order->address }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($userOrders->hasPages())
        <div class="px-6 py-4 border-t border-gray-800 custom-pagination">
            {{ $userOrders->links() }}
        </div>
        @endif
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ДОСТИЖЕНИЯ + ПОДДЕРЖКА                            --}}
    {{-- ══════════════════════════════════════════════════ --}}
    @php
    $achIcons = [
        'registered'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'comment_1'      => '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/><line x1="9" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="13" y2="14"/>',
        'comment_3'      => '<path d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1"/><path d="M15 5H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4h4a2 2 0 002-2V7a2 2 0 00-2-2z"/>',
        'comment_5'      => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>',
        'first_order'    => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>',
        'order_10k'      => '<path d="M12 2a8 8 0 000 16"/><path d="M12 2c2.5 2.5 4 5.5 4 8s-1.5 5.5-4 8"/><path d="M2 12h16"/><path d="M12 18a8 8 0 010-16"/><path d="M20 12c0 4.4-3.6 8-8 8s-8-3.6-8-8 3.6-8 8-8"/>',
        'order_50k'      => '<path d="M2 19l3-9 4 4 3-7 3 7 4-4 3 9H2z"/>',
        'all_categories' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    ];
    $defaultAchIcon = '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>';
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ДОСТИЖЕНИЯ --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between flex-shrink-0">
                <h2 class="text-base font-bold text-white">Достижения</h2>
                <button onclick="document.getElementById('modal-ach').showModal()"
                        class="text-sm font-semibold text-orange-500 hover:text-orange-400 transition">
                    Все →
                </button>
            </div>

            <div class="p-6 flex-1 flex flex-col min-h-0">
            @php $achList = auth()->user()->achievements->sortByDesc('pivot.awarded_at'); @endphp
            @if($achList->count() > 0)
                <div id="ach-track" class="flex gap-3 overflow-x-auto flex-1 min-h-0" style="scroll-snap-type:x mandatory;scroll-behavior:smooth;">
                    @foreach($achList as $a)
                    <div class="flex-shrink-0 bg-gray-900/60 border border-gray-700/60 rounded-xl p-4 flex flex-col justify-between"
                         style="width:calc(33.333% - 8px);min-width:140px;scroll-snap-align:start;">
                        <div class="w-9 h-9 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                {!! $achIcons[$a->slug] ?? $defaultAchIcon !!}
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-white leading-snug line-clamp-3 my-3">{{ $a->title }}</p>
                        <p class="text-xs font-bold text-orange-500 flex-shrink-0">+{{ $a->experience }} XP</p>
                    </div>
                    @endforeach
                </div>
                @if($achList->count() > 3)
                <div class="flex justify-end gap-2 mt-3 flex-shrink-0">
                    <button onclick="document.getElementById('ach-track').scrollBy({left:-200,behavior:'smooth'})"
                            class="w-8 h-8 border border-gray-700 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:border-gray-600 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="document.getElementById('ach-track').scrollBy({left:200,behavior:'smooth'})"
                            class="w-8 h-8 border border-gray-700 rounded-lg flex items-center justify-center text-gray-500 hover:text-white hover:border-gray-600 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            @else
            <div class="flex-1 flex flex-col items-center justify-center text-center">
                <p class="text-sm font-semibold text-gray-500">Нет достижений</p>
                <p class="text-xs text-gray-600 mt-1">Первое выдаётся при регистрации</p>
            </div>
            @endif
            </div>
        </div>

        {{-- ПОДДЕРЖКА --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <h2 class="text-base font-bold text-white">Поддержка</h2>
                <a href="{{ route('support') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-400 transition">
                    + Создать
                </a>
            </div>

            @if($userTickets->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm font-semibold text-gray-500">Обращений нет</p>
                <a href="{{ route('support') }}" class="inline-block mt-3 text-sm font-bold text-orange-500 hover:text-orange-400 transition">
                    Создать тикет →
                </a>
            </div>
            @else
            <div class="divide-y divide-gray-800 max-h-80 overflow-y-auto cs">
                @foreach($userTickets as $ticket)
                @php
                    $tc = match($ticket->status){
                        'pending'=>'text-blue-400','replied'=>'text-orange-400',
                        'closed'=>'text-gray-500',default=>'text-gray-500'};
                    $tl = match($ticket->status){
                        'pending'=>'На рассмотрении','replied'=>'Получен ответ',
                        'closed'=>'Закрыт',default=>$ticket->status};
                @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-mono text-gray-600">#TC-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}</span>
                                <span class="text-xs font-bold {{ $tc }}">{{ $tl }}</span>
                            </div>
                            <p class="text-sm font-semibold text-white line-clamp-1">{{ $ticket->name }}</p>
                            <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $ticket->content }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <form action="{{ route('ticket.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Удалить тикет?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-600 hover:text-red-400 transition rounded-lg hover:bg-red-500/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            <button onclick="document.getElementById('tc-{{ $ticket->id }}').showModal()"
                                    class="px-3 py-1.5 text-xs font-semibold text-gray-400 border border-gray-700 hover:border-gray-600 hover:text-white rounded-lg transition">
                                Открыть
                            </button>
                        </div>
                    </div>
                </div>

                <dialog id="tc-{{ $ticket->id }}" class="tcmodal bg-[#111318] border border-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl focus:outline-none">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-800 pb-4">
                            <div>
                                <span class="text-xs font-mono text-gray-600 block">#TC-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}</span>
                                <h3 class="text-base font-bold text-white mt-0.5">{{ $ticket->name }}</h3>
                            </div>
                            <button onclick="document.getElementById('tc-{{ $ticket->id }}').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
                        </div>
                        @if($ticket->category)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Категория</p>
                            <span class="text-sm text-gray-300 bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-800">{{ $ticket->category }}</span>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Сообщение</p>
                            <div class="bg-gray-900/50 border border-gray-800 rounded-xl p-3.5 text-sm text-gray-300 max-h-32 overflow-y-auto cs whitespace-pre-line leading-relaxed">{{ $ticket->content }}</div>
                        </div>
                        <div>
                            <p class="text-xs text-orange-400/80 uppercase tracking-wider mb-2">Ответ поддержки</p>
                            @if($ticket->reply)
                            <div class="bg-orange-500/5 border border-orange-500/20 rounded-xl p-3.5 text-sm text-gray-200 max-h-36 overflow-y-auto cs whitespace-pre-line leading-relaxed">{{ $ticket->reply }}</div>
                            @else
                            <div class="bg-gray-900/40 border border-gray-800 rounded-xl p-3.5 text-center">
                                <p class="text-sm text-gray-500 italic">Ожидает ответа специалиста</p>
                            </div>
                            @endif
                        </div>
                        <button onclick="document.getElementById('tc-{{ $ticket->id }}').close()"
                                class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-sm font-semibold text-gray-400 hover:text-white rounded-xl transition">
                            Закрыть
                        </button>
                    </div>
                </dialog>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ИГРОВАЯ ЗОНА                                      --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6 flex items-center justify-between gap-6">
        <div>
            <h2 class="text-base font-bold text-white">Мини-игра Buzzword Blast</h2>
            <p class="text-sm text-gray-500 mt-1">Покажи свой скилл и выиграй призы!</p>
        </div>
        <button onclick="document.getElementById('mod-game').showModal(); setTimeout(()=>document.getElementById('game-iframe').focus(),100);"
                class="flex-shrink-0 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">
            Запустить
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ВЫХОД                                             --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="pt-2 pb-4">
        <button onclick="toggleLogout(true)" class="text-sm text-gray-600 hover:text-red-400 transition font-medium">
            Выйти из аккаунта
        </button>
    </div>

    </div>{{-- /space-y-5 --}}

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- DIALOGS                                           --}}
    {{-- ══════════════════════════════════════════════════ --}}

    {{-- Achievements modal --}}
    <dialog id="modal-ach" class="bg-[#111318] border border-gray-800 rounded-2xl p-0 w-full max-w-xl shadow-2xl focus:outline-none" style="max-height:88vh;">
        <div class="flex flex-col" style="max-height:88vh;">
            <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4 flex-shrink-0">
                <h2 class="text-base font-bold text-white">Все достижения</h2>
                <button onclick="document.getElementById('modal-ach').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4 cs divide-y divide-gray-800/60">
                @forelse($allAchievements->sortBy('experience') as $ach)
                @php
                    $got = in_array($ach->id, $userAchievementIds);
                    $ua  = auth()->user()->achievements()->where('achievement_id',$ach->id)->first();
                @endphp
                <div class="flex items-center gap-4 py-3.5 {{ !$got ? 'opacity-40' : '' }}">
                    <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                         {{ $got ? 'bg-orange-500/15 border border-orange-500/30' : 'bg-gray-900 border border-gray-800' }}">
                        <svg class="w-5 h-5 {{ $got ? 'text-orange-400' : 'text-gray-700' }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            {!! $achIcons[$ach->slug] ?? $defaultAchIcon !!}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white">{{ $ach->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $ach->description }}</p>
                        @if($got && $ua)
                        <p class="text-xs text-gray-600 mt-1">{{ \Carbon\Carbon::parse($ua->pivot->awarded_at)->diffForHumans() }}</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm font-black {{ $got ? 'text-orange-500' : 'text-gray-700' }}">+{{ $ach->experience }} XP</p>
                        <p class="text-xs mt-0.5 {{ $got ? 'text-green-400' : 'text-gray-600' }} font-semibold">{{ $got ? 'Получено' : 'Не получено' }}</p>
                    </div>
                </div>
                @empty
                <p class="py-8 text-center text-sm text-gray-500">Достижений нет</p>
                @endforelse
            </div>
            <div class="border-t border-gray-800 px-6 py-4 flex-shrink-0">
                <button onclick="document.getElementById('modal-ach').close()"
                        class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-sm font-semibold text-gray-400 hover:text-white rounded-xl transition">
                    Закрыть
                </button>
            </div>
        </div>
    </dialog>

    {{-- Game modal --}}
    <dialog id="mod-game" class="bg-[#111318] border border-gray-800 p-0 rounded-2xl w-[70vw] h-[96vh] shadow-2xl focus:outline-none">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between border-b border-gray-800 px-5 py-4 flex-shrink-0">
                <h3 class="text-base font-bold text-white">Мини-игра RuGear</h3>
                <button onclick="document.getElementById('mod-game').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
            </div>
            <div class="flex-1 bg-black overflow-hidden relative">
                <iframe id="game-iframe" src="{{ asset('index.html') }}"
                        class="absolute inset-0 w-full h-full border-0"
                        allow="autoplay; keyboard; fullscreen" loading="lazy"></iframe>
            </div>
            <div class="border-t border-gray-800 px-4 py-3 flex-shrink-0">
                <button onclick="document.getElementById('mod-game').close()"
                        class="w-full py-2 text-sm font-semibold border border-gray-700 text-gray-500 hover:text-white rounded-xl transition">
                    Закрыть
                </button>
            </div>
        </div>
    </dialog>

    {{-- Logout modal --}}
    <div id="mod-logout" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4">
        <div class="bg-[#161920] border border-gray-800 w-full max-w-sm rounded-2xl p-8 shadow-2xl">
            <h3 class="text-xl font-black text-white text-center">Выход</h3>
            <p class="text-sm text-gray-500 text-center mt-2 mb-8">Вы уверены, что хотите выйти из аккаунта?</p>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="toggleLogout(false)"
                        class="py-3 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-white font-semibold rounded-xl transition">
                    Остаться
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-black font-black rounded-xl transition">
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleLogout(show){
            const m=document.getElementById('mod-logout');
            if(show){m.classList.remove('hidden');m.classList.add('flex');document.body.style.overflow='hidden';}
            else{m.classList.add('hidden');m.classList.remove('flex');document.body.style.overflow='';}
        }
        window.addEventListener('click',e=>{
            if(e.target===document.getElementById('mod-logout')) toggleLogout(false);
        });
        document.querySelectorAll('.tcmodal').forEach(d=>{
            d.addEventListener('click',e=>{
                const r=d.getBoundingClientRect();
                if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom) d.close();
            });
        });
        function toggleDetails(id){
            const el=document.getElementById('det-'+id);
            const icon=document.getElementById('btn-icon-'+id);
            if(!el) return;
            const open=el.classList.contains('hidden');
            el.classList.toggle('hidden');
            if(icon) icon.style.transform=open?'rotate(180deg)':'';
        }
    </script>
</x-shop-layout>
