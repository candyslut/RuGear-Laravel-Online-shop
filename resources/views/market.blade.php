<x-shop-layout>
    <x-slot name="title">Мини-маркет | RuGear</x-slot>

    <style>
        /* ════════════════════════════════════════════════════════════
           МИНИ-МАРКЕТ · «Витрина-display case» (theme-aware)
           Вместо сетки карточек — большой спотлайт-инспектор на каждую
           категорию + лоток-селектор со свотчами. Цвет редкости в --rgb.
           ──────────────────────────────────────────────────────────── */

        .mk-panel { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 1.25rem; }

        /* Hero */
        .mk-hero {
            position: relative;
            overflow: hidden;
            background: var(--bg-secondary);
            border: 1px solid rgba(249, 115, 22, .30);
            border-radius: 1.5rem;
        }
        .mk-hero::before { content: ''; position: absolute; inset: 0 0 auto 0; height: 2px; background: #f97316; opacity: .55; }
        .mk-coin { color: #fbbf24; }
        [data-theme="light"] .mk-coin { color: #b45309; }
        .mk-chip { background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-secondary); }

        /* Sidebar preview / bars / legend */
        .mk-preview-art { background: var(--bg-tertiary); border: 1px solid var(--border-color); }
        .mk-bar { height: .55rem; border-radius: 999px; background: var(--bg-tertiary); border: 1px solid var(--border-color); overflow: hidden; }
        .mk-bar > span { display: block; height: 100%; border-radius: 999px; background: #f97316; transition: width .8s cubic-bezier(.34, 1.56, .64, 1); }
        .mk-bar--mini > span { background: rgb(var(--rgb)); box-shadow: none; }
        .mk-legend-dot { width: .6rem; height: .6rem; border-radius: 999px; background: rgb(var(--rgb)); box-shadow: 0 0 6px rgba(var(--rgb), .6); }

        /* ── SPOTLIGHT (большой инспектор) ── */
        .spot {
            position: relative;
            display: flex;
            gap: 1.75rem;
            align-items: center;
            padding: 1.75rem;
            border-radius: 1.5rem;
            min-height: 12.5rem;
            background: var(--bg-secondary);
            border: 1px solid rgba(var(--rgb), .42);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .18);
            overflow: hidden;
            transition: border-color .35s ease, box-shadow .35s ease, background .35s ease;
        }
        .spot__art {
            position: relative;
            z-index: 1;
            flex-shrink: 0;
            width: 9.5rem;
            height: 9.5rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-tertiary);
            border: 1px solid rgba(var(--rgb), .28);
        }
        .spot__info { position: relative; z-index: 1; flex: 1; min-width: 0; display: flex; flex-direction: column; gap: .55rem; }
        .spot__meta { display: flex; align-items: center; gap: .55rem; flex-wrap: wrap; }
        .spot__tier { font-size: .62rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; color: rgb(var(--rgb)); background: rgba(var(--rgb), .14); border: 1px solid rgba(var(--rgb), .4); padding: .24rem .6rem; border-radius: .5rem; }
        .spot__stars { font-size: .95rem; letter-spacing: 2px; line-height: 1; }
        .spot__status { font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; padding: .22rem .5rem; border-radius: .45rem; border: 1px solid transparent; }
        .spot__status--active { background: rgba(16, 185, 129, .18); color: #10b981; border-color: rgba(16, 185, 129, .4); }
        .spot__status--owned { background: rgba(var(--rgb), .16); color: rgb(var(--rgb)); border-color: rgba(var(--rgb), .4); }
        .spot__name { font-size: 1.5rem; font-weight: 900; color: var(--text-primary); line-height: 1.05; }
        .spot__desc { font-size: .9rem; color: var(--text-secondary); line-height: 1.5; }
        .spot__action { margin-top: .5rem; }

        @media (max-width: 640px) {
            .spot { flex-direction: column; text-align: center; align-items: center; padding: 1.4rem; }
            .spot__meta { justify-content: center; }
        }

        /* ── Лоток-селектор (свотчи) ── */
        .orb-rail { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1rem; }
        .orb {
            position: relative;
            width: 3.4rem;
            height: 3.4rem;
            border-radius: .9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }
        .orb:hover { transform: translateY(-3px); border-color: rgb(var(--rgb)); }
        .orb.is-active { border-color: rgb(var(--rgb)); box-shadow: 0 0 0 2px rgb(var(--rgb)), 0 8px 18px rgba(var(--rgb), .28); }
        .orb__dot { width: 1.65rem; height: 1.65rem; border-radius: 999px; display: block; }
        .orb__rainbow { padding: 2px; border-radius: 999px; display: flex; background: conic-gradient(from 0deg, #ff4444, #ff8800, #ffee00, #44ff44, #0099ff, #aa00ff, #ff4444); }
        .orb__rainbow > span { width: 1.25rem; height: 1.25rem; border-radius: 999px; background: var(--bg-secondary); display: block; }
        .orb__check { position: absolute; top: -5px; right: -5px; width: 1.05rem; height: 1.05rem; border-radius: 999px; background: #10b981; color: #fff; font-size: .58rem; display: flex; align-items: center; justify-content: center; border: 2px solid var(--bg-primary); }
        .orb__own { position: absolute; bottom: 4px; right: 4px; width: .42rem; height: .42rem; border-radius: 999px; background: rgb(var(--rgb)); }

        /* Кнопки действий */
        .rbtn { padding: .7rem 1.4rem; border-radius: .8rem; font-size: .82rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: 1px solid transparent; transition: all .2s ease; }
        .rbtn--buy { background: rgba(var(--rgb), .16); border-color: rgba(var(--rgb), .5); color: rgb(var(--rgb)); }
        .rbtn--buy:hover { background: rgba(var(--rgb), .26); box-shadow: 0 6px 16px rgba(var(--rgb), .28); transform: translateY(-1px); }
        .rbtn--equip { background: rgba(249, 115, 22, .16); border-color: rgba(249, 115, 22, .5); color: #f97316; }
        .rbtn--equip:hover { background: rgba(249, 115, 22, .26); box-shadow: 0 6px 16px rgba(249, 115, 22, .24); transform: translateY(-1px); }
        .rbtn--unequip { background: rgba(239, 68, 68, .14); border-color: rgba(239, 68, 68, .45); color: #ef4444; }
        .rbtn--unequip:hover { background: rgba(239, 68, 68, .22); transform: translateY(-1px); }

        @keyframes rainbow-shadow {
            0%   { box-shadow: 0 0 0 3px #ff4444, 0 0 14px rgba(255, 68, 68, .5); }
            16%  { box-shadow: 0 0 0 3px #ff8800, 0 0 14px rgba(255, 136, 0, .5); }
            33%  { box-shadow: 0 0 0 3px #ffee00, 0 0 14px rgba(255, 238, 0, .5); }
            50%  { box-shadow: 0 0 0 3px #44ff44, 0 0 14px rgba(68, 255, 68, .5); }
            66%  { box-shadow: 0 0 0 3px #0099ff, 0 0 14px rgba(0, 153, 255, .5); }
            83%  { box-shadow: 0 0 0 3px #aa00ff, 0 0 14px rgba(170, 0, 255, .5); }
            100% { box-shadow: 0 0 0 3px #ff4444, 0 0 14px rgba(255, 68, 68, .5); }
        }
        .avatar-rainbow { animation: rainbow-shadow 2.5s linear infinite; }
        .spot-rainbow-ring { padding: 5px; border-radius: 999px; background: conic-gradient(from 0deg, #ff4444, #ff8800, #ffee00, #44ff44, #0099ff, #aa00ff, #ff4444); display: flex; }

        @keyframes mkToastIn { from { transform: translateX(420px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes spotIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .spot__info, .spot__art { animation: spotIn .3s ease both; }

        /* ════════════════════════════════════════════════════════════
           СТИКЕРПАКИ · «в разработке» (teaser-секция)
           Функции стикеров ещё нет — это витрина запланированного
           геймплея: gacha-вскрытие паков, осколки за дубликаты,
           ежедневный пак, коллекционные награды.
           ──────────────────────────────────────────────────────────── */
        .stk {
            position: relative;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
        }

        .stk-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .6rem; font-weight: 900; letter-spacing: .16em; text-transform: uppercase;
            color: #c084fc; background: rgba(168,85,247,.14);
            border: 1px solid rgba(168,85,247,.45); padding: .3rem .65rem; border-radius: .55rem;
        }
        .stk-badge .dot { width: .42rem; height: .42rem; border-radius: 999px; background: #c084fc; }

        /* ── Стикерпаки · ровная «полка» карточек ──
              Чисто и сдержанно: нейтральные рамки, без сияний, градиентов и
              лишних обводок. Редкость показываем самоцветом и звёздами. */
        .pkx-rail { display: grid; grid-template-columns: repeat(auto-fill, minmax(13.5rem, 1fr)); gap: 1rem; }

        .pkx {
            --rgb: 148,163,184;
            display: flex; flex-direction: column; gap: .75rem;
            padding: 1.1rem; border-radius: 1.1rem;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            transition: transform .18s ease, border-color .18s ease;
        }
        .pkx:hover { transform: translateY(-2px); border-color: var(--text-tertiary); }

        /* сцена-превью — ровный нейтральный фон, без градиента */
        .pkx__stage {
            height: 6.5rem; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border-radius: .85rem; background: var(--bg-secondary);
        }
        .pkx__stage canvas, .pkx__stage video, .pkx__stage img { display: block; }

        /* самоцвет редкости (без свечения) */
        .gem { display: inline-flex; align-items: center; gap: .4rem; font-size: .58rem; font-weight: 900;
            letter-spacing: .12em; text-transform: uppercase; color: rgb(var(--rgb)); }
        .gem::before { content: ''; width: .55rem; height: .55rem; border-radius: 2px; transform: rotate(45deg); background: rgb(var(--rgb)); }

        /* Fixed 2-line height so the gem/stars row and the buy/owned footer sit on
           the same Y across every card, regardless of how long the name is. */
        .pkx__name {
            font-size: .98rem; font-weight: 800; color: var(--text-primary); line-height: 1.15;
            min-height: 2.3rem;
            display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden;
        }

        /* ряд: звёзды слева, ярлык анимации справа — в потоке, без перекрытий */
        .pkx__metarow { display: flex; align-items: center; gap: .5rem; min-height: 1.1rem; }
        .pkx__stars { font-size: .8rem; letter-spacing: 1px; line-height: 1; }
        .anim-pill { display: inline-flex; align-items: center; gap: .3rem; margin-left: auto;
            font-size: .56rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; color: var(--text-secondary); }
        .anim-pill .dot { width: .36rem; height: .36rem; border-radius: 999px; background: #10b981; }

        /* подвал */
        .pkx__foot { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: .6rem; padding-top: .25rem; }
        .pkx__price { display: inline-flex; align-items: center; gap: .35rem; font-size: .9rem; font-weight: 900; color: #fbbf24; }
        [data-theme="light"] .pkx__price { color: #b45309; }
        .pkx__owned { display: inline-flex; align-items: center; gap: .3rem; font-size: .62rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .06em; color: #10b981; }
        .pkx__count { font-size: .64rem; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: .05em; }

        .pkx-buy { padding: .5rem 1.05rem; border-radius: .65rem; font-size: .76rem; font-weight: 800; cursor: pointer;
            white-space: nowrap; border: 1px solid var(--border-color); background: var(--bg-secondary); color: var(--text-primary);
            transition: border-color .18s ease, color .18s ease; }
        .pkx-buy:hover { border-color: #f97316; color: #f97316; }

        /* превью-кнопка пака (вся сцена кликабельна) */
        .pkx__stage--btn { position: relative; width: 100%; border: none; cursor: pointer; padding: 0;
            transition: background .18s ease; }
        .pkx__stage--btn:hover { background: var(--bg-tertiary); }
        .pkx__peek { position: absolute; bottom: .3rem; left: 50%; transform: translateX(-50%);
            font-size: .54rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
            color: rgb(var(--rgb)); background: rgba(var(--rgb), .14); border: 1px solid rgba(var(--rgb), .35);
            padding: .12rem .45rem; border-radius: .4rem; opacity: 0; transition: opacity .18s ease; pointer-events: none; }
        .pkx__stage--btn:hover .pkx__peek { opacity: 1; }

        /* ── Модалка-предпросмотр стикерпака ── */
        .pkm { position: fixed; inset: 0; z-index: 60; display: none; align-items: center; justify-content: center; padding: 1rem; }
        .pkm.is-open { display: flex; }
        .pkm__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(2px); }
        .pkm__panel { position: relative; z-index: 1; width: min(40rem, 100%); max-height: 86vh; display: flex; flex-direction: column;
            background: var(--bg-secondary); border: 1px solid rgba(var(--rgb), .4); border-radius: 1.25rem;
            box-shadow: 0 24px 60px rgba(0,0,0,.45); overflow: hidden; animation: spotIn .25s ease both; }
        .pkm__head { display: flex; align-items: flex-start; gap: 1rem; padding: 1.25rem 1.4rem; border-bottom: 1px solid var(--border-color); }
        .pkm__close { margin-left: auto; flex-shrink: 0; width: 2rem; height: 2rem; border-radius: .6rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem; line-height: 1;
            color: var(--text-secondary); background: var(--bg-tertiary); border: 1px solid var(--border-color); transition: all .18s ease; }
        .pkm__close:hover { color: #ef4444; border-color: rgba(239,68,68,.45); }
        .pkm__title { font-size: 1.3rem; font-weight: 900; color: var(--text-primary); line-height: 1.1; }
        .pkm__grid { padding: 1.1rem 1.4rem; overflow-y: auto; display: grid; gap: .5rem;
            grid-template-columns: repeat(auto-fill, minmax(4.5rem, 1fr)); }
        .pkm__cell { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; padding: .35rem;
            border-radius: .7rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); }
        .pkm__cell canvas, .pkm__cell video, .pkm__cell img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .pkm__foot { display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            padding: 1.1rem 1.4rem; border-top: 1px solid var(--border-color); }
    </style>

    @php
        $user     = auth()->user();
        $ownedIds = $user->shopItems()->pluck('shop_item_id')->toArray();
        $catLabels = ['border' => 'Обводки аватарки', 'nickname_color' => 'Цвет никнейма'];
        $activeMap = [
            'border'         => $user->cosmetic_border,
            'nickname_color' => $user->cosmetic_nickname_color,
        ];

        $rarityMeta = [
            'common'    => ['l' => 'Обычный',     'rgb' => '148,163,184', 'stars' => 1],
            'rare'      => ['l' => 'Редкий',      'rgb' => '59,130,246',  'stars' => 2],
            'epic'      => ['l' => 'Эпический',   'rgb' => '168,85,247',  'stars' => 3],
            'mythic'    => ['l' => 'Мифический',  'rgb' => '236,72,153',  'stars' => 4],
            'legendary' => ['l' => 'Легендарный', 'rgb' => '245,158,11',  'stars' => 5],
        ];
        $rarityOf = function ($item) {
            if ($item->css_value === 'rainbow') return 'mythic';
            $p = (int) $item->price;
            return $p >= 250 ? 'legendary' : ($p >= 160 ? 'mythic' : ($p >= 120 ? 'epic' : ($p >= 80 ? 'rare' : 'common')));
        };

        // Статистика коллекции
        $catStats = [];
        foreach ($items as $cat => $group) {
            $own = 0;
            foreach ($group as $it) { if (in_array($it->id, $ownedIds)) $own++; }
            $catStats[$cat] = ['total' => count($group), 'owned' => $own];
        }

        // Стикерпаки тоже часть коллекции.
        $packTotal = $stickerPacks->count();
        $packOwned = $stickerPacks->filter(fn ($p) => $p['owned'])->count();

        $totalCount = array_sum(array_column($catStats, 'total')) + $packTotal;
        $ownedCount = array_sum(array_column($catStats, 'owned')) + $packOwned;
        $pct = $totalCount ? (int) round($ownedCount / $totalCount * 100) : 0;
        $packPct = $packTotal ? (int) round($packOwned / $packTotal * 100) : 0;

        // Данные паков для JS (модалка-предпросмотр + живое обновление коллекции),
        // ключ — slug.
        $packsForJs = [];
        foreach ($stickerPacks as $p) {
            $rk = $p['rarity'] ?? 'common';
            $m  = $rarityMeta[$rk] ?? $rarityMeta['common'];
            $packsForJs[$p['slug']] = [
                'slug'        => $p['slug'],
                'name'        => $p['name'],
                'rarityLabel' => $m['l'],
                'rgb'         => $m['rgb'],
                'stars'       => $m['stars'],
                'animated'    => !empty($p['animated']),
                'owned'       => (bool) $p['owned'],
                'price'       => (int) ($p['price'] ?? 0),
                'shopItemId'  => $p['shop_item_id'],
                'count'       => (int) $p['count'],
                'stickers'    => $p['stickers'] ?? [],
            ];
        }

        // Данные для JS-спотлайта
        $catData = [];
        foreach ($items as $cat => $group) {
            foreach ($group as $it) {
                $r = $rarityOf($it);
                $m = $rarityMeta[$r];
                $catData[$cat][] = [
                    'id'          => $it->id,
                    'name'        => $it->name,
                    'desc'        => $it->description,
                    'price'       => (int) $it->price,
                    'css'         => $it->css_value,
                    'category'    => $it->category,
                    'rarityLabel' => $m['l'],
                    'rgb'         => $m['rgb'],
                    'stars'       => $m['stars'],
                    'mythic'      => $r === 'mythic',
                    'owned'       => in_array($it->id, $ownedIds),
                    'equipped'    => ($activeMap[$it->category] ?? null) === $it->css_value,
                ];
            }
        }
    @endphp

    <div class="space-y-6">

        {{-- ═══════════════════ HERO ═══════════════════ --}}
        <div class="mk-hero px-6 sm:px-8 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <h1 class="text-3xl font-black text-[var(--text-primary)]">Мини-маркет</h1>
                <p class="text-sm text-[var(--text-secondary)] mt-1">Добывай редкую косметику и собирай коллекцию профиля</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="mk-chip rounded-2xl px-5 py-3 flex items-center gap-2.5">
                    <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5" />
                        <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5" />
                        <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text>
                    </svg>
                    <div>
                        <p id="market-coins" class="mk-coin text-xl font-black tabular-nums leading-none">{{ number_format($user->coins) }}</p>
                        <p class="text-[10px] text-[var(--text-tertiary)] font-bold uppercase tracking-wider mt-0.5">Монет</p>
                    </div>
                </div>
                <div class="mk-chip rounded-2xl px-4 py-3 text-center">
                    <p class="text-xl font-black text-orange-500 leading-none">{{ $user->level }}</p>
                    <p class="text-[10px] text-[var(--text-tertiary)] font-bold uppercase tracking-wider mt-0.5">Уровень</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════ SIDEBAR + ВИТРИНА ═══════════════════ --}}
        <div class="grid lg:grid-cols-[330px_1fr] gap-6 items-start">

            {{-- ── Sticky sidebar ── --}}
            <aside class="lg:sticky lg:top-24 space-y-5">

                <div class="mk-panel p-5">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs text-blue-400 uppercase tracking-widest font-black">Предпросмотр</p>
                        <span class="text-[10px] font-mono text-[var(--text-tertiary)] flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>LIVE
                        </span>
                    </div>
                    <div class="mk-preview-art rounded-xl px-4 py-6 flex flex-col items-center text-center gap-3">
                        <div id="preview-avatar"
                             class="w-16 h-16 rounded-full flex-shrink-0 flex items-center justify-center font-black text-xl overflow-hidden bg-orange-500 text-black ring-2 ring-orange-500/40 {{ $user->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                             style="{{ $user->cosmetic_border && $user->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$user->cosmetic_border.';' : '' }}">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <p id="preview-name" class="text-lg font-black leading-tight"
                               @if($user->cosmetic_nickname_color) style="color:{{ $user->cosmetic_nickname_color }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}" @endif>
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-[var(--text-secondary)] mt-1.5 flex items-center justify-center gap-1.5">
                                Уровень <span class="text-orange-400 font-bold">{{ $user->level }}</span> ·
                                <span class="text-blue-400 font-bold">{{ number_format($user->experience) }} XP</span>
                            </p>
                        </div>
                    </div>
                    <p class="text-[11px] text-[var(--text-tertiary)] text-center mt-3">Нажми на свотч, чтобы примерить</p>
                </div>

                <div class="mk-panel p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs uppercase tracking-widest font-black text-[var(--text-secondary)]">Коллекция</p>
                        <span data-collection-fraction class="text-xs font-black {{ $pct === 100 ? 'text-emerald-400' : 'text-orange-400' }}">{{ $ownedCount }}/{{ $totalCount }}</span>
                    </div>
                    <div class="mk-bar mb-1"><span data-collection-bar style="width: {{ $pct }}%"></span></div>
                    <p class="text-[11px] text-[var(--text-tertiary)] mb-4"><span data-collection-pct>{{ $pct }}</span>% собрано</p>
                    <div class="space-y-3">
                        @foreach($catStats as $cat => $st)
                        @php $cp = $st['total'] ? (int) round($st['owned'] / $st['total'] * 100) : 0; @endphp
                        <div style="--rgb: 249,115,22;" data-cat-stat="{{ $cat }}">
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-[var(--text-secondary)] font-semibold">{{ $catLabels[$cat] ?? $cat }}</span>
                                <span data-catstat-fraction class="text-[var(--text-tertiary)] font-bold tabular-nums">{{ $st['owned'] }}/{{ $st['total'] }}</span>
                            </div>
                            <div class="mk-bar mk-bar--mini"><span data-catstat-bar style="width: {{ $cp }}%"></span></div>
                        </div>
                        @endforeach

                        {{-- Стикерпаки --}}
                        <div style="--rgb: 168,85,247;" data-pack-stat>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-[var(--text-secondary)] font-semibold">Стикерпаки</span>
                                <span data-packstat-fraction class="text-[var(--text-tertiary)] font-bold tabular-nums">{{ $packOwned }}/{{ $packTotal }}</span>
                            </div>
                            <div class="mk-bar mk-bar--mini"><span data-packstat-bar style="width: {{ $packPct }}%"></span></div>
                        </div>
                    </div>
                    <div data-collection-trophy class="mt-4 text-center text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 rounded-xl py-2.5 {{ $pct === 100 ? '' : 'hidden' }}">Коллекция собрана полностью!</div>
                </div>

                <div class="mk-panel p-5">
                    <p class="text-xs uppercase tracking-widest font-black text-[var(--text-secondary)] mb-3">Редкость</p>
                    <div class="grid grid-cols-2 gap-y-2 gap-x-3">
                        @foreach($rarityMeta as $key => $meta)
                        <div class="flex items-center gap-2" style="--rgb: {{ $meta['rgb'] }};">
                            <span class="mk-legend-dot flex-shrink-0"></span>
                            <span class="text-[11px] font-semibold" style="color: rgb({{ $meta['rgb'] }});">{{ $meta['l'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- ── Витрина: спотлайт + лоток ── --}}
            <div class="space-y-9 min-w-0">
                @foreach($items as $category => $group)
                <section data-cat="{{ $category }}">
                    <div class="flex items-center gap-3 mb-4">
                        <h2 class="text-sm font-black uppercase tracking-widest text-[var(--text-primary)]">{{ $catLabels[$category] ?? $category }}</h2>
                        <span class="text-[11px] font-bold text-[var(--text-tertiary)] bg-[var(--bg-tertiary)] border border-[var(--border-color)] rounded-full px-2.5 py-0.5"
                              data-section-count="{{ $category }}">
                            {{ $catStats[$category]['owned'] }}/{{ $catStats[$category]['total'] }}
                        </span>
                        <div class="h-px flex-1 bg-[var(--border-color)]"></div>
                    </div>

                    {{-- Спотлайт (наполняется JS) --}}
                    <div class="spot" id="spot-{{ $category }}"></div>

                    {{-- Лоток-селектор --}}
                    <div class="orb-rail" data-cat="{{ $category }}">
                        @foreach($group as $i => $item)
                        @php
                            $r = $rarityOf($item);
                            $m = $rarityMeta[$r];
                            $isOwned = in_array($item->id, $ownedIds);
                            $isEquipped = ($activeMap[$item->category] ?? null) === $item->css_value;
                            $orbColor = $category === 'nickname_color'
                                ? $item->css_value
                                : (preg_match('/#[0-9a-fA-F]{6}/', $item->css_value, $mm) ? $mm[0] : '#f97316');
                        @endphp
                        <button type="button" class="orb {{ $isEquipped ? 'is-active' : '' }}"
                                data-cat="{{ $category }}" data-idx="{{ $i }}"
                                style="--rgb: {{ $m['rgb'] }};"
                                title="{{ $item->name }}"
                                onclick="selectItem('{{ $category }}', {{ $i }})"
                                onmouseenter="hoverItem('{{ $category }}', {{ $i }})"
                                onmouseleave="unhoverItem('{{ $category }}')">
                            @if($item->css_value === 'rainbow')
                                <span class="orb__rainbow"><span></span></span>
                            @else
                                <span class="orb__dot" style="background: {{ $orbColor }};"></span>
                            @endif
                            @if($isEquipped)
                                <span class="orb__check">✓</span>
                            @elseif($isOwned)
                                <span class="orb__own"></span>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </section>
                @endforeach
            </div>
        </div>

        {{-- ═══════════════════ СТИКЕРПАКИ · ВИТРИНА ═══════════════════ --}}
        @php
            // Иерархия качества (от низшего к высшему).
            $rarityRank = ['common' => 1, 'rare' => 2, 'epic' => 3, 'mythic' => 4, 'legendary' => 5];
            $sortedPacks = $stickerPacks
                ->sortBy(fn ($p) => $rarityRank[$p['rarity']] ?? 0)
                ->values();

            // Звёзды редкости (1–5), как в спотлайте косметики.
            $stars = function ($n, $rgb) {
                $out = '';
                for ($i = 1; $i <= 5; $i++) {
                    $on = $i <= $n;
                    $out .= '<span style="color:' . ($on ? 'rgb(' . $rgb . ')' : 'var(--text-tertiary)') . ';' . ($on ? '' : 'opacity:.32;') . '">★</span>';
                }
                return $out;
            };
        @endphp
        <section class="stk px-6 sm:px-8 py-7">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-black text-[var(--text-primary)]">Стикерпаки</h2>
                        <span class="stk-badge"><span class="dot"></span>{{ $stickerPacks->count() }} наборов</span>
                    </div>
                    <p class="text-sm text-[var(--text-secondary)] max-w-xl">
                        Собирай редкие наборы и реагируй стикерами в комментариях.
                    </p>
                </div>
            </div>

            @if($stickerPacks->isEmpty())
                <p class="text-sm text-[var(--text-tertiary)]">Стикерпаки скоро появятся.</p>
            @else
                <div class="pkx-rail">
                    @foreach($sortedPacks as $pack)
                        @include('partials.pack-card', ['pack' => $pack])
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- Toast --}}
    <div id="market-toast" class="fixed bottom-6 right-6 z-50 pointer-events-none" style="min-width:260px;max-width:340px;"></div>

    {{-- Модалка-предпросмотр стикерпака (наполняется JS) --}}
    <div id="pack-modal" class="pkm" onclick="if (event.target === this || event.target.classList.contains('pkm__backdrop')) closePackPreview()">
        <div class="pkm__backdrop"></div>
        <div class="pkm__panel" id="pack-modal-panel"></div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const market = @json($catData);
        const packData = @json($packsForJs);
        const packCollection = { owned: {{ $packOwned }}, total: {{ $packTotal }} };
        const selected = {};   // индекс предмета в спотлайте
        const locked = {};     // примерка «зафиксирована» кликом

        const equipped = {
            border:         @json($user->cosmetic_border),
            nickname_color: @json($user->cosmetic_nickname_color),
            font:           @json($user->cosmetic_font),
        };

        const COIN_SVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/><circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/></svg>';

        function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

        // ── Live-update helpers (no page reload) ──
        function setCoins(value) {
            const formatted = Number(value).toLocaleString('ru-RU');
            const el = document.getElementById('market-coins');
            if (el) el.textContent = formatted;
            const g = document.getElementById('coin-count');
            if (g) g.textContent = formatted;
        }

        function idxOf(cat, id) { return market[cat].findIndex(i => i.id === id); }

        // Mark exactly one item as equipped in a category (id=null → none).
        function markEquipped(cat, id) {
            market[cat].forEach(it => { it.equipped = (it.id === id); });
        }

        function orbColorFor(item) {
            if (item.category === 'nickname_color') return item.css;
            const m = String(item.css).match(/#[0-9a-fA-F]{6}/);
            return m ? m[0] : '#f97316';
        }

        // Rebuild a category's swatch tray from the data model. `is-active`
        // tracks the spotlight selection; the corner badge tracks equip/own.
        function renderOrbs(cat) {
            const rail = document.querySelector(`.orb-rail[data-cat="${cat}"]`);
            if (!rail) return;
            const sel = selected[cat] ?? 0;
            rail.innerHTML = market[cat].map((item, i) => {
                const dot = item.css === 'rainbow'
                    ? `<span class="orb__rainbow"><span></span></span>`
                    : `<span class="orb__dot" style="background:${orbColorFor(item)};"></span>`;
                const badge = item.equipped ? `<span class="orb__check">✓</span>`
                    : (item.owned ? `<span class="orb__own"></span>` : '');
                return `<button type="button" class="orb ${i === sel ? 'is-active' : ''}" data-cat="${cat}" data-idx="${i}" style="--rgb:${item.rgb};" title="${esc(item.name)}" onclick="selectItem('${cat}',${i})" onmouseenter="hoverItem('${cat}',${i})" onmouseleave="unhoverItem('${cat}')">${dot}${badge}</button>`;
            }).join('');
        }

        // Re-render spotlight + tray for a category after a state change.
        function refreshCategory(cat) {
            const idx = selected[cat] ?? 0;
            renderSpotlight(cat, idx);
            renderOrbs(cat);
        }

        // Recompute the sidebar collection panel + per-section counters from data.
        function updateCollectionStats() {
            let total = 0, owned = 0;
            Object.keys(market).forEach(cat => {
                const items = market[cat];
                const o = items.filter(i => i.owned).length;
                total += items.length; owned += o;

                const cp = items.length ? Math.round(o / items.length * 100) : 0;
                const wrap = document.querySelector(`[data-cat-stat="${cat}"]`);
                if (wrap) {
                    const f = wrap.querySelector('[data-catstat-fraction]');
                    const b = wrap.querySelector('[data-catstat-bar]');
                    if (f) f.textContent = `${o}/${items.length}`;
                    if (b) b.style.width = cp + '%';
                }
                const sc = document.querySelector(`[data-section-count="${cat}"]`);
                if (sc) sc.textContent = `${o}/${items.length}`;
            });

            // Sticker packs are part of the collection too.
            total += packCollection.total; owned += packCollection.owned;
            const ppct = packCollection.total ? Math.round(packCollection.owned / packCollection.total * 100) : 0;
            const pwrap = document.querySelector('[data-pack-stat]');
            if (pwrap) {
                const pf = pwrap.querySelector('[data-packstat-fraction]');
                const pb = pwrap.querySelector('[data-packstat-bar]');
                if (pf) pf.textContent = `${packCollection.owned}/${packCollection.total}`;
                if (pb) pb.style.width = ppct + '%';
            }

            const pct = total ? Math.round(owned / total * 100) : 0;
            const frac = document.querySelector('[data-collection-fraction]');
            if (frac) {
                frac.textContent = `${owned}/${total}`;
                frac.classList.toggle('text-emerald-400', pct === 100);
                frac.classList.toggle('text-orange-400', pct !== 100);
            }
            const bar = document.querySelector('[data-collection-bar]');
            if (bar) bar.style.width = pct + '%';
            const pctEl = document.querySelector('[data-collection-pct]');
            if (pctEl) pctEl.textContent = pct;
            const trophy = document.querySelector('[data-collection-trophy]');
            if (trophy) trophy.classList.toggle('hidden', pct !== 100);
        }

        function bigArt(item) {
            if (item.category === 'border') {
                if (item.css === 'rainbow') {
                    return `<div class="spot-rainbow-ring"><div style="width:5rem;height:5rem;border-radius:999px;background:#f97316;display:flex;align-items:center;justify-content:center;color:#000;font-weight:900;font-size:1.6rem;">А</div></div>`;
                }
                return `<div style="width:5rem;height:5rem;border-radius:999px;background:#f97316;display:flex;align-items:center;justify-content:center;color:#000;font-weight:900;font-size:1.6rem;box-shadow:${item.css};">А</div>`;
            }
            return `<span style="display:inline-block;padding:0 1rem;font-size:2rem;font-weight:900;color:${item.css};">Никнейм</span>`;
        }

        function starsHTML(stars, rgb) {
            let out = '';
            for (let i = 1; i <= 5; i++) out += `<span style="color:${i <= stars ? 'rgb(' + rgb + ')' : 'var(--text-tertiary)'};${i <= stars ? '' : 'opacity:.4;'}">★</span>`;
            return out;
        }

        function actionHTML(item) {
            const cssArg = String(item.css).replace(/'/g, "\\'");
            if (item.equipped) return `<button class="rbtn rbtn--unequip" onclick="marketUnequip('${item.category}')">Снять с профиля</button>`;
            if (item.owned)    return `<button class="rbtn rbtn--equip" onclick="marketEquip(${item.id}, '${item.category}', '${cssArg}')">Надеть</button>`;
            return `<button class="rbtn rbtn--buy" onclick="marketBuy(${item.id}, ${item.price}, '${item.category}', '${cssArg}')">${COIN_SVG} Купить · ${item.price.toLocaleString('ru-RU')}</button>`;
        }

        function renderSpotlight(cat, idx) {
            const item = market[cat][idx];
            const spot = document.getElementById('spot-' + cat);
            if (!spot || !item) return;
            spot.style.setProperty('--rgb', item.rgb);
            const status = item.equipped
                ? '<span class="spot__status spot__status--active">✓ Активно</span>'
                : (item.owned ? '<span class="spot__status spot__status--owned">В коллекции</span>' : '');
            spot.innerHTML =
                `<div class="spot__art">${bigArt(item)}</div>` +
                `<div class="spot__info">` +
                    `<div class="spot__meta"><span class="spot__tier">${esc(item.rarityLabel)}</span><span class="spot__stars">${starsHTML(item.stars, item.rgb)}</span>${status}</div>` +
                    `<h3 class="spot__name">${esc(item.name)}</h3>` +
                    `<p class="spot__desc">${esc(item.desc)}</p>` +
                    `<div class="spot__action">${actionHTML(item)}</div>` +
                `</div>`;
        }

        function highlightOrb(cat, idx) {
            document.querySelectorAll(`.orb[data-cat="${cat}"]`).forEach(o => o.classList.toggle('is-active', +o.dataset.idx === idx));
        }

        // Клик — фиксирует предмет в спотлайте И примеряет его на аватар (до следующего выбора)
        function selectItem(cat, idx) {
            selected[cat] = idx;
            locked[cat] = true;
            renderSpotlight(cat, idx);
            highlightOrb(cat, idx);
            applyPreview(cat, market[cat][idx].css);
        }
        // Наведение — временная примерка
        function hoverItem(cat, idx) {
            renderSpotlight(cat, idx);
            highlightOrb(cat, idx);
            applyPreview(cat, market[cat][idx].css);
        }
        // Уход курсора — возврат к зафиксированному выбору (или к надетому, если ничего не выбрано)
        function unhoverItem(cat) {
            const idx = selected[cat] ?? 0;
            renderSpotlight(cat, idx);
            highlightOrb(cat, idx);
            if (locked[cat]) applyPreview(cat, market[cat][idx].css);
            else restorePreview(cat);
        }

        function applyPreview(category, css) {
            const avatar = document.getElementById('preview-avatar');
            const name = document.getElementById('preview-name');
            if (category === 'border') {
                avatar.classList.remove('avatar-rainbow');
                avatar.style.boxShadow = '';
                if (css === 'rainbow') avatar.classList.add('avatar-rainbow');
                else if (css) avatar.style.boxShadow = css;
            } else if (category === 'nickname_color') {
                name.style.color = css || '';
            } else if (category === 'font') {
                name.style.fontFamily = css || '';
            }
        }

        function restorePreview(category) {
            const eq = equipped[category];
            if (category === 'border') {
                const avatar = document.getElementById('preview-avatar');
                avatar.classList.remove('avatar-rainbow');
                avatar.style.boxShadow = '';
                if (eq === 'rainbow') avatar.classList.add('avatar-rainbow');
                else if (eq) avatar.style.boxShadow = eq;
            } else {
                applyPreview(category, eq || '');
            }
        }

        async function marketBuy(id, price, category, css) {
            const coinsEl = document.getElementById('market-coins');
            const current = parseInt(coinsEl.textContent.replace(/\s/g, '').replace(/,/g, ''), 10);
            if (current < price) { marketToast('Недостаточно монет!', false); return; }

            const res = await fetch(`/market/buy/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
            const data = await res.json();
            if (!res.ok) { marketToast(data.error === 'not_enough_coins' ? 'Недостаточно монет!' : 'Ошибка транзакции', false); return; }

            setCoins(data.coins);

            // Buying a cosmetic also equips it: own it, equip it, unequip siblings.
            const item = market[category][idxOf(category, id)];
            if (item) item.owned = true;
            markEquipped(category, id);
            selected[category] = idxOf(category, id);
            locked[category] = true;

            equipped[category] = css;
            applyPreview(category, css);
            if (category === 'border') window.dispatchEvent(new CustomEvent('cosmetic-border-changed', { detail: { value: css } }));
            refreshCategory(category);
            updateCollectionStats();
            marketToast('Куплено и надето!');
        }

        async function marketEquip(id, category, css) {
            const res = await fetch(`/market/equip/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
            if (!res.ok) { marketToast('Ошибка при экипировке', false); return; }

            markEquipped(category, id);
            selected[category] = idxOf(category, id);
            locked[category] = true;

            equipped[category] = css;
            applyPreview(category, css);
            if (category === 'border') window.dispatchEvent(new CustomEvent('cosmetic-border-changed', { detail: { value: css } }));
            refreshCategory(category);
            marketToast('Экипировано успешно!');
        }

        async function marketUnequip(category) {
            const res = await fetch('/market/unequip', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ category }) });
            if (!res.ok) { marketToast('Ошибка при снятии', false); return; }

            markEquipped(category, null);
            equipped[category] = null;
            restorePreview(category);
            if (category === 'border') window.dispatchEvent(new CustomEvent('cosmetic-border-changed', { detail: { value: null } }));
            refreshCategory(category);
            marketToast('Снято с профиля');
        }

        function marketToast(msg, ok = true) {
            const t = document.getElementById('market-toast');
            const color = ok ? 'rgba(74,222,128,.45)' : 'rgba(248,113,113,.45)';
            const textColor = ok ? '#4ade80' : '#f87171';
            t.innerHTML = `<div style="background:var(--bg-secondary);border:1px solid ${color};border-radius:14px;padding:14px 18px;color:${textColor};font-size:13px;font-weight:600;box-shadow:0 12px 32px rgba(0,0,0,.35);animation:mkToastIn .3s cubic-bezier(.34,1.56,.64,1) forwards;">${esc(msg)}</div>`;
            setTimeout(() => { t.innerHTML = ''; }, 3500);
        }

        // ── Стикерпаки: предпросмотр + покупка (без перезагрузки) ──

        // Build one preview cell mirroring partials/pack-preview (lottie/video/image).
        function stickerCellHTML(s) {
            if (s.type === 'lottie')
                return `<div class="pkm__cell"><canvas data-lottie-src="${s.url}" data-lottie-hover width="200" height="200"></canvas></div>`;
            if (s.type === 'video')
                return `<div class="pkm__cell"><video src="${s.url}#t=0.001" muted playsinline loop preload="metadata" onmouseenter="this.play()" onmouseleave="this.pause()"></video></div>`;
            return `<div class="pkm__cell"><img src="${s.url}" loading="lazy" alt=""></div>`;
        }

        function packFootHTML(pack) {
            if (pack.owned)
                return `<span class="pkx__owned">✓ В коллекции</span><span class="pkx__count">${pack.count} шт.</span>`;
            return `<span class="pkx__price">${COIN_SVG} ${pack.price.toLocaleString('ru-RU')}</span>` +
                   `<button type="button" class="rbtn rbtn--buy" onclick="purchasePack('${pack.slug}')">Купить</button>`;
        }

        function renderPackModal(pack) {
            const panel = document.getElementById('pack-modal-panel');
            panel.style.setProperty('--rgb', pack.rgb);
            const cells = pack.stickers.map(stickerCellHTML).join('') ||
                `<p style="grid-column:1/-1;text-align:center;color:var(--text-tertiary);font-size:.85rem;padding:1.5rem 0;">В наборе пока нет стикеров.</p>`;
            panel.innerHTML =
                `<div class="pkm__head">` +
                    `<div>` +
                        `<div class="spot__meta" style="--rgb:${pack.rgb};"><span class="spot__tier">${esc(pack.rarityLabel)}</span><span class="spot__stars">${starsHTML(pack.stars, pack.rgb)}</span></div>` +
                        `<h3 class="pkm__title" style="margin-top:.4rem;">${esc(pack.name)}</h3>` +
                        `<p style="font-size:.78rem;color:var(--text-tertiary);margin-top:.25rem;">${pack.count} стикеров${pack.animated ? ' · анимированные' : ''}</p>` +
                    `</div>` +
                    `<button type="button" class="pkm__close" onclick="closePackPreview()" title="Закрыть">✕</button>` +
                `</div>` +
                `<div class="pkm__grid">${cells}</div>` +
                `<div class="pkm__foot" id="pack-modal-foot">${packFootHTML(pack)}</div>`;
            // Mount the lazy Lottie cells we just injected (video/image self-render).
            window.scanLottieCanvases?.(panel);
        }

        let modalPackSlug = null;
        function openPackPreview(slug) {
            const pack = packData[slug];
            if (!pack) return;
            modalPackSlug = slug;
            renderPackModal(pack);
            document.getElementById('pack-modal').classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
        function closePackPreview() {
            modalPackSlug = null;
            document.getElementById('pack-modal').classList.remove('is-open');
            document.body.style.overflow = '';
            document.getElementById('pack-modal-panel').innerHTML = '';
        }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePackPreview(); });

        // Покупка стикерпака — переиспользует тот же endpoint, что и косметика.
        async function purchasePack(slug) {
            const pack = packData[slug];
            if (!pack || pack.owned) return;

            const coinsEl = document.getElementById('market-coins');
            const current = parseInt(coinsEl.textContent.replace(/\s/g, '').replace(/,/g, ''), 10);
            if (current < pack.price) { marketToast('Недостаточно монет!', false); return; }

            const res = await fetch(`/market/buy/${pack.shopItemId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
            const data = await res.json();
            if (!res.ok) { marketToast(data.error === 'not_enough_coins' ? 'Недостаточно монет!' : 'Ошибка транзакции', false); return; }

            setCoins(data.coins);
            pack.owned = true;
            packCollection.owned = Math.min(packCollection.owned + 1, packCollection.total);

            // Swap the card footer to the "owned" state in place.
            const foot = document.getElementById('pack-foot-' + pack.shopItemId);
            if (foot) foot.innerHTML = `<span class="pkx__owned">✓ В коллекции</span><span class="pkx__count">${pack.count} шт.</span>`;
            // Refresh the modal footer if it's open for this pack.
            const modalFoot = document.getElementById('pack-modal-foot');
            if (modalFoot && modalPackSlug === slug) modalFoot.innerHTML = packFootHTML(pack);

            updateCollectionStats();
            marketToast('Стикерпак куплен!');
        }

        // Инициализация: спотлайт показывает надетый предмет (или первый)
        Object.keys(market).forEach(cat => {
            let idx = market[cat].findIndex(i => i.equipped);
            if (idx < 0) idx = 0;
            selected[cat] = idx;
            renderSpotlight(cat, idx);
            highlightOrb(cat, idx);
        });
    </script>
</x-shop-layout>
