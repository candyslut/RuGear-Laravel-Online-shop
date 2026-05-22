<div class="space-y-4">
    <form wire:submit="submitComment" class="space-y-3">
        <div>
            <textarea 
                wire:model="content"
                rows="3"
                placeholder="Напишите отзыв..."
                class="w-full bg-[#0f1117] border border-gray-800 rounded-xl p-4 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-orange-500 transition-colors resize-none"
            ></textarea>
            @error('content')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button 
            type="submit"
            wire:loading.attr="disabled"
            class="w-full py-3 bg-white hover:bg-orange-500 hover:text-black text-black text-xs font-bold uppercase tracking-wider rounded-xl transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove>Отправить комментарий</span>
            <span wire:loading>Отправка...</span>
        </button>
    </form>
</div>

<script>
    function showAchievementToast(achievement) {
        const toastContainer = document.createElement('div');
        toastContainer.innerHTML = `
            <div class="fixed bottom-6 right-6 z-50 w-full max-w-sm rounded-3xl border border-orange-500/20 bg-[#111318] p-5 shadow-2xl shadow-orange-500/10 text-white ring-1 ring-orange-500/20 achievement-toast pointer-events-auto" style="animation: toast-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);">
                <div class="flex items-start gap-4">
                    <div class="rounded-2xl bg-orange-500/10 text-orange-300 p-3 flex-shrink-0">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <div class="space-y-2 flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.24em] text-orange-400 font-bold">🎉 Достижение разблокировано!</p>
                                <h3 class="text-sm font-black text-white line-clamp-1">${achievement.title}</h3>
                            </div>
                            <button type="button" onclick="this.closest('.achievement-toast').parentElement.remove()" class="text-gray-400 hover:text-white transition-colors flex-shrink-0 text-xl leading-none">
                                ×
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 line-clamp-2">${achievement.description}</p>
                        <div class="text-xs text-orange-300 bg-orange-500/10 px-3 py-2 rounded-2xl border border-orange-500/20 font-semibold">
                            ⭐ +${achievement.experience} опыта
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(toastContainer);

        setTimeout(() => {
            toastContainer.remove();
        }, 5000);
    }

    // Wait for Livewire to be ready
    function registerAchievementToastListener() {
        if (typeof Livewire !== 'undefined') {
            Livewire.on('show-toast', (data) => {
                console.log('Toast event received:', data);
                // Data comes as an array, get the first element
                const achievement = Array.isArray(data) ? data[0] : data;
                if (achievement && achievement.title) {
                    showAchievementToast(achievement);
                }
            });
        } else {
            // Retry after 100ms if Livewire not ready
            setTimeout(registerAchievementToastListener, 100);
        }
    }

    registerAchievementToastListener();
</script>

<style>
    @keyframes toast-in {
        from {
            opacity: 0;
            transform: translateX(420px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
