<x-shop-layout>
    <x-slot name="title">Установка нового пароля | RuGear</x-slot>

    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="w-full max-w-md mx-auto">
            
            <!-- Заголовок -->
            <div class="text-center mb-8">
                <div class="inline-flex p-4 rounded-full bg-orange-500/10 mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04zM12 7V3m0 0v4m0-4H8m4 0h4m-4 9a3 3 0 100-6 3 3 0 000 6z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-tighter">Новый <span class="text-orange-500">Пароль</span></h1>
                <p class="text-gray-500 text-sm mt-2 font-medium">Придумайте надежную комбинацию</p>
            </div>

            <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl shadow-orange-500/5">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address (обычно предзаполнен из ссылки) -->
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Ваш Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                            class="w-full bg-[#0f1115]/50 border border-gray-800 rounded-2xl px-5 py-4 text-gray-500 focus:outline-none cursor-not-allowed transition-all"
                            placeholder="mail@rugear.com">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Новый пароль</label>
                        <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Повторите пароль</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password_confirmation')" />
                    </div>

                    <!-- Кнопка -->
                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black uppercase py-4 rounded-2xl transition-all shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)]">
                            Обновить пароль
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>