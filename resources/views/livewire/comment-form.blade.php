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
    (function() {
        function register() {
            if (typeof Livewire === 'undefined') {
                setTimeout(register, 100);
                return;
            }
            Livewire.on('show-achievement', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                if (d && typeof showToast === 'function') showToast('achievement', d);
            });
            Livewire.on('show-levelup', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                if (d && typeof showToast === 'function') showToast('levelup', d);
            });
        }
        register();
    })();
</script>
