<x-admin-layout>
<x-slot:title>RuGear Admin | Пользователи</x-slot:title>

<style>
    dialog[open] { position:fixed;top:50%!important;left:50%!important;transform:translate(-50%,-50%)!important;margin:0!important; }
    dialog::backdrop { background:rgba(0,0,0,0.75); }
    dialog { animation:dlgIn .2s ease-out; }
    @keyframes dlgIn { from{opacity:0;transform:translate(-50%,-48%) scale(.97)} to{opacity:1;transform:translate(-50%,-50%) scale(1)} }

    @keyframes rainbow-shadow {
        0%   { box-shadow:0 0 0 3px #ff4444,0 0 14px rgba(255,68,68,.5); }
        33%  { box-shadow:0 0 0 3px #44ff44,0 0 14px rgba(68,255,68,.5); }
        66%  { box-shadow:0 0 0 3px #0099ff,0 0 14px rgba(0,153,255,.5); }
        100% { box-shadow:0 0 0 3px #ff4444,0 0 14px rgba(255,68,68,.5); }
    }
    .avatar-rainbow { animation:rainbow-shadow 2.5s linear infinite; }

    .usr-row { transition:background .15s; }
    .usr-row:hover { background:rgba(255,255,255,.02); }
</style>

<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        <span>Вернуться в личный кабинет</span>
    </a>
</div>

{{-- Header --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black uppercase tracking-wider text-white">Пользователи</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $users->total() }} аккаунтов · найдено {{ $users->total() }}</p>
    </div>
    <div class="bg-[#161920] border border-gray-800 px-5 py-2.5 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-users text-orange-500 text-sm"></i>
        <span class="text-xl font-black text-white">{{ $users->total() }}</span>
    </div>
</div>

{{-- Search + Sort bar --}}
<form method="GET" action="{{ route('admin.users') }}" id="filter-form" class="flex flex-col sm:flex-row gap-3 mb-5">
    {{-- Search --}}
    <div class="relative flex-1">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Поиск по имени или email..."
               oninput="debounceSubmit()"
               class="w-full bg-[#161920] border border-gray-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-orange-500 transition">
        @if($search)
        <a href="{{ route('admin.users', ['sort' => $sort]) }}"
           class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-white transition text-lg leading-none">×</a>
        @endif
    </div>

    {{-- Sort buttons --}}
    <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
    <div class="flex items-center gap-1.5 bg-[#161920] border border-gray-800 rounded-xl p-1 flex-shrink-0">
        @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'level' => 'По уровню', 'coins' => 'По монетам'] as $key => $label)
        <button type="button" onclick="setSort('{{ $key }}')"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap sort-btn {{ $sort === $key ? 'bg-orange-500 text-black' : 'text-gray-500 hover:text-white' }}"
                data-sort="{{ $key }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
</form>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3.5 rounded-2xl text-sm flex items-center gap-3 mb-5">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-500/10 border border-red-500/20 text-red-400 px-5 py-3.5 rounded-2xl text-sm flex items-center gap-3 mb-5">
    <i class="fa-solid fa-triangle-exclamation"></i>
    {{ session('error') }}
</div>
@endif

{{-- Table --}}
<div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">

    {{-- Table head --}}
    <div class="grid gap-4 px-5 py-3 border-b border-gray-800 text-[10px] font-bold uppercase tracking-widest text-gray-600"
         style="grid-template-columns:2.5rem 1fr 6rem 5rem 5rem 6rem 8rem">
        <span>#</span>
        <span>Пользователь</span>
        <span class="text-center">Уровень</span>
        <span class="text-center">Монеты</span>
        <span class="text-center">Заказы</span>
        <span class="text-center">Достижения</span>
        <span class="text-right">Действия</span>
    </div>

    @forelse($users as $user)
    @php
        $borderCss = $user->cosmetic_border && $user->cosmetic_border !== 'rainbow' ? 'box-shadow:'.$user->cosmetic_border.';' : '';
        $isRainbow = $user->cosmetic_border === 'rainbow';
        $genderMap = ['male'=>'Мужской','female'=>'Женский'];
    @endphp
    <div class="usr-row grid gap-4 px-5 py-3.5 border-b border-gray-800/50 items-center last:border-b-0"
         style="grid-template-columns:2.5rem 1fr 6rem 5rem 5rem 6rem 8rem">

        {{-- ID --}}
        <span class="text-xs font-mono text-gray-600">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>

        {{-- User --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl flex-shrink-0 overflow-hidden flex items-center justify-center font-black text-sm
                        {{ $user->role === 'admin' ? 'bg-orange-500 text-black' : 'bg-gray-800 text-white' }}
                        {{ $isRainbow ? 'avatar-rainbow' : '' }}"
                 style="{{ $borderCss }}">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold truncate"
                   style="color:{{ $user->cosmetic_nickname_color ?? '#fff' }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}">
                    {{ $user->name }}
                    @if($user->role === 'admin')
                        <span class="text-[9px] text-orange-500 font-bold ml-1">ADMIN</span>
                    @endif
                </p>
                <p class="text-xs text-gray-600 truncate">{{ $user->email }}</p>
            </div>
        </div>

        {{-- Level --}}
        <div class="text-center">
            <p class="text-sm font-black text-orange-500">{{ $user->level }}</p>
            <p class="text-[9px] text-gray-600 mt-0.5">{{ $user->experience }} XP</p>
        </div>

        {{-- Coins --}}
        <div class="text-center">
            <p class="text-sm font-black" style="color:#fbbf24">{{ number_format($user->coins) }}</p>
        </div>

        {{-- Orders --}}
        <div class="text-center">
            <p class="text-sm font-bold text-white">{{ $user->orders_count }}</p>
        </div>

        {{-- Achievements --}}
        <div class="text-center">
            <p class="text-sm font-bold text-white">{{ $user->achievements->count() }}</p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-1.5">
            <button onclick="openUserModal({{ $user->id }})"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-300 hover:text-white border border-gray-700 hover:border-gray-600 transition">
                Подробнее
            </button>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                  onsubmit="return confirm('Удалить {{ addslashes($user->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="p-1.5 rounded-lg text-gray-600 hover:text-red-400 border border-transparent hover:border-red-500/20 hover:bg-red-500/5 transition
                               {{ auth()->id() === $user->id ? 'opacity-30 pointer-events-none' : '' }}"
                        title="Удалить">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Hidden detail modal data --}}
    <template id="user-data-{{ $user->id }}">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-2xl flex-shrink-0 overflow-hidden flex items-center justify-center font-black text-xl
                        {{ $user->role === 'admin' ? 'bg-orange-500 text-black' : 'bg-gray-800 text-white' }}
                        {{ $isRainbow ? 'avatar-rainbow' : '' }}"
                 style="{{ $borderCss }}">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <p class="text-lg font-black"
                   style="color:{{ $user->cosmetic_nickname_color ?? '#fff' }};{{ $user->cosmetic_font ? 'font-family:'.$user->cosmetic_font.';' : '' }}">
                    {{ $user->name }}
                    @if($user->role === 'admin')<span class="text-xs text-orange-500 ml-1">ADMIN</span>@endif
                </p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <p class="text-xs text-gray-600 mt-0.5">Регистрация: {{ $user->created_at->format('d.m.Y') }}</p>
            </div>
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-4 gap-2 mb-5">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                <p class="text-xl font-black text-orange-500">{{ $user->level }}</p>
                <p class="text-[9px] text-gray-600 uppercase mt-0.5">Уровень</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                <p class="text-xl font-black text-white">{{ $user->experience }}</p>
                <p class="text-[9px] text-gray-600 uppercase mt-0.5">XP</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                <p class="text-xl font-black" style="color:#fbbf24">{{ number_format($user->coins) }}</p>
                <p class="text-[9px] text-gray-600 uppercase mt-0.5">Монеты</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
                <p class="text-xl font-black text-white">{{ $user->achievements->count() }}</p>
                <p class="text-[9px] text-gray-600 uppercase mt-0.5">Ачивки</p>
            </div>
        </div>

        {{-- XP bar --}}
        <div class="mb-5">
            <div class="h-1.5 bg-gray-900 rounded-full overflow-hidden">
                <div class="h-full bg-orange-500 rounded-full" style="width:{{ min(100, $user->experienceProgress) }}%"></div>
            </div>
            <p class="text-[10px] text-gray-600 mt-1">До уровня {{ $user->level + 1 }}: {{ $user->next_level_experience - $user->experienceProgress }} XP</p>
        </div>

        {{-- Personal info --}}
        <div class="space-y-2 mb-5">
            @if($user->phone)
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <span class="text-[9px] text-gray-600 uppercase tracking-widest w-12 flex-shrink-0">Телефон</span>
                <span class="text-white font-medium">{{ $user->phone }}</span>
            </div>
            @endif
            @if($user->gender && isset($genderMap[$user->gender]))
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <span class="text-[9px] text-gray-600 uppercase tracking-widest w-12 flex-shrink-0">Пол</span>
                <span class="text-white font-medium">{{ $genderMap[$user->gender] }}</span>
            </div>
            @endif
            @if($user->about)
            <div class="flex items-start gap-2 text-sm text-gray-400">
                <span class="text-[9px] text-gray-600 uppercase tracking-widest w-12 flex-shrink-0 mt-0.5">О себе</span>
                <span class="text-gray-300 leading-relaxed">{{ $user->about }}</span>
            </div>
            @endif
        </div>

        {{-- Achievements --}}
        @if($user->achievements->count() > 0)
        <div class="mb-5">
            <p class="text-[9px] text-gray-600 uppercase tracking-widest mb-2">Достижения</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach($user->achievements as $ach)
                <span class="text-[10px] text-orange-400 bg-orange-500/10 border border-orange-500/20 rounded-lg px-2 py-0.5">{{ $ach->title }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Add coins form --}}
        <div class="border-t border-gray-800 pt-4">
            <p class="text-xs font-bold text-gray-400 mb-3">Начислить монеты</p>
            <form action="{{ route('admin.users.coins', $user) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="number" name="amount" min="1" max="99999" placeholder="Сумма"
                       class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-orange-500 transition">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-black transition"
                        style="background:#f59e0b;"
                        onmouseover="this.style.background='#d97706'"
                        onmouseout="this.style.background='#f59e0b'">
                    <i class="fa-solid fa-coins mr-1"></i> Начислить
                </button>
            </form>
        </div>
    </template>

    @empty
    <div class="px-5 py-16 text-center text-gray-600 text-sm">Пользователи не найдены</div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-6 custom-pagination">
    {{ $users->onEachSide(1)->links() }}
</div>

{{-- User detail dialog --}}
<dialog id="modal-user" class="bg-[#111318] border border-gray-800 rounded-2xl p-0 w-full max-w-lg shadow-2xl focus:outline-none" style="max-height:90vh;">
    <div class="flex flex-col" style="max-height:90vh;">
        <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4 flex-shrink-0">
            <h2 class="text-sm font-bold text-white">Профиль пользователя</h2>
            <button onclick="document.getElementById('modal-user').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition">×</button>
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
    document.querySelectorAll('.sort-btn').forEach(btn => {
        const active = btn.dataset.sort === val;
        btn.className = btn.className.replace(/bg-orange-500 text-black|text-gray-500 hover:text-white/g, '').trim();
        btn.classList.add(...(active ? ['bg-orange-500','text-black'] : ['text-gray-500','hover:text-white']));
    });
    document.getElementById('filter-form').submit();
}

let _searchTimer;
function debounceSubmit() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}
</script>

<style>
    .custom-pagination p { color:#6b7280!important;font-style:italic!important;font-size:.875rem!important; }
    .custom-pagination nav a,.custom-pagination nav span[aria-disabled="true"] span,.custom-pagination nav span[aria-current="page"] span { background-color:#161920!important;border-color:#1f2937!important;color:#9ca3af!important;border-radius:.75rem;margin:0 2px; }
    .custom-pagination nav span[aria-current="page"] span { background-color:#f97316!important;border-color:#f97316!important;color:#000!important;font-weight:900!important; }
    .custom-pagination nav a:hover { background-color:#1f2937!important;color:#f97316!important;border-color:#f97316!important; }
</style>

</x-admin-layout>
