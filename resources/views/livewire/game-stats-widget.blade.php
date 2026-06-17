@php
    $GS = \App\Support\GameStats::class;

    $keys = array_keys($games);
    $n    = count($keys);

    // Чисто презентационный слой поверх уже готовой статистики: подпись,
    // награда за прогресс и глобальный обработчик запуска для каждой игры.
    // (openGame / openRunner объявлены глобально в dashboard.blade.php.)
    $present = [
        'buzzword' => ['tagline' => 'Сбивай астероиды из слов и забирай награды за каждый уровень.', 'reward' => '+1 монета / уровень', 'action' => 'openGame()'],
        'redline'  => ['tagline' => 'Беги, прыгай через преграды и копи награды за дистанцию.',       'reward' => '+1 монета / рубеж',  'action' => 'openRunner()'],
    ];
@endphp

{{-- Игротека: единый слайдер-«консоль». Один экран = одна игра: слева
     карточка запуска, справа геймифицированная телеметрия (рекорд, ранг-гейдж,
     трасса активности, сводка). Живёт внутри .hud дашборда и наследует его
     палитру; акцент каждой игры — мятный (buzzword) и красный (redline). --}}
<div class="arc" x-data="{
        i: 0,
        n: {{ $n }},
        go(d) { this.i = (this.i + d + this.n) % this.n; },
        set(k) { this.i = k; }
     }">

    <div class="arc__bar">
        <div class="arc__head">
            <span class="arc__kicker">Статистика игр</span>
            <span class="arc__title">Ваш прогресс</span>
        </div>
        @if ($n > 1)
        <div class="arc__nav">
            <span class="arc__count hud-tnum"><b x-text="String(i + 1).padStart(2, '0')"></b><span class="arc__count-d">/</span><span x-text="String(n).padStart(2, '0')"></span></span>
            <button type="button" class="arc__arrow" @click="go(-1)" aria-label="Предыдущая игра">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="arc__arrow" @click="go(1)" aria-label="Следующая игра">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
            </button>
        </div>
        @endif
    </div>

    <div class="arc__vp">
        <div class="arc__track" :style="`transform: translateX(-${i * 100}%)`">
            @foreach ($keys as $idx => $key)
                @php
                    $g  = $games[$key];
                    $m  = $g['meta'];
                    $s  = $g['summary'];
                    $rk = $g['rank'];
                    $p  = $present[$key] ?? ['tagline' => '', 'reward' => null, 'action' => null];

                    $pct = $rk['percentile'];
                    $hasRank = $rk['rank'] !== null;
                    // Кольцо ранга: r=30 → длина окружности ≈ 188.5.
                    $C   = 2 * M_PI * 30;
                    $off = $hasRank && $pct !== null ? $C * (1 - max(0, min(100, $pct)) / 100) : $C;

                    // Спарклайн активности: точки линии (координаты viewBox) и
                    // данные для всплывающей плашки (позиция в %, дата, число).
                    $vals  = array_map(fn ($d) => max(0, (int) $d['plays']), $g['series']);
                    $nv    = count($vals);
                    $maxv  = max(1, $vals ? max($vals) : 1);
                    $sw = 120; $sh = 44; $sp = 3; $spw = $sw - 2 * $sp; $sph = $sh - 2 * $sp; $sby = $sh - $sp;
                    $linePts = [];
                    $tipPts  = [];
                    foreach ($vals as $vi => $v) {
                        $x = $nv > 1 ? $sp + $vi * ($spw / ($nv - 1)) : $sw / 2;
                        $y = $sby - ($v / $maxv) * $sph;
                        $linePts[] = round($x, 2) . ',' . round($y, 2);
                        $tipPts[]  = ['l' => round($x / $sw * 100, 3), 't' => round($y / $sh * 100, 3), 'lab' => $g['series'][$vi]['label'], 'v' => $v];
                    }
                    $linePts = implode(' ', $linePts);
                @endphp

                <div class="arc-cell" wire:key="arc-cell-{{ $key }}"
                     :class="{ 'is-on': i === {{ $idx }} }"
                     style="--ga: var(--ga-{{ $key }}, var(--accent))">
                    <div class="arc-game hud-grid-bg clip-tr">
                        <span class="arc-game__spine"></span>

                        {{-- ── Карточка запуска ── --}}
                        <div class="arc-cab">
                            <div class="arc-cab__id">
                                <span class="arc-cab__frame">
                                    <i class="b tl"></i><i class="b tr"></i><i class="b bl"></i><i class="b br"></i>
                                    <svg class="arc-cab__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $m['icon'] !!}</svg>
                                </span>
                                <div class="arc-cab__meta">
                                    <span class="arc-cab__kick"><span class="arc-cab__live"></span>Мини-игра // {{ mb_strtoupper($m['short']) }}</span>
                                    <h3 class="arc-cab__name">{{ $m['name'] }}</h3>
                                    <p class="arc-cab__tag">{{ $p['tagline'] }}</p>
                                </div>
                            </div>

                            <div class="arc-cab__rewards">
                                <span class="arc-rw">
                                    <svg class="arc-rw__bolt" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    +5&nbsp;XP
                                </span>
                                @if ($p['reward'])
                                <span class="arc-rw">
                                    <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/><circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/><text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text></svg>
                                    {{ $p['reward'] }}
                                </span>
                                @endif
                            </div>

                            @if ($p['action'])
                            <button type="button" class="arc-play" onclick="{{ $p['action'] }}">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                Играть
                            </button>
                            @endif
                        </div>

                        <span class="arc-game__div"></span>

                        {{-- ── Телеметрия ── --}}
                        <div class="arc-tel">
                            @if ($s['plays'] === 0)
                                <div class="arc-empty">
                                    <span class="arc-empty__ic">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="11" rx="3"/><path d="M7 12h3M8.5 10.5v3"/><circle cx="15.5" cy="11.5" r=".6" fill="currentColor"/><circle cx="17.5" cy="13.5" r=".6" fill="currentColor"/></svg>
                                    </span>
                                    <p class="arc-empty__t">Статистика пуста</p>
                                    <p class="arc-empty__s">Сыграйте забег — рекорд, ранг и активность появятся здесь.</p>
                                </div>
                            @else
                                <div class="arc-tel__top">
                                    <div class="arc-best">
                                        <span class="arc-best__lab">{{ $m['score_label'] }} · рекорд</span>
                                        <span class="arc-best__num">{{ $GS::spaced($s['best']) }}<span class="arc-best__unit">{{ $m['unit'] }}</span></span>
                                        @if ($pct !== null)
                                            <span class="arc-pct">Топ&nbsp;{{ max(1, 100 - $pct) }}%</span>
                                        @endif
                                    </div>

                                    @if ($hasRank)
                                    <div class="arc-gauge" role="img" aria-label="Ранг {{ $rk['rank'] }} из {{ $rk['total'] }}">
                                        <svg class="arc-gauge__svg" viewBox="0 0 72 72">
                                            <circle class="arc-gauge__track" cx="36" cy="36" r="30"/>
                                            <circle class="arc-gauge__fill" cx="36" cy="36" r="30"
                                                    style="--c: {{ round($C, 1) }}; --off: {{ round($off, 1) }}"/>
                                        </svg>
                                        <div class="arc-gauge__c">
                                            <span class="arc-gauge__rank hud-tnum">#{{ $rk['rank'] }}</span>
                                            <span class="arc-gauge__of">из {{ $rk['total'] }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="arc-trace">
                                    <div class="arc-trace__head">
                                        <span class="arc-trace__lab">Активность</span>
                                        <span class="arc-trace__sub">14 дней</span>
                                    </div>
                                    <div class="arc-trace__plot"
                                         x-data="{
                                            hover: false, k: -1,
                                            pts: @js($tipPts),
                                            word(n) { n = Math.abs(n) % 100; const d = n % 10; if (n > 10 && n < 20) return 'забегов'; if (d === 1) return 'забег'; if (d > 1 && d < 5) return 'забега'; return 'забегов'; },
                                            move(e) {
                                                const r = e.currentTarget.getBoundingClientRect();
                                                const x = (e.clientX - r.left) / r.width * 100;
                                                let best = 0, bd = Infinity;
                                                this.pts.forEach((p, idx) => { const dd = Math.abs(p.l - x); if (dd < bd) { bd = dd; best = idx; } });
                                                this.k = best; this.hover = true;
                                            }
                                         }"
                                         @mousemove="move($event)" @mouseleave="hover = false; k = -1"
                                         @touchstart.passive="move($event.touches[0])" @touchmove.passive="move($event.touches[0])" @touchend="hover = false; k = -1">
                                        <svg class="arc-spark" viewBox="0 0 120 44" preserveAspectRatio="none">
                                            <polyline points="{{ $linePts }}" fill="none" stroke="var(--ga)" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
                                        </svg>
                                        <div class="arc-spark__guide" x-show="hover" x-cloak :style="`left:${pts[k] ? pts[k].l : 0}%`"></div>
                                        <div class="arc-spark__dot" x-show="hover" x-cloak :style="`left:${pts[k] ? pts[k].l : 0}%; top:${pts[k] ? pts[k].t : 0}%`"></div>
                                        <div class="arc-spark__tip" x-show="hover" x-cloak
                                             :style="`left:${pts[k] ? Math.min(Math.max(pts[k].l, 16), 84) : 50}%`">
                                            <span class="arc-spark__tip-d" x-text="pts[k] ? pts[k].lab : ''"></span>
                                            <span class="arc-spark__tip-v"><b x-text="pts[k] ? pts[k].v : ''"></b> <span x-text="pts[k] ? word(pts[k].v) : ''"></span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="arc-ribbon">
                                    <div class="arc-ribbon__seg">
                                        <span class="arc-ribbon__v hud-tnum">{{ $GS::spaced($s['plays']) }}</span>
                                        <span class="arc-ribbon__l">Забегов</span>
                                    </div>
                                    <span class="arc-ribbon__sep"></span>
                                    <div class="arc-ribbon__seg">
                                        <span class="arc-ribbon__v hud-tnum">{{ $GS::formatScore($key, $s['last_score']) }}</span>
                                        <span class="arc-ribbon__l">Последний</span>
                                    </div>
                                    <span class="arc-ribbon__sep"></span>
                                    <div class="arc-ribbon__seg">
                                        <span class="arc-ribbon__v hud-tnum">{{ $s['best_level'] }}</span>
                                        <span class="arc-ribbon__l">{{ $m['level_label'] }}</span>
                                    </div>
                                </div>
                            @endif

                            <button type="button" class="arc-more" onclick="openModal('open-game-stats', { game: '{{ $key }}' })">
                                Подробная статистика
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($n > 1)
    <div class="arc__rail">
        @foreach ($keys as $idx => $key)
            <button type="button" class="arc__seg"
                    style="--ga: var(--ga-{{ $key }}, var(--accent))"
                    :class="{ 'is-active': i === {{ $idx }} }"
                    @click="set({{ $idx }})">
                <span class="arc__seg-dot"></span>
                <span class="arc__seg-name">{{ $games[$key]['meta']['short'] }}</span>
            </button>
        @endforeach
    </div>
    @endif

