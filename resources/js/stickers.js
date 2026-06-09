import { DotLottie } from '@lottiefiles/dotlottie-web';

/** Force a dotLottie player to render its first frame as a static poster. */
function paintPoster(inst) {
    try { inst.setFrame(0); inst.render(); } catch (e) {}
}

/**
 * Mount a Lottie/dotLottie animation on a <canvas>, deferring creation until it
 * is near the viewport and — crucially — tearing the instance down again once it
 * scrolls away. dotLottie players are WASM-backed and expensive, so recycling
 * keeps the number of live players bounded no matter how many stickers exist.
 *
 * @param {HTMLCanvasElement} canvas
 * @param {{ src: string, hover?: boolean, loop?: boolean }} opts
 * @returns {() => void} cleanup
 */
export function mountLottie(canvas, { src, hover = false, loop = !hover }) {
    if (!(canvas instanceof HTMLCanvasElement) || !src) return () => {};

    let instance = null;
    const create = () => {
        if (instance) return;
        instance = new DotLottie({ canvas, src, loop, autoplay: !hover });
        // Paint the first frame as soon as the animation loads. A freshly-created
        // dotLottie canvas can otherwise stay blank until something forces a render:
        // a paused (hover) cell has nothing playing, and an autoplaying one created
        // during a Livewire DOM morph doesn't reliably paint from autoplay alone —
        // that's the "posted sticker is blank until you reload" bug. Hover cells stay
        // on this poster; autoplaying ones simply continue animating from it.
        instance.addEventListener('load', () => {
            paintPoster(instance);
            // A paused (hover) player that has never run won't always paint from
            // setFrame()/render() alone — that's the blank pack-cover thumbnail
            // (e.g. the BlackCherry tab). Nudge it one tick then freeze on frame 0.
            if (hover) {
                try { instance.play(); } catch (e) {}
                requestAnimationFrame(() => { try { instance.pause(); } catch (e) {} paintPoster(instance); });
            }
        });
    };
    const destroy = () => { instance?.destroy(); instance = null; };
    const onEnter = () => { create(); instance?.play(); };
    // Rewind to the first frame on leave so the canvas never freezes mid-animation.
    const onLeave = () => { instance?.stop(); try { instance?.setFrame(0); instance?.render(); } catch (e) {} };

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                create();
                if (hover) instance?.pause(); // show a static first frame as preview
            } else {
                destroy();                    // free the player again once off-screen
            }
        });
    }, { rootMargin: '200px' });
    io.observe(canvas);

    if (hover) {
        canvas.addEventListener('pointerenter', onEnter);
        canvas.addEventListener('pointerleave', onLeave);
    }

    return () => {
        io.disconnect();
        canvas.removeEventListener('pointerenter', onEnter);
        canvas.removeEventListener('pointerleave', onLeave);
        destroy();
    };
}

/* ──────────────────────────────────────────────────────────────────────────
   Picker grid cells: a cell shows a cheap placeholder until it scrolls into the
   (scrollable) grid, then a paused first frame is mounted that plays on hover.
   Scrolling away tears the player down, so only the visible handful are live.
   ────────────────────────────────────────────────────────────────────────── */
function mountCell(cell) {
    if (cell._mounted) return;
    cell._mounted = true;
    const ph = cell.querySelector('.sticker-ph');
    if (!ph) return;
    const { type, src } = cell.dataset;

    if (type === 'lottie') {
        const canvas = document.createElement('canvas');
        canvas.width = 100; canvas.height = 100;
        canvas.className = 'w-full h-full';
        ph.replaceChildren(canvas);
        const inst = new DotLottie({ canvas, src, loop: true, autoplay: false });
        // Paint the first frame as a static poster once loaded — otherwise a
        // paused cell stays blank until it is hovered. The play→pause nudge forces
        // a render for players that have never run (some files/timings need it).
        inst.addEventListener('load', () => {
            paintPoster(inst);
            try { inst.play(); } catch (e) {}
            requestAnimationFrame(() => { try { inst.pause(); } catch (e) {} paintPoster(inst); });
        });
        cell._inst = inst;
        cell._enter = () => inst.play();
        // Rewind to the first frame on leave so the cell never freezes mid-animation.
        cell._leave = () => { inst.stop(); try { inst.setFrame(0); inst.render(); } catch (e) {} };
    } else if (type === 'video') {
        const v = document.createElement('video');
        v.src = src + '#t=0.001'; // first frame as a still poster
        v.muted = true; v.loop = true; v.playsInline = true; v.preload = 'metadata';
        v.className = 'w-full h-full object-contain';
        ph.replaceChildren(v);
        cell._inst = v;
        cell._enter = () => v.play().catch(() => {});
        // Rewind to the first frame on leave — pause() alone freezes on a random
        // frame, which is the "won't display correctly until reload" bug.
        cell._leave = () => { v.pause(); try { v.currentTime = 0.001; } catch (e) {} };
    } else {
        return;
    }
    cell.addEventListener('pointerenter', cell._enter);
    cell.addEventListener('pointerleave', cell._leave);
}

