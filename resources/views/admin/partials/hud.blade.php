{{-- ═══════════════════════════════════════════════════════════════
     HUD design system for the admin command surfaces.
     Theme-aware (dark default + [data-theme="light"] overrides).
     Include once per page: @include('admin.partials.hud')
     Wrap content in a .hud root to inherit the token scope.
     ═══════════════════════════════════════════════════════════════ --}}
<style>
    .hud {
        --bg:        #0e1014;
        --bg-2:      #14171d;
        --inset:     #090b0e;
        --line:      #232a36;
        --line-2:    #333c4b;
        --grid:      rgba(148,163,184,.06);
        --text:      #e6e8ec;
        --dim:       #828b9b;
        --dim-2:     #525a68;
        --accent:    #f97316;
        --track:     #1a1e26;
    }
    [data-theme="light"] .hud {
        --bg:        #ffffff;
        --bg-2:      #f1f4f8;
        --inset:     #e9edf2;
        --line:      #d7dde6;
        --line-2:    #c2cad6;
        --grid:      rgba(100,116,139,.10);
        --text:      #141a22;
        --dim:       #5b6472;
        --dim-2:     #97a0ad;
        --accent:    #ea580c;
        --track:     #e6eaf0;
    }

    .hud { color: var(--text); }

    /* ── color helpers ── */
    .t-text { color: var(--text); }
    .t-dim  { color: var(--dim); }
    .t-dim2 { color: var(--dim-2); }
    .t-acc  { color: var(--accent); }
    .hud-mono { font-family: ui-monospace, "SF Mono", "JetBrains Mono", Menlo, monospace; }

    /* ── framed module ── */
    .hud-panel { position: relative; background: var(--bg); border: 1px solid var(--line); }
    .hud-inset { background: var(--inset); border: 1px solid var(--line); }

    /* corner brackets — restrained HUD accent on key modules */
    .hud-corner::before,
    .hud-corner::after {
        content: ''; position: absolute; width: 11px; height: 11px;
        border: 2px solid var(--accent); pointer-events: none; z-index: 2;
    }
    .hud-corner::before { top: -1px;    left: -1px;  border-right: 0; border-bottom: 0; }
    .hud-corner::after  { bottom: -1px; right: -1px; border-left: 0;  border-top: 0; }

    /* module header strip */
    .hud-head { display: flex; align-items: center; gap: .6rem; padding: .7rem .95rem; border-bottom: 1px solid var(--line); }
    .hud-head__bar { width: 3px; height: 15px; background: var(--accent); flex-shrink: 0; }
    .hud-head__title { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .2em; color: var(--text); }
    .hud-head__code { margin-left: auto; font-size: .6rem; letter-spacing: .14em; color: var(--dim-2); }

    /* technical column header */
    .hud-colhead { font-size: .58rem; font-weight: 700; text-transform: uppercase; letter-spacing: .2em; color: var(--dim-2); }

    /* log / data row with status rail on hover */
    .hud-row { position: relative; transition: background .12s ease, box-shadow .12s ease; }
    .hud-row + .hud-row { border-top: 1px solid var(--line); }
    .hud-row:hover { background: var(--bg-2); box-shadow: inset 3px 0 0 var(--accent); }
    .hud-row__rail { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }

    /* buttons — mechanical */
    .hud-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        padding: .45rem .75rem; border: 1px solid var(--line-2); background: transparent;
        color: var(--dim); font-size: .66rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em;
        transition: color .12s ease, border-color .12s ease, background .12s ease, transform .08s ease; cursor: pointer;
    }
    .hud-btn:hover { color: var(--text); border-color: var(--accent); background: var(--bg-2); }
    .hud-btn:active { transform: translateY(1px); }
    .hud-btn--solid { background: var(--accent); border-color: var(--accent); color: #0a0a0a; }
    .hud-btn--solid:hover { filter: brightness(1.08); color: #0a0a0a; background: var(--accent); }
    .hud-btn--danger:hover { color: #ef4444; border-color: #ef4444; background: rgba(239,68,68,.08); }

    /* inputs */
    .hud-input { background: var(--inset); border: 1px solid var(--line); color: var(--text); transition: border-color .12s ease; }
    .hud-input::placeholder { color: var(--dim-2); }
    .hud-input:focus { outline: none; border-color: var(--accent); }

    /* segmented control */
    .hud-seg { display: inline-flex; border: 1px solid var(--line); background: var(--inset); }
    .hud-seg__btn {
        padding: .5rem .85rem; font-size: .66rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em;
        color: var(--dim); background: transparent; border: 0; border-right: 1px solid var(--line);
        transition: color .12s ease, background .12s ease; cursor: pointer; white-space: nowrap;
    }
    .hud-seg__btn:last-child { border-right: 0; }
    .hud-seg__btn:hover { color: var(--text); background: var(--bg-2); }
    .hud-seg__btn--on { color: #0a0a0a; background: var(--accent); }
    .hud-seg__btn--on:hover { color: #0a0a0a; background: var(--accent); }

    /* segmented tier bar (level / progress as ticks) */
    .hud-ticks { display: flex; gap: 2px; }
    .hud-ticks span { width: 4px; height: 12px; background: var(--track); }
    .hud-ticks span.on { background: var(--accent); }

    /* thin meter */
    .hud-meter { height: 5px; background: var(--track); overflow: hidden; }
    .hud-meter > span { display: block; height: 100%; }

    /* faint engineering grid background */
    .hud-grid-bg {
        background-image: linear-gradient(var(--grid) 1px, transparent 1px),
                          linear-gradient(90deg, var(--grid) 1px, transparent 1px);
        background-size: 24px 24px;
    }

    /* square state marker */
    .hud-state { width: 9px; height: 9px; flex-shrink: 0; }

    .hud-tnum { font-variant-numeric: tabular-nums; }
</style>
