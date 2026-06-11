<div class="arc-stats-wrap">
@php
    $GS = \App\Support\GameStats::class;
    $keys = array_keys($games);
    $n = count($keys);
@endphp

{{-- Компактная панель статистики «Игротеки». Живёт внутри .hud дашборда и
     наследует его палитру (var(--bg)/--line/--text/--accent…). Слайдер
     переключает игры; «Подробнее» открывает полноэкранную модалку. --}}
<div class="arc-stats" x-data="{ i: 0, n: {{ $n }} }">
    <div class="arc-stats__top">
        <div class="arc-stats__head">
            <span class="arc-stats__kicker">Статистика игр</span>
            <span class="arc-stats__title">Ваш прогресс</span>
        </div>
        @if ($n > 1)
        <div class="arc-stats__nav">
            <button type="button" class="arc-nav" @click="i = (i - 1 + n) % n" aria-label="Предыдущая игра">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="arc-nav" @click="i = (i + 1) % n" aria-label="Следующая игра">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
            </button>
        </div>
        @endif
    </div>

    <div class="arc-stats__view">
        @foreach ($keys as $idx => $key)
            @php
                $g  = $games[$key];
                $m  = $g['meta'];
                $s  = $g['summary'];
                $rk = $g['rank'];
            @endphp
            <div class="arc-slide" wire:key="arc-slide-{{ $key }}"
                 x-show="i === {{ $idx }}" x-transition.opacity.duration.250ms
                 style="--ga: var(--ga-{{ $key }})">

                <div class="arc-slide__head">
                    <span class="arc-slide__ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $m['icon'] !!}</svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="arc-slide__name">{{ $m['name'] }}</h3>
                        <p class="arc-slide__cap">{{ $m['score_label'] }} · рекорд</p>
                    </div>
                </div>

                @if ($s['plays'] === 0)
                    <div class="arc-empty">
                        <p class="arc-empty__t">Вы ещё не играли</p>
                        <p class="arc-empty__s">Сыграйте забег — здесь появится статистика.</p>
                    </div>
                @else
                    <div class="arc-hero">
                        <span class="arc-hero__best">{{ $GS::formatScore($key, $s['best']) }}</span>
                        <div class="arc-hero__chips">
                            @if ($rk['rank'])
                                <span class="arc-chip arc-chip--rank">#{{ $rk['rank'] }} из {{ $rk['total'] }}</span>
                            @endif
                            @if ($rk['percentile'] !== null)
                                <span class="arc-chip">Топ&nbsp;{{ max(1, 100 - $rk['percentile']) }}%</span>
                            @endif
                        </div>
                    </div>

                    <div class="arc-spark">
                        @include('partials.stats.sparkline', ['series' => $g['spark'], 'accent' => 'var(--ga)', 'height' => 40])
                        <span class="arc-spark__cap">Активность · 14 дней</span>
                    </div>

                    <div class="arc-mini">
                        <div class="arc-mini__c">
                            <span class="arc-mini__v">{{ $GS::spaced($s['plays']) }}</span>
                            <span class="arc-mini__l">Забегов</span>
                        </div>
                        <div class="arc-mini__c">
                            <span class="arc-mini__v">{{ $GS::formatScore($key, $s['last_score']) }}</span>
                            <span class="arc-mini__l">Последний</span>
                        </div>
                        <div class="arc-mini__c">
                            <span class="arc-mini__v">{{ $s['best_level'] }}</span>
                            <span class="arc-mini__l">{{ $m['level_label'] }}</span>
                        </div>
                    </div>
                @endif

                <button type="button" onclick="openModal('open-game-stats')" class="arc-more">
                    Подробнее
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </div>
        @endforeach
    </div>

    @if ($n > 1)
    <div class="arc-dots">
        @foreach ($keys as $idx => $key)
            <button type="button" class="arc-dot" :class="i === {{ $idx }} && 'is-active'"
                    style="--ga: var(--ga-{{ $key }})"
                    @click="i = {{ $idx }}" aria-label="{{ $games[$key]['meta']['short'] }}"></button>
        @endforeach
    </div>
    @endif
</div>

