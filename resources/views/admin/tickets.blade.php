<x-admin-layout>
<x-slot:title>RuGear Admin | Заявки</x-slot:title>

<style>
    dialog[open] { position:fixed;top:50%!important;left:50%!important;transform:translate(-50%,-50%)!important;margin:0!important; }
    dialog::backdrop { background:rgba(0,0,0,0.8); }
    dialog { animation:dlgIn .2s ease-out; }
    @keyframes dlgIn { from{opacity:0;transform:translate(-50%,-48%) scale(.97)} to{opacity:1;transform:translate(-50%,-50%) scale(1)} }

    .tkt-row { transition:background .15s; }
    .tkt-row:hover { background:rgba(255,255,255,.02); }

    .cs::-webkit-scrollbar { width:4px; }
    .cs::-webkit-scrollbar-track { background:transparent; }
    .cs::-webkit-scrollbar-thumb { background:#374151;border-radius:4px; }

    .custom-pagination p { color:#6b7280!important;font-style:italic!important;font-size:.875rem!important; }
    .custom-pagination nav a,.custom-pagination nav span[aria-disabled="true"] span,.custom-pagination nav span[aria-current="page"] span { background-color:#161920!important;border-color:#1f2937!important;color:#9ca3af!important;border-radius:.75rem;margin:0 2px; }
    .custom-pagination nav span[aria-current="page"] span { background-color:#f97316!important;border-color:#f97316!important;color:#000!important;font-weight:900!important; }
    .custom-pagination nav a:hover { background-color:#1f2937!important;color:#f97316!important;border-color:#f97316!important; }
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
        <h1 class="text-2xl font-black uppercase tracking-wider text-white">Заявки</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $tickets->total() }} заявок · найдено</p>
    </div>
    {{-- Status counters --}}
    <div class="flex items-center gap-2 flex-wrap">
        <div class="bg-[#161920] border border-gray-800 px-4 py-2 rounded-xl flex items-center gap-2">
            <span class="text-[9px] text-gray-600 uppercase tracking-widest">Ожидает</span>
            <span class="text-sm font-black text-blue-400">{{ $counts['pending'] }}</span>
        </div>
        <div class="bg-[#161920] border border-gray-800 px-4 py-2 rounded-xl flex items-center gap-2">
            <span class="text-[9px] text-gray-600 uppercase tracking-widest">Отвечен</span>
            <span class="text-sm font-black text-orange-400">{{ $counts['replied'] }}</span>
        </div>
        <div class="bg-[#161920] border border-gray-800 px-4 py-2 rounded-xl flex items-center gap-2">
            <span class="text-[9px] text-gray-600 uppercase tracking-widest">Закрыт</span>
            <span class="text-sm font-black text-gray-500">{{ $counts['closed'] }}</span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3.5 rounded-2xl text-sm flex items-center gap-3 mb-5">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

{{-- Search + Filter + Sort --}}
<form method="GET" action="{{ route('admin.tickets.index') }}" id="filter-form" class="flex flex-col gap-3 mb-5">

    <div class="flex flex-col sm:flex-row gap-3">
        {{-- Search --}}
        <div class="relative flex-1">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Поиск по теме, содержимому или имени пользователя..."
                   oninput="debounceSubmit()"
                   class="w-full bg-[#161920] border border-gray-800 rounded-xl pl-10 pr-9 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-orange-500 transition">
            @if($search)
            <a href="{{ route('admin.tickets.index', ['sort' => $sort, 'status' => $status]) }}"
               class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-white transition text-lg leading-none">×</a>
            @endif
        </div>

        {{-- Sort --}}
        <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
        <input type="hidden" name="status" id="status-input" value="{{ $status }}">
        <div class="flex items-center gap-1.5 bg-[#161920] border border-gray-800 rounded-xl p-1 flex-shrink-0">
            @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'status' => 'По статусу'] as $key => $label)
            <button type="button" onclick="setSort('{{ $key }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap sort-btn {{ $sort === $key ? 'bg-orange-500 text-black' : 'text-gray-500 hover:text-white' }}"
                    data-sort="{{ $key }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex items-center gap-1.5">
        @foreach(['all' => 'Все ('.$counts['all'].')', 'pending' => 'Ожидает ('.$counts['pending'].')', 'replied' => 'Отвечен ('.$counts['replied'].')', 'closed' => 'Закрыт ('.$counts['closed'].')'] as $key => $label)
        <button type="button" onclick="setStatus('{{ $key }}')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap status-btn
                    @if($status === $key)
                        @if($key === 'pending') bg-blue-500/20 text-blue-400 border border-blue-500/30
                        @elseif($key === 'replied') bg-orange-500/20 text-orange-400 border border-orange-500/30
                        @elseif($key === 'closed') bg-gray-700 text-gray-400 border border-gray-600
                        @else bg-white/10 text-white border border-white/20
                        @endif
                    @else text-gray-600 hover:text-gray-400 border border-transparent
                    @endif"
                data-status="{{ $key }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
</form>

{{-- Table --}}
@if($tickets->isEmpty())
<div class="bg-[#161920] border border-gray-800 rounded-2xl p-16 text-center">
    <i class="fa-solid fa-folder-open text-3xl text-gray-700 mb-3 block"></i>
    <p class="text-sm text-gray-500">Заявок не найдено</p>
</div>
@else

<div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden">

    {{-- Head --}}
    <div class="grid gap-4 px-5 py-3 border-b border-gray-800 text-[10px] font-bold uppercase tracking-widest text-gray-600"
         style="grid-template-columns:4.5rem 1fr 11rem 7rem 7rem 6rem">
        <span>ID</span>
        <span>Заявка</span>
        <span>Пользователь</span>
        <span class="text-center">Статус</span>
        <span class="text-center">Дата</span>
        <span class="text-right">Действие</span>
    </div>

    @foreach($tickets as $ticket)
    @php
        $statusStyle = match($ticket->status) {
            'pending' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            'replied' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            'closed'  => 'bg-gray-900 text-gray-600 border-gray-800',
            default   => 'bg-gray-800 text-gray-400 border-gray-700',
        };
        $statusLabel = match($ticket->status) {
            'pending' => 'Ожидает',
            'replied' => 'Отвечен',
            'closed'  => 'Закрыт',
            default   => $ticket->status,
        };
        $catStyle = match(mb_strtolower($ticket->category ?? '')) {
            'гарантия','брак'           => 'bg-red-500/10 text-red-400 border-red-500/20',
            'возврат','оплата'          => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'техподдержка','софт'       => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            default                     => 'bg-gray-800/60 text-gray-500 border-gray-700/50',
        };
    @endphp
    <div class="tkt-row grid gap-4 px-5 py-3.5 border-b border-gray-800/50 items-center last:border-b-0"
         style="grid-template-columns:4.5rem 1fr 11rem 7rem 7rem 6rem">

        {{-- ID --}}
        <span class="text-xs font-mono text-gray-600">#TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>

        {{-- Ticket --}}
        <div class="min-w-0">
            <p class="text-sm font-bold text-white truncate">{{ $ticket->name }}</p>
            <div class="flex items-center gap-1.5 mt-0.5">
                <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded border {{ $catStyle }}">
                    {{ $ticket->category }}
                </span>
                @if($ticket->reply)
                <span class="text-[9px] text-gray-600">· есть ответ</span>
                @endif
            </div>
        </div>

        {{-- User --}}
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-lg bg-gray-800 flex-shrink-0 flex items-center justify-center text-xs font-black text-gray-300 overflow-hidden">
                @if($ticket->user?->avatar)
                    <img src="{{ Storage::url($ticket->user->avatar) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(mb_substr($ticket->user->name ?? 'А', 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-300 truncate">{{ $ticket->user->name ?? 'Аноним' }}</p>
                <p class="text-[9px] text-gray-600 truncate">{{ $ticket->user->email ?? '' }}</p>
            </div>
        </div>

        {{-- Status --}}
        <div class="text-center">
            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded border {{ $statusStyle }}">
                {{ $statusLabel }}
            </span>
        </div>

        {{-- Date --}}
        <div class="text-center">
            <p class="text-xs text-gray-400">{{ $ticket->created_at->format('d.m.Y') }}</p>
            <p class="text-[9px] text-gray-600 mt-0.5">{{ $ticket->created_at->format('H:i') }}</p>
        </div>

        {{-- Action --}}
        <div class="flex justify-end">
            <button onclick="document.getElementById('reply-modal-{{ $ticket->id }}').showModal()"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border
                           {{ $ticket->reply ? 'text-orange-400 border-orange-500/20 hover:bg-orange-500/10' : 'text-gray-300 border-gray-700 hover:border-gray-600 hover:text-white' }}">
                {{ $ticket->reply ? 'Редактировать' : 'Ответить' }}
            </button>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6 custom-pagination">
    {{ $tickets->onEachSide(1)->links() }}
</div>

{{-- Reply dialogs --}}
@foreach($tickets as $ticket)
<dialog id="reply-modal-{{ $ticket->id }}"
        class="bg-[#111318] border border-gray-800 rounded-2xl p-0 w-full max-w-lg shadow-2xl focus:outline-none">
    <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4">
        <div>
            <h3 class="text-sm font-black text-white">#TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }} · {{ $ticket->name }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">от {{ $ticket->user->name ?? 'Аноним' }} · {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <button onclick="document.getElementById('reply-modal-{{ $ticket->id }}').close()" class="text-gray-500 hover:text-white text-2xl leading-none transition ml-4">×</button>
    </div>

    <div class="px-6 py-5 space-y-4">
        {{-- Content --}}
        <div>
            <p class="text-[9px] text-gray-600 uppercase tracking-widest mb-2">Обращение</p>
            <div class="bg-gray-900/60 border border-gray-800 rounded-xl p-3.5 text-xs text-gray-400 leading-relaxed font-mono max-h-32 overflow-y-auto cs whitespace-pre-line">{{ $ticket->content }}</div>
        </div>

        <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] text-gray-600 uppercase tracking-widest block mb-2">Ответ</label>
                <textarea name="reply" rows="4" required
                          class="w-full bg-gray-900 border border-gray-800 rounded-xl p-3.5 text-sm text-white focus:outline-none focus:border-orange-500 transition placeholder-gray-700 resize-none"
                          placeholder="Напишите ответ пользователю...">{{ $ticket->reply }}</textarea>
            </div>

            <div>
                <label class="text-[9px] text-gray-600 uppercase tracking-widest block mb-2">Статус</label>
                <div class="flex gap-2">
                    @foreach(['pending' => 'Ожидает', 'replied' => 'Отвечен', 'closed' => 'Закрыт'] as $val => $lbl)
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="status" value="{{ $val }}" class="sr-only peer" {{ $ticket->status === $val ? 'checked' : '' }}>
                        <div class="text-center py-2 rounded-xl text-xs font-semibold border transition
                                    peer-checked:{{ $val === 'pending' ? 'bg-blue-500/20 text-blue-400 border-blue-500/40' : ($val === 'replied' ? 'bg-orange-500/20 text-orange-400 border-orange-500/40' : 'bg-gray-700 text-gray-300 border-gray-600') }}
                                    border-gray-800 text-gray-500 hover:border-gray-700 hover:text-gray-400">
                            {{ $lbl }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 pt-2 border-t border-gray-800">
                <button type="button" onclick="document.getElementById('reply-modal-{{ $ticket->id }}').close()"
                        class="w-1/3 py-2.5 bg-gray-900 hover:bg-gray-800 text-xs font-bold rounded-xl border border-gray-800 text-gray-400 transition">
                    Отмена
                </button>
                <button type="submit"
                        class="w-2/3 py-2.5 bg-orange-500 hover:bg-orange-600 text-black text-xs font-black uppercase tracking-wider rounded-xl transition">
                    Применить
                </button>
            </div>
        </form>
    </div>
</dialog>
@endforeach

@endif

<script>
let _searchTimer;
function debounceSubmit() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}

function setSort(val) {
    document.getElementById('sort-input').value = val;
    document.querySelectorAll('.sort-btn').forEach(btn => {
        const active = btn.dataset.sort === val;
        btn.className = btn.className
            .replace('bg-orange-500 text-black', '')
            .replace('text-gray-500 hover:text-white', '')
            .trim();
        btn.classList.add(...(active ? ['bg-orange-500','text-black'] : ['text-gray-500','hover:text-white']));
    });
    document.getElementById('filter-form').submit();
}

function setStatus(val) {
    document.getElementById('status-input').value = val;
    document.getElementById('filter-form').submit();
}
</script>

</x-admin-layout>
