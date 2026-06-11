<div>
@if($open)
<div class="qm-hud qm-overlay" x-data x-on:keydown.escape.window="$wire.close()">
    <div class="qm-backdrop" wire:click="close"></div>

    <div class="qm-panel" role="dialog" aria-modal="true"
         x-data="{ rolling: false, shake: false, left: {{ $resetIn }}, fmt() { const h=Math.floor(this.left/3600), m=Math.floor(this.left%3600/60), s=Math.floor(this.left%60); return String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0'); } }"
         x-init="setInterval(() => { if (left > 0) left--; }, 1000)"
         x-on:quests-rerolled.window="rolling = false"
         x-on:quest-reroll-failed.window="rolling = false; shake = true; setTimeout(() => shake = false, 450)">

        {{-- Header --}}
        <div class="qm-head">
            <span class="qm-head__bar"></span>
            <div class="min-w-0">
                <h2 class="qm-head__title">Ежедневные задания</h2>
                <p class="qm-head__sub">
                    <span class="qm-acc">{{ $completed }}</span>/{{ $total }} выполнено
                    <span class="mx-1">·</span> обновление через <span x-text="fmt()"></span>
                </p>
            </div>
            <button wire:click="close" class="qm-x ml-auto" aria-label="Закрыть">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        {{-- Completion meter --}}
        <div class="qm-meterwrap">
            <div class="qm-meter"><span style="width:{{ $total > 0 ? round($completed / $total * 100) : 0 }}%"></span></div>
        </div>

        {{-- Quests --}}
        <div class="qm-body" x-bind:class="rolling && 'qst-rolling'">
            @forelse($quests as $q)
                <div wire:key="qm-quest-{{ $q->id }}">
                    @include('partials.quest-card', ['q' => $q])
                </div>
            @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm font-bold qm-dim uppercase tracking-widest">Заданий пока нет</p>
                    <p class="text-xs qm-dim2 mt-1.5">Загляните завтра за новой тройкой</p>
                </div>
            @endforelse
            <p class="qm-foot">Задания обновляются каждый день в полночь. Награды начисляются автоматически.<br>Не подходят задания? Бросьте кубик — раз в сутки можно заменить весь набор за {{ \App\Support\Quests::REROLL_COST }} монет.</p>
        </div>

        <div class="qm-footer flex flex-wrap items-center gap-2.5">
            @if($total > 0)
                @include('partials.quest-reroll')
            @endif
            <button wire:click="close" class="qm-btn flex-1">Закрыть</button>
        </div>
    </div>

    @include('partials.quest-styles')

    <style>
        /* Self-contained HUD palette so the modal looks right on every page,
           not only the dashboard (where the .hud vars are defined). */
        .qm-hud {
            --bg:#0e1014; --bg-2:#14171d; --inset:#090b0e; --line:#232a36; --line-2:#333c4b;
            --text:#e6e8ec; --dim:#828b9b; --dim-2:#525a68; --accent:#f97316; --track:#1a1e26;
        }
        [data-theme="light"] .qm-hud {
            --bg:#ffffff; --bg-2:#f1f4f8; --inset:#e9edf2; --line:#d7dde6; --line-2:#c2cad6;
            --text:#141a22; --dim:#5b6472; --dim-2:#97a0ad; --accent:#ea580c; --track:#e6eaf0;
        }
        /* Тема «Космос» из мини-маркета. */
        [data-theme="cosmic"] .qm-hud {
            --bg:#10153a; --bg-2:#161d4a; --inset:#0a0e29; --line:#36418c; --line-2:#4651a3;
            --text:#eef1ff; --dim:#aab4e8; --dim-2:#7681bd; --accent:#a78bfa; --track:#1c2354;
        }
        /* Тема «Няшный розовый» из мини-маркета. */
        [data-theme="pink"] .qm-hud {
            --bg:#fff7fb; --bg-2:#fbe9f3; --inset:#f7ddeb; --line:#f0c6dc; --line-2:#e4abc9;
            --text:#4a2338; --dim:#92607a; --dim-2:#bd93a9; --accent:#db2777; --track:#f3d9e6;
        }
        .qm-overlay { position:fixed; inset:0; z-index:1000; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .qm-backdrop { position:absolute; inset:0; background:rgba(0,0,0,.78); backdrop-filter:blur(4px); }
        :is([data-theme="light"], [data-theme="pink"]) .qm-backdrop { background:rgba(15,23,42,.45); }
        .qm-panel { position:relative; z-index:1; display:flex; flex-direction:column; width:100%; max-width:38rem; max-height:92vh; background:var(--bg); border:1px solid var(--line); border-radius:14px; box-shadow:0 30px 80px rgba(0,0,0,.55); color:var(--text); }
        @media (prefers-reduced-motion: no-preference) { .qm-panel { animation:qmIn .22s cubic-bezier(.22,1,.36,1); } }
        @keyframes qmIn { from { opacity:0; transform:translateY(10px) scale(.985); } to { opacity:1; transform:none; } }
        .qm-head { display:flex; align-items:center; gap:.7rem; padding:1rem 1.2rem; border-bottom:1px solid var(--line); background:var(--bg-2); border-radius:14px 14px 0 0; flex-shrink:0; }
        .qm-head__bar { width:3px; height:18px; background:var(--accent); flex-shrink:0; }
        .qm-head__title { font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.18em; color:var(--text); }
        .qm-head__sub { font-family:ui-monospace,Menlo,monospace; font-size:.64rem; color:var(--dim-2); margin-top:.2rem; }
        .qm-acc { color:var(--accent); font-weight:700; }
        .qm-x { width:2rem; height:2rem; display:flex; align-items:center; justify-content:center; color:var(--dim); border:1px solid var(--line); border-radius:8px; background:transparent; flex-shrink:0; cursor:pointer; transition:color .12s, border-color .12s, background .12s; }
        .qm-x:hover { color:var(--text); border-color:var(--accent); background:var(--bg-2); }
        .qm-x svg { width:1rem; height:1rem; }
        .qm-meterwrap { padding:.9rem 1.2rem 0; flex-shrink:0; }
        .qm-meter { height:6px; border-radius:4px; background:var(--track); overflow:hidden; }
        .qm-meter > span { display:block; height:100%; border-radius:4px; background:var(--accent); transition:width .5s cubic-bezier(.22,1,.36,1); }
        .qm-body { padding:1.1rem 1.2rem; overflow-y:auto; display:flex; flex-direction:column; gap:.7rem; }
        .qm-dim { color:var(--dim); } .qm-dim2 { color:var(--dim-2); }
        .qm-foot { font-family:ui-monospace,Menlo,monospace; font-size:.62rem; color:var(--dim-2); text-align:center; margin-top:.4rem; line-height:1.5; }
        .qm-footer { padding:1rem 1.2rem; border-top:1px solid var(--line); flex-shrink:0; }
        .qm-btn { width:100%; padding:.7rem 1rem; border:1px solid var(--line-2); background:transparent; color:var(--dim); font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.14em; border-radius:9px; cursor:pointer; transition:color .12s, border-color .12s, background .12s; }
        .qm-btn:hover { color:var(--text); border-color:var(--accent); background:var(--bg-2); }
    </style>
</div>
@endif
</div>
