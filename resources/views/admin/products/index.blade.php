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

    /* Плавное появление модалок */
    .modal-backdrop {
        transition: opacity 0.25s ease-out;
    }
    .modal-content {
        transition: transform 0.25s ease-out, opacity 0.25s ease-out;
    }
    
    /* Тонкий кастомный скроллбар для форм внутри модалок */
    .modal-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .modal-scroll::-webkit-scrollbar-track {
        background: #090b0f;
        border-radius: 8px;
    }
    .modal-scroll::-webkit-scrollbar-thumb {
        background: #1f2937;
        border-radius: 8px;
    }
    .modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #f97316;
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
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
            <form method="GET" action="{{ route('admin.products.index') }}" class="relative w-full sm:w-[320px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск девайса..." class="w-full bg-[#161920] border border-gray-800 focus:border-orange-500 rounded-2xl px-5 py-3 pr-12 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:ring-0 transition-all">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-orange-500 transition-colors cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            @if(request('search'))
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 hover:bg-red-500/10 border border-gray-800 hover:border-red-500/20 text-gray-400 hover:text-red-400 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i class="fa-solid fa-xmark"></i> Сброс
            </a>
            @endif

            <button type="button" id="open-create-modal-btn" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-2xl transition-all active:scale-95 cursor-pointer shadow-lg shadow-orange-500/10">
                <i class="fa-solid fa-plus"></i> Добавить девайс
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
                    <div class="w-12 h-12 bg-gray-900 border border-gray-800 rounded-2xl flex items-center justify-center font-black text-orange-500 text-sm overflow-hidden p-2">
                        <img src="{{ asset($product->image) }}" alt="" class="w-full h-full object-contain opacity-80 group-hover:opacity-100 transition-opacity" onerror="this.style.display='none'">
                    </div>
                    <span class="text-[9px] bg-gray-900 text-gray-400 px-2 py-0.5 rounded-md border border-gray-800 font-bold uppercase tracking-widest">
                        {{ $product->category->name ?? 'Без категории' }}
                    </span>
                </div>

                <div>
                    <h3 class="text-md font-black text-white group-hover:text-orange-400 transition-colors line-clamp-1">
                        {{ $product->name }}
                    </h3>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-lg font-mono font-black text-white">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                        <span class="text-xs font-mono {{ $product->quantity > 0 ? 'text-gray-500' : 'text-red-500 font-bold' }}">
                            {{ $product->quantity }} шт.
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-900">
                <button type="button"
                    class="open-edit-modal-btn p-2.5 bg-gray-900 hover:bg-orange-500/10 text-gray-500 hover:text-orange-500 rounded-xl transition-all border border-gray-800 hover:border-orange-500/20 active:scale-95 cursor-pointer"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-quantity="{{ $product->quantity }}"
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

    <div id="create-product-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 modal-backdrop opacity-0">
        <div class="max-w-2xl w-full max-h-[90vh] bg-[#161920] border border-gray-800 rounded-3xl shadow-2xl flex flex-col relative modal-content scale-95 opacity-0 overflow-hidden">
            
            <div class="p-6 border-b border-gray-900 flex justify-between items-center bg-[#161920]">
                <h2 class="text-xl font-black uppercase tracking-tight text-white">
                    Добавление девайса
                </h2>
                <button type="button" id="close-create-modal-btn" class="text-gray-500 hover:text-white transition-colors cursor-pointer text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-6 modal-scroll bg-gray-950/20">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Название девайса</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Цена (₽)</label>
                        <input type="number" name="price" value="{{ old('price') }}" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Количество (шт.)</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Описание</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 resize-none transition-colors">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Фото девайса</label>
                        <input type="file" name="image" accept="image/*" required class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-2 text-sm text-gray-400 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-orange-500 file:text-black hover:file:bg-orange-400 cursor-pointer">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-orange-500 font-bold uppercase tracking-widest block">Категория девайса</label>
                        <select id="create_category_type" name="category_type" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 cursor-pointer transition-colors">
                            <option value="" disabled selected>Выберите категорию...</option>
                            <option value="mouse">Мыши</option>
                            <option value="keyboard">Клавиатуры</option>
                            <option value="headphone">Наушники</option>
                            <option value="pad">Ковры</option>
                        </select>
                    </div>
                </div>

                <div id="create-spec-fields" class="pt-2">
                    <div id="create-spec-mouse" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-950 border border-gray-900 p-5 rounded-2xl">
                        <h3 class="col-span-full text-xs font-black text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-2"><i class="fa-solid fa-computer-mouse text-orange-500"></i> Спецификация мыши</h3>
                        <input type="text" name="sensor" placeholder="Сенсор" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="number" name="max_dpi" placeholder="Макс. DPI" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="number" name="polling_rate" placeholder="Частота опроса (Гц)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="switches" placeholder="Переключатели" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="number" name="battery_life" placeholder="Батарея (часов)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="number" name="weight" placeholder="Вес (г)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                    </div>

                    <div id="create-spec-keyboard" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-950 border border-gray-900 p-5 rounded-2xl">
                        <h3 class="col-span-full text-xs font-black text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-2"><i class="fa-solid fa-keyboard text-orange-500"></i> Спецификация клавиатуры</h3>
                        <input type="text" name="switch_type" placeholder="Свитчи" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="form_factor" placeholder="Форм-фактор" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="hotswap" placeholder="Hot-swap" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="connection" placeholder="Интерфейс" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="illumination" placeholder="Подсветка" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="construction" placeholder="Строение" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                    </div>

                    <div id="create-spec-headphone" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-950 border border-gray-900 p-5 rounded-2xl">
                        <h3 class="col-span-full text-xs font-black text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-2"><i class="fa-solid fa-headphones text-orange-500"></i> Спецификация наушников</h3>
                        <input type="text" name="sound_type" placeholder="Звук" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="drivers" placeholder="Динамики" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="frequency" placeholder="Частотный диапазон" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="impedance" placeholder="Сопротивление" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="microphone" placeholder="Микрофон" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                    </div>

                    <div id="create-spec-pad" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-950 border border-gray-900 p-5 rounded-2xl">
                        <h3 class="col-span-full text-xs font-black text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-2"><i class="fa-solid fa-scroll text-orange-500"></i> Спецификация коврика</h3>
                        <input type="text" name="surface" placeholder="Тип поверхности" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="material" placeholder="Материал покрытия" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="base_material" placeholder="Основание" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="dimensions" placeholder="Размеры (ШхВ)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="thickness" placeholder="Толщина (мм)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                        <input type="text" name="edges" placeholder="Обработка краев" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors" disabled>
                    </div>
                </div>
            </form>

            <div class="p-4 border-t border-gray-900 bg-[#161920] px-6">
                <button type="button" id="submit-create-form" class="w-full py-3.5 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-xl transition-all cursor-pointer">
                    Сохранить девайс
                </button>
            </div>
        </div>
    </div>

    <div id="edit-product-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 modal-backdrop opacity-0">
        <div class="max-w-2xl w-full max-h-[90vh] bg-[#161920] border border-gray-800 rounded-3xl shadow-2xl flex flex-col relative modal-content scale-95 opacity-0 overflow-hidden">
            
            <div class="p-6 border-b border-gray-900 flex justify-between items-center bg-[#161920]">
                <h2 class="text-xl font-black uppercase tracking-tight text-white line-clamp-1">
                    Редактирование: <span id="edit-modal-title-name" class="text-orange-500">Девайс</span>
                </h2>
                <button type="button" id="close-edit-modal-btn" class="text-gray-500 hover:text-white transition-colors cursor-pointer text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="edit-product-form" action="" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-6 modal-scroll bg-gray-950/20">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Название девайса</label>
                    <input type="text" id="edit-name" name="name" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Цена (₽)</label>
                        <input type="number" id="edit-price" name="price" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Количество (шт.)</label>
                        <input type="number" id="edit-quantity" name="quantity" required class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Описание</label>
                    <textarea id="edit-description" name="description" rows="3" class="w-full bg-gray-950 border border-gray-900 focus:border-orange-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-0 resize-none transition-colors"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] text-gray-500 font-bold uppercase tracking-widest block">Изменить фото девайса (необязательно)</label>
                    <input type="file" name="image" accept="image/*" class="w-full bg-gray-950 border border-gray-900 rounded-xl px-4 py-2 text-sm text-gray-400 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-orange-500 file:text-black hover:file:bg-orange-400 cursor-pointer">
                </div>

                <div id="edit-spec-container" class="bg-gray-950 border border-gray-900 p-5 rounded-2xl space-y-4 hidden">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider border-b border-gray-900 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-orange-500"></i> Спецификация: <span id="edit-spec-category-title" class="text-orange-400">Спецификация</span>
                    </h3>

                    <div id="edit-fields-mouse" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="sensor" placeholder="Сенсор" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="number" name="max_dpi" placeholder="Макс. DPI" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="number" name="polling_rate" placeholder="Частота опроса (Гц)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="switches" placeholder="Переключатели" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="number" name="battery_life" placeholder="Батарея (часов)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="number" name="weight" placeholder="Вес (г)" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>

                    <div id="edit-fields-keyboard" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="switch_type" placeholder="Свитчи" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="form_factor" placeholder="Форм-фактор" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="hotswap" placeholder="Hot-swap" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="connection" placeholder="Интерфейс" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="illumination" placeholder="Подсветка" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="construction" placeholder="Строение" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>

                    <div id="edit-fields-headphone" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="sound_type" placeholder="Звук" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="drivers" placeholder="Динамики" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="frequency" placeholder="Частоты" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="impedance" placeholder="Сопротивление" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="connection" placeholder="Подключение" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="microphone" placeholder="Микрофон" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>

                    <div id="edit-fields-pad" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="surface" placeholder="Тип поверхности" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="material" placeholder="Материал покрытия" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="base_material" placeholder="Основание" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="dimensions" placeholder="Размеры" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="thickness" placeholder="Толщина" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                        <input type="text" name="edges" placeholder="Края" class="bg-gray-900/60 border border-gray-800 focus:border-orange-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-0 transition-colors">
                    </div>
                </div>
            </form>

            <div class="p-4 border-t border-gray-900 bg-[#161920] px-6">
                <button type="button" id="submit-edit-form" class="w-full py-3.5 bg-orange-500 hover:bg-orange-400 text-black font-black uppercase text-xs tracking-widest rounded-xl transition-all cursor-pointer">
                    Обновить девайс
                </button>
            </div>
        </div>
    </div>

    <script>
        // Функция управления анимацией окон при закрытии/открытии
        function toggleModalVisibility(modalElement, show = true) {
            if (show) {
                modalElement.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(() => {
                    modalElement.classList.remove('opacity-0');
                    modalElement.querySelector('.modal-content').classList.remove('scale-95', 'opacity-0');
                }, 10);
            } else {
                modalElement.classList.add('opacity-0');
                modalElement.querySelector('.modal-content').classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modalElement.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }, 250);
            }
        }

        // --- СОЗДАНИЕ ТОВАРА ---
        const createModal = document.getElementById('create-product-modal');
        const openCreateBtn = document.getElementById('open-create-modal-btn');
        const closeCreateBtn = document.getElementById('close-create-modal-btn');
        const createSelect = document.getElementById('create_category_type');
        const submitCreateBtn = document.getElementById('submit-create-form');
        const createForm = createModal.querySelector('form');

        openCreateBtn.addEventListener('click', () => toggleModalVisibility(createModal, true));
        closeCreateBtn.addEventListener('click', () => toggleModalVisibility(createModal, false));
        submitCreateBtn.addEventListener('click', () => createForm.reportValidity() && createForm.submit());

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

        // --- РЕДАКТИРОВАНИЕ ТОВАРА ---
        const editModal = document.getElementById('edit-product-modal');
        const editForm = document.getElementById('edit-product-form');
        const closeEditBtn = document.getElementById('close-edit-modal-btn');
        const submitEditBtn = document.getElementById('submit-edit-form');

        closeEditBtn.addEventListener('click', () => toggleModalVisibility(editModal, false));
        submitEditBtn.addEventListener('click', () => editForm.reportValidity() && editForm.submit());

        document.querySelectorAll('.open-edit-modal-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const price = this.getAttribute('data-price');
                const quantity = this.getAttribute('data-quantity');
                const description = this.getAttribute('data-description');
                const categoryType = this.getAttribute('data-category-type');
                const specs = JSON.parse(this.getAttribute('data-specs') || '{}');

                editForm.action = `/admin/products/${id}`;

                document.getElementById('edit-modal-title-name').textContent = name;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-price').value = price;
                document.getElementById('edit-quantity').value = quantity || 0;
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

                toggleModalVisibility(editModal, true);
            });
        });

        // Закрытие кликом по серой области бэкдропа для обоих окон
        [createModal, editModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    toggleModalVisibility(modal, false);
                }
            });
        });
    </script>
</x-admin-layout>