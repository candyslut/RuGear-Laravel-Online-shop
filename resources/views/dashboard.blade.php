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

        #modal-ach[open] {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

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

        /* ── Expanded achievement layout (triggered when ticket list scrolls) ── */
        #ach-track.ach-expanded .ach-card {
            width: calc(50% - 6px) !important;
            min-width: 0 !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        #ach-track.ach-expanded .ach-icon-wrap {
            width: 3.25rem !important;
            height: 3.25rem !important;
            flex-shrink: 0;
        }
        #ach-track.ach-expanded .ach-svg {
            width: 1.6rem !important;
            height: 1.6rem !important;
        }
        #ach-track.ach-expanded .ach-title {
            margin-top: 0.65rem;
            margin-bottom: 0.2rem;
        }
        #ach-track.ach-expanded .ach-desc {
            display: block !important;
        }
        #ach-track.ach-expanded .ach-xp {
            margin-top: 0.5rem;
        }
    </style>

    <div class="space-y-5">

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ПРОФИЛЬ                                           --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="bg-[#161920] border border-gray-800 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-orange-500 flex items-center justify-center text-black text-2xl font-black flex-shrink-0 overflow-hidden mt-0.5">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Аватар" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white leading-tight">{{ auth()->user()->name }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->phone || auth()->user()->gender)
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if(auth()->user()->phone)
                        <span class="flex items-center gap-1.5 text-xs text-gray-400 bg-gray-800/60 rounded-lg px-2.5 py-1">
                            <svg class="w-3.5 h-3.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ auth()->user()->phone }}
                        </span>
                        @endif
                        @if(auth()->user()->gender)
                        <span class="text-xs text-gray-400 bg-gray-800/60 rounded-lg px-2.5 py-1">
                            {{ ['male' => 'Мужской', 'female' => 'Женский'][auth()->user()->gender] ?? '' }}
                        </span>
                        @endif
                    </div>
                    @endif
                    @if(auth()->user()->about)
                    @php $aboutLong = mb_strlen(auth()->user()->about) > 150; @endphp
                    <div class="mt-2 max-w-lg">
                        <div id="about-wrap"
                             class="text-sm text-gray-500 leading-relaxed overflow-hidden transition-all duration-300 ease-in-out"
                             style="{{ $aboutLong ? 'max-height:4.2rem;' : '' }}">{{ auth()->user()->about }}</div>
                        @if($aboutLong)
                        <button onclick="toggleAbout(this)" data-open="0"
                                class="mt-1.5 text-xs font-semibold text-orange-500 hover:text-orange-400 transition">
                            Показать ещё ↓
                        </button>
                        @endif
                    </div>
                    @endif
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

        <div class="mt-5">
            <button onclick="document.getElementById('modal-leaderboard').showModal()"
                    class="w-full flex items-center justify-center gap-2.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200"
                    style="background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.2);color:#f59e0b;"
                    onmouseover="this.style.background='rgba(245,158,11,0.14)';this.style.borderColor='rgba(245,158,11,0.4)';"
                    onmouseout="this.style.background='rgba(245,158,11,0.07)';this.style.borderColor='rgba(245,158,11,0.2)';">
                Рейтинг пользователей
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ИГРОВАЯ ЗОНА                                      --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-2xl"
         style="background:#0b0b0d url('{{ asset('images/game-bg.jpg') }}') right center/cover no-repeat; border:1px solid rgba(32,248,192,0.18);">

        {{-- Gradient: opaque on left (text), semi-transparent on right (bg shows) --}}
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(11,11,13,0.97) 0%,rgba(11,11,13,0.85) 45%,rgba(11,11,13,0.45) 100%);"></div>

        {{-- Top accent line --}}
        <div class="absolute top-0 left-0 right-0 h-px" style="background:linear-gradient(to right,transparent,rgba(32,248,192,0.55),transparent);"></div>

        {{-- Content --}}
        <div class="relative z-10 px-7 py-5 flex items-center justify-between gap-8">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:#20f8c0;box-shadow:0 0 6px #20f8c0;"></div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-500">Мини-игра</span>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest leading-none"
                    style="color:#20f8c0;text-shadow:0 0 18px rgba(32,248,192,0.6),0 0 4px rgba(32,248,192,0.4);">BUZZWORD BLAST</h2>
                <p class="text-sm text-gray-500 mt-1.5">Уничтожь астероиды — заработай награды</p>
                <div class="flex items-center gap-5 mt-3">
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#20f8c0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        +5 XP за уровень
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
                            <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                            <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text>
                        </svg>
                        +1 монета за уровень
                    </span>
                </div>
            </div>

            <button onclick="openGame()"
                    class="flex-shrink-0 flex items-center gap-2.5 px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest transition-all duration-200"
                    style="background:rgba(32,248,192,0.08);border:1px solid rgba(32,248,192,0.35);color:#20f8c0;text-shadow:0 0 10px rgba(32,248,192,0.6);box-shadow:0 0 18px rgba(32,248,192,0.08);"
                    onmouseover="this.style.background='rgba(32,248,192,0.16)';this.style.boxShadow='0 0 28px rgba(32,248,192,0.2)';"
                    onmouseout="this.style.background='rgba(32,248,192,0.08)';this.style.boxShadow='0 0 18px rgba(32,248,192,0.08)';">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                Играть
            </button>
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

                {{-- thumbnails (left) | timeline (center) | price (right) --}}
                <div class="flex items-center gap-4 mb-4">
                    {{-- Left: thumbnails --}}
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

                    {{-- Center: status timeline --}}
                    @if($order->status !== 'cancelled')
                    <div class="flex-1 min-w-0 px-2">
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
                            <div class="flex-1 h-px {{ $step > $s ? 'bg-orange-500' : 'bg-gray-800' }}"></div>
                            @endif
                            @endfor
                        </div>
                        <div class="flex mt-1.5">
                            @foreach(['Заказ принят в обработку', 'Заказ едет к вам', 'Заказ доставлен'] as $si => $sl)
                            <span class="flex-1 text-[9px] leading-tight font-medium {{ $step >= $si ? 'text-orange-400' : 'text-gray-600' }} {{ $si === 0 ? 'text-left' : ($si === 1 ? 'text-center' : 'text-right') }}">{{ $sl }}</span>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="flex-1"></div>
                    @endif

                    {{-- Right: price --}}
                    <div class="text-right flex-shrink-0">
                        <p class="text-2xl font-black text-white tabular-nums leading-none">
                            {{ number_format($order->total_price, 0, '.', ' ') }}&nbsp;<span class="text-orange-500">₽</span>
                        </p>
                        <p class="text-xs text-gray-600 mt-1">{{ $cnt }} {{ $word }}</p>
                    </div>
                </div>

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
        'order_10k'      => '<circle cx="12" cy="12" r="9"/><path d="M11 17V7h1.5a2.5 2.5 0 0 1 0 5h-1.5M9 13.5h6M9 15.5h6"/>',
        'order_50k'      => '<path d="M3 11C5 7 9 5 13 5c5 0 8 2.5 8 6.5s-3 7.5-8 7.5C9 19 5 17 3 13"/><path d="M3 11L1 8M3 13L1 16"/><path d="M10 5L12 2L14 5"/><circle cx="18" cy="11" r="0.75" fill="currentColor"/>',
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
                    <div class="ach-card flex-shrink-0 bg-gray-900/60 border border-gray-700/60 rounded-xl p-4 flex flex-col justify-between"
                         style="width:calc(33.333% - 8px);min-width:140px;scroll-snap-align:start;">
                        <div class="ach-icon-wrap w-9 h-9 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="ach-svg w-4 h-4 text-orange-400" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                {!! $achIcons[$a->slug] ?? $defaultAchIcon !!}
                            </svg>
                        </div>
                        <p class="ach-title text-sm font-bold text-white leading-snug line-clamp-3 my-3">{{ $a->title }}</p>
                        <p class="ach-desc hidden text-xs text-gray-500 leading-snug line-clamp-3 mb-2">{{ $a->description }}</p>
                        <p class="ach-xp text-xs font-bold text-orange-500 flex-shrink-0">+{{ $a->experience }} XP</p>
                    </div>
                    @endforeach
                </div>
                @if($achList->count() > 3)
                <div id="ach-nav-btns" class="flex justify-end gap-2 mt-3 flex-shrink-0">
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
            <div id="ticket-list" class="divide-y divide-gray-800 max-h-80 overflow-y-auto cs">
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

    </div>{{-- /space-y-5 --}}

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- DIALOGS                                           --}}
    {{-- ══════════════════════════════════════════════════ --}}

    {{-- Leaderboard modal --}}
    <dialog id="modal-leaderboard" class="bg-[#111318] border border-gray-800 rounded-2xl p-0 w-full max-w-4xl shadow-2xl focus:outline-none" style="height:92vh;max-height:92vh;">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4 flex-shrink-0">
                <h2 class="text-base font-bold text-white">Рейтинг игроков</h2>
                <button onclick="document.getElementById('modal-leaderboard').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
            </div>

            <div class="px-6 py-3 border-b border-gray-800 flex-shrink-0">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="leader-search" type="text" placeholder="Поиск по имени..."
                           oninput="leaderSearch(this.value)"
                           class="w-full bg-[#0f1115] border border-gray-700 rounded-xl pl-9 pr-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-orange-500 transition">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-gray-800/50" id="leader-list" style="min-height:0;">
                @foreach($leaderboard as $i => $u)
                @php
                    $isMe  = $u->id === auth()->id();
                    $prog  = $u->experienceProgress;
                    $rankColors = ['#f59e0b','#9ca3af','#cd7c3a'];
                @endphp
                <div class="leader-row" data-name="{{ strtolower($u->name) }}">
                    <div class="flex items-center gap-3 px-6 py-3.5 {{ $isMe ? 'bg-orange-500/5' : 'hover:bg-white/[0.02]' }} transition-colors">
                        {{-- Rank --}}
                        <div class="w-6 flex-shrink-0 text-center">
                            @if($i < 3)
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24" style="color:{{ $rankColors[$i] }}">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            @else
                            <span class="text-xs font-bold text-gray-600">{{ $i + 1 }}</span>
                            @endif
                        </div>

                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-xl flex-shrink-0 overflow-hidden flex items-center justify-center {{ $isMe ? 'bg-orange-500' : 'bg-gray-700' }} text-sm font-black {{ $isMe ? 'text-black' : 'text-white' }} {{ $u->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                             style="{{ $u->cosmetic_border && $u->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$u->cosmetic_border.';' : '' }}">
                            @if($u->avatar)
                                <img src="{{ Storage::url($u->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                            @endif
                        </div>

                        {{-- Name --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold truncate"
                               style="{{ $u->cosmetic_nickname_color ? 'color:'.$u->cosmetic_nickname_color.';' : ($isMe ? 'color:#fb923c;' : 'color:#fff;') }}{{ $u->cosmetic_font ? 'font-family:'.$u->cosmetic_font.';' : '' }}">
                                {{ $u->name }}@if($isMe)<span class="text-xs font-normal text-orange-500/80 ml-1" style="font-family:inherit;">вы</span>@endif
                            </p>
                        </div>

                        {{-- Stats --}}
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="text-center w-10">
                                <p class="text-sm font-black text-orange-500 leading-none">{{ $u->level }}</p>
                                <p class="text-[9px] text-gray-600 uppercase mt-0.5">ур.</p>
                            </div>
                            <div class="text-center w-10">
                                <p class="text-sm font-black text-white leading-none">{{ $u->experience }}</p>
                                <p class="text-[9px] text-gray-600 uppercase mt-0.5">XP</p>
                            </div>
                            <div class="text-center w-12">
                                <p class="text-sm font-black leading-none" style="color:#f59e0b">{{ number_format($u->coins) }}</p>
                                <p class="text-[9px] text-gray-600 uppercase mt-0.5">коины</p>
                            </div>
                            <div class="text-center w-10">
                                <p class="text-sm font-black text-white leading-none">{{ $u->achievements->count() }}</p>
                                <p class="text-[9px] text-gray-600 uppercase mt-0.5">ачивки</p>
                            </div>
                        </div>

                        {{-- Expand button --}}
                        <button id="leader-btn-{{ $u->id }}" onclick="toggleLeader({{ $u->id }})"
                                class="flex-shrink-0 text-xs font-semibold text-gray-500 hover:text-white border border-gray-700 hover:border-gray-600 rounded-lg px-2.5 py-1.5 transition whitespace-nowrap">
                            Подробнее
                        </button>
                    </div>

                    {{-- Expanded details --}}
                    <div id="leader-det-{{ $u->id }}" class="hidden px-6 pb-4">
                        <div class="bg-gray-900/40 border border-gray-800/60 rounded-xl p-4 space-y-3 ml-9">
                            {{-- Gender pill --}}
                            @if($u->gender && in_array($u->gender, ['male','female']))
                            <div>
                                <span class="text-xs text-gray-400 bg-gray-800/60 rounded-lg px-2.5 py-1">
                                    {{ ['male'=>'Мужской','female'=>'Женский'][$u->gender] }}
                                </span>
                            </div>
                            @endif

                            {{-- About --}}
                            @if($u->about)
                            <p class="text-sm text-gray-400 leading-relaxed">{{ $u->about }}</p>
                            @else
                            <p class="text-xs text-gray-600 italic">О себе не указано</p>
                            @endif

                            {{-- Level progress bar --}}
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1.5">
                                    <span>Уровень {{ $u->level }}</span>
                                    <span>{{ $prog }} / 100 XP</span>
                                </div>
                                <div class="h-1.5 bg-gray-900 rounded-full overflow-hidden">
                                    <div class="h-full bg-orange-500 rounded-full transition-all duration-500" style="width:{{ $prog }}%"></div>
                                </div>
                                <p class="text-[10px] text-gray-600 mt-1">До уровня {{ $u->level + 1 }}: {{ 100 - $prog }} XP</p>
                            </div>

                            {{-- Achievement pills --}}
                            @if($u->achievements->count() > 0)
                            <div>
                                <p class="text-[9px] text-gray-600 uppercase tracking-widest mb-1.5">Достижения</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($u->achievements as $ach)
                                    <span class="text-[10px] text-orange-400 bg-orange-500/10 border border-orange-500/20 rounded-lg px-2 py-0.5">{{ $ach->title }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="border-t border-gray-800 px-6 py-4 flex-shrink-0">
                <button onclick="document.getElementById('modal-leaderboard').close()"
                        class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-sm font-semibold text-gray-400 hover:text-white rounded-xl transition">
                    Закрыть
                </button>
            </div>
        </div>
    </dialog>

    {{-- Achievements modal --}}
    <dialog id="modal-ach" class="bg-[#111318] border border-gray-800 rounded-2xl p-0 w-full max-w-xl shadow-2xl focus:outline-none" style="max-height:88vh;">
        <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
            <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4 flex-shrink-0">
                <h2 class="text-base font-bold text-white">Все достижения</h2>
                <button onclick="document.getElementById('modal-ach').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
            </div>
            <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4 cs divide-y divide-gray-800/60">
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
                <button onclick="closeGame()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
            </div>
            <div class="flex-1 bg-black overflow-hidden relative">
                <iframe id="game-iframe" src=""
                        class="absolute inset-0 w-full h-full border-0"
                        allow="autoplay; keyboard; fullscreen"></iframe>
                <div id="game-toast-wrap"
                     class="absolute bottom-5 right-5 z-50 flex flex-col-reverse gap-3 items-end pointer-events-none"></div>
            </div>
            <div class="border-t border-gray-800 px-4 py-3 flex-shrink-0">
                <button onclick="closeGame()"
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
        const GAME_SRC = '{{ asset('index.html') }}';
        function openGame() {
            const iframe = document.getElementById('game-iframe');
            iframe.src = GAME_SRC;
            document.getElementById('mod-game').showModal();
            setTimeout(() => iframe.focus(), 150);
        }
        function closeGame() {
            document.getElementById('mod-game').close();
            const iframe = document.getElementById('game-iframe');
            iframe.src = '';
        }

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

        function showGameToast(data) {
            const wrap = document.getElementById('game-toast-wrap');
            if (!wrap) return;
            const id = 'gt-' + Date.now();
            const el = document.createElement('div');
            el.id = id;
            el.className = 'w-72 bg-[#1a1d24] rounded-2xl p-4 shadow-2xl toast pointer-events-auto';
            el.style.border = '1px solid rgba(168,85,247,0.45)';
            el.style.boxShadow = '0 0 24px rgba(168,85,247,0.15)';
            el.innerHTML =
                `<div class="flex gap-3 items-start">` +
                    `<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(168,85,247,0.18)">` +
                        `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#c084fc">` +
                            `<path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>` +
                        `</svg>` +
                    `</div>` +
                    `<div class="flex-1 min-w-0">` +
                        `<p class="text-xs font-bold uppercase tracking-widest" style="color:#c084fc">Buzzword Blast</p>` +
                        `<p class="text-sm font-bold text-white mt-0.5">Уровень пройден!</p>` +
                        `<div class="flex items-center gap-3 mt-1">` +
                            `<span class="text-xs text-gray-400">+${data.xp} XP</span>` +
                            `<span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b">` +
                                `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0"><circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/><circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/><text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text></svg>` +
                                `+${data.coins} монета` +
                            `</span>` +
                        `</div>` +
                    `</div>` +
                    `<button onclick="(function(id){const t=document.getElementById(id);if(!t)return;t.classList.add('out');setTimeout(()=>t.remove(),300);}('${id}'))" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>` +
                `</div>`;
            wrap.prepend(el);
            setTimeout(() => {
                const t = document.getElementById(id);
                if (!t) return;
                t.classList.add('out');
                setTimeout(() => t.remove(), 300);
            }, 5000);

            const coinEl = document.getElementById('coin-count');
            if (coinEl && data.coins) {
                const cur = parseInt(coinEl.textContent.replace(/\D/g, ''), 10) || 0;
                coinEl.textContent = (cur + data.coins).toLocaleString('ru-RU');
            }
        }

        window.addEventListener('message', function(e) {
            if (!e.data || e.data.type !== 'game_level_complete') return;
            fetch('{{ route('game.reward') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => showGameToast(data));
        });

        function leaderSearch(q) {
            q = q.toLowerCase().trim();
            document.querySelectorAll('#leader-list .leader-row').forEach(row => {
                row.style.display = (!q || row.dataset.name.includes(q)) ? '' : 'none';
            });
        }

        function toggleLeader(id) {
            const det = document.getElementById('leader-det-' + id);
            const btn = document.getElementById('leader-btn-' + id);
            const isHidden = det.classList.contains('hidden');
            det.classList.toggle('hidden');
            btn.textContent = isHidden ? 'Свернуть' : 'Подробнее';
        }

        function toggleAbout(btn) {
            const wrap = document.getElementById('about-wrap');
            const open = btn.dataset.open === '1';
            if (open) {
                wrap.style.maxHeight = '4.2rem';
                btn.textContent = 'Показать ещё ↓';
                btn.dataset.open = '0';
            } else {
                wrap.style.maxHeight = wrap.scrollHeight + 'px';
                btn.textContent = 'Скрыть ↑';
                btn.dataset.open = '1';
            }
        }

        function syncAchLayout() {
            const list  = document.getElementById('ticket-list');
            const track = document.getElementById('ach-track');
            if (!list || !track) return;
            const expanded = list.scrollHeight > list.clientHeight + 1;
            track.classList.toggle('ach-expanded', expanded);
        }
        syncAchLayout();
        window.addEventListener('resize', syncAchLayout);
    </script>
</x-shop-layout>
