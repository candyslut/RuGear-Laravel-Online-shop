<x-admin-layout>
    <x-slot name="title">Управление заказами | RuGear Admin</x-slot>

    <div class="space-y-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tight">
                Управление <span class="text-orange-500">заказами</span>
            </h1>
            <p class="text-gray-500 mt-2 font-medium italic">
                Всего заказов: {{ $orders->total() }}
            </p>
        </div>

        <div class="bg-[#111318] border border-gray-900 rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#161920] border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-white">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-white">Клиент</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-white">Сумма</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-white">Статус</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-white">Дата</th>
                            <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-wider text-white">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($orders as $order)
                            <tr class="hover:bg-[#161920] transition">
                                <td class="px-6 py-4 text-sm font-mono text-gray-400">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-white">{{ $order->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-black text-white">
                                    {{ number_format($order->total_price, 0, '.', ' ') }} ₽
                                </td>
                                <td class="px-6 py-4">
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
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400">
                                    {{ $order->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-500 hover:text-orange-400 font-bold text-sm transition">
                                        Просмотр →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <p class="text-gray-500 text-sm">Заказы не найдены</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-800 bg-[#161920]">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