function unmountCell(cell) {
    if (!cell._mounted) return;
    cell._mounted = false;
    if (cell._enter) cell.removeEventListener('pointerenter', cell._enter);
    if (cell._leave) cell.removeEventListener('pointerleave', cell._leave);
    const inst = cell._inst;
    if (inst && typeof inst.destroy === 'function') inst.destroy();                 // dotLottie
    else if (inst instanceof HTMLVideoElement) { inst.pause(); inst.removeAttribute('src'); inst.load(); }
    cell._inst = cell._enter = cell._leave = null;
    cell.querySelector('.sticker-ph')?.replaceChildren();
}

/* Build one Recent-grid cell, mirroring partials/sticker-button.blade.php.
   It is inserted into the live picker DOM; recordRecent() runs Alpine.initTree on
   it to wire its @click, and x-sticker-grid's MutationObserver mounts the animated
   player. Kept attribute-for-attribute in sync with the blade so a server
   re-render and this client insert are visually identical. */
function createRecentCell({ id, url, type, emoji = '' }) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.setAttribute('@click', `select(${id}, '${url}', '${type}')`);
    btn.setAttribute('wire:key', `pick-recent-${id}`);
    btn.setAttribute('data-sticker-cell', '');
    btn.setAttribute('data-sticker-id', String(id));
    btn.setAttribute('data-type', type);
    btn.setAttribute('data-src', url);
    btn.title = emoji;
    btn.className = 'sticker-cell aspect-square rounded-xl p-1 flex items-center justify-center ' +
        'hover:bg-gray-800/80 ring-1 ring-transparent hover:ring-orange-500/40 transition-all active:scale-90';
    if (type === 'image') {
        const img = document.createElement('img');
        img.src = url; img.alt = emoji; img.loading = 'lazy';
        img.className = 'w-full h-full object-contain transition-transform group-hover:scale-110';
        btn.appendChild(img);
    } else {
        const span = document.createElement('span');
        span.className = 'sticker-ph w-full h-full flex items-center justify-center';
        btn.appendChild(span);
    }
    return btn;
}

/* ──────────────────────────────────────────────────────────────────────────
   Alpine glue
   ────────────────────────────────────────────────────────────────────────── */
