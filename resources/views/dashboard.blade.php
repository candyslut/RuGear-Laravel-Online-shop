<x-shop-layout>
    <x-slot name="title">Личный кабинет | RuGear</x-slot>

    <style>
        /* Центрирование и фикс позиции для нативного dialog */
        dialog[open] {
            position: fixed;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            margin: 0 !important;
        }

        /* Кастомный скроллбар для длинных ответов техподдержки */
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

        /* Полная кастомизация пагинации Laravel под темную тему RuGear */
        .custom-pagination nav {
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            display: flex !important;
            justify-content: flex-end !important;
            /* Сдвигаем строго вправо */
        }

        /* Скрываем текстовое сопровождение "Showing 1 to 2..." и мобильные дубли кнопок */
        .custom-pagination nav>div:first-child,
        .custom-pagination nav flex[span],
        .custom-pagination nav p {
            display: none !important;
        }

        /* Оставляем только контейнер с цифрами и стрелочками */
        .custom-pagination nav>div:last-child {
            display: flex !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .custom-pagination nav>div:last-child span[span] {
            display: none !important;
            /* Убираем лишние обертки */
        }

        /* Стилизация КНОПОК-ССЫЛОК (активные стрелки и другие страницы) */
        .custom-pagination a,
        .custom-pagination span[aria-current="page"] span {
            background-color: #161920 !important;
            color: #9ca3af !important;
            border: 1px solid #1f2937 !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            font-family: monospace !important;
            padding: 8px 14px !important;
            margin: 0 3px !important;
            border-radius: 12px !important;
            /* Скругление как у карточек */
            transition: all 0.2s ease !important;
        }

        /* Стилизация НЕАКТИВНЫХ СТРЕЛОК (Назад на 1-й странице, Вперед на последней) */
        .custom-pagination span[aria-disabled="true"] span {
            background-color: #161920 !important;
            color: #ffffff !important;
            /* Белый цвет для заблокированных стрелок */
            border: 1px solid #1f2937 !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            font-family: monospace !important;
            padding: 8px 14px !important;
            margin: 0 3px !important;
            border-radius: 12px !important;
            cursor: not-allowed !important;
            /* Меняем курсор, чтобы показать, что нажать нельзя */
            opacity: 0.9 !important;
        }

        /* Активная страница (яркая оранжевая кнопка) */
        .custom-pagination span[aria-current="page"] span {
            background-color: #f97316 !important;
            color: #000000 !important;
            border-color: #f97316 !important;
            font-weight: 900 !important;
        }

        /* Эффект наведения только на кликабельные страницы и стрелки */
        .custom-pagination a:hover {
            background-color: #1f2937 !important;
            color: #ffffff !important;
            border-color: #374151 !important;
        }

        @keyframes toast-in {
            from {
                opacity: 0;
                transform: translateX(420px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toast-out {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(420px);
            }
        }

        .achievement-toast {
            animation: toast-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .achievement-toast.hide {
            animation: toast-out 0.4s ease-in forwards;
        }

        .achievements-slider {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-right: 1rem;
        }

        .achievements-slider::-webkit-scrollbar {
            height: 6px;
        }

        .achievements-slider::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 10px;
        }

        .achievements-slider::-webkit-scrollbar-thumb {
            background: #f97316;
            border-radius: 10px;
        }

        .achievement-card {
            flex: 0 0 calc(50% - 0.5rem);
            min-width: 280px;
        }

        @media (min-width: 1024px) {
            .achievement-card {
                flex: 0 0 calc(33.333% - 0.667rem);
            }
        }

        .achievements-slider-nav {
            display: none;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .achievements-slider-nav {
                display: flex;
            }
        }

        /* Новые стили для сетки достижений */
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .achievements-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }

        @media (max-width: 640px) {
            .achievements-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="space-y-8">
        @if(session('achievement_awarded'))
            <div id="achievement-toast" class="fixed bottom-6 right-6 z-50 w-full max-w-sm rounded-3xl border border-orange-500/20 bg-[#111318] p-5 shadow-2xl shadow-orange-500/10 text-white ring-1 ring-orange-500/20 achievement-toast pointer-events-auto">
                <div class="flex items-start gap-4">
                    <div class="rounded-2xl bg-orange-500/10 text-orange-300 p-3 flex-shrink-0">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <div class="space-y-2 flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.24em] text-orange-400 font-bold">🎉 Достижение разблокировано!</p>
                                <h3 class="text-sm font-black text-white line-clamp-1">{{ session('achievement_awarded.title') }}</h3>
                            </div>
                            <button type="button" onclick="closeAchievementToast()" class="text-gray-400 hover:text-white transition-colors flex-shrink-0 text-xl leading-none">
                                ×
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 line-clamp-2">{{ session('achievement_awarded.description') }}</p>
                        <div class="text-xs text-orange-300 bg-orange-500/10 px-3 py-2 rounded-2xl border border-orange-500/20 font-semibold">
                            ⭐ +{{ session('achievement_awarded.experience') }} опыта
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function closeAchievementToast() {
                    const toast = document.getElementById('achievement-toast');
                    if (toast) {
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }

                const achievementToast = document.getElementById('achievement-toast');
                if (achievementToast) {
                    setTimeout(() => closeAchievementToast(), 5000);
                }
            </script>
        @endif

        @if(session('success'))
            <div id="success-toast" class="fixed bottom-6 right-6 z-50 w-full max-w-sm rounded-3xl border border-green-500/20 bg-[#111318] p-5 shadow-2xl shadow-green-500/10 text-white ring-1 ring-green-500/20 achievement-toast pointer-events-auto">
                <div class="flex items-start gap-4">
                    <div class="rounded-2xl bg-green-500/10 text-green-300 p-3 flex-shrink-0">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                        </svg>
                    </div>
                    <div class="space-y-2 flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.24em] text-green-400 font-bold">✓ Успешно!</p>
                                <h3 class="text-sm font-black text-white line-clamp-1">Заказ создан</h3>
                            </div>
                            <button type="button" onclick="closeSuccessToast()" class="text-gray-400 hover:text-white transition-colors flex-shrink-0 text-xl leading-none">
                                ×
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 line-clamp-2">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            <script>
                function closeSuccessToast() {
                    const toast = document.getElementById('success-toast');
                    if (toast) {
                        toast.classList.add('hide');
                        setTimeout(() => toast.remove(), 400);
                    }
                }

                const successToast = document.getElementById('success-toast');
                if (successToast) {
                    setTimeout(() => closeSuccessToast(), 4000);
                }
            </script>
        @endif

        <div>
            <h1 class="text-4xl font-black uppercase tracking-tight">
                Личный <span class="text-orange-500">кабинет</span>
            </h1>
            <p class="text-gray-500 mt-2 font-medium italic">
                Вы авторизованы как: {{ auth()->user()->name }}
            </p>
        </div>

        <livewire:profile-cart />

        <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6 space-y-6 mt-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between border-b border-gray-900 pb-4">
                <div>
                    <h4 class="text-sm font-black uppercase tracking-wider text-white">Прогресс и достижения</h4>
                    <p class="text-[11px] text-gray-500">Уровень и опыт начисляются за выполненные достижения.</p>
                </div>
                <div class="space-y-2 text-right">
                    <div class="text-xs uppercase tracking-[0.24em] text-gray-400 font-bold">Уровень {{ auth()->user()->level }}</div>
                    <div class="text-lg font-black text-white">{{ auth()->user()->experience }} XP</div>
                    <div class="text-[11px] text-gray-500">До следующего уровня: {{ auth()->user()->next_level_experience - auth()->user()->experienceProgress }} XP</div>
                </div>
            </div>

            <div class="rounded-full bg-gray-900 h-3 overflow-hidden">
                <div class="h-full bg-orange-500 transition-all duration-500" style="width: {{ min(100, auth()->user()->experience > 0 ? (int) round(auth()->user()->experience / auth()->user()->next_level_experience * 100) : 0) }}%"></div>
            </div>

            @if(auth()->user()->achievements->count() > 0)
                <div class="achievements-grid">
                    @foreach(auth()->user()->achievements->sortByDesc('pivot.awarded_at') as $achievement)
                        <div class="achievement-card bg-[#161920] border border-gray-800 rounded-3xl p-4 space-y-3 hover:border-orange-500/30 hover:shadow-lg hover:shadow-orange-500/10 transition-all">
                            <div class="flex items-start gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-orange-500/20 to-orange-500/10 text-orange-300 flex-shrink-0">
                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-sm font-black text-white line-clamp-2">{{ $achievement->title }}</h5>
                                    <p class="text-xs text-orange-400 font-semibold">+{{ $achievement->experience }} XP</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed line-clamp-3">{{ $achievement->description }}</p>
                            <div class="pt-2 border-t border-gray-800/40">
                                <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500">
                                    {{ $achievement->pivot->awarded_at ? \Carbon\Carbon::parse($achievement->pivot->awarded_at)->diffForHumans() : 'недавно' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-gray-800 bg-[#161920] p-8 text-center text-gray-500 space-y-2">
                    <p class="text-sm font-semibold">🏆 Пока нет достижений</p>
                    <p class="text-xs">Первое достижение вы получите после регистрации. Затем можете получать их за комментарии!</p>
                </div>
            @endif

            <button onclick="document.getElementById('achievements-modal').showModal()" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-black text-sm font-bold uppercase tracking-wider rounded-xl transition-all">
                🎯 Все достижения
            </button>
        </div>

        <!-- Achievements Modal -->
        <dialog id="achievements-modal" class="fixed inset-0 w-full max-w-3xl max-h-[90vh] rounded-3xl bg-[#111318] border border-gray-800 p-0 shadow-2xl">
            <div class="flex flex-col h-full">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-800 p-6">
                    <h2 class="text-2xl font-black text-white">🏆 Все достижения</h2>
                    <button onclick="document.getElementById('achievements-modal').close()" class="text-gray-400 hover:text-white text-2xl leading-none transition-colors">
                        ×
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar space-y-3">
                    @forelse($allAchievements->sortBy('experience') as $achievement)
                        @php
                            $isUnlocked = in_array($achievement->id, $userAchievementIds);
                            $userAchievement = auth()->user()->achievements()->where('achievement_id', $achievement->id)->first();
                        @endphp
                        <div class="achievement-modal-card bg-[#161920] border {{ $isUnlocked ? 'border-orange-500/40' : 'border-gray-800' }} rounded-2xl p-4 flex items-start gap-4 hover:border-orange-500/60 transition-all {{ !$isUnlocked ? 'opacity-60' : '' }}">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl {{ $isUnlocked ? 'bg-gradient-to-br from-orange-500/20 to-orange-500/10 text-orange-300' : 'bg-gray-800 text-gray-600' }} flex-shrink-0">
                                @if($isUnlocked)
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                @else
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2 justify-between">
                                    <div>
                                        <h4 class="text-sm font-black text-white">{{ $achievement->title }}</h4>
                                        <p class="text-xs {{ $isUnlocked ? 'text-orange-400' : 'text-gray-500' }} font-semibold">+{{ $achievement->experience }} XP</p>
                                    </div>
                                    @if($isUnlocked)
                                        <div class="flex-shrink-0 px-3 py-1 bg-orange-500/20 border border-orange-500/40 rounded-full">
                                            <p class="text-[10px] uppercase tracking-[0.2em] text-orange-300 font-bold">✓ Получено</p>
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 px-3 py-1 bg-gray-800/40 border border-gray-700 rounded-full">
                                            <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold">Заблокировано</p>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed mt-2">{{ $achievement->description }}</p>
                                @if($isUnlocked && $userAchievement)
                                    <p class="text-[10px] uppercase tracking-[0.2em] text-gray-500 mt-2">
                                        Получено {{ \Carbon\Carbon::parse($userAchievement->pivot->awarded_at)->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">Достижения еще не добавлены</p>
                        </div>
                    @endforelse
                </div>

                <!-- Modal Footer -->
                <div class="border-t border-gray-800 p-6">
                    <button onclick="document.getElementById('achievements-modal').close()" class="w-full py-3 bg-gray-800 hover:bg-gray-700 text-white text-sm font-bold uppercase tracking-wider rounded-xl transition-all">
                        Закрыть
                    </button>
                </div>
            </div>

            <style>
                dialog::backdrop {
                    background-color: rgba(0, 0, 0, 0.7);
                }

                dialog {
                    animation: dialogSlideIn 0.3s ease-out;
                }

                @keyframes dialogSlideIn {
                    from {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
            </style>
        </dialog>
        </div>

        <!-- Orders Section -->
        <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6 space-y-6 mt-8">
            <div class="flex justify-between items-center border-b border-gray-900 pb-4">
                <div>
                    <h4 class="text-sm font-black uppercase tracking-wider text-white">Мои заказы</h4>
                    <p class="text-[11px] text-gray-500">История ваших заказов и их статусы.</p>
                </div>
            </div>

            @if($userOrders->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-xs text-gray-600 font-mono mb-4">У вас пока нет заказов</p>
                    <a href="/" class="text-orange-500 hover:text-orange-400 text-sm font-bold transition">Начните покупать →</a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($userOrders as $order)
                        <div class="bg-[#161920] border border-gray-800/60 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-mono text-gray-600 bg-gray-800 px-2 py-1 rounded">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <div>
                                        <p class="text-xs font-bold text-white">{{ $order->items->count() }} товар{{ $order->items->count() !== 1 ? 'ов' : '' }}</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                                    @switch($order->status)
                                        @case('pending')
                                            bg-gray-700 text-gray-200
                                            @break
                                        @case('processing')
                                            bg-blue-500/20 text-blue-300 border border-blue-500/40
                                            @break
                                        @case('completed')
                                            bg-green-500/20 text-green-300 border border-green-500/40
                                            @break
                                        @case('cancelled')
                                            bg-red-500/20 text-red-300 border border-red-500/40
                                            @break
                                    @endswitch
                                ">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            <div class="pt-2 border-t border-gray-800/40 flex items-center justify-between">
                                <div class="text-sm font-black text-white">
                                    {{ number_format($order->total_price, 0, '.', ' ') }} ₽
                                </div>
                                <button onclick="toggleOrderDetails({{ $order->id }})" class="text-xs font-bold text-orange-500 hover:text-orange-400 transition">
                                    Детали ↓
                                </button>
                            </div>

                            <!-- Order Details (Hidden) -->
                            <div id="order-details-{{ $order->id }}" class="hidden pt-3 border-t border-gray-800/40 space-y-2">
                                @foreach($order->items as $item)
                                    <div class="text-xs flex items-center justify-between">
                                        <span class="text-gray-400">{{ $item->product->name }}</span>
                                        <span class="text-white font-bold">{{ $item->quantity }}x {{ number_format($item->price, 0, '.', ' ') }} ₽</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($userOrders->hasPages())
                    <div class="pt-4 border-t border-gray-800/40">
                        {{ $userOrders->links() }}
                    </div>
                @endif
            @endif
        </div>

        <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6 space-y-6 mt-8">
            <div class="flex justify-between items-center border-b border-gray-900 pb-4">
                <div>
                    <h4 class="text-sm font-black uppercase tracking-wider text-white">Техническая поддержка</h4>
                    <p class="text-[11px] text-gray-500">Статусы ваших недавних запросов и ответы инженеров RuGear.</p>
                </div>
                <a href="{{ route('support') }}" class="text-[11px] font-bold text-orange-500 hover:underline">
                    Создать новый тикет +
                </a>
            </div>

            @if($userTickets->isEmpty())
            <div class="py-4 text-center">
                <p class="text-xs text-gray-600 font-mono">Вы еще не создавали обращений в поддержку.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($userTickets as $ticket)
                <div class="bg-[#161920] border border-gray-800/60 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono text-gray-600">#TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <h5 class="text-xs font-bold text-white">{{ $ticket->name }}</h5>
                        </div>

                        @php
                        $userStatusStyles = match($ticket->status) {
                        'pending' => 'bg-blue-500/10 text-blue-400 border-blue-500/10',
                        'replied' => 'bg-orange-500/10 text-orange-400 border-orange-500/20 animate-pulse',
                        'closed' => 'bg-gray-950 text-gray-500 border-gray-900',
                        default => 'bg-gray-900 text-gray-400'
                        };
                        $userStatusNames = match($ticket->status) {
                        'pending' => 'На рассмотрении',
                        'replied' => 'Получен ответ',
                        'closed' => 'Решено / Закрыто',
                        default => $ticket->status
                        };
                        @endphp
                        <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded border {{ $userStatusStyles }}">
                            {{ $userStatusNames }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-400 font-mono bg-gray-950/30 p-2 rounded-lg border border-gray-900/40 line-clamp-2">
                        {{ $ticket->content }}
                    </p>

                    <div class="flex justify-between items-center pt-2 border-t border-gray-900/40">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] text-gray-600 italic">
                                {{ $ticket->created_at->diffForHumans() }}
                            </span>

                            <form action="{{ route('ticket.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите безвозвратно удалить данный тикет из базы данных?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-red-400 p-1 rounded-md hover:bg-red-500/10 border border-transparent hover:border-red-500/10 transition-all active:scale-95" title="Удалить тикет навсегда">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <button type="button" onclick="document.getElementById('ticket-modal-{{ $ticket->id }}').showModal()" class="text-[11px] font-bold text-orange-500 hover:text-orange-400 transition-colors">
                            Подробнее &rarr;
                        </button>
                    </div>
                </div>

                <dialog id="ticket-modal-{{ $ticket->id }}" class="user-ticket-dialog backdrop:bg-black/80 bg-[#111318] border border-gray-900 p-6 rounded-3xl w-full max-w-lg text-gray-200 shadow-2xl focus:outline-none">
                    <div class="space-y-5">
                        <div class="flex justify-between items-center border-b border-gray-900 pb-4">
                            <div>
                                <span class="text-[9px] font-mono text-gray-600 block mb-0.5">ID ТИКЕТА: #TC-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="text-sm font-black uppercase tracking-wider text-white">Просмотр обращения</h3>
                            </div>
                            <button type="button" onclick="document.getElementById('ticket-modal-{{ $ticket->id }}').close()" class="text-gray-500 hover:text-white transition-colors p-1 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Имя:</span>
                            <p class="text-xs text-white font-bold pl-0.5">{{ $ticket->name }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Категория:</span>
                            <span class="inline-block text-[10px] text-gray-400 bg-gray-950 px-3 py-1.5 border border-gray-900 rounded-md font-mono uppercase">{{ $ticket->category ?? 'Общие вопросы' }}</span>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider block">Ваше сообщение:</span>
                            <div class="text-xs bg-gray-950 p-3.5 rounded-xl border border-gray-900 font-mono text-gray-400 min-h-[50px] max-h-32 overflow-y-auto custom-scrollbar whitespace-pre-line leading-relaxed flex items-center">
                                <div class="w-full">{{ $ticket->content }}</div>
                            </div>
                        </div>

                        <div class="space-y-2 pt-1 border-t border-gray-900">
                            <span class="text-[10px] text-orange-500 font-bold uppercase tracking-wider block">Ответ поддержки:</span>
                            @if($ticket->reply)
                            <div class="bg-orange-500/5 border border-orange-500/20 rounded-xl p-3.5 font-mono text-xs text-gray-300 whitespace-pre-line leading-relaxed min-h-[50px] max-h-40 overflow-y-auto custom-scrollbar flex items-center">
                                <div class="w-full">{{ $ticket->reply }}</div>
                            </div>
                            @else
                            <div class="bg-gray-950 p-3.5 rounded-xl border border-gray-900 min-h-[50px] flex items-center justify-center text-center">
                                <p class="text-xs text-gray-600 font-mono italic leading-relaxed">Инженеры уже изучают вашу проблему. Пожалуйста, ожидайте обновления статуса.</p>
                            </div>
                            @endif
                        </div>

                        <div class="pt-2">
                            <button type="button" onclick="document.getElementById('ticket-modal-{{ $ticket->id }}').close()" class="w-full h-11 flex items-center justify-center bg-gray-900 hover:bg-gray-850 text-xs font-bold rounded-xl border border-gray-850 text-gray-400 transition-colors">
                                Закрыть окно
                            </button>
                        </div>
                    </div>
                </dialog>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-900/60 custom-pagination">
                {{ $userTickets->onEachSide(1)->links() }}
            </div>
            @endif
        </div>

        <button type="button" onclick="toggleModal('logout-modal', true)" class="text-xs text-red-600 hover:text-red-500 uppercase font-bold">
            Выход
        </button>

        <div id="logout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div class="bg-[#161920] border border-gray-800 w-full max-w-md rounded-3xl p-8 shadow-2xl">
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-4 text-center">
                    Уже <span class="text-orange-500">уходишь?</span>
                </h3>
                <p class="text-gray-400 text-center mb-8 italic">Ты уверен, что хочешь покинуть систему RuGear?</p>

                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="toggleModal('logout-modal', false)" class="py-4 bg-gray-800 hover:bg-gray-700 text-white font-bold uppercase tracking-widest rounded-xl transition-all">
                        Остаться
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-black font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-orange-500/20">
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .achievement-toast {
            pointer-events: auto;
        }
    </style>
</x-shop-layout>

<script>
    function toggleModal(modalID, show) {
        const modal = document.getElementById(modalID);
        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }

    window.onclick = function(event) {
        const logoutModal = document.getElementById('logout-modal');
        if (event.target == logoutModal) {
            toggleModal('logout-modal', false);
        }
    }

    document.querySelectorAll('.user-ticket-dialog').forEach(dialog => {
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

    function toggleOrderDetails(orderId) {
        const detailsElement = document.getElementById(`order-details-${orderId}`);
        if (detailsElement) {
            detailsElement.classList.toggle('hidden');
        }
    }
</script>