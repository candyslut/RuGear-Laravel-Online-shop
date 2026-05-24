<x-shop-layout>
    <x-slot name="title">Настройки профиля | RuGear</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-black text-white">Настройки профиля</h1>
            <p class="text-gray-500 text-sm mt-1">Управляйте своими личными данными</p>
        </div>

        <div class="bg-[#161920] border border-gray-800 rounded-2xl p-8">
            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Фото профиля --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-3">Фото профиля</label>
                    <div class="flex items-center gap-5">
                        {{-- Preview --}}
                        <div class="relative flex-shrink-0">
                            <div id="avatar-preview-wrap" class="w-20 h-20 rounded-2xl overflow-hidden bg-orange-500 flex items-center justify-center">
                                @if($user->avatar)
                                    <img id="avatar-preview" src="{{ Storage::url($user->avatar) }}" alt="Аватар" class="w-full h-full object-cover">
                                @else
                                    <span id="avatar-initials" class="text-black font-black text-2xl">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                    <img id="avatar-preview" src="" alt="" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            {{-- Overlay кнопка --}}
                            <label for="avatar-input" class="absolute inset-0 rounded-2xl bg-black/50 flex items-center justify-center opacity-0 hover:opacity-100 transition cursor-pointer">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </label>
                        </div>

                        <div class="flex-1">
                            <label for="avatar-input" class="inline-flex items-center gap-2 cursor-pointer bg-[#0f1115] border border-gray-700 hover:border-orange-500 text-gray-300 hover:text-white rounded-xl px-4 py-2.5 text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Загрузить фото
                            </label>
                            <input id="avatar-input" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <p id="avatar-filename" class="text-xs text-gray-600 mt-2">JPG, PNG или WebP · до 2 МБ</p>
                        </div>
                    </div>
                    @error('avatar')
                        <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-800"></div>

                {{-- ФИО --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-2" for="name">ФИО</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full bg-[#0f1115] border border-gray-700 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-600 focus:outline-none focus:border-orange-500 transition"
                        placeholder="Введите ваше полное имя"
                    >
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Пол --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-2" for="gender">Пол</label>
                    <select
                        id="gender"
                        name="gender"
                        class="w-full bg-[#0f1115] border border-gray-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500 transition appearance-none"
                    >
                        <option value="" @selected(!$user->gender)>Не указано</option>
                        <option value="male"   @selected($user->gender === 'male')>Мужской</option>
                        <option value="female" @selected($user->gender === 'female')>Женский</option>
                    </select>
                    @error('gender')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Номер телефона --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-2" for="phone">Номер телефона</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full bg-[#0f1115] border border-gray-700 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-600 focus:outline-none focus:border-orange-500 transition"
                        placeholder="+7 (___) ___-__-__"
                    >
                    @error('phone')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- О себе --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-400 mb-2" for="about">О себе</label>
                    <textarea
                        id="about"
                        name="about"
                        rows="4"
                        maxlength="1000"
                        class="w-full bg-[#0f1115] border border-gray-700 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-600 focus:outline-none focus:border-orange-500 transition resize-none"
                        placeholder="Расскажите немного о себе..."
                    >{{ old('about', $user->about) }}</textarea>
                    @error('about')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-300 transition">
                        ← Назад в кабинет
                    </a>
                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-black font-bold px-6 py-2.5 rounded-xl text-sm transition shadow-[0_0_20px_rgba(249,115,22,0.25)]"
                    >
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const preview  = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            const filename = document.getElementById('avatar-filename');

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (initials) initials.classList.add('hidden');
            };
            reader.readAsDataURL(file);

            const mb = (file.size / 1024 / 1024).toFixed(1);
            filename.textContent = file.name + ' · ' + mb + ' МБ';
            filename.classList.remove('text-gray-600');
            filename.classList.add('text-orange-400');
        });
    </script>
</x-shop-layout>
