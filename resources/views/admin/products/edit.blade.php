<x-admin-layout>
    <x-slot name="title">Редактирование: {{ $product->name }} | RuGear Admin</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Назад к списку
        </a>
    </div>

    <div class="max-w-3xl mx-auto p-8 bg-[#161920] border border-gray-800 rounded-3xl shadow-2xl">
        <h1 class="text-2xl font-black uppercase tracking-tight text-white mb-6 border-b border-gray-900 pb-4">
            Редактирование девайса
        </h1>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Название девайса</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Цена (₽)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Описание</label>
                <textarea name="description" rows="3" class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 resize-none">{{ old('description', $product->description) }}</textarea>
            </div>

            @if($product->specification)
                <div class="bg-gray-950/40 p-6 rounded-2xl border border-gray-900 space-y-4">
                    <h3 class="text-xs font-mono text-gray-500 uppercase tracking-wider border-b border-gray-900 pb-2">
                        Изменение технических характеристик ({{ $product->category->name ?? 'Спецификация' }})
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @switch(get_class($product->specification))
                            @case('App\Models\Spec\MouseSpecification')
                                <input type="text" name="sensor" value="{{ $product->specification->sensor }}" placeholder="Сенсор" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="number" name="max_dpi" value="{{ $product->specification->max_dpi }}" placeholder="Макс. DPI" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="number" name="polling_rate" value="{{ $product->specification->polling_rate }}" placeholder="Частота опроса" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="switches" value="{{ $product->specification->switches }}" placeholder="Переключатели" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="connection" value="{{ $product->specification->connection }}" placeholder="Подключение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="number" name="battery_life" value="{{ $product->specification->battery_life }}" placeholder="Батарея (часов)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="number" name="weight" value="{{ $product->specification->weight }}" placeholder="Вес (г)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                @break

                            @case('App\Models\Spec\KeyboardSpecification')
                                <input type="text" name="switch_type" value="{{ $product->specification->switch_type }}" placeholder="Свитчи" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="form_factor" value="{{ $product->specification->form_factor }}" placeholder="Форм-фактор" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="keycap_material" value="{{ $product->specification->keycap_material }}" placeholder="Материал кейкапов" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="hotswap" value="{{ $product->specification->hotswap }}" placeholder="Hot-swap" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="connection" value="{{ $product->specification->connection }}" placeholder="Интерфейс" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="illumination" value="{{ $product->specification->illumination }}" placeholder="Подсветка" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                <input type="text" name="construction" value="{{ $product->specification->construction }}" placeholder="Строение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                                @break

                            @endswitch
                    </div>
                </div>
            @endif

            <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-xl transition-all cursor-pointer">
                Обновить данные девайса
            </button>
        </form>
    </div>
</x-admin-layout>