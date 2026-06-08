<x-admin-layout>
<x-slot:title>RuGear Admin | Заявки</x-slot:title>

@include('admin.partials.hud')

<style>
    dialog[open] { position: fixed; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; margin: 0 !important; }
    dialog::backdrop { background: rgba(0,0,0,.8); backdrop-filter: blur(2px); }
    dialog { animation: dlgIn .18s ease-out; }
    @keyframes dlgIn { from { opacity: 0; transform: translate(-50%, -48%) } to { opacity: 1; transform: translate(-50%, -50%) } }

    .tkt-row { position: relative; transition: background .12s ease, box-shadow .12s ease; }
    .tkt-row:hover { background: var(--bg-2); box-shadow: inset -3px 0 0 var(--accent); }
    .tkt-row + .tkt-row { border-top: 1px solid var(--line); }
    .tkt-rail { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }

    .cs::-webkit-scrollbar { width: 4px; }
    .cs::-webkit-scrollbar-track { background: transparent; }
    .cs::-webkit-scrollbar-thumb { background: var(--line-2); }

    /* status radio segmented */
    .st-opt { transition: border-color .12s ease, color .12s ease, background .12s ease; }
    .st-radio:checked + .st-opt { border-color: var(--st); color: var(--st); background: color-mix(in srgb, var(--st) 14%, transparent); }

    .custom-pagination p { color: var(--dim) !important; font-size: .8rem !important; }
    .custom-pagination nav a, .custom-pagination nav span[aria-disabled="true"] span, .custom-pagination nav span[aria-current="page"] span {
        background-color: var(--bg) !important; border: 1px solid var(--line) !important; color: var(--dim) !important; border-radius: 0; margin: 0 2px;
    }
    .custom-pagination nav span[aria-current="page"] span { background-color: var(--accent) !important; border-color: var(--accent) !important; color: #0a0a0a !important; font-weight: 900 !important; }
    .custom-pagination nav a:hover { border-color: var(--accent) !important; color: var(--accent) !important; }
</style>

@php
    $tmeta = [
        'pending' => ['label' => 'Ожидает', 'c' => '#3b82f6'],
        'replied' => ['label' => 'Отвечен', 'c' => '#f97316'],
        'closed'  => ['label' => 'Закрыт',  'c' => '#6b7280'],
    ];
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
                <p class="hud-mono text-[10px] tracking-[0.3em] t-dim2">RUGEAR // ЗАЯВКИ</p>
                <h1 class="text-3xl font-black uppercase tracking-tight t-text mt-2">Заявки</h1>
                <p class="text-sm t-dim mt-1">Обращения в поддержку · всего {{ $tickets->total() }}</p>
            </div>
            <div class="grid grid-cols-3 lg:flex border-t lg:border-t-0 lg:border-l" style="border-color: var(--line)">
                @foreach($tmeta as $key => $m)
                <div class="flex flex-col justify-center px-5 py-4 lg:py-0 border-r last:border-r-0" style="border-color: var(--line); min-width: 6.5rem">
                    <div class="flex items-center gap-2"><span class="hud-state" style="background: {{ $m['c'] }}"></span><span class="hud-colhead">{{ $m['label'] }}</span></div>
                    <span class="text-2xl font-black hud-tnum t-text mt-1.5">{{ $counts[$key] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="hud-panel flex items-center gap-3 px-5 py-3.5" style="border-color: #22c55e">
        <span class="hud-state" style="background: #22c55e"></span><span class="text-sm t-text">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ══ Control bar ══ --}}
    <form method="GET" action="{{ route('admin.tickets.index') }}" id="filter-form" class="space-y-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 t-dim2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" oninput="debounceSubmit()"
                       placeholder="ПОИСК: ТЕМА / СОДЕРЖИМОЕ / ПОЛЬЗОВАТЕЛЬ"
                       class="hud-input hud-mono w-full pl-10 pr-9 py-2.5 text-xs tracking-wider">
                @if($search)
                <a href="{{ route('admin.tickets.index', ['sort' => $sort, 'status' => $status]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 t-dim2 hover:t-text transition text-lg leading-none">×</a>
                @endif
            </div>
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
            <input type="hidden" name="status" id="status-input" value="{{ $status }}">
            <div class="hud-seg flex-shrink-0">
                @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'status' => 'По статусу'] as $key => $label)
                <button type="button" onclick="setSort('{{ $key }}')" class="hud-seg__btn {{ $sort === $key ? 'hud-seg__btn--on' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            @php $tabMeta = ['all' => ['label' => 'Все', 'c' => 'var(--accent)']] + array_map(fn ($m) => ['label' => $m['label'], 'c' => $m['c']], $tmeta); @endphp
            @foreach($tabMeta as $key => $tab)
            @php $on = $status === $key; @endphp
            <button type="button" onclick="setStatus('{{ $key }}')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-[11px] font-bold uppercase tracking-wider transition"
                    style="border: 1px solid {{ $on ? $tab['c'] : 'var(--line)' }}; color: {{ $on ? $tab['c'] : 'var(--dim)' }}; background: {{ $on ? 'color-mix(in srgb, '.$tab['c'].' 12%, transparent)' : 'transparent' }}">
                @if($key !== 'all')<span class="hud-state" style="background: {{ $tab['c'] }}"></span>@endif
                {{ $tab['label'] }} <span class="hud-mono t-dim2">{{ $counts[$key] }}</span>
            </button>
            @endforeach
        </div>
    </form>

    {{-- ══ List ══ --}}
    @if($tickets->isEmpty())
        <div class="hud-panel py-20 flex flex-col items-center justify-center">
            <svg class="w-10 h-10 t-dim2 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
            <p class="text-xs uppercase tracking-widest t-dim">Заявки не найдены</p>
        </div>
    @else
        <div class="hud-panel">
            <div class="hud-head">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Список заявок</span>
                <span class="hud-head__code hud-mono">{{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} / {{ $tickets->total() }}</span>
            </div>

            <div class="hidden md:grid gap-4 px-5 py-2.5 border-b items-center hud-colhead" style="border-color: var(--line); grid-template-columns: 5rem 1fr 11rem 8rem 5.5rem 6rem">
                <span>ID</span><span>Заявка</span><span>Пользователь</span><span>Статус</span><span class="text-center">Дата</span><span class="text-right">—</span>
            </div>

            @foreach($tickets as $ticket)
            @php $m = $tmeta[$ticket->status] ?? ['label' => $ticket->status, 'c' => '#6b7280']; @endphp
            <div class="tkt-row grid gap-4 px-5 py-3.5 items-center" style="grid-template-columns: 5rem 1fr 11rem 8rem 5.5rem 6rem">
                <span class="tkt-rail" style="background: {{ $m['c'] }}"></span>

                <span class="hud-mono text-xs t-dim2">#TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>

                {{-- ticket --}}
                <div class="min-w-0">
                    <p class="text-sm font-bold t-text truncate">{{ $ticket->name }}</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5" style="border: 1px solid var(--line-2); color: var(--dim)">{{ $ticket->category }}</span>
                        @if($ticket->reply)<span class="text-[9px] t-dim2 hud-mono">· есть ответ</span>@endif
                    </div>
                </div>

                {{-- user --}}
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 flex-shrink-0 flex items-center justify-center text-xs font-black overflow-hidden" style="background: var(--track); color: var(--text)">
                        @if($ticket->user?->avatar)<img src="{{ Storage::url($ticket->user->avatar) }}" class="w-full h-full object-cover">@else{{ strtoupper(mb_substr($ticket->user->name ?? 'А', 0, 1)) }}@endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold truncate" style="{{ $ticket->user?->cosmetic_nickname_color ? 'color:'.$ticket->user->cosmetic_nickname_color.';' : 'color:var(--text);' }}{{ $ticket->user?->cosmetic_font ? 'font-family:'.$ticket->user->cosmetic_font.';' : '' }}">{{ $ticket->user->name ?? 'Аноним' }}</p>
                        <p class="text-[10px] t-dim2 truncate hud-mono">{{ $ticket->user->email ?? '' }}</p>
                    </div>
                </div>

                {{-- status --}}
                <div class="flex items-center gap-2">
                    <span class="hud-state" style="background: {{ $m['c'] }}"></span>
                    <span class="text-[11px] font-bold uppercase tracking-wider" style="color: {{ $m['c'] }}">{{ $m['label'] }}</span>
                </div>

                {{-- date --}}
                <div class="text-center">
                    <p class="text-xs t-dim hud-mono">{{ $ticket->created_at->format('d.m.y') }}</p>
                    <p class="text-[10px] t-dim2 hud-mono">{{ $ticket->created_at->format('H:i') }}</p>
                </div>

                {{-- action --}}
                <div class="flex justify-end">
                    <button onclick="document.getElementById('reply-modal-{{ $ticket->id }}').showModal()" class="hud-btn {{ $ticket->reply ? '' : 'hud-btn--solid' }}">
                        {{ $ticket->reply ? 'Изменить' : 'Ответить' }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="custom-pagination">{{ $tickets->onEachSide(1)->links() }}</div>

        {{-- Reply dialogs --}}
        @foreach($tickets as $ticket)
        <dialog id="reply-modal-{{ $ticket->id }}" class="hud bg-transparent p-0 w-full max-w-lg focus:outline-none">
            <div class="hud-panel">
                <div class="hud-head">
                    <span class="hud-head__bar"></span>
                    <div class="min-w-0">
                        <p class="hud-head__title truncate">#TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }} · {{ $ticket->name }}</p>
                        <p class="text-[10px] t-dim2 mt-0.5 hud-mono">от {{ $ticket->user->name ?? 'Аноним' }} · {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <button onclick="document.getElementById('reply-modal-{{ $ticket->id }}').close()" class="ml-auto t-dim2 hover:t-text text-2xl leading-none transition">×</button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div>
                        <p class="hud-colhead mb-2">Обращение</p>
                        <div class="hud-inset p-3.5 text-xs t-dim leading-relaxed hud-mono max-h-32 overflow-y-auto cs whitespace-pre-line">{{ $ticket->content }}</div>
                    </div>

                    <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="hud-colhead block mb-2">Ответ</label>
                            <textarea name="reply" rows="4" required class="hud-input w-full p-3.5 text-sm resize-none" placeholder="Напишите ответ пользователю...">{{ $ticket->reply }}</textarea>
                        </div>

                        <div>
                            <label class="hud-colhead block mb-2">Статус</label>
                            <div class="flex gap-2">
                                @foreach($tmeta as $val => $opt)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="status" value="{{ $val }}" class="sr-only peer st-radio" {{ $ticket->status === $val ? 'checked' : '' }}>
                                    <div class="st-opt text-center py-2 text-xs font-bold uppercase tracking-wider" style="--st: {{ $opt['c'] }}; border: 1px solid var(--line); color: var(--dim)">{{ $opt['label'] }}</div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2 border-t" style="border-color: var(--line)">
                            <button type="button" onclick="document.getElementById('reply-modal-{{ $ticket->id }}').close()" class="hud-btn w-1/3 py-2.5">Отмена</button>
                            <button type="submit" class="hud-btn hud-btn--solid w-2/3 py-2.5">Применить</button>
                        </div>
                    </form>
                </div>
            </div>
        </dialog>
        @endforeach
    @endif
</div>

<script>
let _searchTimer;
function debounceSubmit() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}
function setSort(val) {
    document.getElementById('sort-input').value = val;
    document.getElementById('filter-form').submit();
}
function setStatus(val) {
    document.getElementById('status-input').value = val;
    document.getElementById('filter-form').submit();
}
</script>

</x-admin-layout>
