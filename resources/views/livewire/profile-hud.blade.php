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

<section wire:poll.4s="refresh" class="hudpanel hud-corner hud-grid-bg">
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
                    <button onclick="Livewire.dispatch('open-leaderboard')" class="hud-btn w-full sm:flex-1 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        Рейтинг
                    </button>
                    <button onclick="Livewire.dispatch('open-achievements')" class="hud-btn w-full sm:flex-1 whitespace-nowrap">
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

    {{-- ── Лента достижений ── --}}
    <div class="border-t" style="border-color:var(--line)">
        <div class="hud-head" style="border-bottom:1px solid var(--line)">
            <span class="hud-head__bar"></span>
            <span class="hud-head__title">Достижения</span>
            <span class="hud-head__code">{{ $achCount }} ПОЛУЧЕНО</span>
            <button onclick="Livewire.dispatch('open-achievements')"
                class="ml-3 hud-mono text-[11px] font-bold uppercase tracking-widest t-acc hover:opacity-80 transition">
                Все →
            </button>
        </div>

        @if($achievements->count() > 0)
        <div class="p-5 lg:p-6">
            <div id="ach-track" class="flex items-center justify-center gap-3 overflow-x-auto pb-1" style="scroll-snap-type:x mandatory;scroll-behavior:smooth;">
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
                    <p class="ach-medallion__xp">+{{ $a['experience'] }} XP</p>
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
