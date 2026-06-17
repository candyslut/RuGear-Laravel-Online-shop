<div>
@if ($ready ?? false)
@php
    $GS = \App\Support\GameStats::class;
    $private = $private ?? false;
    $isSelf  = $isSelf ?? true;
    $games   = $games ?? [];
    // Открываем вкладку игры, с которой пришли из виджета; иначе — любимую игру.
    $defaultTab = (isset($initialGame) && isset($games[$initialGame]))
        ? $initialGame
        : (($overview['favorite'] ?? null) ?: (array_key_first($games) ?: 'redline'));
@endphp

<div class="gs-hud gs-overlay" x-data x-init="window.hideModalLoader && hideModalLoader()" x-on:keydown.escape.window="$wire.close()">
    <div class="gs-backdrop" wire:click="close"></div>

    <div class="gs-panel" role="dialog" aria-modal="true">
        {{-- ── Header ── --}}
        <div class="gs-head">
            <span class="gs-head__bar"></span>
            @unless ($isSelf)
                <span class="gs-head__ava {{ $target->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}">
                    @if ($target->avatar)
                        <img src="{{ Storage::url($target->avatar) }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(mb_substr($target->name, 0, 1)) }}
                    @endif
                </span>
            @endunless
            <div class="min-w-0">
                <h2 class="gs-head__title">{{ $isSelf ? 'Игровая статистика' : 'Профиль игрока' }}</h2>
                <p class="gs-head__sub">{{ $isSelf ? 'Ваши мини-игры' : $target->name }}</p>
            </div>
            <button wire:click="close" class="gs-x ml-auto" aria-label="Закрыть">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        @if ($private)
            {{-- ── Privacy notice ── --}}
            <div class="gs-private">
                <span class="gs-private__ic">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <p class="gs-private__t">Игрок скрыл свою статистику</p>
                <p class="gs-private__s">{{ $target->name }} отключил публичный игровой профиль в настройках.</p>
            </div>
            <div class="gs-footer">
                <button wire:click="close" class="gs-btn gs-btn--ghost w-full">Закрыть</button>
            </div>
        @else
            {{-- ── Cross-game overview strip ── --}}
            <div class="gs-overview">
                <div class="gs-ov">
                    <span class="gs-ov__v">{{ $GS::spaced($overview['plays']) }}</span>
                    <span class="gs-ov__l">забегов</span>
                </div>
                <div class="gs-ov">
                    <span class="gs-ov__v">{{ $overview['plays'] > 0 ? $GS::formatDuration($overview['time_ms']) : '—' }}</span>
                    <span class="gs-ov__l">в игре</span>
                </div>
                <div class="gs-ov">
                    <span class="gs-ov__v gs-ov__v--coin">{{ $GS::spaced($overview['coins']) }}</span>
                    <span class="gs-ov__l">монет с игр</span>
                </div>
                <div class="gs-ov">
                    <span class="gs-ov__v gs-ov__v--xp">{{ $GS::spaced($overview['xp']) }}</span>
                    <span class="gs-ov__l">опыта с игр</span>
                </div>
            </div>

            <div x-data="{ tab: @js($defaultTab) }" class="gs-tabs-wrap">
                {{-- Tabs --}}
                <div class="gs-tabs" role="tablist">
                    @foreach ($games as $key => $g)
                        <button type="button" class="gs-tab" :class="tab === @js($key) && 'is-active'"
                                style="--gs-accent: {{ $g['meta']['accent'] }}"
                                @click="tab = @js($key)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="gs-tab__ic">{!! $g['meta']['icon'] !!}</svg>
                            <span>{{ $g['meta']['short'] }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Per-game panels --}}
                <div class="gs-body">
                    @foreach ($games as $key => $g)
                        @php
                            $m   = $g['meta'];
                            $s   = $g['summary'];
                            $rk  = $g['rank'];
                            // Игровая активность — число завершённых забегов за
                            // каждый из последних 30 дней (нули на пустых днях).
                            $activity = array_map(
                                fn ($d) => ['label' => $d['label'], 'value' => $d['plays']],
                                $g['daily']
                            );
                            $activeDays = count(array_filter($activity, fn ($d) => $d['value'] > 0));
                        @endphp

                        <div x-show="tab === @js($key)" x-cloak wire:key="gs-panel-{{ $key }}"
                             class="gs-game" style="--gs-accent: {{ $m['accent'] }}">

                            @if ($s['plays'] === 0)
                                <div class="gs-empty gs-empty--tall">
                                    <p class="gs-empty__t">{{ $isSelf ? 'Вы ещё не играли' : 'Игрок ещё не играл' }} в {{ $m['name'] }}</p>
                                    @if ($isSelf)<p class="gs-empty__s">Сыграйте забег — и здесь появятся графики и рекорды.</p>@endif
                                </div>
                            @else
                                {{-- Hero: rank ring + headline record --}}
                                <div class="gs-hero">
                                    @include('partials.stats.chart-ring', [
                                        'value'  => $rk['percentile'] ?? 0,
                                        'max'    => 100,
                                        'center' => $rk['rank'] ? '#' . $rk['rank'] : '—',
                                        'label'  => $rk['rank'] ? 'из ' . $rk['total'] : 'нет ранга',
                                        'accent' => $m['accent'],
                                        'size'   => 104,
                                    ])
                                    <div class="gs-hero__main">
                                        <span class="gs-hero__cap">{{ $m['score_label'] }} · рекорд</span>
                                        <span class="gs-hero__best">{{ $GS::formatScore($key, $s['best']) }}</span>
                                        <div class="gs-hero__meta">
                                            @if ($rk['percentile'] !== null)
                                                <span class="gs-chip">Топ&nbsp;{{ max(1, 100 - $rk['percentile']) }}%</span>
                                            @endif
                                            <span class="gs-chip gs-chip--soft">{{ $m['level_label'] }}:&nbsp;{{ $s['best_level'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Stat tiles --}}
                                <div class="gs-tiles">
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Забегов', 'value' => $GS::spaced($s['plays']), 'accent' => $m['accent'],
                                        'icon'  => '<path d="M3 12h4l3 8 4-16 3 8h4"/>',
                                    ])
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Средний ' . mb_strtolower($m['score_label']), 'value' => $GS::formatScore($key, $s['avg']), 'accent' => $m['accent'],
                                        'icon'  => '<path d="M4 19V5M4 19h16M8 16l3-4 3 2 4-6"/>',
                                    ])
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Суммарно', 'value' => $GS::formatScore($key, $s['total']), 'accent' => $m['accent'],
                                        'icon'  => '<path d="M4 4h16v4H4zM4 10h10v4H4zM4 16h16v4H4z"/>',
                                    ])
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Время в игре', 'value' => $GS::formatDuration($s['time_ms']), 'accent' => $m['accent'],
                                        'icon'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
                                    ])
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Последний', 'value' => $GS::formatScore($key, $s['last_score']), 'accent' => $m['accent'],
                                        'icon'  => '<path d="M3 12a9 9 0 1 0 9-9 9 9 0 0 0-6.5 2.8L3 8M3 4v4h4"/>',
                                    ])
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Монет с игр', 'value' => $GS::spaced($s['coins']), 'accent' => $m['accent'],
                                        'icon'  => '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M9.5 10.5h3a1.5 1.5 0 0 1 0 3h-3"/>',
                                    ])
                                    @include('partials.stats.stat-tile', [
                                        'label' => 'Опыта с игр', 'value' => $GS::spaced($s['xp']) . ' xp', 'accent' => $m['accent'],
                                        'icon'  => '<path d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                    ])
                                    {{-- Game-specific extras (asteroids, deaths, jumps…) --}}
                                    @foreach ($s['extras'] as $extra)
                                        @include('partials.stats.stat-tile', [
                                            'label' => $extra['label'], 'value' => $GS::spaced($extra['value']),
                                            'accent' => $m['accent'], 'icon' => $extra['icon'],
                                        ])
                                    @endforeach
                                </div>

                                {{-- Game activity (runs per day), full width --}}
                                <div class="gs-section">
                                    <div class="gs-section__h">
                                        <span class="gs-section__t">Игровая активность</span>
                                        <span class="gs-section__n">забегов в день · {{ $activeDays }} из 30 дн.</span>
                                    </div>
                                    @include('partials.stats.chart-bars', ['series' => $activity, 'accent' => $m['accent'], 'unit' => 'забег.', 'height' => 150])
                                </div>

                                {{-- Best runs --}}
                                <div class="gs-section">
                                    <div class="gs-section__h">
                                        <span class="gs-section__t">Лучшие забеги</span>
                                    </div>
                                    <div class="gs-bests">
                                        @foreach ($g['bests'] as $i => $b)
                                            <div class="gs-best">
                                                <span class="gs-best__rk">{{ $i + 1 }}</span>
                                                <div class="gs-best__main">
                                                    <span class="gs-best__sc">{{ $GS::formatScore($key, (int) $b->score) }}</span>
                                                    <span class="gs-best__meta">{{ $m['level_label'] }} {{ $b->level ?? 0 }} · {{ $GS::formatDuration((int) $b->duration_ms) }}</span>
                                                </div>
                                                <span class="gs-best__date">{{ $b->created_at->format('d.m.y') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Footer ── --}}
            <div class="gs-footer">
                <button wire:click="openLeaderboards" onclick="showModalLoader()" class="gs-btn gs-btn--accent">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M8 21V9M16 21V5M3 21h18M12 21v-6"/></svg>
                    Лидерборды
                </button>
                <button wire:click="close" class="gs-btn gs-btn--ghost">Закрыть</button>
            </div>
        @endif
    </div>

    @include('partials.stats.styles')
</div>
@endif
</div>
