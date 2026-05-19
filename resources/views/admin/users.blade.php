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
</style>
<x-admin-layout>
    <x-slot:title>RuGear Admin | Пользователи</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-orange-500 transition-colors group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Вернуться в личный кабинет</span>
        </a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-2xl md:text-3xl font-black uppercase tracking-wider text-white">
                Управление пользователями
            </h1>
        </div>

        <div class="bg-[#161920] border border-gray-800 px-6 py-3 rounded-2xl flex items-center gap-4">
            <div class="text-right">
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Всего в базе</span>
                <span class="text-xl font-black text-white">{{ $users->total() }} чел.</span>
            </div>
            <div class="w-10 h-10 bg-orange-500/10 border border-orange-500/20 text-orange-500 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl animate-fade-in">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-6 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-xl animate-fade-in">
        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    @if($users->isEmpty())
    <div class="bg-[#161920] border border-gray-800 rounded-[2rem] p-16 text-center space-y-4">
        <div class="w-16 h-16 bg-gray-900 text-gray-600 rounded-2xl flex items-center justify-center text-2xl mx-auto border border-gray-800">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <h3 class="text-md font-bold text-white uppercase tracking-wider">Пользователи не найдены</h3>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($users as $user)
        <div class="bg-[#161920] border border-gray-800 rounded-3xl p-6 flex flex-col justify-between hover:border-gray-700/60 transition-all group relative overflow-hidden">

            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-orange-500/40 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>

            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-12 h-12 bg-gray-900 border border-gray-800 rounded-2xl flex items-center justify-center font-black text-orange-500 text-sm group-hover:border-orange-500/20 transition-colors shadow-inner">
                        {{ mb_strtoupper(mb_substr($user->name ?? 'U', 0, 2)) }}
                    </div>

                    @if(isset($user->is_admin) && $user->is_admin)
                    <span class="text-[9px] bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-md border border-orange-500/20 font-bold uppercase tracking-widest">
                        Admin
                    </span>
                    @else
                    <span class="text-[9px] bg-gray-900 text-gray-500 px-2 py-0.5 rounded-md border border-gray-800 font-bold uppercase tracking-widest">
                        Client
                    </span>
                    @endif
                </div>

                <div>
                    <h3 class="text-md font-black text-white group-hover:text-orange-400 transition-colors line-clamp-1">
                        {{ $user->name }}
                    </h3>
                    <span class="text-xs font-mono text-gray-500 block line-clamp-1 mt-0.5">{{ $user->email }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-900">
                    <div class="bg-gray-950/40 border border-gray-900 p-2.5 rounded-xl text-center">
                        <span class="text-[9px] text-gray-600 block uppercase font-bold tracking-wider">Тикеты</span>
                        <span class="text-xs font-mono font-black text-gray-300">{{ $user->tickets_count }} шт.</span>
                    </div>
                    <div class="bg-gray-950/40 border border-gray-900 p-2.5 rounded-xl text-center">
                        <span class="text-[9px] text-gray-600 block uppercase font-bold tracking-wider">ID Аккаунта</span>
                        <span class="text-xs font-mono font-black text-gray-400">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 mt-6 pt-4 border-t border-gray-900">
                <span class="text-[9px] text-gray-600 italic flex-grow">
                    Регистрация: {{ $user->created_at ? $user->created_at->format('d.m.Y') : '—' }}
                </span>

                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите полностью удалить профиль пользователя {{ $user->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="p-2.5 bg-gray-900 hover:bg-red-500/10 text-gray-500 hover:text-red-400 rounded-xl transition-all border border-gray-800 hover:border-red-500/20 active:scale-95 group/btn {{ auth()->id() === $user->id ? 'opacity-30 cursor-not-allowed pointer-events-none' : '' }}"
                        title="Удалить пользователя">
                        <i class="fa-solid fa-user-xmark text-xs transition-transform group-hover/btn:scale-110"></i>
                    </button>
                </form>
            </div>

        </div>
        @endforeach
    </div>

    <div class="mt-16 custom-pagination">
        {{ $users->onEachSide(1)->links() }}
    </div>
    @endif
</x-admin-layout>