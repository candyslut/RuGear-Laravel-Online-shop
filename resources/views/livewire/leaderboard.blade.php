@php use App\Support\Gamification; @endphp
<div>
@if($open)
<div class="lb-overlay" x-data="{}" x-init="window.hideModalLoader && hideModalLoader()" x-on:keydown.escape.window="dismissModal($el); $wire.close()">
    <div class="lb-backdrop" @click="dismissModal($el); $wire.close()"></div>

    <div class="hud lb-panel hud-corner" role="dialog" aria-modal="true">
        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 sm:px-6 py-4 border-b flex-shrink-0" style="border-color:var(--line)">
            <span class="lb-head-mark t-acc flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
            </span>
            <h2 class="hud-head__title">Рейтинг пользователей</h2>
            @if($myRank)
            <span class="ml-auto rank-chip" title="Ваше место">
                <span class="hud-label">Вы</span>
                <span class="text-base font-black t-acc hud-tnum leading-none">#{{ $myRank }}</span>
            </span>
            @endif
            <button @click="dismissModal($el); $wire.close()" class="lb-x {{ $myRank ? 'ml-2' : 'ml-auto' }}" aria-label="Закрыть">
                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="px-5 sm:px-6 py-3 border-b flex-shrink-0" style="border-color:var(--line)">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 t-dim2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Поиск пользователя…" oninput="leaderSearch(this.value)"
                    class="lb-search w-full pl-9 pr-4 py-2.5 text-sm hud-mono">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto cs" style="min-height:0">
            {{-- Podium (top 3) --}}
            @if($total >= 3)
            <div id="lb-podium" class="lb-podium">
                @php $order = [1, 0, 2]; $rc = ['#f5a623', '#9aa4b2', '#cd7c3a']; @endphp
                @foreach($order as $pos)
                    @php $u = $users[$pos]; $tier = Gamification::rankTier($u->level); $isMe = $u->id === $meId; @endphp
                    <div class="lb-podium__col {{ $pos === 0 ? 'lb-podium__col--first' : '' }}">
                        <div class="lb-podium__medal" style="--mc:{{ $rc[$pos] }}">
                            <div class="lb-podium__ava {{ $isMe ? 'is-me' : '' }} {{ $u->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                                 style="{{ $u->cosmetic_border && $u->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$u->cosmetic_border.';' : '' }}">
                                @if($u->avatar)<img src="{{ Storage::url($u->avatar) }}" class="w-full h-full object-cover">
                                @else<span>{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>@endif
                            </div>
                            <span class="lb-podium__rank">{{ $pos + 1 }}</span>
                        </div>
                        <p class="lb-podium__name truncate" style="{{ $u->cosmetic_nickname_color ? 'color:'.$u->cosmetic_nickname_color.';' : '' }}{{ $u->cosmetic_font ? 'font-family:'.$u->cosmetic_font.';' : '' }}">{{ $u->name }}</p>
                        @if($u->full_name)<p class="text-[10px] t-dim2 truncate w-full">{{ $u->full_name }}</p>@endif
                        <p class="hud-mono text-[10px]" style="color:{{ $tier['color'] }}">{{ $tier['name'] }}</p>
                        <p class="lb-podium__lvl">LV {{ $u->level }} · {{ number_format($u->experience) }} XP</p>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Full list --}}
            <div id="lb-list" class="divide-y" style="border-color:var(--line)">
                @foreach($users as $i => $u)
                @php
                    $isMe = $u->id === $meId;
                    $tier = Gamification::rankTier($u->level);
                    $prog = $u->experienceProgress;
                @endphp
                <div class="lb-row" data-name="{{ mb_strtolower(trim($u->name.' '.$u->full_name)) }}">
                    <div class="hud-row flex flex-wrap items-center gap-x-3 gap-y-2 px-4 sm:px-6 py-3 {{ $isMe ? 'lb-row--me' : '' }}">
                        <div class="w-7 flex-shrink-0 text-center">
                            @if($i < 3)
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 24 24" style="color:{{ ['#f5a623','#9aa4b2','#cd7c3a'][$i] }}">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                            </svg>
                            @else<span class="hud-mono text-xs font-bold t-dim2">{{ $i + 1 }}</span>@endif
                        </div>

                        <div class="w-9 h-9 flex-shrink-0 overflow-hidden flex items-center justify-center {{ $isMe ? 'bg-orange-500 text-black' : 'bg-gray-700 text-white' }} text-sm font-black {{ $u->cosmetic_border === 'rainbow' ? 'avatar-rainbow' : '' }}"
                             style="{{ $u->cosmetic_border && $u->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$u->cosmetic_border.';' : '' }}">
                            @if($u->avatar)<img src="{{ Storage::url($u->avatar) }}" class="w-full h-full object-cover">
                            @else{{ strtoupper(mb_substr($u->name, 0, 1)) }}@endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold truncate" style="{{ $u->cosmetic_nickname_color ? 'color:'.$u->cosmetic_nickname_color.';' : ($isMe ? 'color:var(--accent);' : 'color:var(--text);') }}{{ $u->cosmetic_font ? 'font-family:'.$u->cosmetic_font.';' : '' }}">
                                {{ $u->name }}@if($isMe)<span class="text-[10px] font-normal t-acc ml-1" style="font-family:inherit">вы</span>@endif
                            </p>
                            @if($u->full_name)<p class="text-[11px] t-dim2 truncate">{{ $u->full_name }}</p>@endif
                            <span class="lb-tier" style="--tc:{{ $tier['color'] }}"><span class="lb-tier__dot"></span>{{ $tier['name'] }}</span>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto justify-end flex-shrink-0">
                            <div class="text-center w-9"><p class="text-sm font-black t-acc leading-none">{{ $u->level }}</p><p class="hud-label mt-0.5">ур.</p></div>
                            <div class="text-center w-12"><p class="text-sm font-black t-text leading-none hud-tnum">{{ number_format($u->experience) }}</p><p class="hud-label mt-0.5">XP</p></div>
                            <div class="text-center w-12"><p class="text-sm font-black leading-none hud-tnum" style="color:#f59e0b">{{ number_format($u->coins) }}</p><p class="hud-label mt-0.5">коины</p></div>
                            <div class="text-center w-9"><p class="text-sm font-black t-text leading-none">{{ $u->achievements->count() }}</p><p class="hud-label mt-0.5">ачивки</p></div>
                            <button onclick="toggleLeader({{ $u->id }})" id="leader-btn-{{ $u->id }}"
                                class="flex-shrink-0 hud-mono text-[10px] font-bold uppercase tracking-widest t-dim hover:text-orange-500 transition px-2.5 py-1.5" style="border:1px solid var(--line)">
                                Профиль
                            </button>
                        </div>
                    </div>

                    {{-- Expanded --}}
                    <div id="leader-det-{{ $u->id }}" class="hidden px-4 sm:px-6 pb-4">
                        <div class="p-4 space-y-3 ml-0 sm:ml-10" style="background:var(--inset);border:1px solid var(--line)">
                            @if($u->gender && in_array($u->gender, ['male','female']))
                            <span class="text-xs t-dim px-2.5 py-1" style="background:var(--bg-2);border:1px solid var(--line)">{{ ['male'=>'Мужской','female'=>'Женский'][$u->gender] }}</span>
                            @endif
                            @if($u->about)<p class="text-sm t-dim leading-relaxed">{{ $u->about }}</p>
                            @else<p class="text-xs t-dim2 italic">О себе не указано</p>@endif
                            <div>
                                <div class="flex justify-between hud-mono text-[11px] t-dim2 mb-1.5">
                                    <span>Уровень {{ $u->level }}</span><span>{{ $prog }} / 100 XP</span>
                                </div>
                                <div class="hud-meter"><span style="width:{{ $prog }}%"></span></div>
                            </div>
                            @if($u->achievements->count() > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($u->achievements as $ach)
                                @php $r = Gamification::rarity($ach); @endphp
                                <span class="text-[10px] px-2 py-0.5" style="color:{{ $r['color'] }};background:color-mix(in srgb, {{ $r['color'] }} 12%, transparent);border:1px solid color-mix(in srgb, {{ $r['color'] }} 35%, transparent)">{{ $ach->title }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                <div id="lb-empty" class="hidden px-6 py-10 text-center"><p class="text-sm t-dim2">Никого не найдено</p></div>
            </div>
        </div>

        <div class="px-5 sm:px-6 py-4 border-t flex-shrink-0 flex flex-wrap gap-2" style="border-color:var(--line)">
            <button wire:click="openRanks" onclick="showModalLoader()" class="hud-btn flex-1 justify-center" style="border-color:var(--accent);color:var(--accent)">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M12 2 4 5v6c0 5 3.4 7.7 8 9 4.6-1.3 8-4 8-9V5l-8-3z"/><polyline points="8.5 11.5 11 14 15.5 9.5"/>
                </svg>
                Все ранги
            </button>
            <button wire:click="openGameBoards" onclick="showModalLoader()" class="hud-btn flex-1 justify-center" style="border-color:var(--accent);color:var(--accent)">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M8 21V9M16 21V5M3 21h18M12 21v-6"/>
                </svg>
                Игры
            </button>
            <button @click="dismissModal($el); $wire.close()" class="hud-btn w-full justify-center">Закрыть</button>
        </div>
    </div>
</div>
@endif
</div>
