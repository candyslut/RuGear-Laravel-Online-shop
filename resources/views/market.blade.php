<x-shop-layout>
    <x-slot name="title">Мини-маркет | RuGear</x-slot>

    <style>
        .item-card { 
            transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
        }
        .item-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249,115,22,.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity .3s;
            pointer-events: none;
        }
        .item-card:hover::before { opacity: 1; }
        .item-card:hover { 
            border-color: rgba(249,115,22,.5);
            box-shadow: 0 0 24px rgba(249,115,22,.15), inset 0 0 24px rgba(249,115,22,.05);
            transform: translateY(-4px);
        }
        .item-card.owned { 
            border-color: rgba(249,115,22,.3);
            box-shadow: 0 0 12px rgba(249,115,22,.1);
        }
        .item-card.equipped { 
            border-color: #f97316;
            background: linear-gradient(135deg, rgba(249,115,22,.08) 0%, transparent 100%);
            box-shadow: 0 0 32px rgba(249,115,22,.25), inset 0 0 24px rgba(249,115,22,.08);
        }
        .item-card.equipped::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #f97316, transparent);
        }

        .preview-font-mono { font-family: ui-monospace,'Courier New',monospace; }
        .preview-font-serif { font-family: Georgia,'Times New Roman',serif; }
        .preview-font-cursive { font-family: cursive; }

        @keyframes rainbow-shadow {
            0%   { box-shadow: 0 0 0 3px #ff4444, 0 0 14px rgba(255,68,68,.5); }
            16%  { box-shadow: 0 0 0 3px #ff8800, 0 0 14px rgba(255,136,0,.5); }
            33%  { box-shadow: 0 0 0 3px #ffee00, 0 0 14px rgba(255,238,0,.5); }
            50%  { box-shadow: 0 0 0 3px #44ff44, 0 0 14px rgba(68,255,68,.5); }
            66%  { box-shadow: 0 0 0 3px #0099ff, 0 0 14px rgba(0,153,255,.5); }
            83%  { box-shadow: 0 0 0 3px #aa00ff, 0 0 14px rgba(170,0,255,.5); }
            100% { box-shadow: 0 0 0 3px #ff4444, 0 0 14px rgba(255,68,68,.5); }
        }
        .avatar-rainbow { animation: rainbow-shadow 2.5s linear infinite; }

        @keyframes scan-line {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }

        .market-header-glow {
            background: linear-gradient(135deg, rgba(249,115,22,.15) 0%, rgba(249,115,22,.05) 50%, transparent 100%);
        }

        .category-divider {
            background: linear-gradient(90deg, transparent, rgba(249,115,22,.3), transparent);
        }

        .btn-equipped {
            background: linear-gradient(135deg, rgba(239,68,68,.15) 0%, transparent 100%);
            border-color: rgba(239,68,68,.4);
            transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-equipped:hover {
            background: linear-gradient(135deg, rgba(239,68,68,.25) 0%, transparent 100%);
            border-color: rgba(239,68,68,.6);
            box-shadow: 0 0 16px rgba(239,68,68,.2);
        }

        .btn-own {
            background: linear-gradient(135deg, rgba(249,115,22,.15) 0%, transparent 100%);
            border-color: rgba(249,115,22,.4);
            transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-own:hover {
            background: linear-gradient(135deg, rgba(249,115,22,.25) 0%, transparent 100%);
            border-color: rgba(249,115,22,.6);
            box-shadow: 0 0 16px rgba(249,115,22,.2);
        }

        .btn-buy {
            background: linear-gradient(135deg, rgba(251,191,36,.15) 0%, transparent 100%);
            border-color: rgba(251,191,36,.4);
            transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-buy:hover {
            background: linear-gradient(135deg, rgba(251,191,36,.25) 0%, transparent 100%);
            border-color: rgba(251,191,36,.6);
            box-shadow: 0 0 16px rgba(251,191,36,.2);
        }

        .coin-badge {
            background: linear-gradient(135deg, rgba(251,191,36,.15) 0%, transparent 100%);
            border: 1px solid rgba(251,191,36,.3);
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 8px rgba(251,191,36,.1); }
            50% { box-shadow: 0 0 16px rgba(251,191,36,.2); }
        }
    </style>

    @php
        $user      = auth()->user();
        $ownedIds  = $user->shopItems()->pluck('shop_item_id')->toArray();
        $catLabels = ['font' => 'Шрифты', 'border' => 'Обводки аватарки', 'nickname_color' => 'Цвет никнейма'];
        $activeMap = [
            'font'           => $user->cosmetic_font,
            'border'         => $user->cosmetic_border,
            'nickname_color' => $user->cosmetic_nickname_color,
        ];
    @endphp

    <div class="space-y-8">

        {{-- Header Hero --}}
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 via-transparent to-purple-500/5 pointer-events-none"></div>
            <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(45deg, transparent 48%, rgba(249,115,22,.1) 49%, rgba(249,115,22,.1) 51%, transparent 52%);background-size: 3px 3px;"></div>
            
            <div class="relative bg-gradient-to-br from-[#1a1d24] via-[#161920] to-[#0f1115] border border-orange-500/30 rounded-3xl px-8 py-6 flex items-center justify-between backdrop-blur-xl" style="box-shadow: 0 0 40px rgba(249, 115, 22, 0.15), inset 0 0 40px rgba(249, 115, 22, 0.05);">
                <div class="relative">
                    <div class="flex items-baseline gap-3">
                        <h1 class="text-3xl font-black text-white">Мини-маркет</h1>
                        <span class="text-xs font-black uppercase text-orange-400 tracking-[0.15em]">V2.1</span>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">Персонализируй свой профиль косметикой</p>
                </div>

                <div class="relative flex items-center gap-4">
                    <div class="coin-badge rounded-2xl px-6 py-3 flex items-center gap-2 backdrop-blur-sm">
                        <div class="relative">
                            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text>
                            </svg>
                        </div>
                        <div>
                            <p id="market-coins" class="text-xl font-black tabular-nums" style="color:#fbbf24;">{{ number_format($user->coins) }}</p>
                            <p class="text-[10px] text-gray-500 font-bold">Монет</p>
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center gap-1 text-[10px] text-gray-500 px-3 py-2 rounded-lg bg-gray-800/30 border border-gray-700/50">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        {{ $user->level }} уровень
                    </div>
                </div>
            </div>
        </div>

        {{-- Live preview panel --}}
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-transparent pointer-events-none rounded-2xl"></div>
            <div class="relative bg-gradient-to-br from-[#1a1d24] via-[#161920] to-[#0f1115] border border-blue-500/20 rounded-2xl px-6 py-5 backdrop-blur-xl">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-blue-400 uppercase tracking-widest font-black">Предпросмотр вашего профиля</p>
                    <span class="text-[10px] font-mono text-gray-600">LIVE</span>
                </div>
                <div class="flex items-center gap-4">
                    <div id="preview-avatar"
                         class="w-14 h-14 rounded-full flex-shrink-0 flex items-center justify-center font-black text-lg overflow-hidden bg-orange-500 text-black ring-2 ring-orange-500/50
                                {{ $user->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                         style="{{ $user->cosmetic_border && $user->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$user->cosmetic_border.';' : '' }}">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <p id="preview-name"
                           class="text-lg font-black leading-none"
                           style="color:{{ $user->cosmetic_nickname_color ?? '#ffffff' }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}">
                            {{ $user->name }}
                        </p>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-400"></span>
                            Уровень <span class="text-orange-400 font-bold">{{ $user->level }}</span> · 
                            <span class="text-blue-400">{{ $user->experience }} XP</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sections --}}
        @foreach($items as $category => $group)
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div class="h-px flex-1 category-divider"></div>
                <span class="text-xs font-black uppercase tracking-widest text-orange-400 px-4 py-2 bg-gradient-to-r from-orange-500/10 to-transparent rounded-lg border border-orange-500/20">
                    {{ $catLabels[$category] ?? $category }}
                </span>
                <div class="h-px flex-1 category-divider"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($group as $item)
                @php
                    $isOwned    = in_array($item->id, $ownedIds);
                    $isEquipped = $activeMap[$item->category] === $item->css_value;
                @endphp
                <div class="item-card bg-gradient-to-br from-[#1a1d24] to-[#0f1115] border border-gray-700 rounded-2xl p-4 flex flex-col gap-3 h-full
                            {{ $isEquipped ? 'equipped' : ($isOwned ? 'owned' : '') }}"
                     id="card-{{ $item->id }}">

                    {{-- Preview --}}
                    <div class="relative h-20 rounded-xl bg-gradient-to-br from-gray-800/40 to-gray-900/60 flex items-center justify-center overflow-hidden border border-gray-700/30">
                        <div class="absolute inset-0 opacity-30" style="background: linear-gradient(135deg, rgba(249,115,22,.1) 0%, transparent 100%);"></div>
                        <div class="relative">
                            @if($category === 'font')
                                <span class="text-lg font-bold text-white preview-font-{{ str_replace('font_','', $item->slug) }}">
                                    Текст
                                </span>
                            @elseif($category === 'border')
                                @if($item->css_value === 'rainbow')
                                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center text-black font-black avatar-rainbow">А</div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center text-black font-black"
                                         style="box-shadow:{{ $item->css_value }};">А</div>
                                @endif
                            @elseif($category === 'nickname_color')
                                <span class="text-xl font-black" style="color:{{ $item->css_value }};">Никнейм</span>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="text-sm font-bold text-white leading-tight">{{ $item->name }}</p>
                            @if($isEquipped)
                                <span class="text-[8px] font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/20 border border-emerald-500/40 rounded-md px-2 py-1 flex-shrink-0 whitespace-nowrap">Активно</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $item->description }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-1">
                        @if($isEquipped)
                            <button onclick="marketUnequip('{{ $item->category }}')"
                                    class="btn-equipped w-full py-2.5 rounded-xl text-xs font-semibold transition"
                                    style="color:#f87171;">
                                Снять
                            </button>
                        @elseif($isOwned)
                            <button onclick="marketEquip({{ $item->id }}, '{{ $item->category }}', '{{ addslashes($item->css_value) }}')"
                                    class="btn-own w-full py-2.5 rounded-xl text-xs font-semibold transition"
                                    style="color:#f97316;">
                                Надеть
                            </button>
                        @else
                            <button onclick="marketBuy({{ $item->id }}, {{ $item->price }}, '{{ $item->category }}', '{{ addslashes($item->css_value) }}')"
                                    class="btn-buy w-full py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1"
                                    style="color:#fbbf24;">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/><circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/></svg>
                                {{ number_format($item->price) }}
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- Toast --}}
    <div id="market-toast" class="fixed bottom-6 right-6 z-50 pointer-events-none" style="min-width:260px;max-width:340px;"></div>

    <script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function marketToast(msg, ok = true) {
        const t = document.getElementById('market-toast');
        const color = ok ? 'rgba(74, 222, 128, 0.4)' : 'rgba(248, 113, 113, 0.4)';
        const textColor = ok ? '#4ade80' : '#f87171';
        t.innerHTML = `<div style="
            background:linear-gradient(135deg, rgba(17,19,24,.95) 0%, rgba(20,23,29,.95) 100%);
            border:1px solid ${color};
            border-radius:14px;
            padding:14px 18px;
            color:${textColor};
            font-size:13px;
            font-weight:600;
            box-shadow:0 8px 32px rgba(0,0,0,.5), 0 0 20px ${color.replace('0.4', '0.2')};
            animation:slideIn .3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;">
            <span>${msg}</span>
        </div>
        <style>
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        </style>`;
        setTimeout(() => { t.innerHTML = ''; }, 3500);
    }

    function applyPreview(category, css) {
        const avatar = document.getElementById('preview-avatar');
        const name   = document.getElementById('preview-name');
        if (category === 'border') {
            avatar.classList.remove('avatar-rainbow');
            avatar.style.boxShadow = '';
            if (css === 'rainbow') avatar.classList.add('avatar-rainbow');
            else avatar.style.boxShadow = css;
        } else if (category === 'nickname_color') {
            name.style.color = css;
        } else if (category === 'font') {
            name.style.fontFamily = css;
        }
    }

    function clearPreview(category) {
        const avatar = document.getElementById('preview-avatar');
        const name   = document.getElementById('preview-name');
        if (category === 'border') {
            avatar.classList.remove('avatar-rainbow');
            avatar.style.boxShadow = '';
        } else if (category === 'nickname_color') {
            name.style.color = '#ffffff';
        } else if (category === 'font') {
            name.style.fontFamily = '';
        }
    }

    async function marketBuy(id, price, category, css) {
        const coinsEl = document.getElementById('market-coins');
        const current = parseInt(coinsEl.textContent.replace(/\s/g, '').replace(',', ''));
        if (current < price) {
            marketToast('Недостаточно монет!', false);
            return;
        }

        const res = await fetch(`/market/buy/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (!res.ok) {
            marketToast(data.error === 'not_enough_coins' ? 'Недостаточно монет!' : 'Ошибка транзакции', false);
            return;
        }

        // Update coin counters
        const formatted = data.coins.toLocaleString('ru-RU');
        coinsEl.textContent = formatted;
        const globalCoin = document.getElementById('coin-count');
        if (globalCoin) globalCoin.textContent = formatted;

        applyPreview(category, css);
        marketToast('Куплено и надето!');
        setTimeout(() => location.reload(), 600);
    }

    async function marketEquip(id, category, css) {
        const res = await fetch(`/market/equip/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });
        if (!res.ok) { marketToast('Ошибка при экипировке', false); return; }

        applyPreview(category, css);
        marketToast('Экипировано успешно!');
        setTimeout(() => location.reload(), 600);
    }

    async function marketUnequip(category) {
        const res = await fetch('/market/unequip', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ category })
        });
        if (!res.ok) { marketToast('Ошибка при снятии', false); return; }

        clearPreview(category);
        marketToast('Снято с профиля');
        setTimeout(() => location.reload(), 600);
    }
    </script>
</x-shop-layout>
