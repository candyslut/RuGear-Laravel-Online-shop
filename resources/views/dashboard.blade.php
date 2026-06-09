<x-shop-layout>
    <x-slot name="title">Личный кабинет | RuGear</x-slot>

    <style>
        /* ── Ticket <dialog> modals (kept native) ── */
        dialog[open] {
            position: fixed;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
        }
        dialog::backdrop { background: rgba(0, 0, 0, 0.8); }
        dialog { animation: dlgIn 0.2s ease-out; }
        @keyframes dlgIn {
            from { opacity: 0; transform: translate(-50%, -48%) scale(.97); }
            to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        .cs::-webkit-scrollbar { width: 4px; }
        .cs::-webkit-scrollbar-track { background: transparent; }
        .cs::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }

        .custom-pagination nav { background: transparent !important; box-shadow: none !important; border: none !important; padding: 0 !important; display: flex !important; justify-content: flex-end !important; }
        .custom-pagination nav>div:first-child, .custom-pagination nav p { display: none !important; }
        .custom-pagination nav>div:last-child { display: flex !important; background: transparent !important; }
        .custom-pagination a, .custom-pagination span[aria-current="page"] span { background: #161920 !important; color: #9ca3af !important; border: 1px solid #374151 !important; font-size: 12px !important; padding: 7px 13px !important; margin: 0 2px !important; border-radius: 10px !important; transition: all .15s !important; }
        .custom-pagination span[aria-disabled="true"] span { background: #161920 !important; color: #4b5563 !important; border: 1px solid #374151 !important; font-size: 12px !important; padding: 7px 13px !important; margin: 0 2px !important; border-radius: 10px !important; cursor: not-allowed !important; }
        .custom-pagination span[aria-current="page"] span { background: #f97316 !important; color: #000 !important; border-color: #f97316 !important; font-weight: 900 !important; }
        .custom-pagination a:hover { background: #1f2937 !important; color: #fff !important; }

        #ach-track { scrollbar-width: none; -ms-overflow-style: none; }
        #ach-track::-webkit-scrollbar { display: none; }

        /* ══════════════════════════════════════════════════ */
        /* ИГРОВОЙ БАННЕР                                      */
        /* ══════════════════════════════════════════════════ */
        .game-banner { --gb: var(--mint); background: var(--bg); border: 1px solid var(--line); }
        .game-banner__rail { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--gb); z-index: 20; }
        .game-banner-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--gb); }
        .game-banner-title { color: var(--gb); }
        .game-banner-bolt  { color: var(--gb); }
        .game-chip { display: inline-flex; align-items: center; gap: .4rem; padding: .32rem .6rem; font-size: .72rem; color: var(--dim); background: var(--inset); border: 1px solid var(--line); }
        .game-banner-btn { display: inline-flex; align-items: center; justify-content: center; gap: .55rem; padding: .72rem 1.5rem; border: 1px solid var(--gb); background: transparent; color: var(--gb); font-weight: 800; text-transform: uppercase; letter-spacing: .14em; transition: background .12s ease, transform .08s ease; }
        .game-banner-btn:hover  { background: color-mix(in srgb, var(--gb) 14%, transparent); }
        .game-banner-btn:active { transform: translateY(1px); }

        /* ── Light-theme dashboard fixes ── */
        [data-theme="light"] .border-\[\#161920\] { border-color: #ffffff !important; }
        [data-theme="light"] .hover\:bg-white\/\[0\.02\]:hover { background-color: rgba(0, 0, 0, .035) !important; }

        /* ── Ticket modal accents (light) ── */
        [data-theme="light"] .tcmodal { border-color: #d8dce2 !important; box-shadow: 0 24px 60px rgba(15, 23, 42, .22) !important; }
        [data-theme="light"] .tcmodal .text-gray-600 { color: #64748b !important; }
        [data-theme="light"] .tcmodal .tc-label { color: #475569 !important; }
        [data-theme="light"] .tcmodal .tc-cat { background: #e0f2fe !important; border-color: #7dd3fc !important; color: #0369a1 !important; font-weight: 600; }
        [data-theme="light"] .tcmodal .tc-msg { background: #f4f6f9 !important; border-color: #d3d8e0 !important; color: #1f2937 !important; }
        [data-theme="light"] .tcmodal .tc-reply-label { color: #ea580c !important; }
        [data-theme="light"] .tcmodal .tc-reply { background: #fff7ed !important; border: 1px solid #fb923c !important; color: #1f2937 !important; box-shadow: inset 3px 0 0 #f97316; }
        [data-theme="light"] .tcmodal .tc-noreply { background: #f8fafc !important; border: 1px dashed #cbd5e1 !important; }
        [data-theme="light"] .tcmodal .tc-noreply p { color: #64748b !important; }

        /* ════════════════════════════════════════════════════════════ */
        /* HUD DESIGN LANGUAGE                                            */
        /* ════════════════════════════════════════════════════════════ */
        .hud {
            --bg: #0e1014; --bg-2: #14171d; --inset: #090b0e;
            --line: #232a36; --line-2: #333c4b; --grid: rgba(148,163,184,.06);
            --text: #e6e8ec; --dim: #828b9b; --dim-2: #525a68;
            --accent: #f97316; --mint: #20f8c0; --track: #1a1e26;
            color: var(--text);
        }
        [data-theme="light"] .hud {
            --bg: #ffffff; --bg-2: #f1f4f8; --inset: #e9edf2;
            --line: #d7dde6; --line-2: #c2cad6; --grid: rgba(100,116,139,.10);
            --text: #141a22; --dim: #5b6472; --dim-2: #97a0ad;
            --accent: #ea580c; --mint: #0d9488; --track: #e6eaf0;
        }
        .hud .t-text { color: var(--text); }
        .hud .t-dim  { color: var(--dim); }
        .hud .t-dim2 { color: var(--dim-2); }
        .hud .t-acc  { color: var(--accent); }
        .hud-mono { font-family: ui-monospace, "SF Mono", "JetBrains Mono", Menlo, monospace; }
        .hud-tnum { font-variant-numeric: tabular-nums; }

        .hudpanel { position: relative; background: var(--bg); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
        .hud-corner::before, .hud-corner::after { content: ''; position: absolute; width: 14px; height: 14px; border: 2px solid var(--accent); pointer-events: none; z-index: 3; }
        .hud-corner::before { top: -1px; left: -1px; border-right: 0; border-bottom: 0; border-top-left-radius: 12px; }
        .hud-corner::after  { bottom: -1px; right: -1px; border-left: 0; border-top: 0; border-bottom-right-radius: 12px; }

        .hud-head { display: flex; align-items: center; gap: .6rem; padding: .7rem .95rem; border-bottom: 1px solid var(--line); }
        .hud-head__bar { width: 3px; height: 15px; background: var(--accent); flex-shrink: 0; }
        .hud-head__title { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .2em; color: var(--text); }
        .hud-head__code { margin-left: auto; font-size: .6rem; letter-spacing: .14em; color: var(--dim-2); font-family: ui-monospace, Menlo, monospace; }

        .hud-kicker { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .3em; color: var(--dim-2); font-family: ui-monospace, Menlo, monospace; }
        .hud-label { font-size: .56rem; font-weight: 700; text-transform: uppercase; letter-spacing: .2em; color: var(--dim-2); }
        .hud-grid-bg { background-image: linear-gradient(var(--grid) 1px, transparent 1px), linear-gradient(90deg, var(--grid) 1px, transparent 1px); background-size: 26px 26px; }

        .hud-meter { height: 6px; background: var(--track); overflow: hidden; }
        .hud-meter > span { display: block; height: 100%; background: var(--accent); }
        @media (prefers-reduced-motion: no-preference) { .hud-meter > span { transition: width .6s cubic-bezier(.22,1,.36,1); } }
        .hud-ticks { display: flex; gap: 3px; }
        .hud-ticks i { flex: 1; height: 6px; background: var(--track); transition: background .15s ease; }
        .hud-ticks i.on { background: var(--accent); }

        .hud-btn { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; padding: .6rem 1rem; border: 1px solid var(--line-2); background: transparent; color: var(--dim); font-size: .66rem; font-weight: 800; text-transform: uppercase; letter-spacing: .14em; transition: color .12s ease, border-color .12s ease, background .12s ease, transform .08s ease; cursor: pointer; }
        .hud-btn:hover  { color: var(--text); border-color: var(--accent); background: var(--bg-2); }
        .hud-btn:active { transform: translateY(1px); }
        .hud-btn--danger:hover { color: #ef4444; border-color: #ef4444; background: rgba(239,68,68,.08); }

        .hud-row { position: relative; transition: background .12s ease, box-shadow .12s ease; }
        .hud-row:hover { background: var(--bg-2); }
        .clip-tr { clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 18px, 100% 100%, 0 100%); }

        .game-banner::after { content: ''; position: absolute; top: 0; right: 0; width: 18px; height: 18px; z-index: 30; pointer-events: none; background: linear-gradient(to top right, transparent calc(50% - 0.9px), var(--gb) calc(50% - 0.9px), var(--gb) calc(50% + 0.9px), transparent calc(50% + 0.9px)); }

        .stat-cell { position: relative; background: var(--bg); padding: 1.1rem 1.15rem; }
        .stat-cell::after { content: ''; position: absolute; top: 8px; right: 8px; width: 6px; height: 6px; border-top: 1px solid var(--dim-2); border-right: 1px solid var(--dim-2); }

        .ava-frame { position: relative; flex-shrink: 0; padding: 7px; }
        .ava-frame .b { position: absolute; width: 14px; height: 14px; border: 2px solid var(--accent); }
        .ava-frame .b.tl { top: 0; left: 0;  border-right: 0; border-bottom: 0; }
        .ava-frame .b.tr { top: 0; right: 0; border-left: 0;  border-bottom: 0; }
        .ava-frame .b.bl { bottom: 0; left: 0;  border-right: 0; border-top: 0; }
        .ava-frame .b.br { bottom: 0; right: 0; border-left: 0;  border-top: 0; }

        /* ════════════════════════════════════════════════════════════ */
        /* GAME-HUD LAYER (progression visuals)                          */
        /* ════════════════════════════════════════════════════════════ */

        /* Avatar XP ring + rank emblem */
        .xp-ring { position: absolute; inset: 0; }
        @media (prefers-reduced-motion: no-preference) { .xp-ring__fill { transition: stroke-dashoffset .85s cubic-bezier(.22,1,.36,1); } }
        .tier-emblem { position: absolute; left: 50%; bottom: -3px; transform: translateX(-50%); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; color: var(--tc); background: var(--bg); border: 1.5px solid var(--tc); clip-path: polygon(50% 0, 100% 25%, 100% 75%, 50% 100%, 0 75%, 0 25%); }

        /* Rank pill next to name */
        .rank-pill { display: inline-flex; align-items: center; gap: .4rem; padding: .22rem .55rem; font-size: .64rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; color: var(--tc); border: 1px solid color-mix(in srgb, var(--tc) 45%, transparent); background: color-mix(in srgb, var(--tc) 12%, transparent); }
        .rank-pill__dot { width: 6px; height: 6px; background: var(--tc); border-radius: 50%; }
        .rank-pill__sep { opacity: .5; }

        /* Live stat value (count-up + increase flash) */
        .stat-val { display: inline-block; transition: color .2s ease; }
        @media (prefers-reduced-motion: no-preference) { .stat-val.stat-up { animation: statUp .55s ease; } }
        @keyframes statUp { 0% { transform: scale(1); } 32% { transform: scale(1.14); } 100% { transform: scale(1); } }

        /* Achievement medallions (strip) */
        .ach-medallion { position: relative; padding: 1rem .85rem; text-align: center; background: var(--inset); border: 1px solid var(--line); border-top: 2px solid var(--rc); display: flex; flex-direction: column; align-items: center; justify-content: center; transition: transform .12s ease, border-color .12s ease; }
        .ach-medallion:hover { transform: translateY(-2px); border-color: color-mix(in srgb, var(--rc) 50%, var(--line)); }
        .ach-medallion__icon { width: 3rem; height: 3rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--rc); background: color-mix(in srgb, var(--rc) 12%, transparent); border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); margin-bottom: .6rem; clip-path: polygon(50% 0, 100% 25%, 100% 75%, 50% 100%, 0 75%, 0 25%); }
        .ach-medallion__rarity { font-size: .54rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--rc); }
        .ach-medallion__title { font-size: .8rem; font-weight: 700; color: var(--text); line-height: 1.25; margin: .35rem 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2rem; }
        .ach-medallion__xp { font-family: ui-monospace, Menlo, monospace; font-size: .72rem; font-weight: 700; color: var(--accent); margin-top: .4rem; }
        .new-badge { position: absolute; top: 6px; right: 6px; font-size: .5rem; font-weight: 900; letter-spacing: .08em; color: #000; background: var(--accent); padding: .12rem .32rem; }

        /* Overlay modals (leaderboard / achievements) */
        .lb-overlay { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .lb-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.8); }
        [data-theme="light"] .lb-backdrop { background: rgba(15,23,42,.45); }
        .lb-panel { position: relative; z-index: 1; display: flex; flex-direction: column; width: 100%; max-width: 56rem; height: 92vh; max-height: 92vh; background: var(--bg); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 30px 80px rgba(0,0,0,.55); }
        .lb-panel--ach { max-width: 42rem; }
        [data-theme="light"] .lb-panel { box-shadow: 0 24px 60px rgba(15,23,42,.22); }
        @media (prefers-reduced-motion: no-preference) { .lb-panel { animation: lbIn .22s cubic-bezier(.22,1,.36,1); } }
        @keyframes lbIn { from { opacity: 0; transform: translateY(10px) scale(.985); } to { opacity: 1; transform: none; } }
        .lb-head-mark { width: 2.1rem; height: 2.1rem; display: flex; align-items: center; justify-content: center; border: 1px solid color-mix(in srgb, var(--accent) 40%, var(--line)); background: color-mix(in srgb, var(--accent) 10%, transparent); }
        .lb-x { width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; color: var(--dim); border: 1px solid var(--line); border-radius: 8px; background: transparent; transition: color .12s, border-color .12s, background .12s; flex-shrink: 0; cursor: pointer; }
        .lb-x:hover { color: var(--text); border-color: var(--accent); background: var(--bg-2); }
        .lb-x svg { width: 1rem; height: 1rem; }
        .lb-search { background: var(--inset); border: 1px solid var(--line-2); color: var(--text); outline: none; transition: border-color .12s; }
        .lb-search::placeholder { color: var(--dim-2); }
        .lb-search:focus { border-color: var(--accent); }
        .rank-chip { display: inline-flex; flex-direction: column; align-items: flex-end; gap: .1rem; line-height: 1; padding: .3rem .6rem; background: color-mix(in srgb, var(--accent) 10%, transparent); border: 1px solid color-mix(in srgb, var(--accent) 35%, transparent); }

        /* Podium */
        .lb-podium { display: flex; align-items: flex-end; justify-content: center; gap: 1rem; padding: 1.75rem 1rem 1.25rem; border-bottom: 1px solid var(--line); background: var(--inset); }
        .lb-podium__col { display: flex; flex-direction: column; align-items: center; width: 30%; max-width: 9rem; text-align: center; }
        .lb-podium__col--first { transform: translateY(-14px); }
        .lb-podium__medal { position: relative; margin-bottom: .7rem; }
        .lb-podium__ava { width: 3.25rem; height: 3.25rem; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid var(--mc); color: var(--text); font-weight: 900; background: var(--bg-2); }
        .lb-podium__col--first .lb-podium__ava { width: 4rem; height: 4rem; }
        .lb-podium__ava.is-me { background: var(--accent); color: #000; }
        .lb-podium__rank { position: absolute; left: 50%; bottom: -8px; transform: translateX(-50%); width: 1.3rem; height: 1.3rem; display: flex; align-items: center; justify-content: center; font-size: .66rem; font-weight: 900; color: #000; background: var(--mc); }
        .lb-podium__name { font-size: .8rem; font-weight: 700; color: var(--text); margin-top: .35rem; max-width: 100%; }
        .lb-podium__lvl { font-family: ui-monospace, Menlo, monospace; font-size: .62rem; color: var(--dim); margin-top: .15rem; }

        .lb-row--me { background: color-mix(in srgb, var(--accent) 7%, transparent) !important; }
        #lb-list > .lb-row { border-top: 1px solid var(--line); }
        #lb-list > .lb-row:first-child { border-top: 0; }
        .lb-tier { display: inline-flex; align-items: center; gap: .3rem; font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--tc); margin-top: .1rem; }
        .lb-tier__dot { width: 5px; height: 5px; background: var(--tc); border-radius: 50%; }

        /* Achievement filter chips + tiles */
        .ach-chip { font-size: .62rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; padding: .35rem .7rem; color: var(--dim); background: var(--inset); border: 1px solid var(--line); transition: all .12s; cursor: pointer; }
        .ach-chip:hover { color: var(--text); }
        .ach-chip.is-active { color: #000; background: var(--accent); border-color: var(--accent); }
        .ach-tile { display: flex; align-items: flex-start; gap: .85rem; padding: 1rem; background: var(--inset); border: 1px solid var(--line); border-left: 3px solid var(--rc); }
        .ach-tile.is-locked { opacity: .55; border-left-color: var(--line-2); }
        .ach-tile__icon { width: 2.75rem; height: 2.75rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--rc); background: color-mix(in srgb, var(--rc) 12%, transparent); border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); clip-path: polygon(50% 0, 100% 25%, 100% 75%, 50% 100%, 0 75%, 0 25%); }
        .ach-tile.is-locked .ach-tile__icon { color: var(--dim-2); background: var(--bg-2); border-color: var(--line); }
        .ach-tile__title { font-size: .85rem; font-weight: 700; color: var(--text); }
        .ach-tile__rarity { flex-shrink: 0; font-size: .5rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--rc); padding: .06rem .3rem; border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); }
        .ach-tile.is-locked .ach-tile__rarity { color: var(--dim-2); border-color: var(--line); }
        .ach-tile__desc { font-size: .72rem; color: var(--dim); margin-top: .2rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .ach-tile__xp { font-family: ui-monospace, Menlo, monospace; font-size: .9rem; font-weight: 800; color: var(--rc); line-height: 1; }
        .ach-tile.is-locked .ach-tile__xp { color: var(--dim-2); }
        .ach-tile__status { font-size: .54rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; margin-top: .25rem; }
        .ach-tile.is-got .ach-tile__status { color: #22c55e; }
        .ach-tile.is-locked .ach-tile__status { color: var(--dim-2); }

        /* Level-up reveal (floating, theme-neutral) */
        .levelup-reveal { position: fixed; left: 50%; top: 1.5rem; transform: translateX(-50%); z-index: 80; display: flex; align-items: center; gap: .9rem; padding: .85rem 1.3rem; background: #14171d; border: 1px solid #20f8c0; color: #e6e8ec; box-shadow: 0 0 32px rgba(32,248,192,.22); }
        @media (prefers-reduced-motion: no-preference) { .levelup-reveal { animation: luIn .4s cubic-bezier(.22,1,.36,1); } }
        @keyframes luIn { from { opacity: 0; transform: translate(-50%,-16px); } to { opacity: 1; transform: translate(-50%,0); } }
        .levelup-reveal.out { animation: luOut .35s ease forwards; }
        @keyframes luOut { to { opacity: 0; transform: translate(-50%,-16px); } }
        .levelup-reveal__icon { width: 2.4rem; height: 2.4rem; display: flex; align-items: center; justify-content: center; color: #20f8c0; background: rgba(32,248,192,.14); border: 1px solid rgba(32,248,192,.4); flex-shrink: 0; }
    </style>

    <div class="hud space-y-4">

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- РЕАКТИВНЫЙ ПРОФИЛЬ (identity · stats · level · achievements) --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <livewire:profile-hud />

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- ИГРОВАЯ ЗОНА                                      --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <section class="game-banner hud-grid-bg clip-tr relative overflow-hidden">
            <span class="game-banner__rail"></span>
            <div class="relative z-10 p-5 sm:px-7 sm:py-6 flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2.5 mb-2.5">
                        <span class="game-banner-dot flex-shrink-0"></span>
                        <span class="hud-mono text-[10px] font-bold uppercase tracking-[0.22em] t-dim2">МИНИ-ИГРА // ACTIVE</span>
                    </div>
                    <h2 class="game-banner-title text-xl sm:text-2xl font-black uppercase tracking-widest leading-none">BUZZWORD BLAST</h2>
                    <p class="text-sm t-dim mt-2">Уничтожь астероиды — заработай награды</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3.5">
                        <span class="game-chip">
                            <svg class="game-banner-bolt w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="hud-mono">+5&nbsp;XP</span> за уровень
                        </span>
                        <span class="game-chip">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
                                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5" />
                                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5" />
                                <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text>
                            </svg>
                            <span class="hud-mono">+1</span> монета за уровень
                        </span>
                    </div>
                </div>
                <button onclick="openGame()" class="game-banner-btn w-full sm:w-auto flex-shrink-0 text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                    Играть
                </button>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- КОРЗИНА                                           --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <section class="hudpanel overflow-hidden">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Корзина</span>
                <span class="hud-head__code">{{ collect($cartItems)->sum('quantity') }} ТОВ.</span>
            </div>
            <div class="p-5 lg:p-6">
                <livewire:profile-cart />
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- ЗАКАЗЫ                                            --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="hudpanel overflow-hidden">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Журнал заказов</span>
                @if(!$userOrders->isEmpty())
                <span class="hud-head__code">{{ $userOrders->total() }} ЗАП.</span>
                @endif
            </div>

            @if($userOrders->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto mb-4 flex items-center justify-center" style="border:1px solid var(--line);background:var(--inset)">
                    <svg class="w-7 h-7 t-dim2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="text-sm font-bold t-dim uppercase tracking-widest">Заказов пока нет</p>
                <p class="text-xs t-dim2 mt-1.5">Добавьте товары в корзину и оформите первый заказ</p>
                <a href="/" class="hud-btn mt-5" style="border-color:var(--accent);color:var(--accent)">В каталог</a>
            </div>
            @else
            <div>
                @foreach($userOrders as $order)
                @php
                $dlabel = ['courier'=>'Курьер','pickup'=>'Самовывоз','post'=>'Почта / ПВЗ'];
                $plabel = ['card'=>'Карта','cash'=>'Наличные'];
                $steps = ['pending'=>0,'processing'=>1,'completed'=>2];
                $step = $steps[$order->status] ?? 0;
                $cnt = $order->items->count();
                $word = ($cnt%10===1&&$cnt%100!==11)?'позиция':(in_array($cnt%10,[2,3,4])&&!in_array($cnt%100,[12,13,14])?'позиции':'позиций');
                $oid = str_pad($order->id,4,'0',STR_PAD_LEFT);
                $accent = match($order->status){ 'processing'=>'bg-blue-500','completed'=>'bg-green-500','cancelled'=>'bg-red-500',default=>'bg-gray-700'};
                @endphp
                <div class="hud-row relative pl-4 pr-4 py-4 sm:pl-6 sm:pr-5 sm:py-5 {{ !$loop->first ? 'border-t' : '' }}" style="border-color:var(--line)">
                    <div class="absolute left-0 top-0 bottom-0 w-[3px] {{ $accent }}"></div>
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap mb-1">
                                <span class="hud-mono text-sm font-black t-text tracking-wider">ORD-{{ $oid }}</span>
                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-widest hud-mono
                                @switch($order->status)
                                    @case('pending')    bg-gray-800 text-gray-300 @break
                                    @case('processing') bg-blue-500/15 text-blue-300 border border-blue-500/40 @break
                                    @case('completed')  bg-green-500/15 text-green-300 border border-green-500/40 @break
                                    @case('cancelled')  bg-red-500/15 text-red-300 border border-red-500/40 @break
                                @endswitch">{{ $order->status_label }}</span>
                            </div>
                            <p class="hud-mono text-[11px] t-dim2">
                                {{ $order->created_at->format('d.m.Y · H:i') }}
                                @if($order->delivery_type) <span class="mx-1 t-dim2">·</span> {{ $dlabel[$order->delivery_type] ?? '' }} @endif
                                @if($order->payment_method) <span class="mx-1 t-dim2">·</span> {{ $plabel[$order->payment_method] ?? '' }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            @if($order->status === 'pending')
                            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Отменить заказ ORD-{{ $oid }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="hud-mono text-[10px] font-bold uppercase tracking-widest px-2.5 py-1.5 rounded-md bg-red-500/15 text-red-300 border border-red-500/40 hover:bg-red-500/25 hover:text-red-200 transition">Отменить</button>
                            </form>
                            @endif
                            <button onclick="toggleDetails({{ $order->id }})" class="w-8 h-8 flex items-center justify-center t-dim hover:text-orange-500 transition" style="border:1px solid var(--line)">
                                <svg id="btn-icon-{{ $order->id }}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-4">
                        <div class="flex gap-1.5 flex-shrink-0">
                            @foreach($order->items->take(3) as $item)
                            <div class="w-11 h-11 overflow-hidden flex items-center justify-center" style="background:var(--inset);border:1px solid var(--line)">
                                <img src="{{ asset($item->product->image) }}" class="w-full h-full object-contain p-1">
                            </div>
                            @endforeach
                            @if($cnt > 3)
                            <div class="w-11 h-11 flex items-center justify-center" style="background:var(--inset);border:1px solid var(--line)">
                                <span class="hud-mono text-[11px] font-bold t-dim">+{{ $cnt - 3 }}</span>
                            </div>
                            @endif
                        </div>

                        @if($order->status !== 'cancelled')
                        <div class="order-last w-full sm:order-none sm:w-auto sm:flex-1 min-w-0 px-0 sm:px-2 mt-2 sm:mt-0">
                            <div class="flex items-center gap-2">
                                @for($s = 0; $s < 3; $s++)
                                    <div class="w-4 h-4 flex-shrink-0 flex items-center justify-center" style="{{ $step > $s ? 'background:var(--accent)' : ($step === $s ? 'border:2px solid var(--accent)' : 'border:1px solid var(--line)') }}">
                                    @if($step > $s)
                                    <svg class="w-2.5 h-2.5 text-black" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    @elseif($step === $s)
                                    <div class="w-1.5 h-1.5" style="background:var(--accent)"></div>
                                    @endif
                                    </div>
                                    @if($s < 2)
                                    <div class="flex-1 h-px" style="background:{{ $step > $s ? 'var(--accent)' : 'var(--line)' }}"></div>
                                    @endif
                                @endfor
                            </div>
                            <div class="flex mt-1.5">
                                @foreach(['Заказ принят в обработку', 'Заказ едет к вам', 'Заказ доставлен'] as $si => $sl)
                                <span class="flex-1 text-[9px] leading-tight font-medium {{ $step >= $si ? 't-acc' : 't-dim2' }} {{ $si === 0 ? 'text-left' : ($si === 1 ? 'text-center' : 'text-right') }}">{{ $sl }}</span>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="flex-1"></div>
                        @endif

                        <div class="text-right flex-shrink-0 ml-auto sm:ml-0">
                            <p class="text-xl sm:text-2xl font-black t-text hud-tnum leading-none">
                                {{ number_format($order->total_price, 0, '.', ' ') }}&nbsp;<span class="t-acc">₽</span>
                            </p>
                            <p class="hud-mono text-[11px] t-dim2 mt-1">{{ $cnt }} {{ $word }}</p>
                        </div>
                    </div>

                    <div id="det-{{ $order->id }}" class="hidden mt-4 pt-4 border-t space-y-2.5" style="border-color:var(--line)">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 flex-shrink-0 overflow-hidden flex items-center justify-center" style="background:var(--inset);border:1px solid var(--line)">
                                <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-contain p-1">
                            </div>
                            <span class="text-sm t-dim flex-1 truncate">{{ $item->product->name }}</span>
                            <span class="hud-mono text-xs t-dim2 flex-shrink-0">{{ $item->quantity }}&times;</span>
                            <span class="hud-mono text-sm font-bold t-text flex-shrink-0">{{ number_format($item->price * $item->quantity, 0, '.', ' ') }} ₽</span>
                        </div>
                        @endforeach
                        @if($order->address)
                        <div class="flex items-start gap-2 pt-2.5 border-t" style="border-color:var(--line)">
                            <svg class="w-3.5 h-3.5 t-dim2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-xs t-dim">{{ $order->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($userOrders->hasPages())
            <div class="px-6 py-4 border-t custom-pagination" style="border-color:var(--line)">
                {{ $userOrders->links() }}
            </div>
            @endif
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- ПОДДЕРЖКА                                         --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="hudpanel overflow-hidden">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Поддержка</span>
                <a href="{{ route('support') }}" class="ml-auto hud-mono text-[11px] font-bold uppercase tracking-widest t-acc hover:opacity-80 transition">+ Создать</a>
            </div>

            @if($userTickets->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm font-bold t-dim uppercase tracking-widest">Обращений нет</p>
                <a href="{{ route('support') }}" class="inline-block mt-3 hud-mono text-xs font-bold uppercase tracking-widest t-acc hover:opacity-80 transition">Создать тикет →</a>
            </div>
            @else
            <div id="ticket-list" class="divide-y divide-gray-800 max-h-80 overflow-y-auto cs">
                @foreach($userTickets as $ticket)
                @php
                $tc = match($ticket->status){ 'pending'=>'text-blue-400','replied'=>'text-orange-400','closed'=>'text-gray-500',default=>'text-gray-500'};
                $tl = match($ticket->status){ 'pending'=>'На рассмотрении','replied'=>'Получен ответ','closed'=>'Закрыт',default=>$ticket->status};
                @endphp
                <div class="hud-row px-6 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="hud-mono text-xs t-dim2">#TC-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}</span>
                                <span class="hud-mono text-[10px] font-bold uppercase tracking-widest {{ $tc }}">{{ $tl }}</span>
                            </div>
                            <p class="text-sm font-bold t-text line-clamp-1">{{ $ticket->name }}</p>
                            <p class="text-xs t-dim line-clamp-1 mt-0.5">{{ $ticket->content }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <form action="{{ route('ticket.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Удалить тикет?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 t-dim2 hover:text-red-400 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                            <button onclick="document.getElementById('tc-{{ $ticket->id }}').showModal()" class="hud-mono px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest t-dim hover:text-orange-500 transition" style="border:1px solid var(--line)">Открыть</button>
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
                            <span class="tc-cat text-sm text-gray-300 bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-800">{{ $ticket->category }}</span>
                        </div>
                        @endif
                        <div>
                            <p class="tc-label text-xs text-gray-500 uppercase tracking-wider mb-2">Сообщение</p>
                            <div class="tc-msg bg-gray-900/50 border border-gray-800 rounded-xl p-3.5 text-sm text-gray-300 max-h-32 overflow-y-auto cs whitespace-pre-line leading-relaxed">{{ $ticket->content }}</div>
                        </div>
                        <div>
                            <p class="tc-reply-label text-xs text-orange-400/80 uppercase tracking-wider mb-2">Ответ поддержки</p>
                            @if($ticket->reply)
                            <div class="tc-reply bg-orange-500/5 border border-orange-500/20 rounded-xl p-3.5 text-sm text-gray-200 max-h-36 overflow-y-auto cs whitespace-pre-line leading-relaxed">{{ $ticket->reply }}</div>
                            @else
                            <div class="tc-noreply bg-gray-900/40 border border-gray-800 rounded-xl p-3.5 text-center">
                                <p class="text-sm text-gray-500 italic">Ожидает ответа специалиста</p>
                            </div>
                            @endif
                        </div>
                        <button onclick="document.getElementById('tc-{{ $ticket->id }}').close()" class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-sm font-semibold text-gray-400 hover:text-white rounded-xl transition">Закрыть</button>
                    </div>
                </dialog>
                @endforeach
            </div>
            @endif
        </div>

    </div>{{-- /hud --}}

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- РЕАКТИВНЫЕ МОДАЛКИ                                 --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <livewire:leaderboard />
    <livewire:achievements-modal />

    {{-- Game modal --}}
    <dialog id="mod-game" class="bg-[#111318] border border-gray-800 p-0 shadow-2xl focus:outline-none w-screen h-[100dvh] max-w-none rounded-none md:w-[70vw] md:h-[96vh] md:max-w-[90vw] md:rounded-2xl">
        <div class="flex flex-col h-full">
            <div class="hidden md:flex items-center justify-between border-b border-gray-800 px-5 py-4 flex-shrink-0">
                <h3 class="text-base font-bold text-white">Мини-игра RuGear</h3>
                <button onclick="closeGame()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
            </div>
            <div class="flex-1 bg-black overflow-hidden relative">
                <iframe id="game-iframe" src="" class="absolute inset-0 w-full h-full border-0" allow="autoplay; keyboard; fullscreen"></iframe>
                <button onclick="closeGame()" class="md:hidden absolute top-3 right-3 z-[60] w-10 h-10 rounded-full bg-black/60 border border-gray-700 text-white text-2xl leading-none flex items-center justify-center backdrop-blur-sm">×</button>
                <div id="game-toast-wrap" class="absolute bottom-5 right-5 z-50 flex flex-col-reverse gap-3 items-end pointer-events-none"></div>
            </div>
            <div class="hidden md:block border-t border-gray-800 px-4 py-3 flex-shrink-0">
                <button onclick="closeGame()" class="w-full py-2 text-sm font-semibold border border-gray-700 text-gray-500 hover:text-white rounded-xl transition">Закрыть</button>
            </div>
        </div>
    </dialog>

    {{-- Logout modal --}}
    <div id="mod-logout" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4">
        <div class="bg-[#161920] border border-gray-800 w-full max-w-sm rounded-2xl p-8 shadow-2xl">
            <h3 class="text-xl font-black text-white text-center">Выход</h3>
            <p class="text-sm text-gray-500 text-center mt-2 mb-8">Вы уверены, что хотите выйти из аккаунта?</p>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="toggleLogout(false)" class="py-3 bg-gray-900 hover:bg-gray-800 border border-gray-700 text-white font-semibold rounded-xl transition">Остаться</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-black font-black rounded-xl transition">Выйти</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const GAME_SRC = '{{ asset('index.html') }}';
        const REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function focusGame() {
            const iframe = document.getElementById('game-iframe');
            try { iframe.focus(); if (iframe.contentWindow) iframe.contentWindow.focus(); } catch (e) {}
        }
        window.openGame = function() {
            const iframe = document.getElementById('game-iframe');
            if (iframe.src !== GAME_SRC) iframe.src = GAME_SRC;
            document.getElementById('mod-game').showModal();
            focusGame();
            setTimeout(focusGame, 60);
        };
        function closeGame() { document.getElementById('mod-game').close(); }
        document.getElementById('mod-game').addEventListener('close', function() {
            document.getElementById('game-iframe').src = 'about:blank';
        });

        function toggleLogout(show) {
            const m = document.getElementById('mod-logout');
            if (show) { m.classList.remove('hidden'); m.classList.add('flex'); document.body.style.overflow = 'hidden'; }
            else { m.classList.add('hidden'); m.classList.remove('flex'); document.body.style.overflow = ''; }
        }
        window.addEventListener('click', e => { if (e.target === document.getElementById('mod-logout')) toggleLogout(false); });
        document.querySelectorAll('.tcmodal').forEach(d => {
            d.addEventListener('click', e => {
                const r = d.getBoundingClientRect();
                if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) d.close();
            });
        });

        function toggleDetails(id) {
            const el = document.getElementById('det-' + id);
            const icon = document.getElementById('btn-icon-' + id);
            if (!el) return;
            const open = el.classList.contains('hidden');
            el.classList.toggle('hidden');
            if (icon) icon.style.transform = open ? 'rotate(180deg)' : '';
        }

        function toggleAbout(btn) {
            const wrap = document.getElementById('about-wrap');
            const open = btn.dataset.open === '1';
            if (open) { wrap.style.maxHeight = '4.2rem'; btn.textContent = 'Показать ещё ↓'; btn.dataset.open = '0'; }
            else { wrap.style.maxHeight = wrap.scrollHeight + 'px'; btn.textContent = 'Скрыть ↑'; btn.dataset.open = '1'; }
        }

        // ── Leaderboard: client-side search + per-row expand ──
        function leaderSearch(q) {
            q = q.toLowerCase().trim();
            const podium = document.getElementById('lb-podium');
            if (podium) podium.style.display = q ? 'none' : '';
            let any = false;
            document.querySelectorAll('#lb-list .lb-row').forEach(row => {
                const show = !q || row.dataset.name.includes(q);
                row.style.display = show ? '' : 'none';
                if (show) any = true;
            });
            const empty = document.getElementById('lb-empty');
            if (empty) empty.classList.toggle('hidden', any);
        }
        function toggleLeader(id) {
            const det = document.getElementById('leader-det-' + id);
            const btn = document.getElementById('leader-btn-' + id);
            if (!det) return;
            const isHidden = det.classList.contains('hidden');
            det.classList.toggle('hidden');
            if (btn) btn.textContent = isHidden ? 'Свернуть' : 'Профиль';
        }

        // ── Achievements modal: client-side filter chips ──
        function achFilter(btn, f) {
            document.querySelectorAll('#ach-filters .ach-chip').forEach(c => c.classList.toggle('is-active', c === btn));
            let any = false;
            document.querySelectorAll('#ach-grid .ach-tile').forEach(t => {
                const st = t.dataset.state;
                const show = f === 'all' || (f === 'got' && st === 'got') || (f === 'locked' && st === 'locked');
                t.style.display = show ? '' : 'none';
                if (show) any = true;
            });
            const empty = document.getElementById('ach-grid-empty');
            if (empty) empty.classList.toggle('hidden', any);
        }

        // ── Live stat count-up (animates when Livewire morphs new values in) ──
        const countState = new WeakMap();
        const fmtNum = (n, sep) => sep ? Math.round(n).toLocaleString('ru-RU') : String(Math.round(n));
        function animateCounts(scope) {
            (scope || document).querySelectorAll('[data-count-to]').forEach(el => {
                const target = parseInt(el.dataset.countTo, 10) || 0;
                const sep = el.dataset.countFmt === '1';
                const prev = countState.get(el);
                if (prev === undefined) { countState.set(el, target); el.textContent = fmtNum(target, sep); return; }
                if (prev === target) return;
                countState.set(el, target);
                if (REDUCED_MOTION) { el.textContent = fmtNum(target, sep); return; }
                if (target > prev) { el.classList.add('stat-up'); setTimeout(() => el.classList.remove('stat-up'), 550); }
                const t0 = performance.now(), dur = 700;
                (function tick(now) {
                    const p = Math.min(1, (now - t0) / dur);
                    const e = 1 - Math.pow(1 - p, 3);
                    el.textContent = fmtNum(prev + (target - prev) * e, sep);
                    if (p < 1) requestAnimationFrame(tick);
                })(t0);
            });
        }

        // ── Achievement strip: show nav arrows only when the track overflows ──
        function syncAchNav() {
            const track = document.getElementById('ach-track');
            const nav = document.getElementById('ach-nav');
            if (!track || !nav) return;
            const overflows = track.scrollWidth > track.clientWidth + 2;
            nav.style.display = overflows ? 'flex' : 'none';
            // Center cards when they fit; left-align when overflowing so the
            // first cards stay scroll-reachable (justify-center would clip them).
            track.style.justifyContent = overflows ? 'flex-start' : 'center';
        }

        // ── Level-up reveal (fired by ProfileHud when an external level gain lands) ──
        function showLevelUp(payload) {
            const d = Array.isArray(payload) ? payload[0] : payload;
            const level = d && d.level ? d.level : '';
            const tier = d && d.tier ? d.tier : '';
            const el = document.createElement('div');
            el.className = 'levelup-reveal';
            el.innerHTML =
                '<div class="levelup-reveal__icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>' +
                '<div><p style="font-size:.6rem;font-weight:800;letter-spacing:.2em;text-transform:uppercase;color:#20f8c0">Новый уровень</p>' +
                '<p style="font-size:.95rem;font-weight:900;margin-top:.1rem">LV ' + level + (tier ? ' · ' + tier : '') + '</p></div>';
            document.body.appendChild(el);
            setTimeout(() => { el.classList.add('out'); setTimeout(() => el.remove(), 350); }, 4200);
        }

        // ── Game reward toast ──
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
                '<div class="flex gap-3 items-start">' +
                '<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(168,85,247,0.18)">' +
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#c084fc"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>' +
                '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<p class="text-xs font-bold uppercase tracking-widest" style="color:#c084fc">Buzzword Blast</p>' +
                '<p class="text-sm font-bold text-white mt-0.5">Уровень пройден!</p>' +
                '<div class="flex items-center gap-3 mt-1">' +
                '<span class="text-xs text-gray-400">+' + data.xp + ' XP</span>' +
                '<span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b">' +
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0"><circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/><circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/><text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text></svg>' +
                '+' + data.coins + ' монета</span>' +
                '</div></div>' +
                '<button onclick="(function(id){const t=document.getElementById(id);if(!t)return;t.classList.add(\'out\');setTimeout(()=>t.remove(),300);}(\'' + id + '\'))" class="text-gray-600 hover:text-white text-lg leading-none flex-shrink-0">×</button>' +
                '</div>';
            wrap.prepend(el);
            setTimeout(() => { const t = document.getElementById(id); if (!t) return; t.classList.add('out'); setTimeout(() => t.remove(), 300); }, 5000);
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
            .then(data => {
                showGameToast(data);
                if (window.Livewire) Livewire.dispatch('profile-refresh'); // live-sync stat matrix
            });
        });

        // ── Wire reactive hooks once Livewire is ready ──
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morphed', ({ el }) => { animateCounts(el); syncAchNav(); });
            Livewire.on('hud-levelup', (payload) => showLevelUp(payload));
            Livewire.on('lock-body', () => { document.body.style.overflow = 'hidden'; });
            Livewire.on('unlock-body', () => { document.body.style.overflow = ''; });
        });
        document.addEventListener('livewire:navigated', () => { animateCounts(); syncAchNav(); });
        window.addEventListener('DOMContentLoaded', () => { animateCounts(); syncAchNav(); });
        window.addEventListener('resize', syncAchNav);
    </script>
</x-shop-layout>
