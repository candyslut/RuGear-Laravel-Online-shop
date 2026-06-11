@php
    use App\Support\Gamification;
    $nameStyle = ($u->cosmetic_nickname_color ? 'color:' . $u->cosmetic_nickname_color . ';' : '')
               . ($u->cosmetic_font ? 'font-family:' . $u->cosmetic_font . ';' : '');
    $ringC = 219.9; // 2·π·35 — keep in sync with the ring SVG radius below
    $statCells = [
        ['l' => 'Уровень',    'v' => $u->level,        'fmt' => false, 'c' => 'var(--accent)', 'id' => null,
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
        ['l' => 'Опыт · XP',  'v' => $u->experience,   'fmt' => true,  'c' => 'var(--text)',   'id' => null,
         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 7h4v4"/>'],
        ['l' => 'Коины',      'v' => $u->coins,        'fmt' => true,  'c' => '#f59e0b',       'id' => 'coin-count',
         'icon' => '<circle cx="12" cy="12" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M10 10.5h2.5a1.5 1.5 0 010 3H10"/>'],
        ['l' => 'Достижения', 'v' => $achCount,        'fmt' => false, 'c' => 'var(--text)',   'id' => null,
         'icon' => '<polygon points="12 2 15 9 22 9.5 17 14 18 21 12 17.5 6 21 7 14 2 9.5 9 9"/>'],
    ];
@endphp

<section wire:poll.4s="refresh" x-data="{ streakOpen: false }"
         @keydown.escape.window="streakOpen = false"
         class="hudpanel hud-corner hud-grid-bg">
    <div class="flex flex-col lg:flex-row lg:items-stretch">

        {{-- ── Идентификация ── --}}
        <div class="flex-1 min-w-0 p-6 lg:p-7 flex items-center sm:items-start gap-5">

            {{-- Clean circular avatar inside the animated XP ring; tier emblem below --}}
            <div class="relative flex-shrink-0" style="width:84px;height:84px">
                <svg class="xp-ring" width="84" height="84" viewBox="0 0 80 80" aria-hidden="true">
                    <circle cx="40" cy="40" r="35" fill="none" stroke="var(--track)" stroke-width="3"/>
                    <circle class="xp-ring__fill" cx="40" cy="40" r="35" fill="none"
                            stroke="{{ $tier['color'] }}" stroke-width="3" stroke-linecap="round"
                            stroke-dasharray="{{ $ringC }}"
                            stroke-dashoffset="{{ $ringC * (1 - $levelPct / 100) }}"
                            transform="rotate(-90 40 40)"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div data-user-avatar class="w-[62px] h-[62px] rounded-full bg-orange-500 flex items-center justify-center text-black text-xl font-black overflow-hidden {{ $u->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                         style="{{ $u->cosmetic_border && $u->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$u->cosmetic_border.';' : '' }}">
                        @if($u->avatar)
                        <img src="{{ Storage::url($u->avatar) }}" alt="Аватар" class="w-full h-full object-cover">
                        @else
                        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                        @endif
                    </div>
                </div>
                {{-- Rank emblem pinned bottom-centre --}}
                <div class="tier-emblem" style="--tc:{{ $tier['color'] }}" title="{{ $tier['name'] }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        @for($c = 0; $c <= $tier['index']; $c++)
                            <polyline points="6 {{ 15 - $c * 4 }} 12 {{ 11 - $c * 4 }} 18 {{ 15 - $c * 4 }}"/>
                        @endfor
                    </svg>
                </div>
            </div>

            <div class="min-w-0 flex-1">
                <p class="hud-kicker">RUGEAR // ЛИЧНЫЙ КАБИНЕТ</p>
                <h1 class="text-2xl lg:text-3xl font-black leading-tight mt-1.5 t-text" style="{{ $nameStyle }}">{{ $u->name }}</h1>

                <p class="hud-mono text-xs t-dim2 truncate mt-2">{{ $u->email }}</p>

                @if($u->phone || $u->gender)
                <div class="mt-3 flex flex-wrap gap-2">
                    @if($u->phone)
                    <span class="inline-flex items-center gap-1.5 text-xs t-dim px-2.5 py-1" style="border:1px solid var(--line);background:var(--inset)">
                        <svg class="w-3.5 h-3.5 t-dim2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="hud-mono">{{ $u->phone }}</span>
                    </span>
                    @endif
                    @if($u->gender)
                    <span class="inline-flex items-center gap-1.5 text-xs t-dim px-2.5 py-1" style="border:1px solid var(--line);background:var(--inset)">
                        <span class="hud-label">Пол</span> {{ ['male' => 'Мужской', 'female' => 'Женский'][$u->gender] ?? '' }}
                    </span>
                    @endif
                </div>
                @endif

                @if($u->about)
                @php $aboutLong = mb_strlen($u->about) > 150; @endphp
                <div class="mt-3 max-w-lg">
                    <div id="about-wrap"
                        class="text-sm t-dim leading-relaxed overflow-hidden transition-all duration-300 ease-in-out"
                        style="{{ $aboutLong ? 'max-height:4.2rem;' : '' }}">{{ $u->about }}</div>
                    @if($aboutLong)
                    <button onclick="toggleAbout(this)" data-open="0"
                        class="mt-1.5 text-xs font-bold t-acc hover:opacity-80 transition">
                        Показать ещё ↓
                    </button>
                    @endif
                </div>
                @endif

                <div class="mt-4 flex flex-col sm:flex-row gap-2 w-full sm:max-w-sm">
                    <button onclick="openModal('open-leaderboard')" class="hud-btn w-full sm:flex-1 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        Рейтинг
                    </button>
                    <button onclick="openModal('open-achievements')" class="hud-btn w-full sm:flex-1 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 3h14v2a5 5 0 01-3 4.58V11a4 4 0 01-3 3.87V17h3v2H7v-2h3v-2.13A4 4 0 017 11V9.58A5 5 0 014 5V3z" />
                        </svg>
                        Достижения
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Матрица статов (живая, count-up) ── --}}
        <div class="lg:w-[20rem] flex-shrink-0 border-t lg:border-t-0 lg:border-l" style="border-color:var(--line)">
            <div class="grid grid-cols-2 h-full" style="gap:1px;background:var(--line)">
                @foreach($statCells as $s)
                <div class="stat-cell flex flex-col justify-center">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 t-dim2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
                        <p class="hud-label">{{ $s['l'] }}</p>
                    </div>
                    <p @if($s['id']) id="{{ $s['id'] }}" @endif
                       class="stat-val text-3xl font-black hud-tnum leading-none mt-2"
                       style="color:{{ $s['c'] }}"
                       data-count-to="{{ $s['v'] }}"
                       data-count-fmt="{{ $s['fmt'] ? 1 : 0 }}">{{ $s['fmt'] ? number_format($s['v']) : $s['v'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Прогрессия уровня ── --}}
    <div class="px-6 lg:px-7 py-4 border-t flex flex-col sm:flex-row sm:items-center gap-4" style="border-color:var(--line)">
        <div class="flex items-center gap-2.5 flex-shrink-0">
            <span class="hud-label">Прогресс</span>
            <span class="hud-mono text-xs t-acc font-bold">LV {{ $u->level }} → {{ $u->level + 1 }}</span>
        </div>
        <div class="hud-ticks flex-1 w-full">
            @php $xpTicks = (int) round($levelPct / 5); @endphp
            @for($i = 0; $i < 20; $i++)<i class="{{ $i < $xpTicks ? 'on' : '' }}"></i>@endfor
        </div>
        <p class="hud-mono text-xs t-dim flex-shrink-0 whitespace-nowrap">
            <span class="t-text font-bold">{{ $xpToNext }}</span> XP ДО LV {{ $u->level + 1 }}
        </p>
    </div>

    {{-- ── Серия входов (streak) ── --}}
    @php
        $cycleDay = Gamification::streakCycleDay($u->streak);
        $weekDay  = $cycleDay === 0 ? 0 : (($cycleDay - 1) % 7) + 1; // прогресс к недельной награде
    @endphp
    <div class="px-6 lg:px-7 py-4 border-t flex flex-col sm:flex-row sm:items-center gap-4" style="border-color:var(--line)">
        <div class="flex items-center gap-3 flex-shrink-0">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0" aria-hidden="true"
                  style="{{ $u->streak > 0
                        ? 'color:var(--warm-acc, #fb923c);background:var(--warm-acc-bg, rgba(249,115,22,.14));box-shadow:0 0 16px var(--warm-acc-bg, rgba(249,115,22,.25));'
                        : 'color:var(--dim-2);background:var(--inset);' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.5 2c.4 2.8 2.3 4.4 3.6 6 1.2 1.5 1.9 3 1.9 4.8a6 6 0 11-12 0c0-1.6.6-3 1.6-4.2.2.9.9 1.5 1.8 1.5 1.2 0 1.8-1 1.3-2.5C11.8 5.4 12 3.6 12.5 2z"/>
                </svg>
            </span>
            <div>
                <p class="hud-label">Серия входов</p>
                <p class="hud-mono text-lg font-black leading-none mt-1 t-text hud-tnum">
                    {{ $u->streak }} <span class="text-[11px] font-bold t-dim2">дн.</span>
                </p>
            </div>
        </div>
        <div class="flex-1 w-full min-w-[7rem]">
            <div class="flex items-center justify-between mb-1">
                <span class="hud-label">До недельной награды</span>
                <span class="hud-mono text-[10px] t-dim2">{{ $weekDay }}/7</span>
            </div>
            <div class="hud-ticks">
                @for($i = 0; $i < 7; $i++)<i class="{{ $i < $weekDay ? 'on' : '' }}"></i>@endfor
            </div>
        </div>
        <p class="hud-mono text-xs t-dim flex-shrink-0 whitespace-nowrap">
            Рекорд: <span class="t-text font-bold">{{ $u->best_streak }}</span> дн.
        </p>
        {{-- Открыть круговое меню наград --}}
        <button type="button" @click="streakOpen = true"
                class="hud-btn flex-shrink-0 whitespace-nowrap" title="Награды за серию входов">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12v9H4v-9M2 7h20v5H2zM12 22V7M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/>
            </svg>
            Награды
        </button>
    </div>

    {{-- ══ Колесо наград за серию входов — 6 месяцев (Alpine modal) ══ --}}
    @php
        $cycle     = Gamification::STREAK_CYCLE;                  // 180
        $progress  = $cycle > 0 ? (int) round($cycleDay / $cycle * 100) : 0;
        $toWeekly  = ($cycleDay > 0 && $cycleDay % 7 === 0)  ? 0 : (7  - ($cycleDay % 7));
        $toMonthly = ($cycleDay > 0 && $cycleDay % 30 === 0) ? 0 : (30 - ($cycleDay % 30));
        $toGrand   = max(0, $cycle - $cycleDay);
        $wkPct     = ($cycleDay > 0 && $cycleDay % 7 === 0)  ? 100 : ($cycleDay % 7)  / 7  * 100;
        $moPct     = ($cycleDay > 0 && $cycleDay % 30 === 0) ? 100 : ($cycleDay % 30) / 30 * 100;
        // daily идёт через var: тема «Космос» подменяет тёплый оранжевый.
        $tierColors = ['daily' => 'var(--tier-daily, #f97316)', 'weekly' => '#22d3ee', 'monthly' => '#f43f5e', 'grand' => '#fbbf24'];
        // Reward table for the client-side preview slider (one source of truth: PHP).
        $rewardTable = [];
        for ($d = 1; $d <= $cycle; $d++) { $rewardTable[$d] = Gamification::streakReward($d); }
        // Wheel geometry (viewBox 260×260): a clean progress ring. The arc sweeps
        // from the top clockwise to the current day; milestone days (each month +
        // the six-month grand prize) sit on the ring as studs, so nothing overlaps.
        $cx = 130; $cy = 130; $R = 100;
        $circ = 2 * M_PI * $R;
        $dash = round($circ * ($cycle > 0 ? min($cycleDay, $cycle) / $cycle : 0), 2);
        // Кольцевые отметки: каждый месяц (30 дн.) — крупный стад с номером
        // месяца, день 180 — главный приз, и недельные риски (7 дн.) между ними.
        $monthMarks = [];
        for ($d = 30; $d < $cycle; $d += 30) { $monthMarks[$d] = (int) ($d / 30); }
        // Недельную риску не рисуем, если она встаёт вплотную (≤2 дн.) к месячному
        // студу или к финалу — иначе кружок месяца наезжает на пунктир. На месте
        // такой «поглощённой» недели остаётся более крупная веха (дни 28, 91, 119).
        $weekMarks = [];
        for ($d = 7; $d < $cycle; $d += 7) {
            $clash = ($cycle - $d) <= 2;
            foreach (array_keys($monthMarks) as $m) {
                if (abs($d - $m) <= 2) { $clash = true; break; }
            }
            if (!$clash) { $weekMarks[] = $d; }
        }
    @endphp

    <div wire:ignore>
        <div x-show="streakOpen" x-cloak @click.self="streakOpen = false"
             x-transition.opacity.duration.200ms class="sr-overlay">
            <div class="sr-card"
                 x-data="{
                    previewDay: {{ max(1, $cycleDay) }},
                    rewards: @js($rewardTable),
                    tiers: @js(Gamification::STREAK_TIERS),
                    nextWeek() { this.previewDay = Math.min({{ $cycle }}, (Math.floor(this.previewDay / 7) + 1) * 7); }
                 }"
                 x-show="streakOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                {{-- Заголовок --}}
                <div class="sr-head">
                    <span class="sr-head__bar"></span>
                    <span class="sr-head__title">Ежедневный вход</span>
                    <span class="sr-head__code">6 МЕСЯЦЕВ · {{ $cycle }} ДН.</span>
                    <button type="button" @click="streakOpen = false" class="sr-close" aria-label="Закрыть">&times;</button>
                </div>

                <div class="sr-body">
                    {{-- ── Кольцо прогресса — заполняется до текущего дня ── --}}
                    <div class="sr-wheel">
                        <svg viewBox="0 0 260 260" class="sr-wheel-svg" aria-hidden="true">
                            <defs>
                                <linearGradient id="srGrad" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#fbbf24"/>
                                    <stop offset=".55" stop-color="#fb923c"/>
                                    <stop offset="1" stop-color="#f97316"/>
                                </linearGradient>
                            </defs>
                            {{-- Track --}}
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $R }}" fill="none"
                                    stroke="var(--line-2)" stroke-width="9" opacity=".35"/>
                            {{-- Progress arc — sweeps from the top, clockwise, to today --}}
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $R }}" fill="none"
                                    stroke="url(#srGrad)" stroke-width="9" stroke-linecap="round"
                                    stroke-dasharray="{{ $dash }} {{ round($circ - $dash, 2) }}"
                                    transform="rotate(-90 {{ $cx }} {{ $cy }})"/>
                            {{-- Недельные отметки — мелкие риски поперёк кольца --}}
                            @foreach($weekMarks as $d)
                                @php
                                    $ang = deg2rad(-90 + ($d / $cycle) * 360);
                                    $x1  = round($cx + ($R - 6.5) * cos($ang), 2);
                                    $y1  = round($cy + ($R - 6.5) * sin($ang), 2);
                                    $x2  = round($cx + ($R + 6.5) * cos($ang), 2);
                                    $y2  = round($cy + ($R + 6.5) * sin($ang), 2);
                                    $reached = $d <= $cycleDay;
                                @endphp
                                <line x1="{{ $x1 }}" y1="{{ $y1 }}" x2="{{ $x2 }}" y2="{{ $y2 }}"
                                      stroke="{{ $reached ? $tierColors['weekly'] : 'var(--line-2)' }}"
                                      stroke-width="2.5" stroke-linecap="round"
                                      opacity="{{ $reached ? '.95' : '.45' }}"/>
                            @endforeach
                            {{-- Месячные отметки — крупные студы с номером месяца снаружи кольца --}}
                            @foreach($monthMarks as $d => $month)
                                @php
                                    $ang = deg2rad(-90 + ($d / $cycle) * 360);
                                    $mx  = round($cx + $R * cos($ang), 2);
                                    $my  = round($cy + $R * sin($ang), 2);
                                    $lx  = round($cx + ($R + 16) * cos($ang), 2);
                                    $ly  = round($cy + ($R + 16) * sin($ang), 2);
                                    $reached = $d <= $cycleDay;
                                @endphp
                                <circle cx="{{ $mx }}" cy="{{ $my }}" r="5"
                                        fill="{{ $reached ? $tierColors['monthly'] : 'var(--bg)' }}"
                                        stroke="{{ $reached ? $tierColors['monthly'] : 'var(--line-2)' }}" stroke-width="2"/>
                                <text x="{{ $lx }}" y="{{ $ly }}" text-anchor="middle" dominant-baseline="central"
                                      font-size="9" font-weight="800" font-family="ui-monospace,Menlo,monospace"
                                      fill="{{ $reached ? $tierColors['monthly'] : 'var(--dim-2, #7d8694)' }}">{{ $month }}М</text>
                            @endforeach
                            {{-- Главный приз — крупный золотой стад на дне 180 (вершина кольца) --}}
                            @php $gReached = $cycleDay >= $cycle; @endphp
                            <circle cx="{{ $cx }}" cy="{{ $cy - $R }}" r="7"
                                    fill="{{ $gReached ? $tierColors['grand'] : 'var(--bg)' }}"
                                    stroke="{{ $tierColors['grand'] }}" stroke-width="2.5"/>
                            {{-- Current-day knob marks where you are on the ring --}}
                            @if($cycleDay > 0)
                                @php
                                    $cAng = deg2rad(-90 + ($cycleDay / $cycle) * 360);
                                    $kx = round($cx + $R * cos($cAng), 2);
                                    $ky = round($cy + $R * sin($cAng), 2);
                                @endphp
                                <circle cx="{{ $kx }}" cy="{{ $ky }}" r="6" fill="var(--text)"
                                        stroke="var(--bg)" stroke-width="2.5" class="sr-cur"/>
                            @endif
                        </svg>
                        <div class="sr-wheel-center">
                            <svg class="sr-wheel-flame w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.5 2c.4 2.8 2.3 4.4 3.6 6 1.2 1.5 1.9 3 1.9 4.8a6 6 0 11-12 0c0-1.6.6-3 1.6-4.2.2.9.9 1.5 1.8 1.5 1.2 0 1.8-1 1.3-2.5C11.8 5.4 12 3.6 12.5 2z"/>
                            </svg>
                            <span class="sr-wheel-num">{{ $u->streak }}</span>
                            <span class="sr-wheel-day">день {{ $cycleDay }} / {{ $cycle }}</span>
                        </div>
                    </div>

                    {{-- ── Виджеты-статы ── --}}
                    <div class="sr-stats">
                        <div class="sr-stat"><p class="sr-stat__l">Серия</p><p class="sr-stat__v">{{ $u->streak }}</p></div>
                        <div class="sr-stat"><p class="sr-stat__l">Рекорд</p><p class="sr-stat__v">{{ $u->best_streak }}</p></div>
                        <div class="sr-stat"><p class="sr-stat__l">День цикла</p><p class="sr-stat__v">{{ $cycleDay }}<span class="sr-stat__sub">/{{ $cycle }}</span></p></div>
                        <div class="sr-stat"><p class="sr-stat__l">Прогресс</p><p class="sr-stat__v">{{ $progress }}<span class="sr-stat__sub">%</span></p></div>
                    </div>

                    {{-- ── Виджеты-вехи (прогресс к наградам) ── --}}
                    <div class="sr-mile">
                        <div>
                            <div class="sr-mile__top">
                                <span class="sr-mile__name"><span class="sr-mile__dot" style="background:#22d3ee"></span>Недельная награда</span>
                                <span class="sr-mile__val">{{ $toWeekly === 0 ? 'сегодня!' : 'через '.$toWeekly.' дн.' }}</span>
                            </div>
                            <div class="sr-bar"><span style="width:{{ $wkPct }}%;background:#22d3ee"></span></div>
                        </div>
                        <div>
                            <div class="sr-mile__top">
                                <span class="sr-mile__name"><span class="sr-mile__dot" style="background:#f43f5e"></span>Месячная награда</span>
                                <span class="sr-mile__val">{{ $toMonthly === 0 ? 'сегодня!' : 'через '.$toMonthly.' дн.' }}</span>
                            </div>
                            <div class="sr-bar"><span style="width:{{ $moPct }}%;background:#f43f5e"></span></div>
                        </div>
                        <div>
                            <div class="sr-mile__top">
                                <span class="sr-mile__name"><span class="sr-mile__dot" style="background:#fbbf24"></span>Главный приз · 6 мес.</span>
                                <span class="sr-mile__val">{{ $toGrand === 0 ? 'получен!' : 'через '.$toGrand.' дн.' }}</span>
                            </div>
                            <div class="sr-bar"><span style="width:{{ $progress }}%;background:#fbbf24"></span></div>
                        </div>
                    </div>

                    {{-- ── Слайдер-превью наград по любому дню ── --}}
                    <div class="sr-preview">
                        <div class="sr-preview__head">
                            <span class="hud-label" style="color:var(--dim)">Превью · день <span x-text="previewDay" class="t-text font-black"></span></span>
                            <span class="sr-badge"
                                  :style="`color:${tiers[rewards[previewDay].tier].color};border-color:${tiers[rewards[previewDay].tier].color}`"
                                  x-text="tiers[rewards[previewDay].tier].label"></span>
                        </div>
                        <input type="range" min="1" max="{{ $cycle }}" step="1" x-model.number="previewDay"
                               class="sr-range" :style="`--p:${(previewDay - 1) / ({{ $cycle }} - 1) * 100}%`"
                               aria-label="Выбрать день для превью награды">
                        <div class="sr-preview__rewards">
                            <span class="sr-preview__xp">+<span x-text="rewards[previewDay].xp"></span> XP</span>
                            <span class="sr-coin">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                    <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                    <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                                </svg>
                                +<span x-text="rewards[previewDay].coins"></span> монет
                            </span>
                        </div>
                        <div class="sr-preview__jumps">
                            <button type="button" @click="previewDay = {{ max(1, $cycleDay) }}">Сегодня</button>
                            <button type="button" @click="nextWeek()">След. неделя</button>
                            <button type="button" @click="previewDay = {{ $cycle }}">Финал</button>
                        </div>
                    </div>

                    {{-- ── Легенда уровней наград ── --}}
                    <div class="sr-legend">
                        @foreach(Gamification::STREAK_TIERS as $meta)
                            <span><i style="background:{{ $meta['color'] }}"></i>{{ $meta['label'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Стили колеса наград — на CSS-переменных HUD (--bg/--text/--accent/...),
             поэтому меню само адаптируется под светлую и тёмную тему. --}}
        <style>
            .sr-overlay{position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);}
            .sr-card{width:100%;max-width:30rem;max-height:92vh;display:flex;flex-direction:column;background:var(--bg);border:1px solid var(--line);border-radius:16px;box-shadow:0 30px 80px rgba(0,0,0,.5);overflow:hidden;}
            .sr-head{display:flex;align-items:center;gap:.6rem;padding:.85rem 1.1rem;border-bottom:1px solid var(--line);background:var(--bg-2);flex-shrink:0;}
            .sr-head__bar{width:3px;height:15px;background:var(--accent);flex-shrink:0;}
            .sr-head__title{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.2em;color:var(--text);}
            .sr-head__code{margin-left:auto;font-size:.56rem;letter-spacing:.14em;color:var(--dim-2);font-family:ui-monospace,Menlo,monospace;white-space:nowrap;}
            .sr-close{font-size:1.5rem;line-height:1;color:var(--dim-2);background:none;border:0;cursor:pointer;padding:0 .1rem;}
            .sr-close:hover{color:var(--text);}
            .sr-body{padding:1.1rem;overflow-y:auto;}
            .sr-wheel{position:relative;width:clamp(220px,72vw,288px);aspect-ratio:1;margin:.1rem auto 1.2rem;}
            .sr-wheel-svg{width:100%;height:100%;display:block;}
            /* Тема «Космос»: дуга прогресса — аврора вместо золото-оранжевого. */
            [data-theme="cosmic"] #srGrad stop:nth-child(1){stop-color:#22d3ee;}
            [data-theme="cosmic"] #srGrad stop:nth-child(2){stop-color:#a78bfa;}
            [data-theme="cosmic"] #srGrad stop:nth-child(3){stop-color:#f472b6;}
            /* Тема «Няшный розовый»: дуга прогресса — розовый перелив. */
            [data-theme="pink"] #srGrad stop:nth-child(1){stop-color:#fbcfe8;}
            [data-theme="pink"] #srGrad stop:nth-child(2){stop-color:#f472b6;}
            [data-theme="pink"] #srGrad stop:nth-child(3){stop-color:#db2777;}
            .sr-cur{filter:drop-shadow(0 0 5px var(--accent));}
            .sr-wheel-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;pointer-events:none;}
            .sr-wheel-flame{color:var(--accent);}
            .sr-wheel-num{font-size:2.7rem;font-weight:900;line-height:1;color:var(--text);font-variant-numeric:tabular-nums;}
            .sr-wheel-day{font-size:.6rem;font-weight:800;letter-spacing:.13em;text-transform:uppercase;color:var(--dim);margin-top:5px;}
            .sr-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:1px;background:var(--line);border:1px solid var(--line);border-radius:10px;overflow:hidden;}
            @media(min-width:430px){.sr-stats{grid-template-columns:repeat(4,1fr);}}
            .sr-stat{background:var(--bg);padding:.7rem .85rem;}
            .sr-stat__l{font-size:.5rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--dim-2);}
            .sr-stat__v{font-size:1.2rem;font-weight:900;color:var(--text);margin-top:3px;font-variant-numeric:tabular-nums;}
            .sr-stat__sub{font-size:.7rem;font-weight:700;color:var(--dim-2);}
            .sr-mile{margin-top:1rem;display:flex;flex-direction:column;gap:.75rem;}
            .sr-mile__top{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;}
            .sr-mile__name{font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--dim);display:flex;align-items:center;gap:7px;}
            .sr-mile__dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
            .sr-mile__val{font-size:.6rem;font-family:ui-monospace,Menlo,monospace;color:var(--dim-2);white-space:nowrap;}
            .sr-bar{height:6px;border-radius:4px;background:var(--track);overflow:hidden;}
            .sr-bar>span{display:block;height:100%;border-radius:4px;transition:width .5s cubic-bezier(.22,1,.36,1);}
            .sr-preview{margin-top:1.1rem;padding:.9rem;border:1px solid var(--line);border-radius:12px;background:var(--bg-2);}
            .sr-preview__head{display:flex;justify-content:space-between;align-items:center;gap:.5rem;margin-bottom:.75rem;}
            .sr-badge{font-size:.54rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:3px 9px;border:1px solid;border-radius:999px;white-space:nowrap;}
            .sr-range{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:4px;cursor:pointer;background:linear-gradient(90deg,var(--accent) var(--p,0%),var(--track) var(--p,0%));}
            .sr-range::-webkit-slider-thumb{-webkit-appearance:none;width:18px;height:18px;border-radius:50%;background:var(--accent);border:3px solid var(--bg);box-shadow:0 0 0 1px var(--accent);cursor:pointer;}
            .sr-range::-moz-range-thumb{width:16px;height:16px;border-radius:50%;background:var(--accent);border:3px solid var(--bg);cursor:pointer;}
            .sr-preview__rewards{display:flex;align-items:center;gap:1.1rem;margin-top:.8rem;font-size:.9rem;font-weight:800;}
            .sr-preview__xp{color:var(--text);}
            .sr-coin{display:flex;align-items:center;gap:5px;color:#f59e0b;}
            .sr-preview__jumps{display:flex;gap:.4rem;margin-top:.85rem;}
            .sr-preview__jumps button{flex:1;font-size:.54rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:.5rem .35rem;border:1px solid var(--line-2);border-radius:7px;background:transparent;color:var(--dim);cursor:pointer;transition:color .12s,border-color .12s,background .12s;}
            .sr-preview__jumps button:hover{color:var(--text);border-color:var(--accent);background:var(--bg);}
            .sr-legend{margin-top:1rem;display:flex;flex-wrap:wrap;gap:.5rem .9rem;justify-content:center;}
            .sr-legend span{display:inline-flex;align-items:center;gap:6px;font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--dim);}
            .sr-legend i{width:9px;height:9px;border-radius:2px;display:inline-block;flex-shrink:0;}
        </style>
    </div>

    {{-- ── Лента достижений ── --}}
    <div class="border-t" style="border-color:var(--line)">
        <div class="hud-head" style="border-bottom:1px solid var(--line)">
            <span class="hud-head__bar"></span>
            <span class="hud-head__title">Достижения</span>
            <span class="hud-head__code">{{ $achCount }} ПОЛУЧЕНО</span>
            <button onclick="openModal('open-achievements')"
                class="ml-3 hud-mono text-[11px] font-bold uppercase tracking-widest t-acc hover:opacity-80 transition">
                Все →
            </button>
        </div>

        @if($achievements->count() > 0)
        <div class="p-5 lg:p-6">
            <div id="ach-track" class="flex items-center gap-3 overflow-x-auto pb-1" style="scroll-snap-type:x mandatory;scroll-behavior:smooth;">
                @foreach($achievements as $idx => $a)
                <div class="ach-medallion flex-shrink-0" style="--rc:{{ $a['rarity']['color'] }};width:150px;scroll-snap-align:start;">
                    @if($idx === 0)<span class="new-badge">NEW</span>@endif
                    <div class="ach-medallion__icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            {!! $a['icon'] !!}
                        </svg>
                    </div>
                    <p class="ach-medallion__rarity">{{ $a['rarity']['label'] }}</p>
                    <p class="ach-medallion__title">{{ $a['title'] }}</p>
                    <p class="ach-medallion__xp">+{{ $a['experience'] }} XP
                        <span class="ach-medallion__coins">·
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;display:inline">
                                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                            </svg>
                            +{{ $a['coins'] }}
                        </span>
                    </p>
                </div>
                @endforeach
            </div>
            {{-- Nav shown by JS (syncAchNav) only when the track actually overflows --}}
            <div id="ach-nav" class="justify-end gap-2 mt-3" style="display:none">
                <button type="button" onclick="document.getElementById('ach-track').scrollBy({left:-200,behavior:'smooth'})"
                    class="w-8 h-8 flex items-center justify-center t-dim hover:text-orange-500 transition" style="border:1px solid var(--line)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button type="button" onclick="document.getElementById('ach-track').scrollBy({left:200,behavior:'smooth'})"
                    class="w-8 h-8 flex items-center justify-center t-dim hover:text-orange-500 transition" style="border:1px solid var(--line)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>
        @else
        <div class="px-6 py-8 text-center">
            <p class="text-sm font-bold t-dim uppercase tracking-widest">Нет достижений</p>
            <p class="text-xs t-dim2 mt-1.5">Первое выдаётся при регистрации</p>
        </div>
        @endif
    </div>
</section>
