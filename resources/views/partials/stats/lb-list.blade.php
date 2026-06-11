{{--
    Один список лидерборда (топ игроков по рекорду). Строки кликабельны и
    открывают игровой профиль игрока через viewPlayer() родителя.

    Параметры: $rows (Collection), $meId, $gameKey, $meta
--}}
@php $rows = $rows ?? collect(); @endphp
@if ($rows->isEmpty())
    <div class="gs-empty gs-empty--tall">
        <p class="gs-empty__t">Пока никто не играл</p>
        <p class="gs-empty__s">Будьте первым в рейтинге — сыграйте забег!</p>
    </div>
@else
    <div class="gs-lb">
        @foreach ($rows as $i => $r)
            <button type="button" wire:click="viewPlayer({{ $r->user->id }})" onclick="showModalLoader()"
                    class="gs-lbrow {{ $r->user->id === $meId ? 'is-me' : '' }}" style="--gs-accent: {{ $meta['accent'] }}">
                <span class="gs-lbrow__rk gs-lbrow__rk--{{ $i < 3 ? $i + 1 : 'n' }}">{{ $i + 1 }}</span>
                <span class="gs-lbrow__ava {{ $r->user->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}">
                    @if ($r->user->avatar)
                        <img src="{{ Storage::url($r->user->avatar) }}" class="w-full h-full object-cover" alt="">
                    @else
                        {{ strtoupper(mb_substr($r->user->name, 0, 1)) }}
                    @endif
                </span>
                <span class="gs-lbrow__main">
                    <span class="gs-lbrow__name">{{ $r->user->name }}@if ($r->user->id === $meId)<span class="gs-lbrow__you">вы</span>@endif</span>
                    <span class="gs-lbrow__sub">{{ \App\Support\GameStats::spaced($r->plays) }} забег. · {{ $meta['level_label'] }} {{ $r->best_level }}</span>
                </span>
                <span class="gs-lbrow__sc">{{ \App\Support\GameStats::formatScore($gameKey, $r->best) }}</span>
            </button>
        @endforeach
    </div>
@endif
