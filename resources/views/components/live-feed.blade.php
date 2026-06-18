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
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center h-16">

            {{-- Подпись ленты (идентичность полосы) --}}
            <div class="lf-bar-label hidden sm:flex">Лента</div>

            {{-- Зона «Событие дня» (закреплено, висит весь день) --}}
            <div id="live-feed-eod-zone" class="lf-eod-zone hidden">
                <div id="live-feed-eod"></div>
            </div>

            {{-- Поток последних событий (переполнение → старые отбрасываются) --}}
            <div id="live-feed-track" class="flex items-center gap-2.5 flex-1 min-w-0 overflow-hidden"></div>
        </div>
    </div>
</div>