<style>
    /* Акценты игр наследуются из палитры .hud дашборда. */
    .arc { --ga-buzzword: var(--mint); --ga-redline: #f87171;
        display: flex; flex-direction: column; gap: .95rem; padding: 1rem 1.1rem 1.15rem; }
    :is([data-theme="light"], [data-theme="pink"]) .arc { --ga-redline: #dc2626; }

    /* ── Верхняя строка: заголовок + навигация ── */
    .arc__bar { display: flex; align-items: flex-start; gap: .75rem; }
    .arc__head { flex: 1; min-width: 0; }
    .arc__kicker { display: block; font-family: ui-monospace, "JetBrains Mono", Menlo, monospace;
        font-size: .56rem; font-weight: 800; letter-spacing: .26em; text-transform: uppercase; color: var(--dim-2); }
    .arc__title { display: block; margin-top: .2rem; font-size: 1.05rem; font-weight: 900; color: var(--text); line-height: 1.1; }
    .arc__nav { display: flex; align-items: center; gap: .55rem; flex-shrink: 0; }
    .arc__count { font-family: ui-monospace, Menlo, monospace; font-size: .68rem; letter-spacing: .08em; color: var(--dim-2); }
    .arc__count b { color: var(--text); font-weight: 800; }
    .arc__count-d { margin: 0 .1em; opacity: .5; }
    .arc__arrow { width: 1.9rem; height: 1.9rem; display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--line); border-radius: 8px; background: var(--inset); color: var(--dim); cursor: pointer;
        transition: color .12s, border-color .12s, background .12s, transform .08s; }
    .arc__arrow svg { width: .95rem; height: .95rem; }
    .arc__arrow:hover { color: var(--text); border-color: var(--accent); background: var(--bg-2); }
    .arc__arrow:active { transform: translateY(1px); }

    /* ── Слайдер ── */
    .arc__vp { overflow: hidden; }
    .arc__track { display: flex; align-items: stretch; }
    @media (prefers-reduced-motion: no-preference) { .arc__track { transition: transform .5s cubic-bezier(.22, 1, .36, 1); } }
    .arc-cell { flex: 0 0 100%; width: 100%; min-width: 0; display: flex; }

    .arc-game { position: relative; flex: 1; display: flex; flex-direction: column;
        background: var(--inset); overflow: hidden; }
    @media (min-width: 880px) { .arc-game { flex-direction: row; align-items: stretch; } }
    .arc-game__spine { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--ga); z-index: 2; }
    /* Диагональный срез верхнего-правого угла в цвете игры (мотив HUD-баннеров). */
    .arc-game::after { content: ''; position: absolute; top: 0; right: 0; width: 18px; height: 18px; z-index: 3; pointer-events: none;
        background: linear-gradient(to top right, transparent calc(50% - .9px), var(--ga) calc(50% - .9px), var(--ga) calc(50% + .9px), transparent calc(50% + .9px)); }

    /* Карточка запуска */
    .arc-cab { position: relative; z-index: 1; padding: 1.2rem 1.25rem 1.25rem 1.4rem; display: flex; flex-direction: column; gap: 1.05rem; }
    @media (min-width: 880px) { .arc-cab { width: 16.5rem; flex-shrink: 0; } }
    .arc-cab__id { display: flex; gap: .9rem; align-items: flex-start; }
    .arc-cab__frame { position: relative; flex-shrink: 0; width: 3rem; height: 3rem; padding: .62rem; color: var(--ga); }
    .arc-cab__frame .b { position: absolute; width: 11px; height: 11px; border: 2px solid var(--ga); }
    .arc-cab__frame .b.tl { top: 0; left: 0; border-right: 0; border-bottom: 0; }
    .arc-cab__frame .b.tr { top: 0; right: 0; border-left: 0; border-bottom: 0; }
    .arc-cab__frame .b.bl { bottom: 0; left: 0; border-right: 0; border-top: 0; }
    .arc-cab__frame .b.br { bottom: 0; right: 0; border-left: 0; border-top: 0; }
    .arc-cab__icon { width: 100%; height: 100%; }
    .arc-cab__meta { min-width: 0; }
    .arc-cab__kick { display: inline-flex; align-items: center; gap: .42rem; font-family: ui-monospace, Menlo, monospace;
        font-size: .54rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--dim-2); }
    .arc-cab__live { width: 6px; height: 6px; border-radius: 50%; background: var(--ga); flex-shrink: 0; }
    @media (prefers-reduced-motion: no-preference) { .arc-cab__live { animation: arcPulse 2.2s ease-in-out infinite; } }
    @keyframes arcPulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
    .arc-cab__name { font-size: 1.18rem; font-weight: 900; line-height: 1.12; text-transform: uppercase; letter-spacing: .01em;
        color: var(--ga); margin-top: .38rem; }
    .arc-cab__tag { font-size: .73rem; color: var(--dim); margin-top: .45rem; line-height: 1.4; }

    .arc-cab__rewards { display: flex; flex-wrap: wrap; gap: .42rem; }
    .arc-rw { display: inline-flex; align-items: center; gap: .35rem; padding: .32rem .55rem; border-radius: 7px;
        font-family: ui-monospace, Menlo, monospace; font-size: .66rem; font-weight: 700; color: var(--dim);
        background: var(--bg); border: 1px solid var(--line); }
    .arc-rw svg { width: .9rem; height: .9rem; flex-shrink: 0; }
    .arc-rw__bolt { color: var(--ga); }

    .arc-play { margin-top: auto; display: inline-flex; align-items: center; justify-content: center; gap: .55rem;
        padding: .72rem 1.3rem; border-radius: 9px; border: 1px solid var(--ga); background: transparent; color: var(--ga);
        font-size: .8rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; cursor: pointer;
        transition: background .14s ease, transform .08s ease; }
    .arc-play svg { width: 1rem; height: 1rem; }
    .arc-play:hover { background: color-mix(in srgb, var(--ga) 14%, transparent); }
    .arc-play:active { transform: translateY(1px); }

    /* Разделитель карточки и телеметрии */
    .arc-game__div { background: var(--line); height: 1px; margin: 0 1.25rem; }
    @media (min-width: 880px) { .arc-game__div { width: 1px; height: auto; margin: 1.25rem 0; } }

    /* Телеметрия */
    .arc-tel { position: relative; z-index: 1; flex: 1; min-width: 0; padding: 1.2rem 1.25rem 1.25rem;
        display: flex; flex-direction: column; gap: 1.05rem; }

    .arc-tel__top { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
    .arc-best { min-width: 0; }
    .arc-best__lab { display: block; font-size: .55rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--dim-2); }
    .arc-best__num { display: inline-flex; align-items: baseline; gap: .32rem; margin-top: .35rem;
        font-size: 2.2rem; font-weight: 900; color: var(--text); line-height: 1; }
    .arc-best__unit { font-size: .9rem; font-weight: 800; color: var(--ga); }
    .arc-pct { display: inline-flex; align-items: center; margin-top: .55rem; padding: .14rem .5rem; border-radius: 999px;
        font-size: .6rem; font-weight: 800; letter-spacing: .03em; color: var(--ga);
        background: color-mix(in srgb, var(--ga) 14%, transparent); }
    @media (prefers-reduced-motion: no-preference) {
        .arc-cell.is-on .arc-best__num { animation: arcRise .55s cubic-bezier(.22, 1, .36, 1) both; }
        .arc-cell.is-on .arc-pct { animation: arcRise .55s cubic-bezier(.22, 1, .36, 1) .08s both; }
    }
    @keyframes arcRise { from { opacity: 0; transform: translateY(7px); } to { opacity: 1; transform: none; } }

    /* Кольцо ранга */
    .arc-gauge { position: relative; flex-shrink: 0; width: 5.3rem; height: 5.3rem; display: flex; align-items: center; justify-content: center; }
    .arc-gauge__svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .arc-gauge__track { fill: none; stroke: var(--track); stroke-width: 5; }
    .arc-gauge__fill { fill: none; stroke: var(--ga); stroke-width: 5; stroke-linecap: round;
        stroke-dasharray: var(--c); stroke-dashoffset: var(--c); }
    @media (prefers-reduced-motion: no-preference) { .arc-gauge__fill { transition: stroke-dashoffset .9s cubic-bezier(.22, 1, .36, 1); } }
    .arc-cell.is-on .arc-gauge__fill { stroke-dashoffset: var(--off); }
    .arc-gauge__c { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1; }
    .arc-gauge__rank { font-size: 1.02rem; font-weight: 900; color: var(--text); }
    .arc-gauge__of { font-size: .5rem; letter-spacing: .04em; color: var(--dim-2); margin-top: .16rem; }

    /* Трасса активности */
    .arc-trace__head { display: flex; align-items: baseline; justify-content: space-between; }
    .arc-trace__lab { font-size: .55rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--dim-2); }
    .arc-trace__sub { font-family: ui-monospace, Menlo, monospace; font-size: .55rem; letter-spacing: .06em; text-transform: uppercase; color: var(--dim-2); }
    .arc-trace__plot { position: relative; margin-top: .45rem; height: 44px; border-bottom: 1px solid var(--line);
        background-image: linear-gradient(var(--grid) 1px, transparent 1px), linear-gradient(90deg, var(--grid) 1px, transparent 1px);
        background-size: 22px 22px; }
    .arc-trace__plot { cursor: crosshair; }
    .arc-trace__plot svg { display: block; width: 100%; height: 44px; clip-path: inset(0 100% 0 0); }
    @media (prefers-reduced-motion: no-preference) { .arc-trace__plot svg { transition: clip-path .85s cubic-bezier(.22, 1, .36, 1); } }
    .arc-cell.is-on .arc-trace__plot svg { clip-path: inset(0 0 0 0); }

    /* Наведение на график: вертикальная направляющая, точка и плашка */
    .arc-spark__guide { position: absolute; top: 0; bottom: 0; width: 1px; pointer-events: none;
        background: color-mix(in srgb, var(--ga) 50%, transparent); transform: translateX(-.5px); }
    .arc-spark__dot { position: absolute; width: 8px; height: 8px; border-radius: 50%; pointer-events: none;
        background: var(--ga); border: 2px solid var(--inset); transform: translate(-50%, -50%); }
    .arc-spark__tip { position: absolute; bottom: calc(100% + 9px); transform: translateX(-50%); z-index: 6; pointer-events: none;
        display: flex; flex-direction: column; gap: 1px; padding: .34rem .55rem; border-radius: 8px; white-space: nowrap;
        background: var(--bg-2); border: 1px solid var(--line-2); box-shadow: 0 10px 24px rgba(0, 0, 0, .4); }
    :is([data-theme="light"], [data-theme="pink"]) .arc-spark__tip { box-shadow: 0 10px 24px rgba(15, 23, 42, .16); }
    .arc-spark__tip-d { font-family: ui-monospace, Menlo, monospace; font-size: .56rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--dim-2); }
    .arc-spark__tip-v { font-size: .72rem; font-weight: 700; color: var(--text); }
    .arc-spark__tip-v b { color: var(--ga); font-weight: 900; }

    /* Сводка-лента: значения через тонкие разделители вместо сетки коробок */
    .arc-ribbon { display: flex; align-items: stretch; background: var(--bg); border: 1px solid var(--line); border-radius: 10px; }
    .arc-ribbon__seg { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: .18rem; padding: .62rem .7rem; }
    .arc-ribbon__sep { width: 1px; background: var(--line); margin: .55rem 0; flex-shrink: 0; }
    .arc-ribbon__v { font-size: .92rem; font-weight: 800; color: var(--text); line-height: 1.1;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .arc-ribbon__l { font-size: .52rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--dim-2); }

    .arc-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: .5rem; padding: 1.6rem 1rem; }
    .arc-empty__ic { width: 2.6rem; height: 2.6rem; display: flex; align-items: center; justify-content: center; color: var(--dim-2); border: 1px dashed var(--line-2); border-radius: 10px; }
    .arc-empty__ic svg { width: 1.45rem; height: 1.45rem; }
    .arc-empty__t { font-size: .85rem; font-weight: 800; color: var(--dim); }
    .arc-empty__s { font-size: .68rem; color: var(--dim-2); max-width: 18rem; line-height: 1.45; }

    .arc-more { margin-top: auto; align-self: flex-end; display: inline-flex; align-items: center; gap: .42rem;
        padding: .35rem 0 0; background: none; border: 0; color: var(--dim); cursor: pointer;
        font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; transition: color .12s; }
    .arc-more svg { width: .95rem; height: .95rem; transition: transform .14s ease; }
    .arc-more:hover { color: var(--ga); }
    .arc-more:hover svg { transform: translateX(3px); }

    /* Переключатель игр */
    .arc__rail { display: flex; gap: .5rem; }
    .arc__seg { flex: 1; min-width: 0; display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
        padding: .52rem .7rem; border: 1px solid var(--line); border-radius: 9px; background: var(--inset); color: var(--dim-2);
        font-size: .62rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; cursor: pointer;
        transition: color .14s, border-color .14s, background .14s; }
    .arc__seg-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--line-2); flex-shrink: 0; transition: background .14s; }
    .arc__seg-name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .arc__seg:hover { color: var(--text); border-color: var(--line-2); }
    .arc__seg.is-active { color: var(--text); border-color: color-mix(in srgb, var(--ga) 45%, var(--line));
        background: color-mix(in srgb, var(--ga) 10%, transparent); }
    .arc__seg.is-active .arc__seg-dot { background: var(--ga); }
</style>
</div>
