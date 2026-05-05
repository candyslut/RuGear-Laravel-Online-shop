<x-shop-layout>
    <x-slot name="title">Восстановление доступа | RuGear</x-slot>

    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="w-full max-w-md mx-auto">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 rounded-full bg-orange-500/10 mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-black uppercase tracking-tighter italic">
                    RU<span class="text-orange-500">GEAR</span> <span class="text-white">ID</span>
                </h1>

                <div class="mt-4 px-6 text-sm text-gray-500 leading-relaxed font-medium">
                    {{ __('Забыли пароль? Без паники. Просто укажите ваш Email, и мы отправим ссылку для создания нового пароля.') }}
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-500 text-xs text-center font-bold uppercase tracking-wider">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl shadow-orange-500/5">
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Почта для восстановления</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="your-mail@example.com">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black uppercase py-4 rounded-2xl transition-all shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)]">
                            Отправить ссылку
                        </button>
                    </div>

                    <p class="text-center">
                        <a href="{{ route('login') }}" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-orange-500 font-bold transition-colors">
                            ← Вернуться к входу
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>