export function registerStickerDirectives(Alpine) {
    // <canvas x-lottie.hover="'/storage/…'"> — for canvases in Alpine/Livewire trees.
    Alpine.directive('lottie', (el, { modifiers, expression }, { evaluate, cleanup }) => {
        if (!(el instanceof HTMLCanvasElement)) {
            console.warn('x-lottie must be used on a <canvas> element');
            return;
        }
        const hover = modifiers.includes('hover');
        const loop = modifiers.includes('loop') || !hover;
        cleanup(mountLottie(el, { src: evaluate(expression), hover, loop }));
    });

    // <div x-sticker-grid> — lazily mounts/destroys the animated cells inside this
    // scrollable container as they enter/leave view, capping live players.
    // Because the picker is now kept mounted (x-show), this also watches for cells
    // inserted later (e.g. a just-used sticker prepended to the Recent section) and
    // gives them the same lazy treatment.
    const ANIM_SEL = '[data-sticker-cell][data-type="lottie"],[data-sticker-cell][data-type="video"]';
    Alpine.directive('sticker-grid', (el, {}, { cleanup }) => {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => (e.isIntersecting ? mountCell(e.target) : unmountCell(e.target)));
        }, { root: el, rootMargin: '140px' });
        el.querySelectorAll(ANIM_SEL).forEach((c) => io.observe(c));

        const mo = new MutationObserver((muts) => {
            muts.forEach((m) => {
                m.addedNodes.forEach((n) => {
                    if (n.nodeType !== 1) return;
                    if (n.matches?.(ANIM_SEL)) io.observe(n);
                    n.querySelectorAll?.(ANIM_SEL).forEach((c) => io.observe(c));
                });
                m.removedNodes.forEach((n) => {
                    if (n.nodeType === 1 && n.matches?.('[data-sticker-cell]')) unmountCell(n);
                });
            });
        });
        mo.observe(el, { childList: true, subtree: true });

        cleanup(() => { io.disconnect(); mo.disconnect(); el.querySelectorAll('[data-sticker-cell]').forEach(unmountCell); });
    });

    // <video x-sticker-video> — plays only while visible (for posted stickers).
    Alpine.directive('sticker-video', (el, {}, { cleanup }) => {
        if (!(el instanceof HTMLVideoElement)) return;
        el.muted = true; el.loop = true; el.playsInline = true;
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => (e.isIntersecting ? el.play().catch(() => {}) : el.pause()));
        }, { rootMargin: '120px' });
        io.observe(el);
        cleanup(() => { io.disconnect(); el.pause(); });
    });

    // Telegram-style picker popover. Tabs sit on top; clicking one smooth-scrolls
    // the grid to that pack and recenters the tab strip on it. A scroll-spy keeps
    // the active tab in sync while the user scrolls the grid by hand. Selecting a
    // sticker stages it CLIENT-SIDE (sent only when the comment is submitted).
    Alpine.data('stickerPicker', () => ({
        open: false,
        current: '',
        _spy: null,
        _lock: false,
        _lockT: null,

        init() {
            // When a sticker comment/reply is actually sent, the host Livewire
            // component dispatches `sticker-used` with the sticker payload. We
            // insert it into the (kept-mounted) popover's Recent section so it
            // shows up there immediately — no reload.
            this.$wire?.on?.('sticker-used', (e) => {
                const d = Array.isArray(e) ? e[0] : e;
                this.recordRecent(d && d.sticker ? d.sticker : d);
            });
        },

        // Prepend a just-used sticker to the Recent section. The picker is kept
        // mounted (x-show), so we insert straight into the LIVE DOM and run
        // Alpine.initTree on the new nodes (so their @click/:class bindings are
        // wired). Creates the section + tab on first use, de-dupes by sticker id
        // (so a re-used sticker jumps to the front) and caps at 16.
        recordRecent(detail) {
            if (!detail) return;
            const id = Number(detail.id);
            if (!id) return;

            const body = this.$refs.body;
            const tabs = this.$refs.tabs;
            if (!body) return; // empty-state popover (no packs) — nothing to do
            const init = (node) => window.Alpine?.initTree?.(node);

            let section = body.querySelector('section[data-section="recent"]');
            if (!section) {
                section = document.createElement('section');
                section.setAttribute('data-section', 'recent');
                const h = document.createElement('p');
                h.className = 'sticky top-0 z-10 bg-[#0d0f15]/95 backdrop-blur px-1 py-1 ' +
                    'text-[10px] font-bold uppercase tracking-wider text-gray-500';
                h.textContent = 'Недавние';
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-5 gap-1';
                section.append(h, grid);
                body.prepend(section);
                this._spy?.observe(section); // keep scroll-spy aware of the new section

                if (tabs && !tabs.querySelector('[data-tab="recent"]')) {
                    const tab = document.createElement('button');
                    tab.type = 'button';
                    tab.setAttribute('data-tab', 'recent');
                    tab.setAttribute('@click', "scrollTo('recent')");
                    tab.setAttribute(':class', "current === 'recent' ? 'bg-orange-500/15 text-orange-400 ring-1 ring-orange-500/50' : 'text-gray-500 hover:bg-gray-800/70'");
                    tab.className = 'flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200';
                    tab.title = 'Недавние';
                    tab.innerHTML = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>';
                    const divider = document.createElement('span');
                    divider.className = 'flex-shrink-0 w-px h-6 bg-gray-800';
                    tabs.prepend(divider);
                    tabs.prepend(tab);
                    init(tab); // wire @click / :class on the freshly inserted tab
                }
            }

            const grid = section.querySelector('.grid');
            if (!grid) return;

            grid.querySelector(`[data-sticker-id="${id}"]`)?.remove();
            const cell = createRecentCell({ id, url: detail.url, type: detail.type, emoji: detail.emoji });
            grid.prepend(cell);
            init(cell); // wire @click="select(...)" on the new cell
            while (grid.children.length > 16) grid.lastElementChild.remove();
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    // Mount the lazy Lottie pack-cover thumbnails; video/image covers
                    // render their own first frame.
                    window.scanLottieCanvases?.(this.$el);
                    this.initSpy();
                    const first = this.$refs.body?.querySelector('[data-section]');
                    if (first) this.current = first.dataset.section;
                });
            } else {
                this.teardownSpy();
            }
        },

        // Scroll-spy: the section occupying the top of the grid drives `current`.
        initSpy() {
            this.teardownSpy();
            const body = this.$refs.body;
            if (!body) return;
            this._spy = new IntersectionObserver((entries) => {
                if (this._lock) return; // a click is animating the scroll — don't fight it
                const top = entries
                    .filter((e) => e.isIntersecting)
                    .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0];
                if (top) {
                    const slug = top.target.dataset.section;
                    if (slug !== this.current) { this.current = slug; this.centerTab(slug); }
                }
            }, { root: body, rootMargin: '0px 0px -80% 0px', threshold: 0 });
            body.querySelectorAll('[data-section]').forEach((s) => this._spy.observe(s));
        },
        teardownSpy() { this._spy?.disconnect(); this._spy = null; },

        // Horizontally center a tab in the (scrollable) strip.
        centerTab(slug) {
            const strip = this.$refs.tabs;
            const tab = strip?.querySelector(`[data-tab="${slug}"]`);
            if (!strip || !tab) return;
            const target = tab.offsetLeft - strip.clientWidth / 2 + tab.offsetWidth / 2;
            strip.scrollTo({ left: Math.max(0, target), behavior: 'smooth' });
        },

        // Tab click: jump the grid to the section, recenter the tab, and briefly
        // lock the spy so intermediate sections don't hijack the highlight.
        scrollTo(slug) {
            const body = this.$refs.body;
            const sec = body?.querySelector(`[data-section="${slug}"]`);
            if (!body || !sec) return;
            this._lock = true;
            clearTimeout(this._lockT);
            this._lockT = setTimeout(() => { this._lock = false; }, 650);
            this.current = slug;
            this.centerTab(slug);
            body.scrollTo({ top: sec.offsetTop, behavior: 'smooth' });
        },

        select(id, url, type) {
            this.$dispatch('stage-sticker', { id, url, type });
            this.open = false;
            this.teardownSpy();
        },
    }));
}

