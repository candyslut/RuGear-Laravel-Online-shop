{{-- Единая кнопка реролла: заменяет ВСЕ задания дня (включая выполненные) за
     REROLL_COST монет, доступна один раз в сутки. Требует Alpine-контекста с
     { rolling, shake } на родителе (его дают daily-quests и quests-modal) и
     Livewire-метода rerollAll(). --}}
@php
    $cost = \App\Support\Quests::REROLL_COST;
    $used = $rerollUsed ?? (auth()->user()?->last_quest_reroll_date?->isToday() ?? false);
    $can  = !$used && $quests->isNotEmpty() && ($coins ?? 0) >= $cost;

    $title = $used
        ? 'Реролл уже использован — следующий будет доступен завтра'
        : (($coins ?? 0) < $cost
            ? 'Для замены нужно '.$cost.' монет'
            : 'Заменить все задания на новые (раз в сутки)');
@endphp
<button type="button" class="qst-reroll"
        x-bind:class="shake && 'qst-reroll--shake'"
        x-bind:disabled="rolling || {{ $can ? 'false' : 'true' }}"
        @unless($can) disabled @endunless
        @if($can) x-on:click="if (!rolling) { rolling = true; $wire.rerollAll() }" @endif
        title="{{ $title }}">
    <svg class="qst__die" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <rect x="3" y="3" width="18" height="18" rx="4"/>
        <circle cx="8.3" cy="8.3" r=".9" fill="currentColor" stroke="none"/>
        <circle cx="15.7" cy="8.3" r=".9" fill="currentColor" stroke="none"/>
        <circle cx="12" cy="12" r=".9" fill="currentColor" stroke="none"/>
        <circle cx="8.3" cy="15.7" r=".9" fill="currentColor" stroke="none"/>
        <circle cx="15.7" cy="15.7" r=".9" fill="currentColor" stroke="none"/>
    </svg>
    @if($used)
        <span>Реролл будет доступен завтра</span>
    @else
        <span>Заменить задания</span>
        <span class="qst-reroll__cost">
            {{ $cost }}
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
                <circle cx="12" cy="12" r="10" fill="#FBBF24" stroke="#D97706" stroke-width="1.5"/>
                <circle cx="12" cy="12" r="7.5" fill="#F59E0B" stroke="#D97706" stroke-width="0.5"/>
            </svg>
        </span>
    @endif
</button>
