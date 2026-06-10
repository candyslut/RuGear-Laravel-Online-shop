<style>
    /* — Сохранённое: числовые инпуты без стрелок — */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }

    /* — Сохранённое: пагинация — */
    .custom-pagination p { color: #6b7280 !important; font-style: italic !important; font-size: 0.875rem !important; }
    .custom-pagination nav a,
    .custom-pagination nav span[aria-disabled="true"] span,
    .custom-pagination nav span[aria-current="page"] span {
        background-color: #161920 !important; border-color: #1f2937 !important; color: #9ca3af !important;
        border-radius: 0.6rem; margin: 0 2px;
    }
    .custom-pagination nav span[aria-current="page"] span {
        background-color: var(--hud) !important; border-color: var(--hud) !important; color: #000 !important; font-weight: 900 !important;
    }
    .custom-pagination nav a:hover { background-color: #1f2937 !important; color: var(--hud) !important; border-color: var(--hud) !important; }

    /* — Сохранённое: стрелка селекта + раскрытие фильтров — */
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.4em 1.4em;
    }
    #filters-menu { max-height: 0; opacity: 0; transition: all .45s cubic-bezier(.4,0,.2,1); pointer-events: none; }
    #filters-menu.active { max-height: 600px; opacity: 1; margin-top: 1rem; pointer-events: auto; }

    /* ════════════════════════════════════════════════════════════
       КАТАЛОГ · «HUD-лобби» (theme-aware)
       Игровая командная консоль: угловые скобки-брекеты, срезанные
       углы-нотчи, моноширинные статус-метки, тактильные ховеры.
       Акцент — оранжевый #f97316. Поверхности — на переменных темы.
       ──────────────────────────────────────────────────────────── */

    :root { --hud: #f97316; --hud-rgb: 249,115,22; --hud-bright: #fb923c; }
    /* Тема «Космос» из мини-маркета: акцент каталога — фиолет. */
    :root[data-theme="cosmic"] { --hud: #a78bfa; --hud-rgb: 139,92,246; --hud-bright: #c4b5fd; }
    /* Тема «Няшный розовый» из мини-маркета: акцент каталога — розовый. */
    :root[data-theme="pink"] { --hud: #db2777; --hud-rgb: 236,72,153; --hud-bright: #ec4899; }

    .mono { font-family: ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace; }

    /* Моно-метка с ведущим тире */
    .hud-eyebrow {
        display: inline-flex; align-items: center; gap: .55rem;
        font-family: ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace;
        font-size: .65rem; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; color: var(--hud);
    }
    .hud-eyebrow::before { content: ""; width: 1.15rem; height: 2px; background: var(--hud); }

    /* Угловые скобки-брекеты (4 уголка одним псевдоэлементом) */
    .hud-bracket { position: relative; }
    .hud-bracket::after {
        content: ""; position: absolute; inset: .6rem; pointer-events: none;
        --c: rgba(var(--hud-rgb),.5); --len: 13px; --th: 2px;
        background:
            linear-gradient(var(--c),var(--c)) left top    / var(--len) var(--th) no-repeat,
            linear-gradient(var(--c),var(--c)) left top    / var(--th) var(--len) no-repeat,
            linear-gradient(var(--c),var(--c)) right top   / var(--len) var(--th) no-repeat,
            linear-gradient(var(--c),var(--c)) right top   / var(--th) var(--len) no-repeat,
            linear-gradient(var(--c),var(--c)) left bottom / var(--len) var(--th) no-repeat,
            linear-gradient(var(--c),var(--c)) left bottom / var(--th) var(--len) no-repeat,
            linear-gradient(var(--c),var(--c)) right bottom/ var(--len) var(--th) no-repeat,
            linear-gradient(var(--c),var(--c)) right bottom/ var(--th) var(--len) no-repeat;
    }

    /* ── Статус-бар (заголовок) ── */
    .hud-statusbar {
        position: relative; overflow: hidden;
        display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1.5rem;
        padding: 1.6rem 1.85rem;
        background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: .9rem;
        margin-bottom: 1.75rem;
    }
    .hud-statusbar::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--hud); }
    .hud-title {
        margin-top: .6rem; line-height: .95; text-transform: uppercase; letter-spacing: -.02em;
        font-size: clamp(2rem, 4.2vw, 2.9rem); font-weight: 900; color: var(--text-primary);
    }
    .hud-subtitle { margin-top: .55rem; font-size: .9rem; font-style: italic; color: var(--text-tertiary); }
    .hud-readout { display: flex; align-items: baseline; gap: .65rem; padding-left: 1.5rem; border-left: 1px solid var(--border-color); }
    .hud-readout__label { font-size: .58rem; letter-spacing: .2em; text-transform: uppercase; color: var(--text-tertiary); }
    .hud-readout__value { font-size: 1.7rem; font-weight: 900; color: var(--hud); font-variant-numeric: tabular-nums; }

    /* ── Поисковый терминал ── */
    .term { margin-bottom: 2.75rem; }
    .term__row { display: flex; flex-direction: column; gap: .9rem; }
    @media (min-width: 768px) { .term__row { flex-direction: row; } }
    .term__field { position: relative; flex: 1; display: flex; align-items: center; }
    .term__icon { position: absolute; left: 1.15rem; color: var(--text-tertiary); pointer-events: none; transition: color .2s; }
    .term__field:focus-within .term__icon { color: var(--hud); }
    .term__input {
        width: 100%; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: .8rem;
        padding: 1.1rem 9rem 1.1rem 3.2rem; color: var(--text-primary); font-size: .95rem; transition: border-color .2s;
    }
    .term__input::placeholder { color: var(--text-tertiary); }
    .term__input:focus { outline: none; border-color: rgba(var(--hud-rgb),.55); }
    .term__params {
        position: absolute; right: .55rem; top: .55rem; bottom: .55rem;
        display: inline-flex; align-items: center; gap: .5rem; padding: 0 1.05rem;
        background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: .6rem; color: var(--text-secondary);
        font-size: .6rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; cursor: pointer; transition: all .2s;
    }
    .term__params:hover { color: var(--text-primary); border-color: var(--text-tertiary); }
    .term__params.is-active { background: var(--hud); border-color: var(--hud); color: #000; }
    .term__submit {
        min-height: 3.55rem; padding: 0 2.6rem; border-radius: .8rem; border: 1px solid var(--hud); background: var(--hud); color: #000;
        font-size: .72rem; font-weight: 900; letter-spacing: .22em; text-transform: uppercase; transition: background .2s, transform .1s;
    }
    .term__submit:hover { background: var(--hud-bright); }
    .term__submit:active { transform: scale(.97); }

    /* ── Карточка фильтров ── */
    .filters-card {
        display: grid; gap: 1.5rem; padding: 1.6rem;
        background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 1rem;
    }
    @media (min-width: 768px) { .filters-card { grid-template-columns: repeat(3, 1fr); } }
    .field-label {
        display: flex; align-items: center; gap: .55rem; margin-bottom: .8rem;
        font-size: .62rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: var(--text-secondary);
    }
    .field-label::before { content: ""; width: .28rem; height: .9rem; background: var(--hud); border-radius: 2px; }
    .field-control {
        width: 100%; padding: .85rem 1rem; border-radius: .65rem;
        background: var(--bg-tertiary); border: 1px solid var(--border-color); color: var(--text-primary); font-size: .85rem; transition: border-color .2s;
    }
    .field-control:focus { outline: none; border-color: var(--hud); }
    select.field-control { appearance: none; cursor: pointer; padding-right: 2.6rem; }
    .price-pair { display: flex; gap: .75rem; }
    .price-cell { position: relative; flex: 1; }
    .price-cell > span { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); font-size: .58rem; font-weight: 800; letter-spacing: .05em; color: var(--text-tertiary); }
    .price-cell .field-control { padding-left: 2.4rem; }
    .filters-summary {
        align-self: end; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.1rem;
        background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: .8rem;
    }
    .filters-summary__title { font-size: .62rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--hud); }
    .filters-summary__sub { margin-top: .25rem; font-size: .68rem; font-style: italic; color: var(--text-tertiary); }
    .filters-reset {
        display: inline-flex; padding: .6rem; border-radius: .55rem; color: #ef4444;
        background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.25); transition: all .2s;
    }
    .filters-reset:hover { background: #ef4444; color: #fff; }

    /* ── Лобби: ассиметричная раскладка (фичер + правая колонка) ── */
    .lobby { display: grid; gap: 1.25rem; margin-bottom: 3rem; }
    @media (min-width: 1024px) { .lobby { grid-template-columns: 7fr 5fr; align-items: stretch; } }
    .lobby__side { display: flex; flex-direction: column; gap: 1.25rem; min-width: 0; }

    .panel-head { display: flex; flex-direction: column; gap: .55rem; }
    .panel-head__row { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; }
    .panel-title { font-size: 1.45rem; font-weight: 900; color: var(--text-primary); line-height: 1; }
    .panel-meta { font-size: .6rem; letter-spacing: .18em; text-transform: uppercase; color: var(--text-tertiary); white-space: nowrap; }

    /* Фичер «Товар дня» */
    .feature { display: flex; flex-direction: column; gap: 1.35rem; padding: 1.6rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: .9rem; min-width: 0; }
    .feature__media {
        position: relative; overflow: hidden; aspect-ratio: 16 / 10;
        background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: .7rem;
        clip-path: polygon(0 0, 100% 0, 100% calc(100% - 1.5rem), calc(100% - 1.5rem) 100%, 0 100%);
    }
    .feature__media img { width: 100%; height: 100%; object-fit: cover; transition: transform .7s; }
    .feature:hover .feature__media img { transform: scale(1.04); }
    .feature__ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-tertiary); }
    .feature__tag {
        position: absolute; top: .9rem; left: .9rem;
        font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .55rem; font-weight: 800; letter-spacing: .15em; text-transform: uppercase;
        color: var(--hud); background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
        border: 1px solid rgba(var(--hud-rgb),.35); border-radius: .4rem; padding: .32rem .6rem;
    }
    .feature__name { font-size: clamp(1.5rem, 2.4vw, 2rem); font-weight: 900; color: var(--text-primary); line-height: 1.05; overflow-wrap: anywhere; }
    .feature__desc { font-size: .9rem; line-height: 1.55; color: var(--text-secondary); }

    .params { display: grid; grid-template-columns: repeat(3, 1fr); gap: .65rem; }
    @media (max-width: 391px) { .params { grid-template-columns: 1fr; } }
    .param { min-width: 0; padding: .7rem .8rem; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: .6rem; }
    .param__k { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .53rem; letter-spacing: .16em; text-transform: uppercase; color: var(--text-tertiary); }
    .param__v { margin-top: .3rem; font-size: .92rem; font-weight: 800; color: var(--text-primary); line-height: 1.1; overflow-wrap: anywhere; }

    .price-block { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; padding-top: 1.1rem; border-top: 1px solid var(--border-color); }
    .price-block__label { font-size: .55rem; letter-spacing: .2em; text-transform: uppercase; color: var(--text-tertiary); }
    .price-block__value { margin-top: .25rem; font-size: 2rem; font-weight: 900; color: var(--text-primary); line-height: 1; }
    .price-block__chip { font-size: .58rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--hud); background: rgba(var(--hud-rgb),.1); border: 1px solid rgba(var(--hud-rgb),.3); border-radius: .5rem; padding: .35rem .7rem; }

    .cta-row { display: flex; flex-wrap: wrap; gap: .7rem; }
    .cta { flex: 1; min-width: 9rem; display: flex; align-items: center; justify-content: center; text-align: center; padding: .95rem 1.4rem; border-radius: .7rem; font-size: .7rem; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; transition: all .2s; }
    .cta--primary { background: var(--hud); color: #000; border: 1px solid var(--hud); }
    .cta--primary:hover { background: var(--hud-bright); }
    .cta--ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border-color); }
    .cta--ghost:hover { border-color: var(--hud); color: var(--hud); }

    .feature-empty { margin-top: .5rem; padding: 2.5rem 1.5rem; text-align: center; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: .8rem; }
    .feature-empty__t { font-size: 1.05rem; font-weight: 800; color: var(--text-primary); }
    .feature-empty__s { margin-top: .6rem; font-size: .85rem; color: var(--text-tertiary); }

    /* Лидерборд «Топ-товары» */
    .rank { display: flex; flex-direction: column; padding: 1.5rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: .9rem; }
    .rank__list { margin-top: 1.15rem; display: flex; flex-direction: column; gap: .55rem; }
    .rank__row {
        display: flex; align-items: center; gap: .9rem; padding: .7rem .85rem;
        background: var(--bg-tertiary); border: 1px solid var(--border-color); border-left-width: 2px; border-radius: .55rem;
        transition: border-color .2s, transform .15s, background .2s;
    }
    .rank__row:hover { border-color: rgba(var(--hud-rgb),.4); border-left-color: var(--hud); transform: translateX(3px); }
    .rank__idx { width: 1.6rem; flex-shrink: 0; font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .8rem; font-weight: 800; color: var(--text-tertiary); }
    .rank__row:hover .rank__idx { color: var(--hud); }
    .rank__thumb { width: 2.9rem; height: 2.9rem; flex-shrink: 0; overflow: hidden; border-radius: .5rem; background: var(--bg-secondary); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; }
    .rank__thumb img { width: 100%; height: 100%; object-fit: cover; }
    .rank__info { flex: 1; min-width: 0; }
    .rank__name { font-size: .82rem; font-weight: 800; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .rank__cat { font-size: .68rem; color: var(--text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .rank__price { flex-shrink: 0; font-size: .82rem; font-weight: 900; color: var(--hud); }

    /* ── Секторы (быстрый выбор категорий) — внутри правой колонки ── */
    .sectors { display: flex; flex-direction: column; }
    .lobby__side .sectors { flex: 1; }
    .sectors__head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; margin-bottom: 1.1rem; }
    .sectors__grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .9rem; }
    .lobby__side .sectors__grid { flex: 1; grid-auto-rows: 1fr; }
    .sector {
        position: relative; display: flex; flex-direction: column; justify-content: space-between; gap: .9rem; min-height: 5.75rem; padding: 1.15rem 1.2rem; overflow: hidden;
        background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: .75rem;
        transition: border-color .2s, transform .15s;
    }
    .sector:hover { border-color: rgba(var(--hud-rgb),.45); transform: translateY(-3px); }
    .sector__idx { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .56rem; letter-spacing: .15em; color: var(--text-tertiary); transition: color .2s; }
    .sector:hover .sector__idx { color: var(--hud); }
    .sector__body { display: flex; flex-direction: column; gap: .2rem; }
    .sector__name { font-size: 1rem; font-weight: 900; color: var(--text-primary); line-height: 1.1; }
    .sector__count { font-size: .68rem; color: var(--text-tertiary); }

    /* ── Шапка списка инвентаря ── */
    .grid-head { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.4rem; }
    .grid-head__line { flex: 1; height: 1px; background: var(--border-color); }
    .grid-head__count { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .62rem; letter-spacing: .16em; text-transform: uppercase; color: var(--text-tertiary); }

    /* ── Сетка «инвентаря» (товары) ── */
    .gear-grid { display: grid; grid-template-columns: 1fr; gap: 1.25rem; }
    @media (min-width: 640px) { .gear-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .gear-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (min-width: 1280px) { .gear-grid { grid-template-columns: repeat(4, 1fr); } }

    .gear { display: flex; flex-direction: column; overflow: hidden; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: .85rem; transition: border-color .25s, transform .2s; }
    .gear:hover { border-color: rgba(var(--hud-rgb),.5); transform: translateY(-4px); }
    .gear__media { position: relative; display: block; aspect-ratio: 1 / 1; overflow: hidden; background: var(--bg-tertiary); }
    .gear__media img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s; }
    .gear:hover .gear__media img { transform: scale(1.06); }
    .gear__ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-tertiary); }
    .gear__tag {
        position: absolute; top: .8rem; left: .8rem;
        font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .55rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase;
        color: var(--hud); background: rgba(0,0,0,.55); backdrop-filter: blur(4px); border: 1px solid rgba(var(--hud-rgb),.3); border-radius: .4rem; padding: .3rem .55rem;
    }
    .gear__sold { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,.45); backdrop-filter: blur(1.5px); }
    .gear__sold span { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .62rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #f87171; background: rgba(0,0,0,.6); border: 1px solid rgba(248,113,113,.4); border-radius: .5rem; padding: .4rem .85rem; }
    .gear__body { flex: 1; display: flex; flex-direction: column; gap: 1rem; padding: 1.15rem; }
    .gear__name { font-size: .98rem; font-weight: 800; line-height: 1.3; }
    .gear__name a { color: var(--text-primary); transition: color .2s; }
    .gear__name a:hover { color: var(--hud); }
    .gear__foot { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding-top: .9rem; border-top: 1px solid var(--border-color); }
    .gear__price { font-size: 1.3rem; font-weight: 900; font-style: italic; color: var(--text-primary); }

    /* ── Пустое состояние ── */
    .void { grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.1rem; padding: 5rem 2rem; text-align: center; background: var(--bg-secondary); border: 1px dashed var(--border-color); border-radius: 1rem; }
    .void__code { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: .62rem; letter-spacing: .25em; text-transform: uppercase; color: var(--text-tertiary); }
    .void__msg { font-size: 1.05rem; font-weight: 800; font-style: italic; color: var(--text-secondary); }

    .pager { margin-top: 3.5rem; }
</style>

<x-shop-layout>
    <x-slot name="title">Каталог товаров | RuGear</x-slot>

    {{-- ═══════════════ СТАТУС-БАР ═══════════════ --}}
    <header class="hud-statusbar">
        <div>
            <span class="hud-eyebrow">RuGear // Каталог</span>
            <h1 class="hud-title">На витрине</h1>
            <p class="hud-subtitle">Твоё преимущество в катке. Сделано в России.</p>
        </div>
    </header>

    {{-- ═══════════════ ПОИСКОВЫЙ ТЕРМИНАЛ ═══════════════ --}}
    <form action="{{ route('products.index') }}" method="GET" id="search-form" class="term">
        <div class="term__row">
            <div class="term__field">
                <span class="term__icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" class="term__input" placeholder="Найти снаряжение...">
                <button type="button" onclick="toggleFilters()" id="filter-btn" class="term__params">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span class="hidden sm:inline">Параметры</span>
                </button>
            </div>
            <button type="submit" class="term__submit">Поиск</button>
        </div>

        <div id="filters-menu" class="overflow-hidden">
            <div class="filters-card">
                <div>
                    <label class="field-label">Категория</label>
                    <select name="category" class="field-control">
                        <option value="">Все категории</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label">Стоимость (₽)</label>
                    <div class="price-pair">
                        <div class="price-cell">
                            <span class="mono">ОТ</span>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" class="field-control" placeholder="0">
                        </div>
                        <div class="price-cell">
                            <span class="mono">ДО</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" class="field-control" placeholder="∞">
                        </div>
                    </div>
                </div>

                <div class="filters-summary">
                    <div>
                        <h4 class="filters-summary__title">Смарт-фильтр</h4>
                        <p class="filters-summary__sub">Найдено в выборке: {{ $products->total() }}</p>
                    </div>
                    @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
                    <a href="{{ route('products.index') }}" class="filters-reset" title="Сбросить всё">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- ═══════════════ ЛОББИ: ВИДЖЕТЫ (только на 1-й странице без фильтров) ═══════════════ --}}
    @if(!request()->anyFilled(['search', 'category', 'min_price', 'max_price']) && (int)request('page', 1) === 1)
    <div class="lobby">

        {{-- Основная цель: товар дня --}}
        <section class="hud-bracket feature">
            <div class="panel-head">
                <div class="panel-head__row">
                    <span class="hud-eyebrow">Выбор дня</span>
                    <span class="panel-meta mono">PRIORITY 01</span>
                </div>
                <h2 class="panel-title">Товар дня</h2>
            </div>

            @if($featuredProduct)
            <a href="{{ route('products.show', $featuredProduct) }}" class="feature__media">
                @if($featuredProduct->image)
                <img src="{{ asset($featuredProduct->image) }}" alt="{{ $featuredProduct->name }}">
                @else
                <span class="feature__ph">
                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
                @endif
                <span class="feature__tag">{{ $featuredProduct->category->name ?? 'Базовый' }}</span>
            </a>

            <div>
                <h3 class="feature__name">{{ $featuredProduct->name }}</h3>
                <p class="feature__desc mt-3">{{ $featuredProduct->description ?? 'Лучшее предложение дня для тех, кто собирает мощный комплект.' }}</p>
            </div>

            <div class="params">
                <div class="param">
                    <p class="param__k">В наличии</p>
                    <p class="param__v">{{ $featuredProduct->quantity }}</p>
                </div>
                <div class="param">
                    <p class="param__k">Проверено</p>
                    <p class="param__v">{{ $products->total() }}</p>
                </div>
                <div class="param">
                    <p class="param__k">Категория</p>
                    <p class="param__v">{{ $featuredProduct->category->name ?? '—' }}</p>
                </div>
            </div>

            <div class="price-block">
                <div>
                    <p class="price-block__label mono">Цена сегодня</p>
                    <p class="price-block__value">{{ number_format($featuredProduct->price, 0, '.', ' ') }} ₽</p>
                </div>
                <span class="price-block__chip">Премиум</span>
            </div>

            <div class="cta-row">
                <a href="{{ route('products.show', $featuredProduct) }}" class="cta cta--primary">Подробнее</a>
                <a href="{{ route('products.index', ['search' => $featuredProduct->name]) }}" class="cta cta--ghost">Найти похожие</a>
            </div>
            @else
            <div class="feature-empty">
                <p class="feature-empty__t">Товар дня готовится</p>
                <p class="feature-empty__s">Скоро здесь появится новое выгодное предложение.</p>
            </div>
            @endif
        </section>

        {{-- Правая колонка: лидерборд + секторы (заполняет высоту фичера) --}}
        <div class="lobby__side">
            {{-- Лидерборд: топ-товары --}}
            <aside class="rank">
                <div class="panel-head">
                    <div class="panel-head__row">
                        <span class="hud-eyebrow">Хит недели</span>
                        <span class="panel-meta mono">{{ $topProducts->count() }} позиции</span>
                    </div>
                    <h3 class="panel-title">Топ-товары</h3>
                </div>
                <ol class="rank__list">
                    @foreach($topProducts as $topProduct)
                    <li>
                        <a href="{{ route('products.show', $topProduct) }}" class="rank__row">
                            <span class="rank__idx">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="rank__thumb">
                                @if($topProduct->image)
                                <img src="{{ asset($topProduct->image) }}" alt="{{ $topProduct->name }}">
                                @else
                                <svg class="w-6 h-6 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                @endif
                            </span>
                            <span class="rank__info">
                                <span class="rank__name block">{{ $topProduct->name }}</span>
                                <span class="rank__cat block">{{ $topProduct->category->name ?? 'Категория' }}</span>
                            </span>
                            <span class="rank__price">{{ number_format($topProduct->price, 0, '.', ' ') }} ₽</span>
                        </a>
                    </li>
                    @endforeach
                </ol>
            </aside>

            {{-- Категории: быстрый выбор категорий --}}
            <section class="sectors">
                <div class="sectors__head">
                    <div>
                        <span class="hud-eyebrow">Категории</span>
                        <h3 class="panel-title mt-2">Быстрый выбор</h3>
                    </div>
                    <span class="panel-meta mono">Категории</span>
                </div>
                <div class="sectors__grid">
                    @foreach($categories->take(4) as $category)
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="sector">
                        <span class="sector__idx">SECTOR · {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="sector__body">
                            <span class="sector__name">{{ $category->name }}</span>
                            <span class="sector__count">{{ $category->products_count }} товаров</span>
                        </span>
                    </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
    @endif

    {{-- ═══════════════ ИНВЕНТАРЬ: СПИСОК ТОВАРОВ ═══════════════ --}}
    <div class="grid-head">
        <span class="hud-eyebrow">Инвентарь</span>
        <span class="grid-head__line"></span>
        <span class="grid-head__count">{{ $products->total() }} ед.</span>
    </div>

    <div class="gear-grid">
        @forelse($products as $product)
        <article class="gear">
            <a href="{{ route('products.show', $product) }}" class="gear__media">
                @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                @else
                <span class="gear__ph">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
                @endif
                <span class="gear__tag">{{ $product->category->name ?? 'Базовый' }}</span>
                @if($product->quantity <= 0)
                <span class="gear__sold"><span>Нет в наличии</span></span>
                @endif
            </a>
            <div class="gear__body">
                <h3 class="gear__name"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                <div class="gear__foot">
                    <span class="gear__price">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                    <livewire:cart-counter :product="$product" :key="'cart-counter-' . $product->id" />
                </div>
            </div>
        </article>
        @empty
        <div class="void">
            <span class="void__code">// NO_SIGNAL</span>
            <svg class="w-16 h-16 text-[var(--text-tertiary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <p class="void__msg">Объектов по данным координатам не обнаружено.</p>
            <a href="{{ route('products.index') }}" class="cta cta--ghost" style="flex:initial;">Сбросить параметры</a>
        </div>
        @endforelse
    </div>

    <div class="pager custom-pagination">
        {{ $products->onEachSide(1)->links() }}
    </div>

    <script>
        function toggleFilters() {
            const menu = document.getElementById('filters-menu');
            const btn = document.getElementById('filter-btn');
            menu.classList.toggle('active');
            btn.classList.toggle('is-active', menu.classList.contains('active'));
        }

        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            if (params.has('category') || params.has('min_price') || params.has('max_price')) {
                document.getElementById('filters-menu').classList.add('active');
                document.getElementById('filter-btn').classList.add('is-active');
            }
        });
    </script>
</x-shop-layout>
