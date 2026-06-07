<div class="space-y-4">
    <form wire:submit="submitComment" class="space-y-3">
        <div>
            <textarea
                wire:model="content"
                id="comment-textarea-{{ $product->id }}"
                rows="3"
                placeholder="Напишите отзыв..."
                class="w-full bg-[#0f1117] border border-gray-800 rounded-xl p-4 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-orange-500 transition-colors resize-none"
            ></textarea>
            @error('content')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Photo previews --}}
        @if(!empty($photos))
            <div class="flex flex-wrap gap-2">
                @foreach($photos as $i => $photo)
                    <div wire:key="preview-{{ $i }}" class="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-800">
                        @if(is_object($photo) && method_exists($photo, 'temporaryUrl'))
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @endif
                        <button
                            type="button"
                            wire:click="removePhoto({{ $i }})"
                            class="absolute top-0.5 right-0.5 w-5 h-5 flex items-center justify-center rounded-full bg-black/70 text-white text-xs hover:bg-red-500 transition-colors"
                        >&times;</button>
                    </div>
                @endforeach
            </div>
        @endif
        @error('photos.*')
            <p class="text-red-400 text-xs">{{ $message }}</p>
        @enderror
        @error('photos')
            <p class="text-red-400 text-xs">{{ $message }}</p>
        @enderror

        <div class="flex items-center gap-2">
            {{-- Attach photos --}}
            <label
                class="inline-flex items-center gap-2 h-9 px-3 rounded-xl border border-gray-800 bg-[#0f1117] text-gray-400 hover:border-orange-500 hover:text-orange-400 active:scale-95 transition-all cursor-pointer"
                title="Прикрепить фото (до 4)"
            >
                <input type="file" wire:model="photos" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 8.5A1.5 1.5 0 0 1 4.5 7h1.6a1 1 0 0 0 .8-.4l.9-1.2a1 1 0 0 1 .8-.4h4.8a1 1 0 0 1 .8.4l.9 1.2a1 1 0 0 0 .8.4h1.6A1.5 1.5 0 0 1 21 8.5v9A1.5 1.5 0 0 1 19.5 19h-15A1.5 1.5 0 0 1 3 17.5z" />
                    <circle cx="12" cy="12.5" r="3.2" />
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider">Фото</span>
                @if(count($photos))
                    <span class="text-[10px] font-mono text-orange-400">{{ count($photos) }}/4</span>
                @endif
            </label>

            {{-- Emoji picker --}}
            <x-emoji-picker :target="'comment-textarea-' . $product->id" />

            <span wire:loading wire:target="photos" class="text-xs text-gray-500 font-mono">Загрузка…</span>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="submitComment,photos"
            class="w-full py-3 bg-white hover:bg-orange-500 hover:text-black text-black text-xs font-bold uppercase tracking-wider rounded-xl transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="submitComment">Отправить комментарий</span>
            <span wire:loading wire:target="submitComment">Отправка...</span>
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
