<x-admin-layout>
    <x-slot name="title">Заказ #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} | RuGear Admin</x-slot>

    <div class="space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black uppercase tracking-tight">
                    Заказ <span class="text-orange-500">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                </h1>
                <p class="text-gray-500 mt-2 font-medium italic">
                    Клиент: {{ $order->user->name }} ({{ $order->user->email }})
                </p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-white text-sm uppercase font-bold tracking-wider transition">
                ← Назад к заказам
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6">
                    <h2 class="text-lg font-black uppercase tracking-wider mb-6">Товары в заказе</h2>
                    
                    <div class="space-y-4">
                        @forelse($order->items as $item)
                            <div class="flex items-center gap-4 p-4 bg-[#161920] border border-gray-800 rounded-2xl">
                                <div class="w-16 h-16 bg-gray-950 border border-gray-800 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0">
                                    <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-contain p-1">
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-white line-clamp-1">{{ $item->product->name }}</h3>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Категория: {{ $item->product->category->name ?? 'N/A' }}</p>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Кол-во: {{ $item->quantity }} шт</div>
                                    <div class="text-sm font-black text-white">{{ number_format($item->price * $item->quantity, 0, '.', ' ') }} ₽</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">Нет товаров в заказе</p>
                        @endforelse
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6">
                    <h2 class="text-lg font-black uppercase tracking-wider mb-4">Примечания</h2>
                    <div class="text-gray-400 text-sm leading-relaxed">
                        {{ $order->notes ?? 'Нет примечаний' }}
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6 space-y-4">
                    <h2 class="text-lg font-black uppercase tracking-wider">Информация о заказе</h2>
                    
                    <div class="space-y-3 border-b border-gray-800 pb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">ID заказа:</span>
                            <span class="text-white font-mono font-bold">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Дата создания:</span>
                            <span class="text-white font-bold">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Последнее обновление:</span>
                            <span class="text-white font-bold">{{ $order->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="text-gray-500 text-xs uppercase tracking-wider">Клиент</span>
                        <div class="text-white font-bold">{{ $order->user->name }}</div>
                        <div class="text-gray-400 text-sm">{{ $order->user->email }}</div>
                    </div>
                </div>

                <!-- Status Management -->
                <div class="bg-[#111318] border border-gray-900 rounded-3xl p-6 space-y-4">
                    <h2 class="text-lg font-black uppercase tracking-wider">Статус заказа</h2>
                    
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div class="space-y-2">
                            <select name="status" class="w-full bg-[#161920] border border-gray-800 rounded-xl px-4 py-3 text-white font-bold focus:border-orange-500 focus:outline-none transition">
                                <option value="pending" @selected($order->status === 'pending')>В ожидании</option>
                                <option value="processing" @selected($order->status === 'processing')>В обработке</option>
                                <option value="completed" @selected($order->status === 'completed')>Завершен</option>
                                <option value="cancelled" @selected($order->status === 'cancelled')>Отменен</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-black font-black py-3 rounded-xl transition uppercase tracking-wider">
                            Обновить статус
                        </button>
                    </form>

                    <div class="pt-4 border-t border-gray-800">
                        <div class="inline-block px-4 py-2 rounded-full
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
                            <span class="text-xs font-bold uppercase">{{ $order->status_label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Total -->
                <div class="bg-gradient-to-br from-orange-500/20 to-orange-500/5 border border-orange-500/40 rounded-3xl p-6 space-y-2">
                    <span class="text-gray-400 text-xs uppercase tracking-wider">Итоговая сумма</span>
                    <div class="text-4xl font-black text-white">
                        {{ number_format($order->total_price, 0, '.', ' ') }}
                        <span class="text-orange-500">₽</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
