<x-shop-layout>
    <x-slot name="title">Подтверждение почты | RuGear</x-slot>

    <div class="min-h-[60vh] flex flex-col items-center justify-center">
        <div class="w-full max-w-md mx-auto">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 rounded-full bg-orange-500/10 mb-4">
                    <svg class="w-8 h-8 text-orange-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-tighter">Проверьте <span class="text-orange-500">Почту</span></h1>
                
                <div class="mt-4 px-6 text-sm text-gray-500 leading-relaxed font-medium">
                    {{ __('Спасибо за регистрацию! Прежде чем начать, подтвердите свой адрес, перейдя по ссылке в письме. Если письмо не пришло, мы отправим его снова.') }}
                </div>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-500 text-[10px] text-center font-bold uppercase tracking-widest leading-tight">
                    {{ __('Новая ссылка для подтверждения была отправлена на ваш Email.') }}
                </div>
            @endif

            <div class="bg-[#161920] border border-gray-800 p-8 rounded-[2rem] shadow-2xl shadow-orange-500/5">
                <div class="flex flex-col space-y-6">
                    
                   <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" 
                            class="w-full bg-orange-500 hover:bg-orange-600 active:scale-[0.98] text-black font-black uppercase py-4 rounded-2xl transition-all shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)] text-sm">
                            Отправить письмо еще раз
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="text-center">
                        @csrf
                        <button type="submit" class="text-[10px] uppercase tracking-widest text-gray-600 hover:text-orange-500 font-bold transition-colors">
                            Выйти из системы
                        </button>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-8 text-[10px] text-gray-700 uppercase tracking-[0.3em]">
                Статус: Ожидание верификации
            </p>
        </div>
    </div>
</x-shop-layout>