<x-admin-layout>
<x-slot:title>RuGear Admin | Пользователи</x-slot:title>

@include('admin.partials.hud')

<style>
    dialog[open] { position: fixed; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; margin: 0 !important; }
    dialog::backdrop { background: rgba(0,0,0,.78); backdrop-filter: blur(2px); }
    dialog { animation: dlgIn .18s ease-out; }
    @keyframes dlgIn { from { opacity: 0; transform: translate(-50%, -48%) } to { opacity: 1; transform: translate(-50%, -50%) } }

    @keyframes rainbow-shadow {
        0%   { box-shadow: 0 0 0 2px #ff4444; }
        33%  { box-shadow: 0 0 0 2px #44ff44; }
        66%  { box-shadow: 0 0 0 2px #0099ff; }
        100% { box-shadow: 0 0 0 2px #ff4444; }
    }
    .avatar-rainbow { animation: rainbow-shadow 2.5s linear infinite; }

    .reg-row { position: relative; transition: background .12s ease, box-shadow .12s ease; }
    .reg-row:hover { background: var(--bg-2); box-shadow: inset -3px 0 0 var(--accent); }
    .reg-row + .reg-row { border-top: 1px solid var(--line); }
    .reg-rail { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }

    .custom-pagination p { color: var(--dim) !important; font-size: .8rem !important; }
    .custom-pagination nav a, .custom-pagination nav span[aria-disabled="true"] span, .custom-pagination nav span[aria-current="page"] span {
        background-color: var(--bg) !important; border: 1px solid var(--line) !important; color: var(--dim) !important; border-radius: 0; margin: 0 2px;
    }
    .custom-pagination nav span[aria-current="page"] span { background-color: var(--accent) !important; border-color: var(--accent) !important; color: #0a0a0a !important; font-weight: 900 !important; }
    .custom-pagination nav a:hover { border-color: var(--accent) !important; color: var(--accent) !important; }
</style>

@php
    $admins  = $users->getCollection()->where('role', 'admin')->count();
    $genderMap = ['male' => 'Мужской', 'female' => 'Женский'];
@endphp

<div class="hud space-y-5">

    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold t-dim hover:t-acc transition-colors group">
        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        НАЗАД В ПРОФИЛЬ
    </a>

    {{-- ══ Command header ══ --}}
    <div class="hud-panel hud-corner hud-grid-bg">
        <div class="flex flex-col lg:flex-row lg:items-stretch">
            <div class="flex-1 p-6">
                <p class="hud-mono text-[10px] tracking-[0.3em] t-dim2">RUGEAR // ПОЛЬЗОВАТЕЛИ</p>
                <h1 class="text-3xl font-black uppercase tracking-tight t-text mt-2">Пользователи</h1>
                <p class="text-sm t-dim mt-1">Учётные записи · на странице {{ $users->count() }} из {{ $users->total() }}</p>
            </div>
            <div class="grid grid-cols-2 lg:flex border-t lg:border-t-0 lg:border-l" style="border-color: var(--line)">
                <div class="flex flex-col justify-center px-6 py-4 lg:py-0 border-r" style="border-color: var(--line); min-width: 9rem">
                    <span class="hud-colhead">Всего</span>
                    <span class="text-3xl font-black hud-tnum t-acc mt-1">{{ number_format($users->total()) }}</span>
                </div>
                <div class="flex flex-col justify-center px-6 py-4 lg:py-0" style="min-width: 8rem">
                    <span class="hud-colhead">Админы</span>
                    <span class="text-3xl font-black hud-tnum t-text mt-1">{{ $admins }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="hud-panel flex items-center gap-3 px-5 py-3.5" style="border-color: #22c55e">
        <span class="hud-state" style="background: #22c55e"></span><span class="text-sm t-text">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="hud-panel flex items-center gap-3 px-5 py-3.5" style="border-color: #ef4444">
        <span class="hud-state" style="background: #ef4444"></span><span class="text-sm t-text">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ══ Control bar ══ --}}
    <form method="GET" action="{{ route('admin.users') }}" id="filter-form" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 t-dim2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ $search ?? '' }}" oninput="debounceSubmit()"
                   placeholder="ПОИСК: ИМЯ / EMAIL"
                   class="hud-input hud-mono w-full pl-10 pr-9 py-2.5 text-xs tracking-wider">
            @if($search)
            <a href="{{ route('admin.users', ['sort' => $sort]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 t-dim2 hover:t-text transition text-lg leading-none">×</a>
            @endif
        </div>
        <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
        <div class="hud-seg flex-shrink-0">
            @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'level' => 'Уровень', 'coins' => 'Монеты'] as $key => $label)
            <button type="button" onclick="setSort('{{ $key }}')" class="hud-seg__btn {{ $sort === $key ? 'hud-seg__btn--on' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
    </form>

    {{-- ══ Registry ══ --}}
    <div class="hud-panel">
        <div class="hud-head">
            <span class="hud-head__bar"></span>
            <span class="hud-head__title">Учётные записи</span>
            <span class="hud-head__code hud-mono">SORT::{{ strtoupper($sort) }}</span>
        </div>

        <div class="hidden md:grid gap-4 px-5 py-2.5 border-b items-center hud-colhead" style="border-color: var(--line); grid-template-columns: 3.5rem 1fr 7rem 5.5rem 4.5rem 5.5rem 7rem">
            <span>ID</span>
            <span>Пользователь</span>
            <span class="text-center">Уровень</span>
            <span class="text-center">Монеты</span>
            <span class="text-center">Заказы</span>
            <span class="text-center">Ачивки</span>
            <span class="text-right">—</span>
        </div>

        @forelse($users as $user)
        @php
            $borderCss = $user->cosmetic_border && $user->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$user->cosmetic_border.';' : '';
            $isRainbow = $user->cosmetic_border === 'rainbow';
            $isAdmin   = $user->role === 'admin';
            $rail      = $isAdmin ? 'var(--accent)' : 'var(--line-2)';
            $tierFill  = max(1, min(8, (int) $user->level));
        @endphp
        <div class="reg-row flex flex-col gap-3 px-5 py-4 md:grid md:gap-4 md:py-3.5 md:items-center" style="grid-template-columns: 3.5rem 1fr 7rem 5.5rem 4.5rem 5.5rem 7rem">
            <span class="reg-rail" style="background: {{ $rail }}"></span>

            <span class="hidden md:block hud-mono text-xs t-dim2">{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>

            {{-- entity --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 flex-shrink-0 overflow-hidden flex items-center justify-center font-black text-sm {{ $isAdmin ? 'text-black' : 'text-white' }} {{ $isRainbow ? 'avatar-rainbow' : '' }}"
                     style="background: {{ $isAdmin ? 'var(--accent)' : 'var(--track)' }};{{ $borderCss }}">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold truncate" style="color: {{ $user->cosmetic_nickname_color ?? 'var(--text)' }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}">
                        {{ $user->name }}
                        @if($isAdmin)<span class="hud-mono text-[9px] t-acc ml-1 tracking-widest">ADMIN</span>@endif
                    </p>
                    <p class="text-xs t-dim2 truncate hud-mono">{{ $user->email }}</p>
                </div>
            </div>

            {{-- secondary stats: на мобильном — одна строка с подписями; на desktop — отдельные колонки --}}
            <div class="flex items-stretch justify-between gap-2 border-t border-b py-3 md:contents" style="border-color: var(--line)">

                {{-- tier --}}
                <div class="flex flex-col items-center gap-1.5">
                    <span class="md:hidden hud-colhead">Уровень</span>
                    <span class="text-sm font-black t-acc hud-tnum">{{ $user->level }}</span>
                    <div class="hud-ticks">
                        @for($t = 1; $t <= 8; $t++)<span class="{{ $t <= $tierFill ? 'on' : '' }}"></span>@endfor
                    </div>
                </div>

                {{-- coins --}}
                <div class="flex flex-col items-center gap-1 text-center md:block">
                    <span class="md:hidden hud-colhead">Монеты</span>
                    <p class="text-sm font-black hud-tnum" style="color: #f59e0b">{{ number_format($user->coins) }}</p>
                </div>

                {{-- orders --}}
                <div class="flex flex-col items-center gap-1 text-center md:block">
                    <span class="md:hidden hud-colhead">Заказы</span>
                    <p class="text-sm font-bold t-text hud-tnum">{{ $user->orders_count }}</p>
                </div>

                {{-- achievements --}}
                <div class="flex flex-col items-center gap-1 text-center md:block">
                    <span class="md:hidden hud-colhead">Ачивки</span>
                    <p class="text-sm font-bold t-text hud-tnum">{{ $user->achievements->count() }}</p>
                </div>
            </div>

            {{-- actions --}}
            <div class="flex items-center gap-1.5 md:justify-end">
                <button onclick="openUserModal({{ $user->id }})" class="hud-btn flex-1 md:flex-none">Подробнее</button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Удалить {{ addslashes($user->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="hud-btn hud-btn--danger px-2 {{ auth()->id() === $user->id ? 'opacity-30 pointer-events-none' : '' }}" title="Удалить">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12.1a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- modal data --}}
        <template id="user-data-{{ $user->id }}">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 flex-shrink-0 overflow-hidden flex items-center justify-center font-black text-xl {{ $isAdmin ? 'text-black' : 'text-white' }} {{ $isRainbow ? 'avatar-rainbow' : '' }}"
                     style="background: {{ $isAdmin ? 'var(--accent)' : 'var(--track)' }};{{ $borderCss }}">
                    @if($user->avatar)<img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">@else{{ strtoupper(mb_substr($user->name, 0, 1)) }}@endif
                </div>
                <div>
                    <p class="text-lg font-black" style="color: {{ $user->cosmetic_nickname_color ?? 'var(--text)' }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}">
                        {{ $user->name }} @if($isAdmin)<span class="hud-mono text-[10px] t-acc ml-1 tracking-widest">ADMIN</span>@endif
                    </p>
                    <p class="text-sm t-dim hud-mono">{{ $user->email }}</p>
                    <p class="text-xs t-dim2 mt-0.5 hud-mono">Рег.: {{ $user->created_at->format('d.m.Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-px mb-5" style="background: var(--line)">
                @php $cells = [['Уровень', $user->level, 'var(--accent)'], ['Опыт', $user->experience, 'var(--text)'], ['Монеты', number_format($user->coins), '#f59e0b'], ['Награды', $user->achievements->count(), 'var(--text)']]; @endphp
                @foreach($cells as $c)
                <div class="text-center py-3" style="background: var(--bg)">
                    <p class="text-xl font-black hud-tnum" style="color: {{ $c[2] }}">{{ $c[1] }}</p>
                    <p class="hud-colhead mt-1">{{ $c[0] }}</p>
                </div>
                @endforeach
            </div>

            <div class="mb-5">
                <div class="hud-meter"><span style="width: {{ min(100, $user->experienceProgress) }}%; background: var(--accent)"></span></div>
                <p class="text-[10px] t-dim2 mt-1.5 hud-mono">До ур. {{ $user->level + 1 }}: {{ $user->next_level_experience - $user->experienceProgress }} XP</p>
            </div>

            <div class="space-y-2 mb-5">
                @if($user->phone)<div class="flex items-center gap-3 text-sm"><span class="hud-colhead w-16 flex-shrink-0">Телефон</span><span class="t-text font-medium hud-mono">{{ $user->phone }}</span></div>@endif
                @if($user->gender && isset($genderMap[$user->gender]))<div class="flex items-center gap-3 text-sm"><span class="hud-colhead w-16 flex-shrink-0">Пол</span><span class="t-text font-medium">{{ $genderMap[$user->gender] }}</span></div>@endif
                @if($user->about)<div class="flex items-start gap-3 text-sm"><span class="hud-colhead w-16 flex-shrink-0 mt-0.5">О себе</span><span class="t-dim leading-relaxed">{{ $user->about }}</span></div>@endif
            </div>

            @if($user->achievements->count() > 0)
            <div class="mb-5">
                <p class="hud-colhead mb-2">Достижения</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($user->achievements as $ach)
                    <span class="text-[10px] t-acc px-2 py-1" style="border: 1px solid var(--accent); background: color-mix(in srgb, var(--accent) 10%, transparent)">{{ $ach->title }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="border-t pt-4" style="border-color: var(--line)">
                <p class="hud-colhead mb-3">Начислить монеты</p>
                <form action="{{ route('admin.users.coins', $user) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="number" name="amount" min="1" max="99999" placeholder="СУММА" class="hud-input hud-mono flex-1 px-3 py-2 text-sm">
                    <button type="submit" class="hud-btn hud-btn--solid px-4 py-2">Начислить</button>
                </form>
            </div>
        </template>

        @empty
        <div class="py-16 text-center text-xs uppercase tracking-widest t-dim">Пользователи не найдены</div>
        @endforelse
    </div>

    <div class="custom-pagination">{{ $users->onEachSide(1)->links() }}</div>
</div>

{{-- detail dialog --}}
<dialog id="modal-user" class="hud bg-transparent p-0 w-full max-w-lg focus:outline-none" style="max-height: 90vh;">
    <div class="hud-panel flex flex-col" style="max-height: 90vh;">
        <div class="hud-head flex-shrink-0">
            <span class="hud-head__bar"></span>
            <span class="hud-head__title">Профиль пользователя</span>
            <button onclick="document.getElementById('modal-user').close()" class="ml-auto t-dim2 hover:t-text text-2xl leading-none transition">×</button>
        </div>
        <div id="modal-user-body" class="flex-1 overflow-y-auto px-6 py-5"></div>
    </div>
</dialog>

<script>
function openUserModal(id) {
    const tpl = document.getElementById('user-data-' + id);
    if (!tpl) return;
    document.getElementById('modal-user-body').innerHTML = tpl.innerHTML;
    document.getElementById('modal-user').showModal();
}
function setSort(val) {
    document.getElementById('sort-input').value = val;
    document.getElementById('filter-form').submit();
}
let _searchTimer;
function debounceSubmit() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}
</script>

</x-admin-layout>
