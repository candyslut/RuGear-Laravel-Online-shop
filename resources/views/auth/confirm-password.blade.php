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
                        <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="••••••••">
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