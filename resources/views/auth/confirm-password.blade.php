<x-shop-layout>
    <x-slot name="title">Подтверждение доступа | RuGear</x-slot>

    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="w-full max-w-md mx-auto">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 rounded-full bg-orange-500/10 mb-4 animate-pulse">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-tighter text-white">Проверка <span class="text-orange-500">Личности</span></h1>
                
                <div class="mt-4 px-6 text-sm text-gray-500 leading-relaxed font-medium">
                    {{ __('Это защищенная зона. Пожалуйста, введите пароль, чтобы подтвердить, что это именно вы.') }}
                </div>
            </div>

            <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl shadow-orange-500/5">
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Ваш текущий пароль</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                                class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700 pr-12"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-400 transition p-0 w-5 h-5 flex items-center justify-center">
                                <svg id="password-eye-open" class="w-5 h-5 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="password-eye-closed" class="w-5 h-5 absolute hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black uppercase py-4 rounded-2xl transition-all shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)]">
                            Подтвердить доступ
                        </button>
                    </div>

                    <p class="text-center">
                        <a href="javascript:history.back()" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-gray-400 font-bold transition-colors">
                            ← Отмена
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>