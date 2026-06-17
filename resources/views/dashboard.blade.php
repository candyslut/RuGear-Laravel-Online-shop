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
        .custom-pagination nav>div:last-child { display: flex !important; flex-wrap: wrap !important; justify-content: flex-end !important; gap: 4px 0 !important; background: transparent !important; }
        .custom-pagination a, .custom-pagination span[aria-current="page"] span { background: #161920 !important; color: #9ca3af !important; border: 1px solid #374151 !important; font-size: 12px !important; padding: 7px 13px !important; margin: 0 2px !important; border-radius: 10px !important; transition: all .15s !important; }
        .custom-pagination span[aria-disabled="true"] span { background: #161920 !important; color: #4b5563 !important; border: 1px solid #374151 !important; font-size: 12px !important; padding: 7px 13px !important; margin: 0 2px !important; border-radius: 10px !important; cursor: not-allowed !important; }
        .custom-pagination span[aria-current="page"] span { background: #f97316 !important; color: #000 !important; border-color: #f97316 !important; font-weight: 900 !important; }
        [data-theme="cosmic"] .custom-pagination span[aria-current="page"] span { background: #a78bfa !important; border-color: #a78bfa !important; }
        [data-theme="pink"] .custom-pagination span[aria-current="page"] span { background: #ec4899 !important; border-color: #ec4899 !important; color: #fff !important; }
        .custom-pagination a:hover { background: #1f2937 !important; color: #fff !important; }

        #ach-track { scrollbar-width: none; -ms-overflow-style: none; }
        #ach-track::-webkit-scrollbar { display: none; }
        /* Центровка через auto-маржины: justify-center в overflow-контейнере
           обрезает левый край без возможности доскроллить до него. */
        #ach-track > :first-child { margin-left: auto; }
        #ach-track > :last-child  { margin-right: auto; }

        /* Runner modal: red accents on the active site theme */
        .runner-modal { --rn-bg: #140a0c; --rn-line: rgba(127,29,29,.55); --rn-title: #f87171; --rn-dim: rgba(254,202,202,.6); --rn-hint: rgba(254,202,202,.4); --rn-x: rgba(252,165,165,.5); --rn-x-hover: #ffffff; background: var(--rn-bg); }
        :is([data-theme="light"], [data-theme="pink"]) .runner-modal { --rn-bg: #fff7f7; --rn-line: #fecaca; --rn-title: #dc2626; --rn-dim: rgba(127,29,29,.65); --rn-hint: rgba(127,29,29,.45); --rn-x: rgba(127,29,29,.45); --rn-x-hover: #7f1d1d; }
        .runner-toast { background: #1f0d10; border: 1px solid rgba(248,113,113,.45); color: #fecaca; box-shadow: 0 0 24px rgba(248,113,113,.15); }
        /* Мобильная версия: тост полупрозрачный и компактный, чтобы не закрывать трассу */
        @media (max-width: 639px) { .runner-toast { opacity: .92; } .runner-toast .w-10 { width: 2rem; height: 2rem; } }
        :is([data-theme="light"], [data-theme="pink"]) .runner-toast { background: #ffffff; border-color: #fca5a5; color: #7f1d1d; box-shadow: 0 12px 32px rgba(127,29,29,.18); }

        /* ── Light-theme dashboard fixes ── */
        :is([data-theme="light"], [data-theme="pink"]) .border-\[\#161920\] { border-color: #ffffff !important; }
        :is([data-theme="light"], [data-theme="pink"]) .hover\:bg-white\/\[0\.02\]:hover { background-color: rgba(0, 0, 0, .035) !important; }

        /* ── Ticket modal accents (light) ── */
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal { border-color: #d8dce2 !important; box-shadow: 0 24px 60px rgba(15, 23, 42, .22) !important; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .text-gray-600 { color: #64748b !important; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-label { color: #475569 !important; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-cat { background: #e0f2fe !important; border-color: #7dd3fc !important; color: #0369a1 !important; font-weight: 600; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-msg { background: #f4f6f9 !important; border-color: #d3d8e0 !important; color: #1f2937 !important; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-reply-label { color: #ea580c !important; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-reply { background: #fff7ed !important; border: 1px solid #fb923c !important; color: #1f2937 !important; box-shadow: inset 3px 0 0 #f97316; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-noreply { background: #f8fafc !important; border: 1px dashed #cbd5e1 !important; }
        :is([data-theme="light"], [data-theme="pink"]) .tcmodal .tc-noreply p { color: #64748b !important; }

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
        :is([data-theme="light"], [data-theme="pink"]) .hud {
            --bg: #ffffff; --bg-2: #f1f4f8; --inset: #e9edf2;
            --line: #d7dde6; --line-2: #c2cad6; --grid: rgba(100,116,139,.10);
            --text: #141a22; --dim: #5b6472; --dim-2: #97a0ad;
            --accent: #ea580c; --mint: #0d9488; --track: #e6eaf0;
        }
        /* Тема «Космос» из мини-маркета: панели HUD садятся в индиго-палитру. */
        [data-theme="cosmic"] .hud {
            --bg: #10153a; --bg-2: #161d4a; --inset: #0a0e29;
            --line: #36418c; --line-2: #4651a3; --grid: rgba(165,180,252,.07);
            --text: #eef1ff; --dim: #aab4e8; --dim-2: #7681bd;
            --accent: #a78bfa; --mint: #20f8c0; --track: #1c2354;
        }
        /* Тема «Няшный розовый» из мини-маркета: панели HUD — розовая пастель. */
        [data-theme="pink"] .hud {
            --bg: #fff7fb; --bg-2: #fbe9f3; --inset: #f7ddeb;
            --line: #f0c6dc; --line-2: #e4abc9; --grid: rgba(219,39,119,.06);
            --text: #4a2338; --dim: #92607a; --dim-2: #bd93a9;
            --accent: #db2777; --mint: #0d9488; --track: #f3d9e6;
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

        .hud-head { display: flex; align-items: center; flex-wrap: wrap; gap: .35rem .6rem; padding: .7rem .95rem; border-bottom: 1px solid var(--line); }
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
        .ach-medallion__icon { width: 3rem; height: 3rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--rc); background: color-mix(in srgb, var(--rc) 12%, transparent); border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); margin-bottom: .6rem; border-radius: 12px; }
        .ach-medallion__rarity { font-size: .54rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--rc); }
        .ach-medallion__title { font-size: .8rem; font-weight: 700; color: var(--text); line-height: 1.25; margin: .35rem 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2rem; }
        .ach-medallion__xp { font-family: ui-monospace, Menlo, monospace; font-size: .72rem; font-weight: 700; color: var(--accent); margin-top: .4rem; }
        .ach-medallion__coins { color: #f59e0b; white-space: nowrap; }
        .new-badge { position: absolute; top: 6px; right: 6px; font-size: .5rem; font-weight: 900; letter-spacing: .08em; color: #000; background: var(--accent); padding: .12rem .32rem; }

        /* Overlay modals (leaderboard / achievements) */
        .lb-overlay { position: fixed; inset: 0; z-index: 60; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .lb-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.8); }
        :is([data-theme="light"], [data-theme="pink"]) .lb-backdrop { background: rgba(15,23,42,.45); }
        .lb-panel { position: relative; z-index: 1; display: flex; flex-direction: column; width: 100%; max-width: 56rem; height: 92vh; max-height: 92vh; background: var(--bg); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 30px 80px rgba(0,0,0,.55); }
        .lb-panel--ach { max-width: 42rem; }
        :is([data-theme="light"], [data-theme="pink"]) .lb-panel { box-shadow: 0 24px 60px rgba(15,23,42,.22); }
        @media (prefers-reduced-motion: no-preference) {
            .lb-panel { animation: lbIn .22s cubic-bezier(.22,1,.36,1); }
            .lb-backdrop { animation: lbFadeIn .22s ease; }
            .lb-overlay.is-closing .lb-panel { animation: lbOut .16s cubic-bezier(.4,0,1,1) forwards; }
            .lb-overlay.is-closing .lb-backdrop { animation: lbFadeOut .16s ease forwards; }
        }
        .lb-overlay.is-closing { pointer-events: none; }
        @keyframes lbIn { from { opacity: 0; transform: translateY(10px) scale(.985); } to { opacity: 1; transform: none; } }
        @keyframes lbOut { from { opacity: 1; transform: none; } to { opacity: 0; transform: translateY(8px) scale(.985); } }
        @keyframes lbFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes lbFadeOut { from { opacity: 1; } to { opacity: 0; } }
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
        .ach-tile__icon { width: 2.75rem; height: 2.75rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--rc); background: color-mix(in srgb, var(--rc) 12%, transparent); border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); border-radius: 12px; }
        .ach-tile.is-locked .ach-tile__icon { color: var(--dim-2); background: var(--bg-2); border-color: var(--line); }
        .ach-tile__title { font-size: .85rem; font-weight: 700; color: var(--text); }
        .ach-tile__rarity { flex-shrink: 0; font-size: .5rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--rc); padding: .06rem .3rem; border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); }
        .ach-tile.is-locked .ach-tile__rarity { color: var(--dim-2); border-color: var(--line); }
        .ach-tile__desc { font-size: .72rem; color: var(--dim); margin-top: .2rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .ach-tile__aside { display: flex; flex-direction: column; align-items: flex-end; gap: .35rem; }
        .ach-tile__reward { display: inline-flex; align-items: center; gap: .5rem; padding: .3rem .55rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 10px; }
        .ach-tile.is-locked .ach-tile__reward { opacity: .6; }
        .ach-tile__reward-xp { font-family: ui-monospace, Menlo, monospace; font-size: .9rem; font-weight: 800; color: var(--rc); line-height: 1; }
        .ach-tile.is-locked .ach-tile__reward-xp { color: var(--dim-2); }
        .ach-tile__reward-unit { font-size: .55rem; font-weight: 800; letter-spacing: .04em; margin-left: 2px; opacity: .85; }
        .ach-tile__reward-sep { width: 1px; align-self: stretch; background: var(--line); }
        .ach-tile__reward-coins { display: inline-flex; align-items: center; gap: 3px; font-family: ui-monospace, Menlo, monospace; font-size: .8rem; font-weight: 800; color: #f59e0b; line-height: 1; }
        .ach-tile__status { font-size: .54rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .ach-tile.is-got .ach-tile__status { color: #22c55e; }
        .ach-tile.is-locked .ach-tile__status { color: var(--dim-2); }
        .ach-tile { cursor: default; }

        /* Achievement hover tooltip */
        .ach-tip { position: fixed; z-index: 90; width: 20rem; max-width: 86vw; padding: .9rem 1rem; background: var(--bg-2); border: 1px solid color-mix(in srgb, var(--rc) 45%, var(--line)); border-radius: 12px; box-shadow: 0 18px 44px rgba(0,0,0,.5); pointer-events: none; }
        .ach-tip__head { display: flex; align-items: center; gap: .7rem; }
        .ach-tip__icon { width: 2.5rem; height: 2.5rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: var(--rc); background: color-mix(in srgb, var(--rc) 12%, transparent); border: 1px solid color-mix(in srgb, var(--rc) 35%, transparent); border-radius: 10px; }
        .ach-tip__icon svg { width: 1.4rem; height: 1.4rem; }
        .ach-tip__title { font-size: .95rem; font-weight: 800; color: var(--text); line-height: 1.2; }
        .ach-tip__rarity { font-size: .54rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--rc); margin-top: .15rem; }
        .ach-tip__desc { font-size: .8rem; color: var(--dim); line-height: 1.45; margin-top: .65rem; }
        .ach-tip__reward { display: flex; align-items: center; gap: .9rem; margin-top: .65rem; padding-top: .6rem; border-top: 1px solid var(--line); font-family: ui-monospace, Menlo, monospace; font-size: .78rem; font-weight: 800; }
        .ach-tip__reward-coins { display: inline-flex; align-items: center; gap: 4px; color: #f59e0b; }

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
        {{-- ИГРОТЕКА (слайдер-консоль: запуск + статистика по игре) --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <section class="hudpanel overflow-hidden">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Игротека</span>
                @php
                    $gameCount = count(\App\Support\GameStats::keys());
                    $gameWord = ($gameCount % 10 === 1 && $gameCount % 100 !== 11) ? 'ИГРА'
                        : ((in_array($gameCount % 10, [2, 3, 4]) && !in_array($gameCount % 100, [12, 13, 14])) ? 'ИГРЫ' : 'ИГР');
                @endphp
                <span class="hud-head__code">{{ $gameCount }} {{ $gameWord }}</span>
            </div>
            <livewire:game-stats-widget />
        </section>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- ЕЖЕДНЕВНЫЕ ЗАДАНИЯ                                 --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <livewire:daily-quests />

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

                <dialog id="tc-{{ $ticket->id }}" class="tcmodal bg-[#111318] border border-gray-800 rounded-2xl p-5 sm:p-6 w-[calc(100vw-2rem)] max-w-lg shadow-2xl focus:outline-none">
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
    <livewire:ranks-modal />

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

    {{-- Runner game modal (Redline Rush — instant, no iframe) --}}
    <dialog id="mod-runner" class="runner-modal p-0 shadow-2xl focus:outline-none w-[calc(100vw-1rem)] md:w-[80vw] max-w-none rounded-2xl overflow-hidden" style="border:1px solid var(--rn-line)">
        <div class="flex items-center gap-3 px-4 sm:px-5 py-3" style="border-bottom:1px solid var(--rn-line)">
            <h3 class="text-sm sm:text-base font-black uppercase tracking-widest" style="color:var(--rn-title)">Redline Rush</h3>
            <span class="hud-mono text-[11px] sm:text-xs ml-auto whitespace-nowrap" style="color:var(--rn-dim)">
                <span id="runner-dist">0</span> м · <span id="runner-time">00:00</span> · рекорд <span id="runner-best">0</span> м
            </span>
            <button onclick="document.getElementById('mod-runner').close()" class="text-2xl leading-none transition flex-shrink-0 px-1" style="color:var(--rn-x)" onmouseover="this.style.color='var(--rn-x-hover)'" onmouseout="this.style.color='var(--rn-x)'">×</button>
        </div>
        <div class="relative" style="background:var(--rn-bg)">
            <canvas id="runner-canvas" class="block w-full h-[440px] sm:h-[520px] max-h-[calc(100dvh-140px)] touch-none select-none cursor-pointer"></canvas>
            {{-- На мобильных тост живёт сверху (трасса и препятствия — внизу), на десктопе — снизу-справа --}}
            <div id="runner-toast-wrap" class="absolute top-3 right-3 bottom-auto sm:top-auto sm:bottom-4 sm:right-4 z-10 flex flex-col sm:flex-col-reverse gap-2 sm:gap-3 items-end pointer-events-none"></div>
        </div>
        <div class="px-4 sm:px-5 py-2.5" style="border-top:1px solid var(--rn-line)">
            <p class="hud-mono text-[10px] uppercase tracking-widest" style="color:var(--rn-hint)">Пробел / тап — прыжок · Esc — выход</p>
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
        const GAME_SRC = '{{ asset('index.html') }}?v=20260611b';
        const REDUCED_MOTION = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const PLAY_URL = '{{ route('game.play') }}';

        // ── Buzzword Blast run tracking ──
        // A "run" spans from opening the game to game over (or closing the
        // modal). We tally cleared levels and the coins/XP they paid, then post
        // one game_plays row when the run ends (App\Support\GameStats).
        let buzz = null;
        function buzzStart() {
            buzz = { levels: 0, coins: 0, xp: 0, score: 0, asteroids: 0, deaths: 0, start: Date.now(), logged: false };
        }
        function buzzRecord(finalScore) {
            if (!buzz || buzz.logged) return;
            const score = Math.max(finalScore || 0, buzz.score);
            // Nothing happened this run (opened and closed) — don't log noise.
            if (buzz.levels <= 0 && score <= 0) { buzz = null; return; }
            buzz.logged = true;
            fetch(PLAY_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    game: 'buzzword',
                    score: score,
                    level: buzz.levels,
                    duration_ms: Date.now() - buzz.start,
                    coins: buzz.coins,
                    xp: buzz.xp,
                    meta: { asteroids: buzz.asteroids, deaths: buzz.deaths }
                })
            }).then(() => { if (window.Livewire) Livewire.dispatch('game-logged'); }).catch(() => {});
            buzz = null;
        }

        function focusGame() {
            const iframe = document.getElementById('game-iframe');
            try { iframe.focus(); if (iframe.contentWindow) iframe.contentWindow.focus(); } catch (e) {}
        }
        window.openGame = function() {
            const iframe = document.getElementById('game-iframe');
            if (iframe.src !== GAME_SRC) iframe.src = GAME_SRC;
            document.getElementById('mod-game').showModal();
            buzzStart();
            focusGame();
            setTimeout(focusGame, 60);
        };
        function closeGame() { document.getElementById('mod-game').close(); }
        document.getElementById('mod-game').addEventListener('close', function() {
            document.getElementById('game-iframe').src = 'about:blank';
            // Record a run abandoned without a game over (closed mid-game).
            buzzRecord(buzz ? buzz.score : 0);
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
            el.style.border = '1px solid rgba(32,248,192,0.45)';
            el.style.boxShadow = '0 0 24px rgba(32,248,192,0.15)';
            el.innerHTML =
                '<div class="flex gap-3 items-start">' +
                '<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(32,248,192,0.18)">' +
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:#20f8c0"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>' +
                '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<p class="text-xs font-bold uppercase tracking-widest" style="color:#20f8c0">Buzzword Blast</p>' +
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
            if (!e.data) return;

            // Live counter sync (words shot = asteroids, ship crashes = deaths)
            // so an abandoned run still records the right totals, not just a
            // game over.
            if (e.data.type === 'buzz_stats') {
                if (buzz) { buzz.asteroids = e.data.asteroids || 0; buzz.deaths = e.data.deaths || 0; }
                return;
            }

            // Game over — record the run, then we're done with this session.
            if (e.data.type === 'game_over') {
                if (buzz) { buzz.asteroids = e.data.asteroids || 0; buzz.deaths = e.data.deaths || 0; }
                buzzRecord(e.data.score);
                return;
            }

            if (e.data.type !== 'game_level_complete') return;

            // Tally the cleared level toward the current run (in case the modal
            // was already open before buzzStart, lazily begin a session).
            if (!buzz) buzzStart();
            buzz.levels += 1;
            if (typeof e.data.score === 'number') buzz.score = e.data.score;

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
                if (buzz) { buzz.coins += (data.coins || 0); buzz.xp += (data.xp || 0); }
                showGameToast(data);
                // Quest completions / rank-up the level reward may have unlocked.
                (data.quests || []).forEach(q => window.showToast && showToast('quest', q, false));
                if (data.rank_up) window.showToast && showToast('rankup', data.rank_up, false);
                if (window.Livewire) Livewire.dispatch('profile-refresh'); // live-sync stat matrix + quests
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

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- REDLINE RUSH — встроенный canvas-раннер            --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <script>
    (function () {
        const REWARD_URL = '{{ route('game.runner.reward') }}';
        const DIST_URL   = '{{ route('game.runner.distance') }}';
        const PLAY_URL   = '{{ route('game.play') }}';
        const PX_PER_M   = 12;     // world px → "metres"
        const MILESTONE  = 500;    // first reward; each next gap grows by this much
        const BATS_FROM  = 1500;   // metres after which bats join in

        const dlg    = document.getElementById('mod-runner');
        const canvas = document.getElementById('runner-canvas');
        const distEl = document.getElementById('runner-dist');
        const timeEl = document.getElementById('runner-time');
        const bestEl = document.getElementById('runner-best');
        if (!dlg || !canvas) return;
        const ctx = canvas.getContext('2d');

        function palette() {
            const light = document.documentElement.getAttribute('data-theme') === 'light';
            return light ? {
                bg: '#fff7f7', ground: '#fca5a5', groundTop: '#dc2626',
                player: '#dc2626', playerDark: '#7f1d1d',
                spike: '#ef4444', spikeDark: '#b91c1c',
                text: '#7f1d1d', dim: 'rgba(127,29,29,.5)', faint: 'rgba(220,38,38,.06)'
            } : {
                bg: '#140a0c', ground: '#7f1d1d', groundTop: '#ef4444',
                player: '#f87171', playerDark: '#b91c1c',
                spike: '#ef4444', spikeDark: '#991b1b',
                text: '#fecaca', dim: 'rgba(254,202,202,.45)', faint: 'rgba(248,113,113,.07)'
            };
        }
        let C = palette();

        let W = 0, H = 0, groundY = 0, raf = null, lastT = 0, st = null;
        let best = parseInt(localStorage.getItem('runner_best') || '0', 10) || 0;
        bestEl.textContent = best;

        function resize() {
            const dpr = window.devicePixelRatio || 1;
            W = canvas.clientWidth; H = canvas.clientHeight;
            canvas.width = Math.round(W * dpr);
            canvas.height = Math.round(H * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            groundY = H - Math.max(36, Math.round(H * 0.16));
        }

        function reset() {
            st = {
                phase: 'ready', dist: 0, speed: 340, time: 0, runTime: 0, deadAt: 0,
                nextReward: MILESTONE, rewardN: 0,
                py: 0, vy: 0, leg: 0, obstacles: [], nextAt: W + 120, flashes: [],
                // Per-run stats snapshot for the game_plays recorder.
                coins: 0, xp: 0, jumps: 0, logged: false
            };
        }

        function metres() { return Math.floor(st.dist / PX_PER_M); }

        function press() {
            if (st.phase === 'ready') { st.phase = 'run'; return; }
            if (st.phase === 'run' && st.py === 0) { st.vy = 820; st.jumps++; return; }
            // small lockout so a late jump-press doesn't instantly restart
            if (st.phase === 'dead' && st.time - st.deadAt > 0.45) { reset(); st.phase = 'run'; }
        }

        function spawn() {
            let x = st.nextAt;
            if (metres() >= BATS_FROM && Math.random() < 0.35) {
                // bat: alt = bottom edge above ground. Low — jump over it,
                // higher ones — run under (jumping into them is the trap).
                const alt = [8, 55, 95][Math.floor(Math.random() * 3)];
                st.obstacles.push({ type: 'bat', x, w: 40, h: 24, alt, vx: 50 + Math.random() * 70, flap: Math.random() * 6 });
                x += 40;
            } else {
                const n = Math.random() < 0.3 ? 2 : 1; // occasional double spike
                for (let i = 0; i < n; i++) {
                    const w = 16 + Math.random() * 10;
                    const h = 26 + Math.random() * 24;
                    st.obstacles.push({ x, w, h });
                    x += w + 4;
                }
            }
            const gap = 300 + Math.random() * 340 + st.speed * 0.35;
            st.nextAt = x + gap;
        }

        // Best distance unlocks legendary cosmetics in the market, so the
        // server hears about it on every milestone, death and modal close.
        let reportedM = 0;
        function recordDistance(m) {
            if (m <= 0 || m <= reportedM) return;
            reportedM = m;
            fetch(DIST_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ m })
            });
        }

        // End-of-run recorder: one game_plays row per run (death or abandon).
        // Guarded so death + modal-close can't double-log the same run.
        function recordPlay() {
            if (!st || st.logged) return;
            const m = metres();
            if (m <= 0) { return; } // opened and closed without moving
            st.logged = true;
            fetch(PLAY_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    game: 'redline',
                    score: m,
                    level: st.rewardN,
                    duration_ms: Math.round(st.runTime * 1000),
                    coins: st.coins,
                    xp: st.xp,
                    // died === collision (phase 'dead'); abandoned runs aren't a death.
                    meta: { died: st.phase === 'dead' ? 1 : 0, jumps: st.jumps }
                })
            }).then(() => { if (window.Livewire) Livewire.dispatch('game-logged'); }).catch(() => {});
        }

        function claimMilestone(m) {
            recordDistance(m);
            fetch(REWARD_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ m })
            })
            .then(r => r.json())
            .then(data => {
                showRunnerToast(data, m);
                (data.quests || []).forEach(q => window.showToast && showToast('quest', q, false));
                if (data.rank_up) window.showToast && showToast('rankup', data.rank_up, false);
                if (window.Livewire) Livewire.dispatch('profile-refresh');
            });
        }

        function showRunnerToast(data, m) {
            const wrap = document.getElementById('runner-toast-wrap');
            if (!wrap) return;
            const id = 'rt-' + Date.now();
            const el = document.createElement('div');
            el.id = id;
            el.className = 'runner-toast w-56 sm:w-72 rounded-xl sm:rounded-2xl p-2.5 sm:p-4 shadow-2xl toast pointer-events-auto';
            el.innerHTML =
                '<div class="flex gap-3 items-start">' +
                '<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(248,113,113,0.18)">' +
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--rn-title)"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' +
                '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<p class="text-xs font-bold uppercase tracking-widest" style="color:var(--rn-title)">Redline Rush</p>' +
                '<p class="text-sm font-bold mt-0.5">' + m + ' м позади!</p>' +
                '<div class="flex items-center gap-3 mt-1">' +
                '<span class="text-xs opacity-75">+' + data.xp + ' XP</span>' +
                '<span class="flex items-center gap-1 text-xs font-bold" style="color:#f59e0b">' +
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0"><circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/><circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/><text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia,serif">₽</text></svg>' +
                '+' + data.coins + ' монета</span>' +
                '</div></div>' +
                '<button onclick="(function(id){const t=document.getElementById(id);if(!t)return;t.classList.add(\'out\');setTimeout(()=>t.remove(),300);}(\'' + id + '\'))" class="opacity-50 hover:opacity-100 text-lg leading-none flex-shrink-0">×</button>' +
                '</div>';
            wrap.prepend(el);
            setTimeout(() => { const t = document.getElementById(id); if (!t) return; t.classList.add('out'); setTimeout(() => t.remove(), 300); }, 3500);
        }

        function update(dt) {
            st.time += dt;
            if (st.phase !== 'run') return;
            st.runTime += dt;

            // accel ramps with run time (12 → 24 px/s² over ~100 s) so long
            // runs keep getting harder; narrow canvas = less warning
            // distance, so cap top speed lower
            st.speed = Math.min(W < 520 ? 620 : 860, st.speed + (12 + Math.min(12, st.runTime * 0.12)) * dt);
            const dx = st.speed * dt;
            st.dist += dx;

            // jump physics (vy > 0 = upward)
            if (st.py > 0 || st.vy > 0) {
                st.py += st.vy * dt;
                st.vy -= 2400 * dt;
                if (st.py <= 0) { st.py = 0; st.vy = 0; }
            } else {
                st.leg += dt * 12;
            }

            // world scroll + spawn (bats fly towards the player on top of scroll)
            st.nextAt -= dx;
            st.obstacles.forEach(o => {
                o.x -= dx;
                if (o.type === 'bat') { o.x -= o.vx * dt; o.flap += dt; }
            });
            st.obstacles = st.obstacles.filter(o => o.x + o.w > -20);
            if (st.nextAt < W) spawn();

            // collision (player box at fixed x=70, w=30, h=40, with forgiveness)
            const px = 70, pw = 30, ph = 40;
            const pBot = groundY - st.py, pTop = pBot - ph;
            for (const o of st.obstacles) {
                if (px + pw - 6 <= o.x + 3 || px + 6 >= o.x + o.w - 3) continue;
                const hit = o.type === 'bat'
                    ? (pBot - 4 > groundY - o.alt - o.h && pTop + 4 < groundY - o.alt)
                    : (pBot > groundY - o.h + 6);
                if (hit) {
                    st.phase = 'dead';
                    st.deadAt = st.time;
                    const m = metres();
                    if (m > best) { best = m; localStorage.setItem('runner_best', best); bestEl.textContent = best; }
                    recordDistance(m);
                    recordPlay(); // the run is over — snapshot it
                    break;
                }
            }

            // milestone rewards: each next one is further away (500, 1500,
            // 3000, 5000…) so the speed ramp can't farm a reward per second
            if (metres() >= st.nextReward) {
                const at = st.nextReward;
                st.rewardN += 1;
                st.nextReward += MILESTONE * (st.rewardN + 1);
                st.coins += 1; st.xp += 5; // mirrors the runner-reward route payout
                st.flashes.push({ text: '+5 XP · +1 ₽', y: groundY - 90, t: 0 });
                claimMilestone(at);
            }
        }

        function drawPlayer() {
            const x = 70, foot = groundY - st.py;
            ctx.fillStyle = st.phase === 'dead' ? C.playerDark : C.player;
            ctx.fillRect(x, foot - 26, 8, 8);            // tail
            ctx.fillRect(x + 6, foot - 32, 20, 24);      // body
            ctx.fillRect(x + 16, foot - 42, 16, 14);     // head
            ctx.fillStyle = C.bg;
            ctx.fillRect(x + 26, foot - 38, 3, 3);       // eye
            ctx.fillStyle = st.phase === 'dead' ? C.playerDark : C.player;
            if (st.py > 0) {                             // tucked legs in air
                ctx.fillRect(x + 8, foot - 8, 7, 6);
                ctx.fillRect(x + 18, foot - 8, 7, 6);
            } else {
                const a = Math.floor(st.leg) % 2 === 0;
                ctx.fillRect(x + 8, foot - 8, 6, a ? 8 : 5);
                ctx.fillRect(x + 18, foot - 8, 6, a ? 5 : 8);
            }
        }

        function drawSpike(o) {
            ctx.fillStyle = C.spike;
            ctx.beginPath();
            ctx.moveTo(o.x, groundY);
            ctx.lineTo(o.x + o.w / 2, groundY - o.h);
            ctx.lineTo(o.x + o.w, groundY);
            ctx.closePath();
            ctx.fill();
            ctx.fillStyle = C.spikeDark;
            ctx.beginPath();
            ctx.moveTo(o.x + o.w / 2, groundY - o.h);
            ctx.lineTo(o.x + o.w, groundY);
            ctx.lineTo(o.x + o.w / 2, groundY);
            ctx.closePath();
            ctx.fill();
        }

        function drawBat(o) {
            const yt = groundY - o.alt - o.h;          // top edge
            const cx = o.x + o.w / 2;
            const up = Math.floor(o.flap * 9) % 2 === 0;
            ctx.fillStyle = C.spike;
            ctx.beginPath();                            // left wing
            ctx.moveTo(cx - 3, yt + 12);
            ctx.lineTo(o.x, up ? yt : yt + o.h + 2);
            ctx.lineTo(cx - 3, up ? yt + 16 : yt + 8);
            ctx.closePath(); ctx.fill();
            ctx.beginPath();                            // right wing
            ctx.moveTo(cx + 3, yt + 12);
            ctx.lineTo(o.x + o.w, up ? yt : yt + o.h + 2);
            ctx.lineTo(cx + 3, up ? yt + 16 : yt + 8);
            ctx.closePath(); ctx.fill();
            ctx.fillStyle = C.spikeDark;
            ctx.fillRect(cx - 5, yt + 8, 10, 10);       // body
            ctx.fillRect(cx - 5, yt + 4, 3, 4);         // ears
            ctx.fillRect(cx + 2, yt + 4, 3, 4);
            ctx.fillStyle = C.bg;
            ctx.fillRect(cx - 3, yt + 11, 2, 2);        // eyes
            ctx.fillRect(cx + 1, yt + 11, 2, 2);
        }

        function draw() {
            ctx.fillStyle = C.bg;
            ctx.fillRect(0, 0, W, H);

            // faint parallax grid
            ctx.fillStyle = C.faint;
            const off = (st.dist * 0.5) % 52;
            for (let gx = -off; gx < W; gx += 52) ctx.fillRect(gx, 0, 1, H);

            // ground
            ctx.fillStyle = C.groundTop;
            ctx.fillRect(0, groundY, W, 2);
            ctx.fillStyle = C.ground;
            const doff = st.dist % 34;
            for (let gx = -doff; gx < W; gx += 34) ctx.fillRect(gx, groundY + 9, 16, 2);

            st.obstacles.forEach(o => o.type === 'bat' ? drawBat(o) : drawSpike(o));
            drawPlayer();

            // milestone flashes float up
            st.flashes = st.flashes.filter(f => f.t < 1.4);
            st.flashes.forEach(f => {
                ctx.globalAlpha = Math.max(0, 1 - f.t / 1.4);
                ctx.fillStyle = C.player;
                ctx.font = '700 13px ui-monospace, Menlo, monospace';
                ctx.fillText(f.text, 70, f.y - f.t * 28);
                ctx.globalAlpha = 1;
            });

            ctx.textAlign = 'center';
            if (st.phase === 'ready') {
                ctx.fillStyle = C.text;
                ctx.font = '900 16px ui-monospace, Menlo, monospace';
                ctx.fillText('ПРОБЕЛ / ТАП — СТАРТ', W / 2, H / 2 - 14);
                ctx.fillStyle = C.dim;
                ctx.font = '600 11px ui-monospace, Menlo, monospace';
                ctx.fillText('награды на 500 / 1500 / 3000 м и дальше · с ' + BATS_FROM + ' м — летучие мыши', W / 2, H / 2 + 8);
            } else if (st.phase === 'dead') {
                ctx.fillStyle = '#ef4444';
                ctx.font = '900 18px ui-monospace, Menlo, monospace';
                ctx.fillText('СТОЛКНОВЕНИЕ // ' + metres() + ' м', W / 2, H / 2 - 14);
                ctx.fillStyle = C.dim;
                ctx.font = '600 11px ui-monospace, Menlo, monospace';
                ctx.fillText('пробел / тап — ещё раз', W / 2, H / 2 + 8);
            }
            ctx.textAlign = 'left';
        }

        function loop(t) {
            raf = requestAnimationFrame(loop);
            const dt = Math.min(0.05, (t - lastT) / 1000 || 0);
            lastT = t;
            st.flashes.forEach(f => f.t += dt);
            update(dt);
            draw();
            const m = String(metres());
            if (distEl.textContent !== m) distEl.textContent = m;
            const sec = Math.floor(st.runTime);
            const clock = String(Math.floor(sec / 60)).padStart(2, '0') + ':' + String(sec % 60).padStart(2, '0');
            if (timeEl.textContent !== clock) timeEl.textContent = clock;
        }

        window.openRunner = function () {
            C = palette();
            dlg.showModal();
            resize();
            reset();
            lastT = performance.now();
            if (!raf) raf = requestAnimationFrame(loop);
        };
        dlg.addEventListener('close', () => {
            if (raf) { cancelAnimationFrame(raf); raf = null; }
            if (st) { recordDistance(metres()); recordPlay(); } // abandoned run still counts
        });

        document.addEventListener('keydown', e => {
            if (!dlg.open) return;
            if (e.code === 'Space' || e.code === 'ArrowUp' || e.code === 'KeyW') { e.preventDefault(); press(); }
        });
        canvas.addEventListener('pointerdown', e => { e.preventDefault(); press(); });
        window.addEventListener('resize', () => { if (dlg.open) resize(); });
    })();
    </script>
</x-shop-layout>
