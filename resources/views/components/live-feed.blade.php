{{--
    Live Feed («Живая лента») — an editorial strip under the header.

    Two logically separated zones, split by a hairline divider:
      • #live-feed-eod-zone — the pinned "Событие дня" (day's best event), shown
        as a filled, elevated card.
      • #live-feed-track — the flowing recent events, as ghost/outlined chips
        that slide in from the right; oldest fall off (soft edge fade).

    Restrained, cool palette; theme CSS variables → dark/light/cosmic/pink work
    automatically. No SVG / emoji / decorative gradients. Behaviour lives in
    resources/js/livefeed.js. Rendered with the header (sticky right below it)
    and only hidden by JS if there is genuinely no activity.
--}}
<div id="live-feed-bar" class="transition-colors duration-300 sticky top-20 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        {{-- Desktop: a fixed row that drops the oldest chips to fit.
             Mobile (≤639px, via CSS): this same row becomes a single
             horizontally-scrollable, swipeable lane — nothing is dropped, so
             ≥4 events stay reachable by swiping. --}}
        <div id="live-feed-row" class="flex items-center h-16">

            {{-- Подпись ленты (идентичность полосы) --}}
            <div class="lf-bar-label hidden sm:flex">Лента</div>

            {{-- Зона «Событие дня» (закреплено, висит весь день) --}}
            <div id="live-feed-eod-zone" class="lf-eod-zone hidden">
                <div id="live-feed-eod"></div>
            </div>

            {{-- Поток последних событий (десктоп: старые отбрасываются;
                 мобильные: прокручиваются свайпом) --}}
            <div id="live-feed-track" class="flex items-center gap-2.5 flex-1 min-w-0 overflow-hidden"></div>
        </div>
    </div>
</div>
