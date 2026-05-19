<style>
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
    <x-slot:title>RuGear Admin | Управление товарами</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Вернуться в личный кабинет</span>
        </a>
    </div>

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">

        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wider text-white">
                Управление каталогом
            </h1>

            <p class="text-xs text-gray-500 leading-tight">
                Добавление девайсов, изменение технических характеристик и цен.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">

            <form method="GET"
                action="{{ route('admin.products.index') }}"
                class="relative w-full sm:w-[320px]">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Поиск девайса..."
                    class="w-full bg-[#161920] border border-gray-800 rounded-2xl px-5 py-3 pr-12 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-orange-500 transition-all">

                <button
                    type="submit"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-orange-500 transition-colors cursor-pointer">

                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            @if(request('search'))
            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 hover:bg-red-500/10 border border-gray-800 hover:border-red-500/20 text-gray-400 hover:text-red-400 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">

                <i class="fa-solid fa-xmark"></i>
                Сброс
            </a>
            @endif

            <button
                type="button"
                id="open-create-modal-btn"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-2xl transition-all active:scale-95 cursor-pointer">

                <i class="fa-solid fa-plus"></i>
                Добавить девайс
            </button>

        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if($products->isEmpty())
    <div class="bg-[#161920] border border-gray-800 rounded-[2rem] p-16 text-center space-y-4">
        <div class="w-16 h-16 bg-gray-900 text-gray-600 rounded-2xl flex items-center justify-center text-2xl mx-auto border border-gray-800">
            <i class="fa-solid fa-box-open"></i>
        </div>
        <h3 class="text-md font-bold text-white uppercase tracking-wider">Каталог товаров пуст</h3>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-[#161920] border border-gray-800 rounded-3xl p-6 flex flex-col justify-between hover:border-gray-700/60 transition-all group relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-orange-500/40 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>

            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-12 h-12 bg-gray-900 border border-gray-800 rounded-2xl flex items-center justify-center font-black text-orange-500 text-sm overflow-hidden">
                        <img src="{{ asset($product->image) }}" alt="" class="w-8 h-8 object-contain opacity-60 group-hover:opacity-100 transition-opacity" onerror="this.style.display='none'">
                    </div>
                    <span class="text-[9px] bg-gray-900 text-gray-400 px-2 py-0.5 rounded-md border border-gray-800 font-bold uppercase tracking-widest">
                        {{ $product->category->name ?? 'Без категории' }}
                    </span>
                </div>

                <div>
                    <h3 class="text-md font-black text-white group-hover:text-orange-400 transition-colors line-clamp-1">
                        {{ $product->name }}
                    </h3>
                    <span class="text-lg font-mono font-black text-white block mt-1">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                </div>
            </div>

            <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-900">
                <button type="button"
                    class="open-edit-modal-btn p-2.5 bg-gray-900 hover:bg-orange-500/10 text-gray-500 hover:text-orange-500 rounded-xl transition-all border border-gray-800 hover:border-orange-500/20 active:scale-95 cursor-pointer"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-description="{{ $product->description }}"
                    data-category-type="{{ match($product->category->name ?? '') { 'Мыши'=>'mouse', 'Клавиатуры'=>'keyboard', 'Наушники'=>'headphone', 'Ковры'=>'pad', default=>'' } }}"
                    data-specs="{{ json_encode($product->specification) }}"
                    title="Редактировать девайс">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                </button>

                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Вы действительно хотите удалить товар {{ $product->name }} из каталога?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2.5 bg-gray-900 hover:bg-red-500/10 text-gray-500 hover:text-red-400 rounded-xl transition-all border border-gray-800 hover:border-red-500/20 active:scale-95 group/btn cursor-pointer" title="Удалить товар">
                        <i class="fa-solid fa-trash-can text-xs transition-transform group-hover/btn:scale-110"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-16 custom-pagination">
        {{ $products->onEachSide(1)->links() }}
    </div>
    @endif


    <div id="create-product-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="max-w-3xl w-full bg-[#161920] border border-gray-800 rounded-3xl shadow-2xl p-8 relative my-8">
            <button type="button" id="close-create-modal-btn" class="absolute top-6 right-6 text-gray-500 hover:text-white transition-colors cursor-pointer text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h1 class="text-2xl font-black uppercase tracking-tight text-white mb-6 border-b border-gray-900 pb-4">
                Добавление девайса
            </h1>

            <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Название девайса</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Цена (₽)</label>
                        <input type="number" name="price" value="{{ old('price') }}" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Описание</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-orange-500 font-bold uppercase tracking-widest block">Категория девайса</label>
                    <select id="create_category_type" name="category_type" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 cursor-pointer">
                        <option value="" disabled selected>Выберите категорию...</option>
                        <option value="mouse">Мыши</option>
                        <option value="keyboard">Клавиатуры</option>
                        <option value="headphone">Наушники</option>
                        <option value="pad">Ковры</option>
                    </select>
                </div>

                <div id="create-spec-fields" class="pt-2">
                    <div id="create-spec-mouse" class="create-spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                        <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-computer-mouse mr-2"></i> Характеристики мыши</h3>
                        <input type="text" name="sensor" placeholder="Сенсор" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="number" name="max_dpi" placeholder="Макс. DPI" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="number" name="polling_rate" placeholder="Частота опроса" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="switches" placeholder="Переключатели" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="number" name="battery_life" placeholder="Батарея (часов)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="number" name="weight" placeholder="Вес (г)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                    </div>

                    <div id="create-spec-keyboard" class="create-spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                        <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-keyboard mr-2"></i> Характеристики клавиатуры</h3>
                        <input type="text" name="switch_type" placeholder="Свитчи" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="form_factor" placeholder="Форм-фактор" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="hotswap" placeholder="Hot-swap" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="connection" placeholder="Интерфейс" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="illumination" placeholder="Подсветка" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="construction" placeholder="Строение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                    </div>

                    <div id="create-spec-headphone" class="create-spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                        <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-headphones mr-2"></i> Характеристики наушников</h3>
                        <input type="text" name="sound_type" placeholder="Звук" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="drivers" placeholder="Динамики" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="frequency" placeholder="Частоты" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="impedance" placeholder="Сопротивление" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="microphone" placeholder="Микрофон" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                    </div>

                    <div id="create-spec-pad" class="create-spec-group hidden grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-950/40 p-5 rounded-2xl border border-gray-900">
                        <h3 class="col-span-full text-xs font-mono text-gray-500 uppercase tracking-wider mb-2"><i class="fa-solid fa-rug mr-2"></i> Характеристики коврика</h3>
                        <input type="text" name="surface" placeholder="Тип поверхности" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="material" placeholder="Материал покрытия" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="base_material" placeholder="Основание" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="dimensions" placeholder="Размеры" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="thickness" placeholder="Толщина" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                        <input type="text" name="edges" placeholder="Края" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white" disabled>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-xl transition-all cursor-pointer">
                    Сохранить новый девайс
                </button>
            </form>
        </div>
    </div>


    <div id="edit-product-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="max-w-3xl w-full bg-[#161920] border border-gray-800 rounded-3xl shadow-2xl p-8 relative my-8">
            <button type="button" id="close-edit-modal-btn" class="absolute top-6 right-6 text-gray-500 hover:text-white transition-colors cursor-pointer text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h1 class="text-2xl font-black uppercase tracking-tight text-white mb-6 border-b border-gray-900 pb-4">
                Редактирование: <span id="edit-modal-title-name" class="text-orange-500">Девайс</span>
            </h1>

            <form id="edit-product-form" action="" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Название девайса</label>
                        <input type="text" id="edit-name" name="name" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Цена (₽)</label>
                        <input type="number" id="edit-price" name="price" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Описание</label>
                    <textarea id="edit-description" name="description" rows="3" class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500 resize-none"></textarea>
                </div>

                <div id="edit-spec-container" class="bg-gray-950/40 p-6 rounded-2xl border border-gray-900 space-y-4 hidden">
                    <h3 class="text-xs font-mono text-gray-500 uppercase tracking-wider border-b border-gray-900 pb-2">
                        Технические характеристики (<span id="edit-spec-category-title">Спецификация</span>)
                    </h3>

                    <div id="edit-fields-mouse" class="edit-spec-fields-group hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="sensor" placeholder="Сенсор" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="number" name="max_dpi" placeholder="Макс. DPI" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="number" name="polling_rate" placeholder="Частота опроса" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="switches" placeholder="Переключатели" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="number" name="battery_life" placeholder="Батарея (часов)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="number" name="weight" placeholder="Вес (г)" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    </div>

                    <div id="edit-fields-keyboard" class="edit-spec-fields-group hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="switch_type" placeholder="Свитчи" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="form_factor" placeholder="Форм-фактор" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="hotswap" placeholder="Hot-swap" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="connection" placeholder="Интерфейс" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="illumination" placeholder="Подсветка" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="construction" placeholder="Строение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    </div>

                    <div id="edit-fields-headphone" class="edit-spec-fields-group hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="sound_type" placeholder="Звук" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="drivers" placeholder="Динамики" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="frequency" placeholder="Частоты" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="impedance" placeholder="Сопротивление" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="microphone" placeholder="Микрофон" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    </div>

                    <div id="edit-fields-pad" class="edit-spec-fields-group hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="surface" placeholder="Тип поверхности" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="material" placeholder="Материал покрытия" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="base_material" placeholder="Основание" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="dimensions" placeholder="Размеры" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="thickness" placeholder="Толщина" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                        <input type="text" name="edges" placeholder="Края" class="bg-gray-950 border border-gray-900 rounded-xl px-4 py-2.5 text-sm text-white">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-xl transition-all cursor-pointer">
                    Обновить данные девайса
                </button>
            </form>
        </div>
    </div>


    <script>
        // --- Логика модального окна СОЗДАНИЯ ---
        const createModal = document.getElementById('create-product-modal');
        const openCreateBtn = document.getElementById('open-create-modal-btn');
        const closeCreateBtn = document.getElementById('close-create-modal-btn');
        const createSelect = document.getElementById('create_category_type');

        openCreateBtn.addEventListener('click', () => {
            createModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });

        closeCreateBtn.addEventListener('click', () => {
            createModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });

        createSelect.addEventListener('change', function() {
            document.querySelectorAll('.create-spec-group').forEach(group => {
                group.classList.add('hidden');
                group.querySelectorAll('input').forEach(i => i.disabled = true);
            });
            const activeGroup = document.getElementById('create-spec-' + this.value);
            if (activeGroup) {
                activeGroup.classList.remove('hidden');
                activeGroup.querySelectorAll('input').forEach(i => i.disabled = false);
            }
        });

        // --- Логика модального окна РЕДАКТИРОВАНИЯ ---
        const editModal = document.getElementById('edit-product-modal');
        const editForm = document.getElementById('edit-product-form');
        const closeEditBtn = document.getElementById('close-edit-modal-btn');

        document.querySelectorAll('.open-edit-modal-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const price = this.getAttribute('data-price');
                const description = this.getAttribute('data-description');
                const categoryType = this.getAttribute('data-category-type');
                const specs = JSON.parse(this.getAttribute('data-specs') || '{}');

                editForm.action = `/admin/products/${id}`;

                document.getElementById('edit-modal-title-name').textContent = name;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-price').value = price;
                document.getElementById('edit-description').value = description || '';

                document.getElementById('edit-spec-container').classList.add('hidden');
                document.querySelectorAll('.edit-spec-fields-group').forEach(el => {
                    el.classList.add('hidden');
                    el.querySelectorAll('input').forEach(input => input.disabled = true);
                });

                if (categoryType && Object.keys(specs).length > 0) {
                    const targetFieldsBlock = document.getElementById(`edit-fields-${categoryType}`);

                    if (targetFieldsBlock) {
                        document.getElementById('edit-spec-container').classList.remove('hidden');
                        targetFieldsBlock.classList.remove('hidden');

                        const catTitles = {
                            'mouse': 'Мышь',
                            'keyboard': 'Клавиатура',
                            'headphone': 'Наушники',
                            'pad': 'Коврик'
                        };
                        document.getElementById('edit-spec-category-title').textContent = catTitles[categoryType] || 'Спецификация';

                        targetFieldsBlock.querySelectorAll('input').forEach(input => {
                            input.disabled = false;
                            const fieldName = input.getAttribute('name');
                            if (specs[fieldName] !== undefined) {
                                input.value = specs[fieldName];
                            } else {
                                input.value = '';
                            }
                        });
                    }
                }

                editModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        closeEditBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });

        // Закрытие модалок по клику на темную вуаль фона
        window.addEventListener('click', function(e) {
            if (e.target === createModal) {
                createModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            if (e.target === editModal) {
                editModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    </script>
</x-admin-layout>