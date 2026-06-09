<div>
@if($open)
<div class="lb-overlay" x-data="{ tip:false, tipTitle:'', tipRarity:'', tipDesc:'', tipIcon:'', tipColor:'', tipX:0, tipY:0,
        showTip(el, e){ this.tipTitle=el.dataset.title; this.tipRarity=el.dataset.rarity; this.tipDesc=el.dataset.desc; this.tipIcon=el.querySelector('.ach-tile__icon').innerHTML; this.tipColor=el.style.getPropertyValue('--rc'); this.tip=true; this.moveTip(e); },
        moveTip(e){ const w=320, h=200, pad=12; let x=e.clientX+16, y=e.clientY+16; if(x+w+pad>window.innerWidth) x=e.clientX-w-16; if(y+h+pad>window.innerHeight) y=window.innerHeight-h-pad; if(y<pad) y=pad; this.tipX=x; this.tipY=y; } }"
     x-on:keydown.escape.window="$wire.close()">
    <div class="lb-backdrop" wire:click="close"></div>

    <div class="hud lb-panel lb-panel--ach hud-corner" role="dialog" aria-modal="true">
        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b flex-shrink-0" style="border-color:var(--line)">
            <span class="hud-head__bar"></span>
            <div class="min-w-0">
                <h2 class="hud-head__title">Достижения</h2>
                <p class="hud-mono text-[11px] t-dim2 mt-0.5">
                    <span class="t-acc font-bold">{{ $unlocked }}</span> / {{ $total }} ПОЛУЧЕНО
                    <span class="mx-1">·</span> Σ <span class="t-text font-bold">{{ number_format($earnedXp) }}</span> XP
                </p>
            </div>
            <button wire:click="close" class="lb-x ml-auto" aria-label="Закрыть">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        {{-- Completion meter + filters --}}
        <div class="px-5 sm:px-6 py-3 border-b flex-shrink-0 flex flex-col sm:flex-row sm:items-center gap-3" style="border-color:var(--line)">
            <div class="flex-1 w-full">
                <div class="hud-meter"><span style="width:{{ $total > 0 ? round($unlocked / $total * 100) : 0 }}%"></span></div>
            </div>
            <div class="flex gap-1.5 flex-shrink-0" id="ach-filters">
                <button class="ach-chip is-active" data-filter="all" onclick="achFilter(this,'all')">Все</button>
                <button class="ach-chip" data-filter="got" onclick="achFilter(this,'got')">Получены</button>
                <button class="ach-chip" data-filter="locked" onclick="achFilter(this,'locked')">Закрыты</button>
            </div>
        </div>

        {{-- Grid --}}
        <div class="flex-1 overflow-y-auto cs p-4 sm:p-5" style="min-height:0">
            <div id="ach-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($items as $a)
                <div class="ach-tile {{ $a['got'] ? 'is-got' : 'is-locked' }}" data-state="{{ $a['got'] ? 'got' : 'locked' }}" style="--rc:{{ $a['rarity']['color'] }}"
                    data-title="{{ $a['title'] }}" data-rarity="{{ $a['rarity']['label'] }}" data-desc="{{ $a['description'] }}"
                    @mouseenter="showTip($el, $event)" @mousemove="moveTip($event)" @mouseleave="tip=false">
                    <div class="ach-tile__icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            {!! $a['got'] ? $a['icon'] : '<rect x="5" y="11" width="14" height="9" rx="1.5"/><path d="M8 11V8a4 4 0 018 0v3"/>' !!}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="ach-tile__title truncate">{{ $a['title'] }}</p>
                            <span class="ach-tile__rarity">{{ $a['rarity']['label'] }}</span>
                        </div>
                        <p class="ach-tile__desc">{{ $a['description'] }}</p>
                        @if($a['got'] && $a['awarded_at'])
                        <p class="hud-mono text-[10px] t-dim2 mt-1">{{ \Carbon\Carbon::parse($a['awarded_at'])->diffForHumans() }}</p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="ach-tile__xp">+{{ $a['experience'] }}<span class="text-[10px]"> XP</span></p>
                        <p class="ach-tile__status">{{ $a['got'] ? 'Получено' : 'Закрыто' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="ach-grid-empty" class="hidden px-6 py-10 text-center"><p class="text-sm t-dim2">Здесь пусто</p></div>
        </div>

        <div class="px-5 sm:px-6 py-4 border-t flex-shrink-0" style="border-color:var(--line)">
            <button wire:click="close" class="hud-btn w-full justify-center">Закрыть</button>
        </div>
    </div>

    {{-- Floating hover tooltip --}}
    <div class="ach-tip hud" x-show="tip" x-cloak :style="`left:${tipX}px; top:${tipY}px; --rc:${tipColor}`">
        <div class="ach-tip__head">
            <div class="ach-tip__icon" x-html="tipIcon"></div>
            <div class="min-w-0">
                <div class="ach-tip__title" x-text="tipTitle"></div>
                <div class="ach-tip__rarity" x-text="tipRarity"></div>
            </div>
        </div>
        <div class="ach-tip__desc" x-text="tipDesc"></div>
    </div>
</div>
@endif
</div>