<style>
    /* Панель статистики наследует переменные .hud дашборда. Акценты игр —
       мятный (buzzword) и красный (redline), как у баннеров слева. */
    .arc-stats-wrap { height: 100%; }
    .arc-stats {
        --ga-buzzword: var(--mint);
        --ga-redline: #f87171;
        height: 100%; min-height: 100%;
        display: flex; flex-direction: column;
        background: var(--inset); border: 1px solid var(--line); border-radius: 12px;
        padding: 1rem 1.1rem 1.1rem;
    }
    :is([data-theme="light"], [data-theme="pink"]) .arc-stats { --ga-redline: #dc2626; }

    .arc-stats__top { display: flex; align-items: flex-start; gap: .75rem; }
    .arc-stats__head { min-width: 0; flex: 1; }
    .arc-stats__kicker { display: block; font-family: ui-monospace, "JetBrains Mono", Menlo, monospace;
        font-size: .56rem; font-weight: 800; letter-spacing: .26em; text-transform: uppercase; color: var(--dim-2); }
    .arc-stats__title { display: block; margin-top: .2rem; font-size: 1.05rem; font-weight: 900; color: var(--text); line-height: 1.1; }
    .arc-stats__nav { display: flex; gap: .35rem; flex-shrink: 0; }
    .arc-nav { width: 1.85rem; height: 1.85rem; display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--line); border-radius: 8px; background: var(--bg); color: var(--dim); cursor: pointer;
        transition: color .12s, border-color .12s, background .12s, transform .08s; }
    .arc-nav svg { width: .95rem; height: .95rem; }
    .arc-nav:hover { color: var(--text); border-color: var(--accent); }
    .arc-nav:active { transform: translateY(1px); }

    /* Область слайдов растягивается на всю доступную высоту панели. */
    .arc-stats__view { position: relative; flex: 1; display: grid; margin-top: 1rem; min-height: 13rem; }
    .arc-slide { grid-area: 1 / 1; display: flex; flex-direction: column; gap: 1rem; min-width: 0; }

    .arc-slide__head { display: flex; align-items: center; gap: .7rem; }
    .arc-slide__ic { width: 2.5rem; height: 2.5rem; flex-shrink: 0; border-radius: 10px; padding: .55rem;
        color: var(--ga); background: color-mix(in srgb, var(--ga) 14%, transparent);
        border: 1px solid color-mix(in srgb, var(--ga) 35%, transparent); }
    .arc-slide__ic svg { width: 100%; height: 100%; }
    .arc-slide__name { font-size: .95rem; font-weight: 800; color: var(--text); line-height: 1.15;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .arc-slide__cap { font-size: .6rem; text-transform: uppercase; letter-spacing: .1em; color: var(--dim-2); margin-top: .15rem; }

    .arc-hero { display: flex; flex-direction: column; gap: .45rem; }
    .arc-hero__best { font-size: 1.75rem; font-weight: 900; color: var(--text); line-height: 1; }
    .arc-hero__chips { display: flex; flex-wrap: wrap; gap: .35rem; }
    .arc-chip { display: inline-flex; align-items: center; padding: .2rem .55rem; border-radius: 999px;
        font-size: .62rem; font-weight: 800; letter-spacing: .03em; color: var(--dim);
        background: var(--bg); border: 1px solid var(--line); }
    .arc-chip--rank { color: var(--ga); background: color-mix(in srgb, var(--ga) 14%, transparent);
        border-color: color-mix(in srgb, var(--ga) 35%, transparent); }

    .arc-spark { display: flex; flex-direction: column; gap: .25rem; }
    .arc-spark svg { display: block; width: 100%; height: 40px; }
    .arc-spark__cap { font-size: .54rem; text-transform: uppercase; letter-spacing: .1em; color: var(--dim-2); }

    .arc-mini { display: grid; grid-template-columns: repeat(3, 1fr); gap: .5rem; margin-top: auto; }
    .arc-mini__c { display: flex; flex-direction: column; gap: .15rem; padding: .55rem .6rem;
        border: 1px solid var(--line); border-radius: 10px; background: var(--bg); min-width: 0; }
    .arc-mini__v { font-size: .9rem; font-weight: 800; color: var(--text); line-height: 1.1;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .arc-mini__l { font-size: .54rem; text-transform: uppercase; letter-spacing: .08em; color: var(--dim-2); }

    .arc-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: .35rem; text-align: center; padding: 1.5rem .5rem; }
    .arc-empty__t { font-size: .85rem; font-weight: 800; color: var(--dim); }
    .arc-empty__s { font-size: .68rem; color: var(--dim-2); max-width: 16rem; }

    .arc-more { display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
        padding: .7rem 1rem; border-radius: 10px; cursor: pointer;
        border: 1px solid color-mix(in srgb, var(--ga) 45%, var(--line)); background: transparent; color: var(--ga);
        font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em;
        transition: background .12s, transform .08s; }
    .arc-more svg { width: 1rem; height: 1rem; }
    .arc-more:hover { background: color-mix(in srgb, var(--ga) 12%, transparent); }
    .arc-more:active { transform: translateY(1px); }

    .arc-dots { display: flex; justify-content: center; gap: .4rem; margin-top: .9rem; }
    .arc-dot { width: .5rem; height: .5rem; border-radius: 999px; border: 0; padding: 0; cursor: pointer;
        background: var(--line-2); transition: background .15s, width .15s; }
    .arc-dot.is-active { width: 1.4rem; background: var(--ga); }
</style>
</div>
