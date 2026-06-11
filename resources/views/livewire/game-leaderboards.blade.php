<div>
@if ($ready ?? false)
@php $defaultTab = $defaultTab ?? array_key_first($boards); @endphp

<div class="gs-hud gs-overlay" x-data x-init="window.hideModalLoader && hideModalLoader()" x-on:keydown.escape.window="$wire.close()">
    <div class="gs-backdrop" wire:click="close"></div>

    <div class="gs-panel" role="dialog" aria-modal="true"
         x-data="{ tab: @js($defaultTab), per: 'alltime' }">

        {{-- Header --}}
        <div class="gs-head">
            <span class="gs-head__bar"></span>
            <div class="min-w-0">
                <h2 class="gs-head__title">Игровые лидерборды</h2>
                <p class="gs-head__sub">Лучшие игроки по рекордам</p>
            </div>
            <button wire:click="close" class="gs-x ml-auto" aria-label="Закрыть">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        {{-- Game tabs --}}
        <div class="gs-tabs-wrap">
            <div class="gs-tabs" role="tablist">
                @foreach ($boards as $key => $b)
                    <button type="button" class="gs-tab" :class="tab === @js($key) && 'is-active'"
                            style="--gs-accent: {{ $b['meta']['accent'] }}" @click="tab = @js($key)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="gs-tab__ic">{!! $b['meta']['icon'] !!}</svg>
                        <span>{{ $b['meta']['short'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Period toggle --}}
            <div class="gs-period">
                <button type="button" class="gs-period__b" :class="per === 'alltime' && 'is-active'" @click="per = 'alltime'">За всё время</button>
                <button type="button" class="gs-period__b" :class="per === 'week' && 'is-active'" @click="per = 'week'">За неделю</button>
            </div>

            {{-- Boards --}}
            <div class="gs-body">
                @foreach ($boards as $key => $b)
                    <div x-show="tab === @js($key)" x-cloak wire:key="gs-lb-{{ $key }}" style="--gs-accent: {{ $b['meta']['accent'] }}">
                        <div x-show="per === 'alltime'">
                            @include('partials.stats.lb-list', ['rows' => $b['alltime'], 'meId' => $meId, 'gameKey' => $key, 'meta' => $b['meta']])
                        </div>
                        <div x-show="per === 'week'" x-cloak>
                            @include('partials.stats.lb-list', ['rows' => $b['week'], 'meId' => $meId, 'gameKey' => $key, 'meta' => $b['meta']])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="gs-footer">
            <button wire:click="close" class="gs-btn gs-btn--ghost w-full">Закрыть</button>
        </div>
    </div>

    @include('partials.stats.styles')
</div>
@endif
</div>
