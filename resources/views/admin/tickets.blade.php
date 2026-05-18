<style>
    /* Кастомный минималистичный скроллбар для текста внутри карточки */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #1f2937;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #f97316;
    }

    /* Стилизация пагинации Laravel */
    .custom-pagination p {
        color: #6b7280 !important;
        font-style: italic !important;
        font-size: 0.875rem !important;
    }
    .custom-pagination nav a,
    .custom-pagination nav span[aria-disabled="true"] span,
    .custom-pagination nav span[aria-current="page"] span {
        background-color: #161920 !important;
        border-color: #1f2937 !important;
        color: #9ca3af !important;
        border-radius: 0.75rem;
        margin: 0 2px;
        padding: 8px 16px;
    }
    .custom-pagination nav span[aria-current="page"] span {
        background-color: #f97316 !important;
        border-color: #f97316 !important;
        color: #000000 !important;
        font-weight: 900 !important;
    }
    .custom-pagination nav a:hover {
        background-color: #1f2937 !important;
        color: #f97316 !important;
        border-color: #f97316 !important;
    }
</style>

<x-admin-layout>
    <x-slot:title>RuGear Admin | Заявки</x-slot:title>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-2">
        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wider text-white">
                Входящие обращения
            </h1>
            <p class="text-xs text-gray-500 leading-tight">Менеджмент внутренних заявок, тикетов и рекламаций от клиентов платформы.</p>
        </div>

        <div class="bg-[#161920] border border-gray-800 px-6 py-3 rounded-2xl flex items-center gap-4">
            <div class="text-right">
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Всего в работе</span>
                <span class="text-xl font-black text-white">{{ $tickets->total() }} шт.</span>
            </div>
            <div class="w-10 h-10 bg-orange-500/10 border border-orange-500/20 text-orange-500 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($tickets->isEmpty())
        <div class="bg-[#161920] border border-gray-800 rounded-[2rem] p-16 text-center space-y-4">
            <div class="w-16 h-16 bg-gray-900 text-gray-600 rounded-2xl flex items-center justify-center text-2xl mx-auto border border-gray-800">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-md font-bold text-white uppercase tracking-wider">Заявок не обнаружено</h3>
                <p class="text-xs text-gray-500 max-w-xs mx-auto">На данный момент нерешенных дел нет. Все клиенты довольны!</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($tickets as $ticket)
                <div class="bg-[#161920] border border-gray-800 rounded-3xl p-6 flex flex-col justify-between hover:border-gray-700 transition-all group relative overflow-hidden shadow-lg">
                    
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-orange-500/30 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2 border-b border-gray-900 pb-3">
                            <span class="text-xs font-mono text-orange-500/70 font-bold">
                                #TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            
                            @php
                                $categoryStyles = match(mb_strtolower($ticket->category)) {
                                    'гарантия', 'брак' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    'возврат', 'оплата' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    'техподдержка', 'софт' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    default => 'bg-gray-800 text-gray-400 border-gray-700'
                                };
                            @endphp
                            <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border {{ $categoryStyles }}">
                                {{ $ticket->category }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-sm font-black text-white group-hover:text-orange-400 transition-colors line-clamp-1">
                                {{ $ticket->name }}
                            </h3>
                            <div class="h-24 overflow-y-auto custom-scrollbar text-xs text-gray-400 leading-relaxed font-mono whitespace-pre-line bg-gray-950/40 border border-gray-900/60 rounded-xl p-3">
                                {{ $ticket->content }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-gray-950/20 border border-gray-900/40 rounded-xl p-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-gray-900 border border-gray-800 rounded-lg flex items-center justify-center text-[10px] font-bold text-gray-400">
                                    {{ mb_strtoupper(mb_substr($ticket->user->name ?? 'А', 0, 2)) }}
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-gray-300 block leading-tight truncate max-w-[120px]">
                                        {{ $ticket->user->name ?? 'Аноним' }}
                                    </span>
                                    <span class="text-[9px] text-gray-600 block italic leading-none mt-0.5">Отправитель</span>
                                </div>
                            </div>
                            <span class="text-[9px] text-gray-500 font-medium bg-gray-900 px-2 py-1 rounded-md border border-gray-800">
                                {{ $ticket->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-900">
                        <button class="flex-grow flex items-center justify-center gap-2 py-2.5 bg-gray-950 hover:bg-orange-500 hover:text-black text-gray-400 text-xs font-bold rounded-xl border border-gray-900 hover:border-transparent transition-all active:scale-[0.98]" title="Взять в работу и ответить">
                            <i class="fa-solid fa-reply text-[10px]"></i>
                            <span>Ответить</span>
                        </button>

                        <form action="{{ route('admin.ticket.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите закрыть и перенести в архив данный тикет?')" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2.5 bg-gray-950 hover:bg-red-500/10 text-gray-500 hover:text-red-400 rounded-xl transition-all border border-gray-900 hover:border-red-500/20 active:scale-[0.98]" title="Закрыть заявку">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8 bg-[#161920] border border-gray-800 rounded-3xl p-4 custom-pagination">
            {{ $tickets->onEachSide(1)->links() }}
        </div>
    @endif
</x-admin-layout>