<x-admin-layout>
    <x-slot:title>RuGear Admin | Товары</x-slot:title>

    @include('admin.partials.hud')

    <style>
        .prod-card { transition: border-color .12s ease, background .12s ease; }
        .prod-card:hover { border-color: var(--accent); background: var(--bg-2); }

        .modal-backdrop { transition: opacity .25s ease-out; }
        .modal-content { transition: transform .25s ease-out, opacity .25s ease-out; }
        .modal-scroll::-webkit-scrollbar { width: 6px; }
        .modal-scroll::-webkit-scrollbar-track { background: var(--inset); }
        .modal-scroll::-webkit-scrollbar-thumb { background: var(--line-2); }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: var(--accent); }

        .custom-pagination p { color: var(--dim) !important; font-size: .8rem !important; }
        .custom-pagination nav a, .custom-pagination nav span[aria-disabled="true"] span, .custom-pagination nav span[aria-current="page"] span {
            background-color: var(--bg) !important; border: 1px solid var(--line) !important; color: var(--dim) !important; border-radius: 0; margin: 0 2px;
        }
        .custom-pagination nav span[aria-current="page"] span { background-color: var(--accent) !important; border-color: var(--accent) !important; color: #0a0a0a !important; font-weight: 900 !important; }
        .custom-pagination nav a:hover { border-color: var(--accent) !important; color: var(--accent) !important; }
    </style>

<div class="hud space-y-5">

    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold t-dim hover:t-acc transition-colors group">
        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        НАЗАД В ПРОФИЛЬ
    </a>

    {{-- ══ Command header ══ --}}
    <div class="hud-panel hud-corner hud-grid-bg flex flex-col sm:flex-row sm:items-stretch">
        <div class="flex-1 p-6">
            <p class="hud-mono text-[10px] tracking-[0.3em] t-dim2">RUGEAR // ТОВАРЫ</p>
            <h1 class="text-3xl font-black uppercase tracking-tight t-text mt-2">Товары</h1>
            <p class="text-sm t-dim mt-1">Каталог · всего {{ $products->total() }}</p>
        </div>
        <div class="flex items-center p-6 border-t sm:border-t-0 sm:border-l" style="border-color: var(--line)">
            <button type="button" id="open-create-modal-btn" class="hud-btn hud-btn--solid px-5 py-3 gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                Добавить товар
            </button>
        </div>
    </div>

    {{-- ══ Control bar ══ --}}
    <form method="GET" action="{{ route('admin.products.index') }}" id="filter-form" class="space-y-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 t-dim2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search ?? '' }}" oninput="debounceSubmit()"
                       placeholder="ПОИСК ПО НАЗВАНИЮ"
                       class="hud-input hud-mono w-full pl-10 pr-9 py-2.5 text-xs tracking-wider">
                @if($search)
                <a href="{{ route('admin.products.index', ['sort' => $sort, 'category' => $category]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 t-dim2 hover:t-text transition text-lg leading-none">×</a>
                @endif
            </div>
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
            <input type="hidden" name="category" id="category-input" value="{{ $category }}">
            <div class="hud-seg flex-shrink-0">
                @foreach(['newest' => 'Новые', 'oldest' => 'Старые', 'price_asc' => 'Дешевле', 'price_desc' => 'Дороже', 'stock' => 'Остаток'] as $key => $label)
                <button type="button" onclick="setSort('{{ $key }}')" class="hud-seg__btn {{ $sort === $key ? 'hud-seg__btn--on' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            @php $catOn = $category === 'all'; @endphp
            <button type="button" onclick="setCategory('all')"
                    class="px-3.5 py-2 text-[11px] font-bold uppercase tracking-wider transition"
                    style="border: 1px solid {{ $catOn ? 'var(--accent)' : 'var(--line)' }}; color: {{ $catOn ? 'var(--accent)' : 'var(--dim)' }}; background: {{ $catOn ? 'color-mix(in srgb, var(--accent) 12%, transparent)' : 'transparent' }}">
                Все <span class="hud-mono t-dim2">{{ $counts['all'] }}</span>
            </button>
            @foreach($categories as $cat)
            @php $on = $category === $cat->name; @endphp
            <button type="button" onclick="setCategory('{{ $cat->name }}')"
                    class="px-3.5 py-2 text-[11px] font-bold uppercase tracking-wider transition"
                    style="border: 1px solid {{ $on ? 'var(--accent)' : 'var(--line)' }}; color: {{ $on ? 'var(--accent)' : 'var(--dim)' }}; background: {{ $on ? 'color-mix(in srgb, var(--accent) 12%, transparent)' : 'transparent' }}">
                {{ $cat->name }} <span class="hud-mono t-dim2">{{ $counts[$cat->name] ?? 0 }}</span>
            </button>
            @endforeach
        </div>
    </form>

    @if(session('success'))
    <div class="hud-panel flex items-center gap-3 px-5 py-3.5" style="border-color: #22c55e">
        <span class="hud-state" style="background: #22c55e"></span><span class="text-sm t-text">{{ session('success') }}</span>
    </div>
    @endif

    {{-- ══ Catalog grid ══ --}}
    @if($products->isEmpty())
    <div class="hud-panel py-20 flex flex-col items-center justify-center">
        <svg class="w-10 h-10 t-dim2 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <p class="text-xs uppercase tracking-widest t-dim">Товаров нет</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($products as $product)
        <div class="prod-card hud-panel p-5 flex flex-col justify-between group">
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-12 h-12 hud-inset flex items-center justify-center overflow-hidden p-2 flex-shrink-0">
                        <img src="{{ asset($product->image) }}" alt="" class="w-full h-full object-contain opacity-80 group-hover:opacity-100 transition-opacity" onerror="this.style.display='none'">
                    </div>
                    <span class="text-[9px] px-2 py-0.5 font-bold uppercase tracking-widest" style="border: 1px solid var(--line-2); color: var(--dim)">
                        {{ $product->category->name ?? 'Без категории' }}
                    </span>
                </div>

                <div>
                    <h3 class="text-md font-black t-text group-hover:t-acc transition-colors line-clamp-1">{{ $product->name }}</h3>
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-lg hud-mono font-black t-text">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                        <span class="text-xs hud-mono font-bold" style="color: {{ $product->quantity > 0 ? 'var(--dim)' : '#ef4444' }}">{{ $product->quantity }} шт</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 mt-6 pt-4 border-t" style="border-color: var(--line)">
                <button type="button"
                    class="open-edit-modal-btn hud-btn flex-1"
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-quantity="{{ $product->quantity }}"
                    data-description="{{ $product->description }}"
                    data-category-type="{{ match($product->category->name ?? '') { 'Мыши'=>'mouse', 'Клавиатуры'=>'keyboard', 'Наушники'=>'headphone', 'Ковры'=>'pad', default=>'' } }}"
                    data-specs="{{ json_encode($product->specification) }}"
                    title="Редактировать">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                    Изменить
                </button>

                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Удалить товар {{ $product->name }} из каталога?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="hud-btn hud-btn--danger px-2.5" title="Удалить">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12.1a2 2 0 01-2 1.9H7.9a2 2 0 01-2-1.9L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="custom-pagination mt-10">{{ $products->onEachSide(1)->links() }}</div>
    @endif

    {{-- ════════ CREATE MODAL ════════ --}}
    <div id="create-product-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 modal-backdrop opacity-0">
        <div class="max-w-2xl w-full max-h-[90vh] flex flex-col relative modal-content scale-95 opacity-0 overflow-hidden" style="background: var(--bg); border: 1px solid var(--line)">
            <div class="hud-head flex-shrink-0">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title">Новый товар</span>
                <button type="button" id="close-create-modal-btn" class="ml-auto t-dim2 hover:t-text text-2xl leading-none transition">×</button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-5 modal-scroll">
                @csrf
                <div class="space-y-1.5">
                    <label class="hud-colhead block">Название</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="hud-input w-full px-4 py-3 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="hud-colhead block">Цена (₽)</label>
                        <input type="number" name="price" value="{{ old('price') }}" required class="hud-input w-full px-4 py-3 text-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label class="hud-colhead block">Количество (шт)</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}" required class="hud-input w-full px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="hud-colhead block">Описание</label>
                    <textarea name="description" rows="3" class="hud-input w-full px-4 py-3 text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="hud-colhead block">Изображение</label>
                        <input type="file" name="image" accept="image/*" required class="hud-input w-full px-4 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:font-bold file:bg-orange-500 file:text-black hover:file:bg-orange-400 cursor-pointer">
                    </div>
                    <div class="space-y-1.5">
                        <label class="hud-colhead block t-acc">Категория</label>
                        <select id="create_category_type" name="category_type" required class="hud-input w-full px-4 py-3 text-sm cursor-pointer">
                            <option value="" disabled selected>Выберите категорию...</option>
                            <option value="mouse">Мыши</option>
                            <option value="keyboard">Клавиатуры</option>
                            <option value="headphone">Наушники</option>
                            <option value="pad">Ковры</option>
                        </select>
                    </div>
                </div>

                <div id="create-spec-fields" class="pt-1">
                    <div id="create-spec-mouse" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 hud-inset p-5">
                        <h3 class="col-span-full hud-colhead mb-1">Спецификация · мышь</h3>
                        <input type="text" name="sensor" placeholder="Сенсор" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="number" name="max_dpi" placeholder="Макс. DPI" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="number" name="polling_rate" placeholder="Частота опроса (Гц)" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="switches" placeholder="Переключатели" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="connection" placeholder="Подключение" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="number" name="battery_life" placeholder="Батарея (часов)" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="number" name="weight" placeholder="Вес (г)" class="hud-input px-4 py-2.5 text-sm" disabled>
                    </div>

                    <div id="create-spec-keyboard" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 hud-inset p-5">
                        <h3 class="col-span-full hud-colhead mb-1">Спецификация · клавиатура</h3>
                        <input type="text" name="switch_type" placeholder="Свитчи" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="form_factor" placeholder="Форм-фактор" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="hotswap" placeholder="Hot-swap" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="connection" placeholder="Интерфейс" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="illumination" placeholder="Подсветка" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="construction" placeholder="Строение" class="hud-input px-4 py-2.5 text-sm" disabled>
                    </div>

                    <div id="create-spec-headphone" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 hud-inset p-5">
                        <h3 class="col-span-full hud-colhead mb-1">Спецификация · наушники</h3>
                        <input type="text" name="sound_type" placeholder="Звук" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="drivers" placeholder="Динамики" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="frequency" placeholder="Частотный диапазон" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="impedance" placeholder="Сопротивление" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="connection" placeholder="Подключение" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="microphone" placeholder="Микрофон" class="hud-input px-4 py-2.5 text-sm" disabled>
                    </div>

                    <div id="create-spec-pad" class="create-spec-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3 hud-inset p-5">
                        <h3 class="col-span-full hud-colhead mb-1">Спецификация · коврик</h3>
                        <input type="text" name="surface" placeholder="Тип поверхности" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="material" placeholder="Материал покрытия" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="base_material" placeholder="Основание" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="dimensions" placeholder="Размеры (ШхВ)" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="thickness" placeholder="Толщина (мм)" class="hud-input px-4 py-2.5 text-sm" disabled>
                        <input type="text" name="edges" placeholder="Обработка краёв" class="hud-input px-4 py-2.5 text-sm" disabled>
                    </div>
                </div>
            </form>

            <div class="p-4 px-6 border-t flex-shrink-0" style="border-color: var(--line)">
                <button type="button" id="submit-create-form" class="hud-btn hud-btn--solid w-full py-3.5">Сохранить</button>
            </div>
        </div>
    </div>

    {{-- ════════ EDIT MODAL ════════ --}}
    <div id="edit-product-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 modal-backdrop opacity-0">
        <div class="max-w-2xl w-full max-h-[90vh] flex flex-col relative modal-content scale-95 opacity-0 overflow-hidden" style="background: var(--bg); border: 1px solid var(--line)">
            <div class="hud-head flex-shrink-0">
                <span class="hud-head__bar"></span>
                <span class="hud-head__title line-clamp-1">Изменить: <span id="edit-modal-title-name" class="t-acc">Товар</span></span>
                <button type="button" id="close-edit-modal-btn" class="ml-auto t-dim2 hover:t-text text-2xl leading-none transition">×</button>
            </div>

            <form id="edit-product-form" action="" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-5 modal-scroll">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="hud-colhead block">Название</label>
                    <input type="text" id="edit-name" name="name" required class="hud-input w-full px-4 py-3 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="hud-colhead block">Цена (₽)</label>
                        <input type="number" id="edit-price" name="price" required class="hud-input w-full px-4 py-3 text-sm">
                    </div>
                    <div class="space-y-1.5">
                        <label class="hud-colhead block">Количество (шт)</label>
                        <input type="number" id="edit-quantity" name="quantity" required class="hud-input w-full px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="hud-colhead block">Описание</label>
                    <textarea id="edit-description" name="description" rows="3" class="hud-input w-full px-4 py-3 text-sm resize-none"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="hud-colhead block">Изменить изображение (необязательно)</label>
                    <input type="file" name="image" accept="image/*" class="hud-input w-full px-4 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:font-bold file:bg-orange-500 file:text-black hover:file:bg-orange-400 cursor-pointer">
                </div>

                <div id="edit-spec-container" class="hud-inset p-5 space-y-4 hidden">
                    <h3 class="hud-colhead border-b pb-2" style="border-color: var(--line)">Спецификация: <span id="edit-spec-category-title" class="t-acc">—</span></h3>

                    <div id="edit-fields-mouse" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="sensor" placeholder="Сенсор" class="hud-input px-4 py-2.5 text-sm">
                        <input type="number" name="max_dpi" placeholder="Макс. DPI" class="hud-input px-4 py-2.5 text-sm">
                        <input type="number" name="polling_rate" placeholder="Частота опроса (Гц)" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="switches" placeholder="Переключатели" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="connection" placeholder="Подключение" class="hud-input px-4 py-2.5 text-sm">
                        <input type="number" name="battery_life" placeholder="Батарея (часов)" class="hud-input px-4 py-2.5 text-sm">
                        <input type="number" name="weight" placeholder="Вес (г)" class="hud-input px-4 py-2.5 text-sm">
                    </div>

                    <div id="edit-fields-keyboard" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="switch_type" placeholder="Свитчи" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="form_factor" placeholder="Форм-фактор" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="keycap_material" placeholder="Материал кейкапов" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="hotswap" placeholder="Hot-swap" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="connection" placeholder="Интерфейс" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="illumination" placeholder="Подсветка" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="construction" placeholder="Строение" class="hud-input px-4 py-2.5 text-sm">
                    </div>

                    <div id="edit-fields-headphone" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="sound_type" placeholder="Звук" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="drivers" placeholder="Динамики" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="frequency" placeholder="Частоты" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="impedance" placeholder="Сопротивление" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="connection" placeholder="Подключение" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="microphone" placeholder="Микрофон" class="hud-input px-4 py-2.5 text-sm">
                    </div>

                    <div id="edit-fields-pad" class="edit-spec-fields-group hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="surface" placeholder="Тип поверхности" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="material" placeholder="Материал покрытия" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="base_material" placeholder="Основание" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="dimensions" placeholder="Размеры" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="thickness" placeholder="Толщина" class="hud-input px-4 py-2.5 text-sm">
                        <input type="text" name="edges" placeholder="Края" class="hud-input px-4 py-2.5 text-sm">
                    </div>
                </div>
            </form>

            <div class="p-4 px-6 border-t flex-shrink-0" style="border-color: var(--line)">
                <button type="button" id="submit-edit-form" class="hud-btn hud-btn--solid w-full py-3.5">Обновить</button>
            </div>
        </div>
    </div>

</div>

    <script>
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

        [createModal, editModal].forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    toggleModalVisibility(modal, false);
                }
            });
        });

        // ── Search / sort / filter ──
        let _searchTimer;
        function debounceSubmit() {
            clearTimeout(_searchTimer);
            _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
        }
        function setSort(val) {
            document.getElementById('sort-input').value = val;
            document.getElementById('filter-form').submit();
        }
        function setCategory(val) {
            document.getElementById('category-input').value = val;
            document.getElementById('filter-form').submit();
        }
    </script>
</x-admin-layout>
