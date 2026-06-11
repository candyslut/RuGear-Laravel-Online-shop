@once
<style>
    /* ─────────────────────────────────────────────────────────────────────
       Mini-game statistics — self-contained, theme-aware palette so the modal
       and the dashboard widget look right on every page and every site theme.
       Charts read --gs-accent / --gs-line / --gs-bg; an accent is set per game
       on the panel/tab so each game keeps its colour.
       ───────────────────────────────────────────────────────────────────── */
    .gs-hud, .gs-wm {
        --gs-bg:#0e1014; --gs-bg2:#14171d; --gs-inset:#0a0c10;
        --gs-line:#232a36; --gs-line2:#333c4b;
        --gs-text:#e6e8ec; --gs-dim:#8b94a3; --gs-dim2:#5a6473; --gs-track:#1a1e26;
        --gs-accent:#a855f7; --gs-coin:#f59e0b; --gs-xp:#34d399;
        /* Per-game accents — match the dashboard game banners. */
        --gs-bw:#20f8c0; --gs-rl:#f87171;
    }
    [data-theme="light"] .gs-hud, [data-theme="light"] .gs-wm {
        --gs-bg:#ffffff; --gs-bg2:#f1f4f8; --gs-inset:#e9edf2;
        --gs-line:#d7dde6; --gs-line2:#c2cad6;
        --gs-text:#141a22; --gs-dim:#5b6472; --gs-dim2:#97a0ad; --gs-track:#e6eaf0;
        --gs-accent:#9333ea; --gs-coin:#d97706; --gs-xp:#059669;
        --gs-bw:#0d9488; --gs-rl:#dc2626;
    }
    [data-theme="cosmic"] .gs-hud, [data-theme="cosmic"] .gs-wm {
        --gs-bg:#10153a; --gs-bg2:#161d4a; --gs-inset:#0a0e29;
        --gs-line:#36418c; --gs-line2:#4651a3;
        --gs-text:#eef1ff; --gs-dim:#aab4e8; --gs-dim2:#7681bd; --gs-track:#1c2354;
        --gs-accent:#a78bfa; --gs-coin:#fbbf24; --gs-xp:#5eead4;
        --gs-bw:#20f8c0; --gs-rl:#fb7185;
    }
    [data-theme="pink"] .gs-hud, [data-theme="pink"] .gs-wm {
        --gs-bg:#fff7fb; --gs-bg2:#fbe9f3; --gs-inset:#f7ddeb;
        --gs-line:#f0c6dc; --gs-line2:#e4abc9;
        --gs-text:#4a2338; --gs-dim:#92607a; --gs-dim2:#bd93a9; --gs-track:#f3d9e6;
        --gs-accent:#db2777; --gs-coin:#d97706; --gs-xp:#0d9488;
        --gs-bw:#0d9488; --gs-rl:#e11d48;
    }

    [x-cloak] { display: none !important; }

    /* ── Overlay & panel ── */
    .gs-overlay { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .gs-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.78); backdrop-filter: blur(4px); }
    :is([data-theme="light"], [data-theme="pink"]) .gs-backdrop { background: rgba(15,23,42,.45); }
    .gs-panel { position: relative; z-index: 1; display: flex; flex-direction: column; width: 100%; max-width: 44rem; max-height: 92vh; background: var(--gs-bg); border: 1px solid var(--gs-line); border-radius: 16px; box-shadow: 0 30px 80px rgba(0,0,0,.55); color: var(--gs-text); overflow: hidden; }
    @media (prefers-reduced-motion: no-preference) { .gs-panel { animation: gsIn .22s cubic-bezier(.22,1,.36,1); } }
    @keyframes gsIn { from { opacity: 0; transform: translateY(10px) scale(.985); } to { opacity: 1; transform: none; } }

    /* ── Header ── */
    .gs-head { display: flex; align-items: center; gap: .7rem; padding: 1rem 1.2rem; border-bottom: 1px solid var(--gs-line); background: var(--gs-bg2); flex-shrink: 0; }
    .gs-head__bar { width: 3px; height: 20px; background: var(--gs-accent); flex-shrink: 0; border-radius: 2px; }
    .gs-head__ava { width: 2.1rem; height: 2.1rem; border-radius: 9px; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--gs-inset); color: var(--gs-text); font-weight: 800; font-size: .85rem; }
    .gs-head__title { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .16em; color: var(--gs-text); }
    .gs-head__sub { font-size: .68rem; color: var(--gs-dim); margin-top: .12rem; max-width: 16rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gs-x { width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; color: var(--gs-dim); border: 1px solid var(--gs-line); border-radius: 8px; background: transparent; flex-shrink: 0; cursor: pointer; transition: color .12s, border-color .12s, background .12s; }
    .gs-x:hover { color: var(--gs-text); border-color: var(--gs-accent); background: var(--gs-bg); }
    .gs-x svg { width: 1rem; height: 1rem; }

    /* ── Overview strip ── */
    .gs-overview { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem; padding: .9rem 1.2rem; border-bottom: 1px solid var(--gs-line); background: var(--gs-bg2); flex-shrink: 0; }
    .gs-ov { display: flex; flex-direction: column; gap: .15rem; min-width: 0; }
    .gs-ov__v { font-size: 1rem; font-weight: 800; color: var(--gs-text); line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .gs-ov__v--coin { color: var(--gs-coin); }
    .gs-ov__v--xp { color: var(--gs-xp); }
    .gs-ov__l { font-size: .6rem; text-transform: uppercase; letter-spacing: .1em; color: var(--gs-dim2); }

    /* ── Tabs ── */
    .gs-tabs-wrap { display: flex; flex-direction: column; flex: 1; min-height: 0; }
    .gs-tabs { display: flex; gap: .4rem; padding: .8rem 1.2rem 0; flex-shrink: 0; }
    .gs-tab { display: inline-flex; align-items: center; gap: .45rem; padding: .55rem .9rem; border: 1px solid var(--gs-line); border-radius: 10px 10px 0 0; background: transparent; color: var(--gs-dim); font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; cursor: pointer; transition: color .12s, border-color .12s, background .12s; }
    .gs-tab__ic { width: 1rem; height: 1rem; }
    .gs-tab:hover { color: var(--gs-text); }
    .gs-tab.is-active { color: var(--gs-accent); border-color: var(--gs-accent); background: color-mix(in srgb, var(--gs-accent) 10%, transparent); }

    /* ── Body (scrolls) ── */
    .gs-body { padding: 1.1rem 1.2rem; overflow-y: auto; border-top: 1px solid var(--gs-line); }
    .gs-game { display: flex; flex-direction: column; gap: 1.1rem; }

    /* ── Hero ── */
    .gs-hero { display: flex; align-items: center; gap: 1.1rem; }
    .gs-hero__main { min-width: 0; }
    .gs-hero__cap { font-size: .62rem; text-transform: uppercase; letter-spacing: .12em; color: var(--gs-dim2); }
    .gs-hero__best { display: block; font-size: 1.7rem; font-weight: 900; color: var(--gs-text); line-height: 1.1; margin-top: .1rem; }
    .gs-hero__meta { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
    .gs-chip { display: inline-flex; align-items: center; padding: .2rem .55rem; border-radius: 999px; font-size: .64rem; font-weight: 800; letter-spacing: .04em; color: var(--gs-accent); background: color-mix(in srgb, var(--gs-accent) 14%, transparent); }
    .gs-chip--soft { color: var(--gs-dim); background: var(--gs-inset); }

    /* ── Stat tiles ── */
    .gs-tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: .55rem; }
    .gs-tile { display: flex; align-items: center; gap: .6rem; padding: .7rem .75rem; border: 1px solid var(--gs-line); border-radius: 12px; background: var(--gs-bg2); min-width: 0; }
    .gs-tile__ic { width: 2rem; height: 2rem; border-radius: 9px; padding: .42rem; flex-shrink: 0; }
    .gs-tile__body { min-width: 0; display: flex; flex-direction: column; }
    .gs-tile__val { font-size: .92rem; font-weight: 800; color: var(--gs-text); line-height: 1.15; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .gs-tile__lbl { font-size: .6rem; text-transform: uppercase; letter-spacing: .07em; color: var(--gs-dim); margin-top: .1rem; }
    .gs-tile__sub { font-size: .56rem; color: var(--gs-dim2); margin-top: .05rem; }

    /* ── Sections & charts ── */
    .gs-charts { display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.1rem; align-items: start; }
    @media (max-width: 560px) { .gs-charts { grid-template-columns: 1fr; } }
    .gs-section { display: flex; flex-direction: column; gap: .5rem; }
    .gs-section__h { display: flex; align-items: baseline; justify-content: space-between; gap: .5rem; }
    .gs-section__t { font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: var(--gs-text); }
    .gs-section__n { font-size: .58rem; text-transform: uppercase; letter-spacing: .08em; color: var(--gs-dim2); }

    .gs-chart { position: relative; width: 100%; }
    .gs-svg { display: block; width: 100%; height: auto; overflow: visible; }
    .gs-axis { display: flex; justify-content: space-between; margin-top: .3rem; font-size: .56rem; color: var(--gs-dim2); font-family: ui-monospace, Menlo, monospace; }
    .gs-tip { position: absolute; transform: translate(-50%, calc(-100% - 10px)); pointer-events: none; z-index: 5; display: flex; flex-direction: column; gap: 1px; padding: .3rem .5rem; border-radius: 8px; background: var(--gs-inset); border: 1px solid var(--gs-line2); box-shadow: 0 8px 22px rgba(0,0,0,.35); white-space: nowrap; }
    .gs-tip__label { font-size: .56rem; color: var(--gs-dim); font-family: ui-monospace, Menlo, monospace; }
    .gs-tip__val { font-size: .72rem; font-weight: 800; color: var(--gs-text); }

    /* ── Ring ── */
    .gs-ring { position: relative; flex-shrink: 0; }
    .gs-ring__c { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
    .gs-ring__v { font-size: 1.15rem; font-weight: 900; color: var(--gs-text); line-height: 1; }
    .gs-ring__l { font-size: .54rem; text-transform: uppercase; letter-spacing: .06em; color: var(--gs-dim2); margin-top: .15rem; }

    /* ── Donut ── */
    .gs-donut { display: flex; flex-direction: column; align-items: center; gap: .7rem; }
    .gs-donut__ring { position: relative; flex-shrink: 0; }
    .gs-donut__ring svg circle { cursor: default; }
    .gs-legend { display: flex; flex-wrap: wrap; justify-content: center; gap: .3rem .7rem; }
    .gs-legend__i { display: inline-flex; align-items: center; gap: .3rem; font-size: .6rem; font-weight: 700; color: var(--gs-text); }
    .gs-legend__i--off { color: var(--gs-dim2); font-weight: 600; }
    .gs-legend__dot { width: .55rem; height: .55rem; border-radius: 50%; flex-shrink: 0; }
    .gs-legend__v { margin-left: .25rem; color: var(--gs-dim); font-family: ui-monospace, Menlo, monospace; }

    /* ── Best runs ── */
    .gs-bests { display: flex; flex-direction: column; gap: .4rem; }
    .gs-best { display: flex; align-items: center; gap: .7rem; padding: .55rem .7rem; border: 1px solid var(--gs-line); border-radius: 10px; background: var(--gs-bg2); }
    .gs-best__rk { width: 1.4rem; height: 1.4rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 7px; font-size: .68rem; font-weight: 900; color: var(--gs-accent); background: color-mix(in srgb, var(--gs-accent) 14%, transparent); }
    .gs-best__main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .gs-best__sc { font-size: .82rem; font-weight: 800; color: var(--gs-text); }
    .gs-best__meta { font-size: .58rem; color: var(--gs-dim2); }
    .gs-best__date { font-size: .6rem; color: var(--gs-dim); font-family: ui-monospace, Menlo, monospace; flex-shrink: 0; }

    /* ── Empty / private ── */
    .gs-empty { display: flex; align-items: center; justify-content: center; color: var(--gs-dim2); font-size: .72rem; }
    .gs-empty--tall { flex-direction: column; gap: .4rem; padding: 2.5rem 1rem; text-align: center; }
    .gs-empty__t { font-size: .8rem; font-weight: 800; color: var(--gs-dim); }
    .gs-empty__s { font-size: .68rem; color: var(--gs-dim2); }
    .gs-private { display: flex; flex-direction: column; align-items: center; gap: .55rem; padding: 3rem 1.5rem; text-align: center; }
    .gs-private__ic { width: 3rem; height: 3rem; color: var(--gs-dim); }
    .gs-private__ic svg { width: 100%; height: 100%; }
    .gs-private__t { font-size: .9rem; font-weight: 800; color: var(--gs-text); }
    .gs-private__s { font-size: .72rem; color: var(--gs-dim2); max-width: 22rem; }

    /* ── Leaderboards ── */
    .gs-period { display: flex; gap: .4rem; padding: .7rem 1.2rem 0; flex-shrink: 0; }
    .gs-period__b { flex: 1; padding: .45rem .6rem; border: 1px solid var(--gs-line); border-radius: 9px; background: transparent; color: var(--gs-dim); font-size: .64rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; cursor: pointer; transition: color .12s, border-color .12s, background .12s; }
    .gs-period__b.is-active { color: var(--gs-text); border-color: var(--gs-accent); background: color-mix(in srgb, var(--gs-accent) 10%, transparent); }
    .gs-lb { display: flex; flex-direction: column; gap: .4rem; }
    .gs-lbrow { display: flex; align-items: center; gap: .65rem; width: 100%; padding: .5rem .6rem; border: 1px solid var(--gs-line); border-radius: 11px; background: var(--gs-bg2); cursor: pointer; text-align: left; transition: border-color .12s, background .12s, transform .08s; }
    .gs-lbrow:hover { border-color: var(--gs-accent); }
    .gs-lbrow:active { transform: translateY(1px); }
    .gs-lbrow.is-me { border-color: var(--gs-accent); background: color-mix(in srgb, var(--gs-accent) 9%, var(--gs-bg2)); }
    .gs-lbrow__rk { width: 1.6rem; height: 1.6rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: .72rem; font-weight: 900; color: var(--gs-dim); background: var(--gs-inset); }
    .gs-lbrow__rk--1 { color: #1a1205; background: #fbbf24; }
    .gs-lbrow__rk--2 { color: #1a1d22; background: #cbd5e1; }
    .gs-lbrow__rk--3 { color: #2a1606; background: #d8954e; }
    .gs-lbrow__ava { width: 2rem; height: 2rem; flex-shrink: 0; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--gs-inset); color: var(--gs-text); font-weight: 800; font-size: .8rem; }
    .gs-lbrow__main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .gs-lbrow__name { font-size: .8rem; font-weight: 700; color: var(--gs-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .gs-lbrow__you { display: inline-block; margin-left: .35rem; padding: .05rem .35rem; border-radius: 5px; font-size: .54rem; font-weight: 800; text-transform: uppercase; color: var(--gs-accent); background: color-mix(in srgb, var(--gs-accent) 16%, transparent); vertical-align: middle; }
    .gs-lbrow__sub { font-size: .58rem; color: var(--gs-dim2); }
    .gs-lbrow__sc { font-size: .9rem; font-weight: 800; color: var(--gs-accent); flex-shrink: 0; }

    /* ── Footer ── */
    .gs-footer { display: flex; gap: .6rem; padding: 1rem 1.2rem; border-top: 1px solid var(--gs-line); background: var(--gs-bg2); flex-shrink: 0; }
    .gs-btn { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; padding: .7rem 1rem; border-radius: 10px; font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; cursor: pointer; transition: color .12s, border-color .12s, background .12s, transform .08s; border: 1px solid var(--gs-line2); background: transparent; }
    .gs-btn:active { transform: translateY(1px); }
    .gs-btn--accent { color: var(--gs-accent); border-color: var(--gs-accent); }
    .gs-btn--accent:hover { background: color-mix(in srgb, var(--gs-accent) 12%, transparent); }
    .gs-btn--ghost { color: var(--gs-dim); flex: 1; }
    .gs-btn--ghost:hover { color: var(--gs-text); border-color: var(--gs-accent); }

    /* ── Responsive ── */
    @media (max-width: 560px) {
        .gs-overlay { padding: 0; }
        .gs-panel { max-width: 100%; max-height: 100dvh; height: 100dvh; border-radius: 0; border: 0; }
        .gs-tiles { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 420px) {
        .gs-overview { grid-template-columns: repeat(2, 1fr); gap: .7rem .5rem; }
        .gs-hero__best { font-size: 1.45rem; }
    }

    /* ── Dashboard widget (compact summary card) ── */
    .gs-wm { border: 1px solid var(--line, var(--gs-line)); background: var(--bg, var(--gs-bg)); }
    .gs-wm__more { display: inline-flex; align-items: center; gap: .25rem; font-size: .62rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; color: var(--gs-accent); background: transparent; border: 0; cursor: pointer; padding: .2rem .1rem; transition: opacity .12s; }
    .gs-wm__more:hover { opacity: .7; }
    .gs-wm__row { display: flex; align-items: center; gap: 1rem; }
    .gs-wm__stat { display: flex; flex-direction: column; }
    .gs-wm__v { font-size: 1.1rem; font-weight: 900; color: var(--gs-text); line-height: 1.05; }
    .gs-wm__l { font-size: .58rem; text-transform: uppercase; letter-spacing: .1em; color: var(--gs-dim2); margin-top: .1rem; }
    .gs-wm__games { display: grid; grid-template-columns: repeat(2, 1fr); gap: .55rem; }
    .gs-wm__game { display: flex; flex-direction: column; gap: .3rem; padding: .65rem .7rem; border: 1px solid var(--gs-line); border-radius: 11px; background: var(--gs-bg2); }
    .gs-wm__ghead { display: flex; align-items: center; gap: .45rem; }
    .gs-wm__gic { width: 1.5rem; height: 1.5rem; border-radius: 7px; padding: .3rem; flex-shrink: 0; }
    .gs-wm__gname { font-size: .62rem; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: var(--gs-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .gs-wm__gbest { font-size: .9rem; font-weight: 800; color: var(--gs-text); }
    .gs-wm__gsub { font-size: .56rem; color: var(--gs-dim2); }
    .gs-spark { display: block; width: 100%; height: 30px; }
</style>
@endonce
