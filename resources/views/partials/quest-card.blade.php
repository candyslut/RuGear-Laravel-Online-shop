@php
    /** @var \App\Models\DailyQuest $q */
    $tpl  = $q->template();
    $type = $tpl['type'] ?? 'visit';
    $done = $q->is_completed;
@endphp
<div class="qst {{ $done ? 'is-done' : '' }}">
    <div class="qst__icon">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            {!! \App\Support\Quests::icon($q->slug, $type) !!}
        </svg>
        @if($done)
        <span class="qst__check">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
        </span>
        @endif
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <p class="qst__title">{{ $tpl['title'] ?? 'Задание' }}</p>
            @if($done)<span class="qst__tag">Выполнено</span>@endif
        </div>
        <p class="qst__desc">{{ $tpl['description'] ?? '' }}</p>

        {{-- Progress meter --}}
        <div class="qst__meter">
            <div class="qst__bar"><span style="width:{{ $q->percent }}%"></span></div>
            <span class="qst__count">{{ min($q->progress, $q->goal) }}/{{ $q->goal }}</span>
        </div>
    </div>

    {{-- Rewards --}}
    <div class="qst__reward">
        <span class="qst__xp">+{{ $q->reward_xp }} XP</span>
        <span class="qst__coin">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
                <text x="12" y="16.5" text-anchor="middle" font-size="9.5" font-weight="bold" fill="#78350F" font-family="Georgia, serif">₽</text>
            </svg>
            +{{ $q->reward_coins }}
        </span>
    </div>
</div>
