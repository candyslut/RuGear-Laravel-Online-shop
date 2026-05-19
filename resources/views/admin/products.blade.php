<x-admin-layout>
    <x-slot name="title">Добавление товара | RuGear Admin</x-slot>

    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Вернуться в личный кабинет</span>
        </a>
    </div>

    <div class="max-w-4xl mx-auto p-8 bg-[#161920] border border-gray-800 rounded-3xl shadow-2xl">
        <h1 class="text-2xl font-black uppercase tracking-tighter text-white mb-8 border-b border-gray-900 pb-4">
            Добавление нового девайса
        </h1>

        {{-- Системные уведомления (Успех / Ошибка) --}}
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/25 rounded-xl text-emerald-400 text-sm font-mono flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/25 rounded-xl text-rose-400 text-sm font-mono flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Основные поля продукта --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Название девайса</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 transition-colors">
                    @error('name') <span class="text-xs text-rose-500 font-mono">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Цена (в рублях)</label>
                    <input type="number" name="price" value="{{ old('price') }}" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 transition-colors">
                    @error('price') <span class="text-xs text-rose-500 font-mono">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Маркетинговое описание</label>
                <textarea name="description" rows="3" class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 transition-colors resize-none" placeholder="Краткое описание товара для карточки...">{{ old('description') }}</textarea>
                @error('description') <span class="text-xs text-rose-500 font-mono">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] text-orange-500 font-bold uppercase tracking-widest block">Категория оборудования</label>
                <select id="category_type" name="category_type" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 transition-colors cursor-pointer">
                    <option value="" disabled selected>Выберите категорию...</option>
                    <option value="mouse" {{ old('category_type') == 'mouse' ? 'selected' : '' }}>Мыши</option>
                    <option value="keyboard" {{ old('category_type') == 'keyboard' ? 'selected' : '' }}>Клавиатуры</option>
                    <option value="headphone" {{ old('category_type') == 'headphone' ? 'selected' : '' }}>Наушники</option>
                    <option value="pad" {{ old('category_type') == 'pad' ? 'selected' : '' }}>Ковры</option>
                </select>
                @error('category_type') <span class="text-xs text-rose-500 font-mono">{{ $message }}</span> @enderror
            </div>

            {{-- Динамические спецификации --}}
            <div id="spec-fields" class="pt-2">

                {{-- Спецификации мыши --}}
                <div id="spec-mouse" class="spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                    <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-computer-mouse mr-2"></i> Характеристики мыши</h3>
                    <input type="text" name="sensor" placeholder="Сенсор (н-р: PixArt PAW3395)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="number" name="max_dpi" placeholder="Макс. DPI (н-р: 26000)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="number" name="polling_rate" placeholder="Частота опроса (н-р: 1000 Hz)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="switches" placeholder="Переключатели (н-р: Huano Blue Shell)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="connection" placeholder="Подключение (н-р: Беспроводная 2.4GHz)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="number" name="battery_life" placeholder="Батарея (часов)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="number" name="weight" placeholder="Вес (в граммах)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>

                {{-- Спецификации клавиатуры --}}
                <div id="spec-keyboard" class="spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                    <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-keyboard mr-2"></i> Характеристики клавиатуры</h3>
                    <input type="text" name="switch_type" placeholder="Свитчи (н-р: Gateron Yellow Linear)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="form_factor" placeholder="Форм-фактор (н-р: TKL 80%)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="hotswap" placeholder="Hot-swap" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="connection" placeholder="Интерфейс (н-р: Кабель Type-C)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="illumination" placeholder="Подсветка (н-р: RGB)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="construction" placeholder="Строение (н-р: Gasket Mount)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>

                {{-- Спецификации наушников --}}
                <div id="spec-headphone" class="spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                    <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-headphones mr-2"></i> Характеристики наушников</h3>
                    <input type="text" name="sound_type" placeholder="Звук (н-р: Стерео 2.0)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="drivers" placeholder="Динамики (н-р: 50-мм)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="frequency" placeholder="Частоты (н-р: 20 - 20000 Гц)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="impedance" placeholder="Сопротивление" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="connection" placeholder="Подключение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="microphone" placeholder="Микрофон" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="number" name="battery_life" placeholder="Автономность (часов)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>

                {{-- Спецификации коврика --}}
                <div id="spec-pad" class="spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                    <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-rug mr-2"></i> Характеристики коврика</h3>
                    <input type="text" name="surface" placeholder="Тип поверхности (н-р: Speed)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="material" placeholder="Материал покрытия" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="base_material" placeholder="Основание" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="dimensions" placeholder="Размеры (н-р: 450 x 400 мм)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="thickness" placeholder="Толщина" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    <input type="text" name="edges" placeholder="Края (н-р: Оверлок)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-orange-500/10 active:scale-[0.99] cursor-pointer">
                Добавить девайс в каталог
            </button>
        </form>
    </div>

    <script>
        document.getElementById('category_type').addEventListener('change', function() {
            document.querySelectorAll('.spec-group').forEach(group => {
                group.classList.add('hidden');
                group.querySelectorAll('input').forEach(input => input.disabled = true);
            });

            const activeGroup = document.getElementById('spec-' + this.value);
            if (activeGroup) {
                activeGroup.classList.remove('hidden');
                activeGroup.querySelectorAll('input').forEach(input => input.disabled = false);
            }
        });

        // Триггерим событие при наличии старого ввода (ошибки валидации), чтобы блок характеристик не скрывался
        window.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('category_type');
            if (select.value) select.dispatchEvent(new Event('change'));
        });
    </script>
</x-admin-layout>