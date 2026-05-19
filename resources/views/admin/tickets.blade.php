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

    /* Идеальное центрирование и фикс позиции для нативного dialog */
    dialog[open] {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
    }
</style>

<x-admin-layout>
    <x-slot:title>RuGear Admin | Заявки</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Вернуться в личный кабинет</span>
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-2">
        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wider text-white">
                Входящие обращения
            </h1>
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
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl my-4">
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
                        <div class="flex items-center justify-between gap-2 border-b border-gray-900 pb-3 flex-wrap">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono text-orange-500/70 font-bold">
                                    #TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                
                                @php
                                    $statusStyles = match($ticket->status) {
                                        'pending' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'replied' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                        'closed' => 'bg-gray-900 text-gray-600 border-gray-800',
                                        default => 'bg-gray-850 text-gray-400 border-gray-700'
                                    };
                                    $statusNames = match($ticket->status) {
                                        'pending' => 'Ожидает',
                                        'replied' => 'Отвечен',
                                        'closed' => 'Закрыт',
                                        default => $ticket->status
                                    };
                                @endphp
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border {{ $statusStyles }}">
                                    {{ $statusNames }}
                                </span>
                            </div>
                            
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
                        <button onclick="document.getElementById('reply-modal-{{ $ticket->id }}').showModal()" 
                                class="flex-grow flex items-center justify-center gap-2 py-2.5 bg-gray-950 hover:bg-orange-500 hover:text-black text-gray-400 text-xs font-bold rounded-xl border border-gray-900 hover:border-transparent transition-all active:scale-[0.98]" 
                                title="Изменить ответ или статус">
                            <i class="fa-solid fa-reply text-[10px]"></i>
                            <span>{{ $ticket->reply ? 'Редактировать ответ' : 'Ответить' }}</span>
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-8 bg-[#161920] border border-gray-800 rounded-3xl p-4 custom-pagination">
            {{ $tickets->onEachSide(1)->links() }}
        </div>

        @foreach($tickets as $ticket)
            <dialog id="reply-modal-{{ $ticket->id }}" 
                    class="ticket-dialog backdrop:bg-black/80 bg-[#111318] border border-gray-900 p-6 rounded-3xl w-full max-w-lg text-gray-200 shadow-2xl focus:outline-none">
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-900 pb-3">
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">Ответ на тикет #TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</h3>
                        <button type="button" onclick="document.getElementById('reply-modal-{{ $ticket->id }}').close()" class="text-gray-500 hover:text-white transition-colors text-sm">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="space-y-1">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Описание проблемы:</span>
                        <div class="text-xs bg-gray-950 p-3 rounded-xl border border-gray-900 font-mono text-gray-400 max-h-32 overflow-y-auto custom-scrollbar">
                            {{ $ticket->content }}
                        </div>
                    </div>

                    <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Ответ:</label>
                            <textarea name="reply" rows="4" required class="w-full bg-gray-950 border border-gray-900 rounded-xl p-3 text-xs font-mono text-white focus:outline-none focus:border-orange-500/50 transition-colors placeholder:text-gray-700" placeholder="Опишите решение технической неисправности...">{{ $ticket->reply }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Текущий статус дела:</label>
                            <select name="status" class="w-full bg-gray-950 border border-gray-900 rounded-xl p-3 text-xs font-mono text-gray-300 focus:outline-none focus:border-orange-500/50 cursor-pointer">
                                <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending (В обработке)</option>
                                <option value="replied" {{ $ticket->status === 'replied' ? 'selected' : '' }}>Replied (Дан ответ)</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed (Вопрос закрыт)</option>
                            </select>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-gray-900">
                            <button type="button" onclick="document.getElementById('reply-modal-{{ $ticket->id }}').close()" class="w-1/3 py-2.5 bg-gray-900 hover:bg-gray-850 text-xs font-bold rounded-xl border border-gray-850 text-gray-400 transition-colors">
                                Отмена
                            </button>
                            <button type="submit" class="w-2/3 py-2.5 bg-orange-500 hover:bg-orange-600 text-black text-xs font-black uppercase tracking-wider rounded-xl transition-colors shadow-[0_0_15px_rgba(249,115,22,0.2)]">
                                Применить
                            </button>
                        </div>
                    </form>
                </div>
            </dialog>
        @endforeach
    @endif

    <script>
        document.querySelectorAll('.ticket-dialog').forEach(dialog => {
            dialog.addEventListener('click', (e) => {
                const dialogDimensions = dialog.getBoundingClientRect();
                if (
                    e.clientX < dialogDimensions.left ||
                    e.clientX > dialogDimensions.right ||
                    e.clientY < dialogDimensions.top ||
                    e.clientY > dialogDimensions.bottom
                ) {
                    dialog.close();
                }
            });
        });
    </script>
</x-admin-layout>