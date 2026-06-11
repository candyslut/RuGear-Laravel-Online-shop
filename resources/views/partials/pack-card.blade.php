{{-- One sticker-pack card for the market shelf.
     Inputs: $pack (array). Inherits $rarityMeta and $stars (closure) from the
     market view's scope.

     Every label sits on its own line in normal flow — nothing is positioned
     over the artwork, so nothing overlaps. --}}
@php
    $rk    = $pack['rarity'] ?? 'common';
    $m     = $rarityMeta[$rk] ?? $rarityMeta['common'];
    $rgb   = $m['rgb'];
    $label = $m['l'];
    $src   = $pack['preview'][0] ?? null;
    // Легендарные паки заперты за игровыми рубежами ($legendaryUnlocked из
    // market.blade.php); сервер дублирует проверку (buy → legendary_locked).
    $packLocked = $rk === 'legendary' && !$legendaryUnlocked;
@endphp
<article class="pkx {{ $rk === 'legendary' ? 'pkx--legendary' : '' }}" style="--rgb: {{ $rgb }};">
    <button type="button" class="pkx__stage pkx__stage--btn" title="Посмотреть стикеры"
            onclick="openPackPreview('{{ $pack['slug'] }}')">
        @if($src)
            @include('partials.pack-preview', ['src' => $src, 'size' => '5rem'])
        @else
            <span style="font-size:2rem;">📦</span>
        @endif
        <span class="pkx__peek">Предпросмотр</span>
    </button>

    <span class="gem">{{ $label }}</span>
    <h3 class="pkx__name">{{ $pack['name'] }}</h3>

    <div class="pkx__metarow">
        <span class="pkx__stars">{!! $stars($m['stars'], $rgb) !!}</span>
        @if(!empty($pack['animated']))
            <span class="anim-pill"><span class="dot"></span>Анимация</span>
        @endif
    </div>

    <div class="pkx__foot" id="pack-foot-{{ $pack['shop_item_id'] }}">
        @if($pack['owned'])
            <span class="pkx__owned">✓ В коллекции</span>
            <span class="pkx__count">{{ $pack['count'] }} шт.</span>
        @else
            <span class="pkx__price">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                    <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                </svg>
                {{ number_format($pack['price']) }}
            </span>
            @if($packLocked)
                <button type="button" class="pkx-buy" disabled
                        title="Откроется за {{ \App\Models\User::LEGENDARY_BUZZWORD_LEVELS }} уровней Buzzword Blast и {{ number_format(\App\Models\User::LEGENDARY_REDLINE_DISTANCE, 0, ',', ' ') }} м в Redline Rush">
                    Закрыто
                </button>
            @else
                <button type="button" class="pkx-buy"
                        onclick="purchasePack('{{ $pack['slug'] }}')">
                    Купить
                </button>
            @endif
        @endif
    </div>
</article>
