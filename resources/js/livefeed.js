/**
 * Live Feed («Живая лента») — editorial strip under the header.
 *
 * Two logically separated zones: a pinned, elevated "Событие дня" (the day's
 * best event) and the flowing feed of recent events as ghost/outlined chips.
 * New events slide in from the right; when the row overflows the oldest fall
 * off behind a soft edge fade. Restrained, cool, theme-variable palette →
 * dark/light/cosmic/pink work automatically. Enabled by default. No SVG/emoji.
 */

const POLL_MS = 10000;

// Achievement rarity, derived from the server colour. Drives the coloured
// rarity label (and, via the accent, the card's subtle tint).
const RARITY = {
    '#7d8694': 'Обычное',
    '#3b82f6': 'Редкое',
    '#a855f7': 'Эпическое',
    '#f5a623': 'Легендарное',
};

function initLiveFeed() {
    const bar     = document.getElementById('live-feed-bar');
    const track   = document.getElementById('live-feed-track');
    const eodZone = document.getElementById('live-feed-eod-zone');
    const eodEl   = document.getElementById('live-feed-eod');
    if (!bar || !track || !eodZone || !eodEl) return;

    let lastSeenId = 0;
    let eodId = null;
    let pool = [];   // latest events (newest-first) — the source for (re)fills

    const esc = (s) => {
        const d = document.createElement('div');
        d.textContent = s == null ? '' : String(s);
        return d.innerHTML;
    };

    const accentOf = (ev) => ev.color || '#8b909c';
    const rarityOf = (ev) => RARITY[ev.color] || 'Достижение';

    // Eyebrow micro-label for a recent chip.
    const eyebrowFor = (ev) => ({
        achievement:  rarityOf(ev),
        level_up:     'Новый уровень',
        rank_up:      'Новый ранг',
        order:        'Заказ',
        registration: 'Новый игрок',
    }[ev.type] || '');

    // Short noun used in the Event-of-the-Day eyebrow ("Событие дня · …").
    const descriptorOf = (ev) => ({
        achievement:  rarityOf(ev),
        level_up:     'Уровень',
        rank_up:      'Ранг',
        order:        'Заказ',
        registration: 'Новый игрок',
    }[ev.type] || '');

    // A fuller value string for the hover tooltip.
    const fullValOf = (ev) => ({
        achievement:  ev.value,
        level_up:     'Уровень ' + ev.value,
        rank_up:      'Ранг ' + ev.value,
        order:        'Заказ',
        registration: 'Присоединился к платформе',
    }[ev.type] || ev.value || '');

    const avatarHtml = (ev, size) => {
        const cls = 'lf-ava ' + (size === 'lg' ? 'lf-ava-lg' : 'lf-ava-sm');
        if (ev.avatar) {
            return `<img class="${cls}" src="${esc(ev.avatar)}" alt="">`;
        }
        const letter = (ev.name || '?').trim().charAt(0).toUpperCase() || '?';
        return `<div class="${cls}">${esc(letter)}</div>`;
    };

    const valHtml = (ev) =>
        ev.value ? `<span class="lf-dot">·</span><span class="lf-val">${esc(ev.value)}</span>` : '';

    const block = (ev) => {
        const el = document.createElement('div');
        el.className = 'lf-block';
        el.dataset.id = ev.id;
        el.style.setProperty('--lf-accent', accentOf(ev));
        el.innerHTML =
            avatarHtml(ev, 'sm') +
            `<div class="lf-body">
                <div class="lf-eyebrow"><span class="lf-node"></span><span class="lf-eyebrow-txt">${esc(eyebrowFor(ev))}</span></div>
                <div class="lf-row"><span class="lf-name">${esc(ev.name)}</span>${valHtml(ev)}</div>
            </div>`;
        el.__ev = ev;
        el.__eod = false;
        return el;
    };

    const eodBlock = (ev) => {
        const el = document.createElement('div');
        el.className = 'lf-eod';
        el.style.setProperty('--lf-accent', accentOf(ev));
        el.innerHTML =
            avatarHtml(ev, 'lg') +
            `<div class="lf-body">
                <div class="lf-eod-head">
                    <span class="lf-eod-badge">Событие дня</span>
                    <span class="lf-strong lf-eyebrow-txt">${esc(descriptorOf(ev))}</span>
                </div>
                <div class="lf-row"><span class="lf-name">${esc(ev.name)}</span>${valHtml(ev)}</div>
            </div>`;
        el.__ev = ev;
        el.__eod = true;
        return el;
    };

    // ─── cursor tooltip: the full, untruncated card on hover ──────────────────
    const tip = document.createElement('div');
    tip.className = 'lf-tip';
    document.body.appendChild(tip);
    let tipKey = null;

    const buildTip = (ev, isEod) => {
        tip.style.setProperty('--lf-accent', accentOf(ev));
        const eyebrow = isEod ? `Событие дня · ${descriptorOf(ev)}` : eyebrowFor(ev);
        const val = fullValOf(ev);
        tip.innerHTML =
            `<div class="lf-eyebrow"><span class="lf-node"></span><span>${esc(eyebrow)}</span></div>
             <div class="lf-tip-name">${esc(ev.name)}</div>` +
            (val ? `<div class="lf-tip-val">${esc(val)}</div>` : '') +
            (ev.ago ? `<div class="lf-tip-time">${esc(ev.ago)}</div>` : '');
    };

    const moveTip = (e) => {
        const pad = 14;
        const r = tip.getBoundingClientRect();
        let x = e.clientX + pad;
        let y = e.clientY + pad;
        if (x + r.width > window.innerWidth - 8)   x = e.clientX - pad - r.width;
        if (y + r.height > window.innerHeight - 8) y = e.clientY - pad - r.height;
        tip.style.left = Math.max(8, x) + 'px';
        tip.style.top  = Math.max(8, y) + 'px';
    };

    bar.addEventListener('mousemove', (e) => {
        const b = e.target.closest('.lf-block, .lf-eod');
        if (b && b.__ev) {
            const key = (b.__eod ? 'd' : 'e') + b.__ev.id;
            if (key !== tipKey) { buildTip(b.__ev, b.__eod); tipKey = key; }
            tip.classList.add('show');
            moveTip(e);
        } else {
            tip.classList.remove('show');
            tipKey = null;
        }
    });
    bar.addEventListener('mouseleave', () => { tip.classList.remove('show'); tipKey = null; });

    // ─── strip plumbing ───────────────────────────────────────────────────────
    // Drop the oldest cards (rightmost) until the row fits; keep the newest.
    const fitRight = () => {
        let guard = 0;
        while (track.scrollWidth > track.clientWidth + 1 && track.children.length > 1 && guard++ < 100) {
            track.removeChild(track.lastElementChild);
        }
    };

    // A new event enters at the far left (newest first); existing cards shift
    // right and the oldest fall off the right edge.
    const prependNew = (ev) => {
        // Never render the same event twice (guards against poll races / refills).
        if (track.querySelector(`[data-id="${ev.id}"]`)) return;
        const el = block(ev);
        el.classList.add('enter');
        track.insertBefore(el, track.firstChild);
        requestAnimationFrame(() => requestAnimationFrame(() => el.classList.remove('enter')));
        fitRight();
    };

    const renderEod = (ev) => {
        if (!ev) { eodZone.classList.add('hidden'); eodEl.innerHTML = ''; eodId = null; return; }
        if (ev.id === eodId) return;          // unchanged — leave it pinned
        eodId = ev.id;
        eodEl.innerHTML = '';
        eodEl.appendChild(eodBlock(ev));
        eodZone.classList.remove('hidden');
    };

    const showBar = () => bar.classList.remove('hidden');
    const hideBar = () => bar.classList.add('hidden');

    const fetchFeed = async () => {
        try {
            // Bust both the browser HTTP cache and any intermediary: a fresh URL
            // each poll + no-store, otherwise polling can keep returning stale
            // data and new events never appear.
            const res = await fetch('/feed?_=' + Date.now(), {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!res.ok) return null;
            return await res.json();
        } catch (e) {
            return null;
        }
    };

    // Rebuild the row from the pool, newest → oldest (left → right), so cards
    // dropped to fit a narrower viewport reappear when it grows again. Stable
    // order: just the latest events in sequence, never shuffled.
    const fillFromPool = () => {
        track.innerHTML = '';
        for (const ev of pool) {            // pool is newest-first
            const el = block(ev);
            track.appendChild(el);
            if (track.children.length > 1 && track.scrollWidth > track.clientWidth + 1) {
                track.removeChild(el);      // doesn't fit — stop, keep the newest
                break;
            }
        }
    };

    const apply = (data) => {
        pool = Array.isArray(data.events) ? data.events : [];
        renderEod(data.event_of_day);
        (pool.length || data.event_of_day) ? showBar() : hideBar();
    };

    const tick = async () => {
        if (document.hidden) return;
        const data = await fetchFeed();
        if (!data || !Array.isArray(data.events)) return;
        apply(data);

        const fresh = data.events.filter((e) => e.id > lastSeenId).reverse();
        if (!track.children.length && data.events.length) {
            fillFromPool();                 // (re)fill after an empty or failed start
            lastSeenId = data.events[0].id;
        } else if (fresh.length) {
            // fresh is oldest-first, so prepending each leaves the newest leftmost.
            fresh.forEach((ev) => prependNew(ev));
            lastSeenId = data.events[0].id;
        }
    };

    let resizeT;
    window.addEventListener('resize', () => {
        clearTimeout(resizeT);
        resizeT = setTimeout(fillFromPool, 150);
    });

    // Catch up immediately when the user returns to the tab.
    document.addEventListener('visibilitychange', () => { if (!document.hidden) tick(); });

    (async () => {
        const data = await fetchFeed();
        if (data && Array.isArray(data.events)) {
            apply(data);
            fillFromPool();
            lastSeenId = data.events.length ? data.events[0].id : 0;
        }
        setInterval(tick, POLL_MS);
    })();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLiveFeed);
} else {
    initLiveFeed();
}