/* ──────────────────────────────────────────────────────────────────────────
   Plain-page auto-mount (shop / market previews)
   ────────────────────────────────────────────────────────────────────────── */
export function scanLottieCanvases(root = document) {
    root.querySelectorAll('canvas[data-lottie-src]:not([data-lottie-mounted])').forEach((canvas) => {
        canvas.dataset.lottieMounted = '1';
        mountLottie(canvas, {
            src: canvas.dataset.lottieSrc,
            hover: canvas.hasAttribute('data-lottie-hover'),
            loop: canvas.hasAttribute('data-lottie-loop'),
        });
    });
}

/* ──────────────────────────────────────────────────────────────────────────
   Posted comment stickers (inside the Livewire comments list).

   These use plain data-* markup instead of Alpine directives, because Livewire
   skips Alpine initialisation inside `wire:ignore` subtrees when a comment is
   inserted by an update — which left freshly-posted animated stickers blank
   until a full page reload. We mount them ourselves.

   Mounting is driven by a MutationObserver (below) rather than a Livewire commit
   hook: the observer fires the moment the sticker node is actually attached to
   the DOM, with no dependence on Livewire's commit/morph ordering or timing.
   The `data-mounted` flag keeps it idempotent across re-scans.
   ────────────────────────────────────────────────────────────────────────── */
