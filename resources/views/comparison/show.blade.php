<x-shop-layout>
    <x-slot name="title">Сравнение девайсов | RuGear</x-slot>

    <div class="mb-8 flex items-center justify-between">
        @if(count($products) > 0)
            <a href="{{ route('products.show', $products->first()) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-orange-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Обратно к товару
            </a>
        @else
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-orange-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Назад в каталог
            </a>
        @endif
        <h1 class="text-2xl font-bold text-white">Сравнение ({{ count($products) }}/2)</h1>
    </div>

    @if(count($products) === 0)
        <div class="bg-[#161920] border border-gray-800 rounded-2xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-700 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p class="text-gray-500 mb-6">Сравнение пусто. Добавьте девайсы со странице товара.</p>
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 bg-orange-500 hover:bg-orange-400 text-black font-bold uppercase text-xs tracking-widest rounded-xl transition-all">
                Перейти в каталог
            </a>
        </div>
    @else
        <div class="bg-[#161920] border border-gray-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-800 bg-[#0f1117]">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Характеристика</th>
                            @foreach($products as $product)
                                <th class="px-6 py-4 text-left text-xs font-bold text-orange-500 uppercase tracking-wider min-w-[250px]">{{ $product->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                            <td class="px-6 py-4 text-sm text-gray-500">Цена</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 text-sm font-bold text-orange-500 font-mono">{{ number_format($product->price, 0, '.', ' ') }} ₽</td>
                            @endforeach
                        </tr>

                        <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                            <td class="px-6 py-4 text-sm text-gray-500">Категория</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 text-sm text-gray-300">{{ $product->category->name ?? '—' }}</td>
                            @endforeach
                        </tr>

                        <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                            <td class="px-6 py-4 text-sm text-gray-500">Наличие</td>
                            @foreach($products as $product)
                                <td class="px-6 py-4 text-sm">
                                    @if($product->quantity > 0)
                                        <span class="text-green-500 font-bold">В наличии ({{ $product->quantity }})</span>
                                    @else
                                        <span class="text-red-500 font-bold">Закончился</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        @if($products->first()->specification)
                            @switch(get_class($products->first()->specification))
                                @case('App\Models\Spec\MouseSpecification')
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Сенсор</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->sensor ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Максимальный DPI</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->max_dpi ? number_format($product->specification->max_dpi, 0, '.', ' ') : '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Частота опроса</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->polling_rate ? $product->specification->polling_rate . ' Гц' : '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Переключатели</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->switches ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Подключение</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->connection ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Вес</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->weight ? $product->specification->weight . ' г' : '—' }}</td>
                                        @endforeach
                                    </tr>
                                    @if($products->first()->specification?->battery_life)
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Время работы</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->battery_life ? $product->specification->battery_life . ' ч' : '—' }}</td>
                                        @endforeach
                                    </tr>
                                    @endif
                                    @break

                                @case('App\Models\Spec\KeyboardSpecification')
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Тип переключателей</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->switch_type ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Форм-фактор</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->form_factor ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Материал кейкапов</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->keycap_material ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Hot-Swap</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->hotswap ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Подсветка</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->illumination ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Строение корпуса</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->construction ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    @break

                                @case('App\Models\Spec\HeadphoneSpecification')
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Тип звука</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->sound_type ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Диаметр динамиков</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->drivers ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Частотный диапазон</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->frequency ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Сопротивление</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->impedance ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Микрофон</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->microphone ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    @if($products->first()->specification?->battery_life)
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Автономность</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->battery_life ? $product->specification->battery_life . ' ч' : '—' }}</td>
                                        @endforeach
                                    </tr>
                                    @endif
                                    @break

                                @case('App\Models\Spec\PadSpecification')
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Тип поверхности</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->surface ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Материал покрытия</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->material ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Материал основания</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->base_material ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Размеры</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->dimensions ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Толщина</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300 font-mono">{{ $product->specification?->thickness ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="border-b border-gray-800/40 hover:bg-gray-900/30">
                                        <td class="px-6 py-4 text-sm text-gray-500">Обработка краев</td>
                                        @foreach($products as $product)
                                            <td class="px-6 py-4 text-sm text-gray-300">{{ $product->specification?->edges ?? '—' }}</td>
                                        @endforeach
                                    </tr>
                                    @break
                            @endswitch
                        @endif

                        <tr class="bg-gray-900/30 hover:bg-gray-900/50">
                            <td class="px-6 py-4"></td>
                            @foreach($products as $product)
                                <td class="px-6 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="block py-2 px-4 bg-orange-500 hover:bg-orange-400 text-black text-xs font-bold uppercase text-center rounded-lg transition-all">
                                        Подробнее
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-shop-layout>
