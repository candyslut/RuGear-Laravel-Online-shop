<x-shop-layout>
    <x-slot name="title">Вход | RuGear</x-slot>

    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="w-full max-w-md mx-auto">
            
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black uppercase tracking-tighter italic">
                    RU<span class="text-orange-500">GEAR</span> <span class="text-white">ID</span>
                </h1>
                <p class="text-gray-500 text-sm mt-2 font-medium">Авторизация в системе</p>
            </div>

            <x-auth-session-status class="mb-4 text-center text-orange-500 text-sm" :status="session('status')" />

            <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl shadow-orange-500/5">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Электронная почта</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="pilot@rugear.com">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold">Пароль</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[10px] uppercase tracking-tighter text-gray-600 hover:text-orange-500 transition">
                                    Забыли?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input type="password" name="password" required id="password"
                                class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700 pr-12"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-400 transition p-0 w-5 h-5 flex items-center justify-center">
                                <svg id="password-eye-open" class="w-5 h-5 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="password-eye-closed" class="w-5 h-5 absolute hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.114a1 1 0 10-.894-1.789m.894 1.789a1 1 0 01.894-1.789m-9.595 3.104a1 1 0 10.894 1.789m-.894-1.789a1 1 0 01-.894 1.789m9.595-3.104l3.976 2.888M3.975 7.95l3.975 2.888" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <div class="flex items-center px-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" name="remember" 
                                class="w-4 h-4 rounded border-gray-800 bg-[#0f1115] text-orange-500 focus:ring-orange-500 focus:ring-offset-[#161920] transition cursor-pointer">
                            <span class="ms-2 text-xs text-gray-500 group-hover:text-gray-400 transition">Запомнить меня</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black uppercase py-4 rounded-2xl transition-all shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)]">
                            Войти в аккаунт
                        </button>
                    </div>

                    <p class="text-center text-xs text-gray-600 mt-6">
                        Новый пользователь? 
                        <a href="{{ route('register') }}" class="text-gray-400 hover:text-orange-500 font-bold transition-colors underline decoration-gray-800 underline-offset-4">
                            Создать аккаунт
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>