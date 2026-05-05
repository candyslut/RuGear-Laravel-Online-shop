<x-shop-layout>
    <x-slot name="title">Регистрация | RuGear</x-slot>

    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        
        <div class="w-full max-w-md mx-auto">
            
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black uppercase tracking-tighter italic">
                    RU<span class="text-orange-500">GEAR</span> <span class="text-white">ID</span>
                </h1>
                <p class="text-gray-500 text-sm mt-2 font-medium">Создание профиля</p>
            </div>

            <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl shadow-orange-500/5">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Никнейм / Имя</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="Ghost_Rider">
                        <x-input-error :messages="$errors->get('name')" />
                    </div>


                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Электронная почта</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="mail@rugear.com">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Пароль</label>
                        <input type="password" name="password" required
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold ml-1">Подтверждение</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-[#0f1115] border border-gray-800 rounded-2xl px-5 py-4 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all placeholder:text-gray-700"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black uppercase py-4 rounded-2xl transition-all shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)]">
                            Зарегистрироваться
                        </button>
                    </div>

                    <p class="text-center text-xs text-gray-600 mt-6">
                        Уже в системе? 
                        <a href="{{ route('login') }}" class="text-gray-400 hover:text-orange-500 font-bold transition-colors underline decoration-gray-800 underline-offset-4">
                            Войти в профиль
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-shop-layout>