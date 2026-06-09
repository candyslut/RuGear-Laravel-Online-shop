<style>
    .qst { display:flex; align-items:center; gap:.85rem; padding:.9rem 1rem; background:var(--inset); border:1px solid var(--line); border-left:3px solid var(--accent); border-radius:10px; transition:border-color .12s ease, background .12s ease; }
    .qst.is-done { border-left-color:#10b981; background:color-mix(in srgb, #10b981 7%, var(--inset)); }
    .qst__icon { position:relative; width:2.6rem; height:2.6rem; flex-shrink:0; display:flex; align-items:center; justify-content:center; color:var(--accent); background:color-mix(in srgb, var(--accent) 12%, transparent); border:1px solid color-mix(in srgb, var(--accent) 35%, transparent); border-radius:12px; }
    .qst.is-done .qst__icon { color:#10b981; background:rgba(16,185,129,.12); border-color:rgba(16,185,129,.4); }
    .qst__check { position:absolute; right:-5px; bottom:-5px; width:1.05rem; height:1.05rem; display:flex; align-items:center; justify-content:center; color:#fff; background:#10b981; border:2px solid var(--bg); border-radius:50%; }
    .qst__title { font-size:.86rem; font-weight:800; color:var(--text); }
    .qst__tag { font-size:.5rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase; color:#10b981; border:1px solid rgba(16,185,129,.4); border-radius:4px; padding:.08rem .35rem; }
    .qst__desc { font-size:.72rem; color:var(--dim); margin-top:.15rem; line-height:1.35; }
    .qst__meter { display:flex; align-items:center; gap:.6rem; margin-top:.55rem; }
    .qst__bar { flex:1; height:6px; border-radius:4px; background:var(--track); overflow:hidden; }
    .qst__bar > span { display:block; height:100%; border-radius:4px; background:var(--accent); transition:width .5s cubic-bezier(.22,1,.36,1); }
    .qst.is-done .qst__bar > span { background:#10b981; }
    .qst__count { font-family:ui-monospace,Menlo,monospace; font-size:.66rem; font-weight:700; color:var(--dim-2); white-space:nowrap; flex-shrink:0; }
    .qst__reward { display:flex; flex-direction:column; align-items:flex-end; gap:.25rem; flex-shrink:0; }
    .qst__xp { font-family:ui-monospace,Menlo,monospace; font-size:.74rem; font-weight:800; color:var(--text); }
    .qst__coin { display:inline-flex; align-items:center; gap:4px; font-size:.74rem; font-weight:800; color:#f59e0b; }
    .qst-refresh { font-family:ui-monospace,Menlo,monospace; font-size:.62rem; color:var(--dim-2); }
</style>
