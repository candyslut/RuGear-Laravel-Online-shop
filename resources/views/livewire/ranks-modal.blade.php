<div>
@if($open)
<div class="lb-overlay" x-data x-init="window.hideModalLoader && hideModalLoader()" x-on:keydown.escape.window="$wire.close()">
    <div class="lb-backdrop" wire:click="close"></div>

    <div class="hud lb-panel lb-panel--ach hud-corner" role="dialog" aria-modal="true">
        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b flex-shrink-0" style="border-color:var(--line)">
            <span class="hud-head__bar"></span>
            <div class="min-w-0">
                <h2 class="hud-head__title">Ранги</h2>
                <p class="hud-mono text-[11px] t-dim2 mt-0.5">
                    Текущий: <span class="font-bold" style="color:{{ $current['color'] }}">{{ $current['name'] }}</span>
                    <span class="mx-1">·</span> УРОВЕНЬ <span class="t-text font-bold">{{ $level }}</span>
                </p>
            </div>
            <button wire:click="close" class="lb-x ml-auto" aria-label="Закрыть">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        {{-- Tier ladder --}}
        <div class="flex-1 overflow-y-auto cs p-4 sm:p-5" style="min-height:0">
            <div class="flex flex-col gap-3">
                @foreach($tiers as $t)
                <div class="rk-tile {{ $t['isCurrent'] ? 'is-current' : ($t['achieved'] ? 'is-got' : 'is-locked') }}" style="--tc:{{ $t['color'] }}">
                    {{-- Rounded-square emblem with this rank's unique icon --}}
                    <div class="rk-emblem">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            {!! \App\Support\Gamification::rankIcon($t['code']) !!}
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="rk-name">{{ $t['name'] }}</p>
                            @if($t['isCurrent'])
                                <span class="rk-badge rk-badge--cur">Текущий</span>
                            @elseif($t['achieved'])
                                <span class="rk-badge rk-badge--got">Открыт</span>
                            @else
                                <span class="rk-badge rk-badge--lock">Закрыт</span>
                            @endif
                        </div>
                        <p class="rk-req">
                            @if($t['next'])
                                Уровень {{ $t['min'] }}–{{ $t['next'] - 1 }}
                            @else
                                Уровень {{ $t['min'] }}+
                            @endif
                        </p>
                        {{-- Progress within this tier band (shown for the current tier) --}}
                        @if($t['isCurrent'] && $t['next'])
                        <div class="rk-bar"><span style="width:{{ $t['pct'] }}%;background:var(--tc)"></span></div>
                        @endif
                    </div>

                    <div class="text-right flex-shrink-0">
                        @if($t['reward'] > 0)
                        <p class="rk-reward">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
                                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                                <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
                            </svg>
                            +{{ number_format($t['reward']) }}
                        </p>
                        <p class="rk-reward__l">награда</p>
                        @else
                        <p class="rk-reward__l">старт</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <p class="hud-mono text-[10px] t-dim2 mt-4 text-center">Награда за ранг начисляется один раз при его достижении</p>
        </div>

        <div class="px-5 sm:px-6 py-4 border-t flex-shrink-0" style="border-color:var(--line)">
            <button wire:click="close" class="hud-btn w-full justify-center">Закрыть</button>
        </div>
    </div>

    <style>
        .rk-tile { display:flex; align-items:center; gap:.9rem; padding:.9rem 1rem; background:var(--inset); border:1px solid var(--line); border-left:3px solid var(--tc); border-radius:10px; }
        .rk-tile.is-locked { opacity:.5; border-left-color:var(--line-2); }
        .rk-tile.is-current { background:color-mix(in srgb, var(--tc) 9%, var(--inset)); border-color:color-mix(in srgb, var(--tc) 45%, var(--line)); box-shadow:0 0 0 1px color-mix(in srgb, var(--tc) 25%, transparent); }
        .rk-emblem { width:2.9rem; height:2.9rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; color:var(--tc); background:color-mix(in srgb, var(--tc) 14%, transparent); border:1px solid color-mix(in srgb, var(--tc) 38%, transparent); border-radius:14px; }
        .rk-tile.is-locked .rk-emblem { color:var(--dim-2); background:var(--bg-2); border-color:var(--line); }
        .rk-name { font-size:.92rem; font-weight:800; color:var(--text); text-transform:uppercase; letter-spacing:.06em; }
        .rk-req { font-family:ui-monospace,Menlo,monospace; font-size:.7rem; color:var(--dim); margin-top:.15rem; }
        .rk-bar { height:5px; border-radius:4px; background:var(--track); overflow:hidden; margin-top:.5rem; max-width:14rem; }
        .rk-bar > span { display:block; height:100%; border-radius:4px; transition:width .5s cubic-bezier(.22,1,.36,1); }
        .rk-badge { font-size:.5rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase; padding:.1rem .4rem; border:1px solid; border-radius:4px; }
        .rk-badge--cur { color:var(--tc); border-color:color-mix(in srgb, var(--tc) 45%, transparent); background:color-mix(in srgb, var(--tc) 12%, transparent); }
        .rk-badge--got { color:#22c55e; border-color:rgba(34,197,94,.4); }
        .rk-badge--lock { color:var(--dim-2); border-color:var(--line); }
        .rk-reward { display:inline-flex; align-items:center; gap:5px; font-family:ui-monospace,Menlo,monospace; font-size:.95rem; font-weight:800; color:#f59e0b; line-height:1; }
        .rk-reward__l { font-size:.5rem; font-weight:800; letter-spacing:.12em; text-transform:uppercase; color:var(--dim-2); margin-top:.3rem; }
    </style>
</div>
@endif
</div>
