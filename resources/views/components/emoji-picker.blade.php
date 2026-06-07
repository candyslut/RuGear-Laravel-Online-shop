@props(['target'])

@php
    $emojis = ['😀','😁','😂','🤣','😊','😍','😎','🤩','😉','😋','😜','🤔','😴','😢','😭','😡','🥳','😱','🤯','🙄','😏','🤤','🤗','🤝','👍','👎','👏','🙌','🙏','💪','🔥','✨','⭐','💯','❤️','🧡','💛','💚','💙','💜','🖤','💔','💖','🎉','🎁','💰','🛒','📦','🚀','⚡','💎','👀','💀','👻','🤖','🎮','🎧','📱','💻','⌨️','🖱️','🔋','✅','❌','⚠️','❓'];
@endphp

<div class="relative inline-block" data-emoji-root>
    <button
        type="button"
        data-emoji-toggle
        class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-800 bg-[#0f1117] text-gray-400 hover:border-orange-500 hover:text-orange-400 active:scale-95 transition-all"
        title="Эмодзи"
    >
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
            <circle cx="12" cy="12" r="9" />
            <path d="M8.5 14.5a4 4 0 0 0 7 0" />
            <line x1="9" y1="9.5" x2="9" y2="9.5" />
            <line x1="15" y1="9.5" x2="15" y2="9.5" />
        </svg>
    </button>

    <div
        data-emoji-panel
        class="hidden absolute z-50 bottom-full mb-2 left-0 w-64 max-h-52 overflow-y-auto bg-[#0f1117] border border-gray-800 rounded-xl p-2 shadow-2xl shadow-black/50"
    >
        <div class="grid grid-cols-8 gap-1">
        @foreach($emojis as $emoji)
            <button
                type="button"
                onclick="window.insertEmoji('{{ $target }}', '{{ $emoji }}')"
                class="h-7 text-lg rounded hover:bg-gray-800 transition-colors leading-none"
            >{{ $emoji }}</button>
        @endforeach
        </div>
    </div>
</div>

<script>
    (function () {
        if (window.__emojiPickerInit) return;
        window.__emojiPickerInit = true;

        // Insert an emoji at the caret of the target textarea and notify Livewire.
        window.insertEmoji = function (targetId, emoji) {
                const el = document.getElementById(targetId);
                if (!el) return;
                const start = el.selectionStart ?? el.value.length;
                const end = el.selectionEnd ?? el.value.length;
                el.value = el.value.slice(0, start) + emoji + el.value.slice(end);
                const caret = start + emoji.length;
                el.selectionStart = el.selectionEnd = caret;
                el.focus();
                // Let wire:model pick up the new value.
                el.dispatchEvent(new Event('input', { bubbles: true }));
            };

            // Toggle panels and close them when clicking elsewhere.
            document.addEventListener('click', function (e) {
                const toggle = e.target.closest('[data-emoji-toggle]');
                if (toggle) {
                    const panel = toggle.parentElement.querySelector('[data-emoji-panel]');
                    document.querySelectorAll('[data-emoji-panel]').forEach(function (p) {
                        if (p !== panel) p.classList.add('hidden');
                    });
                    panel.classList.toggle('hidden');
                    return;
                }
                if (!e.target.closest('[data-emoji-root]')) {
                    document.querySelectorAll('[data-emoji-panel]').forEach(function (p) {
                        p.classList.add('hidden');
                    });
                }
            });
    })();
</script>