export function mountPostedStickers(root = document) {
    root.querySelectorAll('[data-posted-sticker]:not([data-mounted])').forEach((el) => {
        el.dataset.mounted = '1';
        if (el.dataset.type === 'lottie') {
            mountLottie(el, { src: el.dataset.src, hover: false, loop: true });
        } else if (el.dataset.type === 'video') {
            el.muted = true; el.loop = true; el.playsInline = true;
            // Posted videos are few, so eagerly buffer them. With preload="metadata"
            // a freshly-inserted .webm shows a blank frame until it is cached (i.e.
            // until a reload) — paint the first frame as soon as data is available so
            // it's never blank, then play once it scrolls into view.
            el.preload = 'auto';
            const showFirstFrame = () => { try { if (!(el.currentTime > 0)) el.currentTime = 0.001; } catch (e) {} };
            el.addEventListener('loadeddata', showFirstFrame, { once: true });
            const io = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        const p = el.play();
                        if (p && typeof p.catch === 'function') p.catch(showFirstFrame);
                    } else {
                        el.pause();
                    }
                });
            }, { rootMargin: '120px' });
            io.observe(el);
        }
    });
}

// Watch for sticker nodes being added by Livewire (new comment / reply) and mount
// them immediately. One observer on <body> covers all comment lists on the page.
function observePostedStickers() {
    if (document.body.__stickerMO) return;
    const mo = new MutationObserver((mutations) => {
        for (const m of mutations) {
            for (const node of m.addedNodes) {
                if (node.nodeType !== 1) continue;
                if (node.matches?.('[data-posted-sticker]') ||
                    node.querySelector?.('[data-posted-sticker]:not([data-mounted])')) {
                    mountPostedStickers();
                    return;
                }
            }
        }
    });
    mo.observe(document.body, { childList: true, subtree: true });
    document.body.__stickerMO = mo;
}

/* ──────────────────────────────────────────────────────────────────────────
   Eager prefetch — warm the browser cache for the sticker files the user can
   actually reach (the picker only renders available packs), so the FIRST open
   is instant too. Runs in idle time and is cheap to repeat: <link rel=prefetch>
   is low-priority and de-duped by URL. Pairs with the long Cache-Control on
   /storage media (see public/.htaccess) so prefetched files stay cached.
   ────────────────────────────────────────────────────────────────────────── */
function prefetchStickerAssets() {
    const cells = document.querySelectorAll('[data-sticker-cell][data-src]');
    if (!cells.length) return;
    const head = document.head;
    const seen = new Set();
    cells.forEach((c) => {
        const url = c.dataset.src;
        if (!url || seen.has(url)) return;
        seen.add(url);
        if (head.querySelector(`link[rel="prefetch"][href="${CSS.escape(url)}"]`)) return;
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        // Same-origin /storage assets — no crossorigin, so the warmed cache entry
        // is reused by the player's plain fetch. `as` only steers priority.
        link.as = c.dataset.type === 'video' ? 'video' : (c.dataset.type === 'image' ? 'image' : 'fetch');
        head.appendChild(link);
    });
}

function scheduleStickerPrefetch() {
    const run = () => prefetchStickerAssets();
    if ('requestIdleCallback' in window) requestIdleCallback(run, { timeout: 3000 });
    else setTimeout(run, 1500);
}

function initStickers() {
    scanLottieCanvases();
    mountPostedStickers();
    observePostedStickers();
    scheduleStickerPrefetch();
}

document.addEventListener('alpine:init', () => registerStickerDirectives(window.Alpine));
document.addEventListener('DOMContentLoaded', initStickers);
document.addEventListener('livewire:navigated', initStickers);
// In case the script loads after DOMContentLoaded has already fired.
if (document.readyState !== 'loading') initStickers();
window.scanLottieCanvases = scanLottieCanvases;
window.mountPostedStickers = mountPostedStickers;
