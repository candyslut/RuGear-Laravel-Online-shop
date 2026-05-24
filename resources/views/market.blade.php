<x-shop-layout>
    <x-slot name="title">Мини-маркет | RuGear</x-slot>

    <style>
        .item-card { transition: border-color .2s, box-shadow .2s; }
        .item-card:hover { border-color: rgba(249,115,22,.35); }
        .item-card.owned { border-color: rgba(249,115,22,.2); }
        .item-card.equipped { border-color: #f97316; box-shadow: 0 0 18px rgba(249,115,22,.2); }

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

        {{-- Header --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl px-6 py-5 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-black text-white">Мини-маркет</h1>
                <p class="text-sm text-gray-500 mt-0.5">Трать монетки на косметику</p>
            </div>
            <div class="flex items-center gap-2 bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5">
                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                    <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                    <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text>
                </svg>
                <span id="market-coins" class="text-base font-black tabular-nums" style="color:#f59e0b;">{{ number_format($user->coins) }}</span>
                <span class="text-xs text-gray-500 ml-0.5">монет</span>
            </div>
        </div>

        {{-- Live preview panel --}}
        <div class="bg-[#161920] border border-gray-800 rounded-2xl px-6 py-5">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-4">Предпросмотр вашего профиля</p>
            <div class="flex items-center gap-4">
                <div id="preview-avatar"
                     class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center font-black text-lg overflow-hidden bg-orange-500 text-black
                            {{ $user->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                     style="{{ $user->cosmetic_border && $user->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$user->cosmetic_border.';' : '' }}">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <p id="preview-name"
                       class="text-base font-black leading-none"
                       style="color:{{ $user->cosmetic_nickname_color ?? '#ffffff' }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}">
                        {{ $user->name }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Уровень {{ $user->level }} · {{ $user->experience }} XP</p>
                </div>
            </div>
        </div>

        {{-- Sections --}}
        @foreach($items as $category => $group)
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px flex-1 bg-gray-800"></div>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500 px-3">{{ $catLabels[$category] ?? $category }}</span>
                <div class="h-px flex-1 bg-gray-800"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($group as $item)
                @php
                    $isOwned    = in_array($item->id, $ownedIds);
                    $isEquipped = $activeMap[$item->category] === $item->css_value;
                @endphp
                <div class="item-card bg-[#161920] border border-gray-800 rounded-2xl p-4 flex flex-col gap-3
                            {{ $isEquipped ? 'equipped' : ($isOwned ? 'owned' : '') }}"
                     id="card-{{ $item->id }}">

                    {{-- Preview --}}
                    <div class="h-16 rounded-xl bg-gray-900/60 flex items-center justify-center overflow-hidden">
                        @if($category === 'font')
                            <span class="text-lg font-bold text-white preview-font-{{ str_replace('font_','', $item->slug) }}">
                                Текст
                            </span>
                        @elseif($category === 'border')
                            @if($item->css_value === 'rainbow')
                                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-black font-black avatar-rainbow">А</div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-black font-black"
                                     style="box-shadow:{{ $item->css_value }};">А</div>
                            @endif
                        @elseif($category === 'nickname_color')
                            <span class="text-lg font-black" style="color:{{ $item->css_value }};">Никнейм</span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-1">
                            <p class="text-sm font-bold text-white leading-tight">{{ $item->name }}</p>
                            @if($isEquipped)
                                <span class="text-[9px] font-bold uppercase tracking-wider text-orange-500 bg-orange-500/10 border border-orange-500/30 rounded-md px-1.5 py-0.5 flex-shrink-0">Надет</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $item->description }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-1.5">
                        @if($isEquipped)
                            <button onclick="marketUnequip('{{ $item->category }}')"
                                    class="w-full py-2 rounded-xl text-xs font-semibold border border-gray-700 text-gray-400 hover:text-white hover:border-gray-600 transition">
                                Снять
                            </button>
                        @elseif($isOwned)
                            <button onclick="marketEquip({{ $item->id }}, '{{ $item->category }}', '{{ addslashes($item->css_value) }}')"
                                    class="w-full py-2 rounded-xl text-xs font-semibold bg-orange-500/10 border border-orange-500/30 text-orange-400 hover:bg-orange-500/20 transition">
                                Надеть
                            </button>
                        @else
                            <button onclick="marketBuy({{ $item->id }}, {{ $item->price }}, '{{ $item->category }}', '{{ addslashes($item->css_value) }}')"
                                    class="w-full py-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition"
                                    style="background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);color:#fbbf24;"
                                    onmouseover="this.style.background='rgba(251,191,36,.16)'"
                                    onmouseout="this.style.background='rgba(251,191,36,.08)'">
                                <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                    <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text>
                                </svg>
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
        t.innerHTML = `<div style="
            background:rgba(17,19,24,.95);
            border:1px solid ${ok ? 'rgba(249,115,22,.4)' : 'rgba(239,68,68,.4)'};
            border-radius:14px;padding:14px 18px;
            color:${ok ? '#fb923c' : '#f87171'};
            font-size:13px;font-weight:600;
            box-shadow:0 8px 32px rgba(0,0,0,.5);
            animation:tin .3s ease-out forwards;">
            ${msg}
        </div>`;
        setTimeout(() => { t.innerHTML = ''; }, 3000);
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

    function setCardEquipped(itemId, category) {
        // Remove equipped state from all cards in same category
        document.querySelectorAll('.item-card').forEach(card => {
            const btn = card.querySelector('button');
            // We'll just reload to sync state cleanly
        });
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
            marketToast(data.error === 'not_enough_coins' ? 'Недостаточно монет!' : 'Ошибка', false);
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
        if (!res.ok) { marketToast('Ошибка', false); return; }

        applyPreview(category, css);
        marketToast('Надето!');
        setTimeout(() => location.reload(), 600);
    }

    async function marketUnequip(category) {
        const res = await fetch('/market/unequip', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ category })
        });
        if (!res.ok) { marketToast('Ошибка', false); return; }

        clearPreview(category);
        marketToast('Снято!');
        setTimeout(() => location.reload(), 600);
    }
    </script>
</x-shop-layout>
