{{--
    Telegram-style sticker picker. Lives inside the CommentForm Livewire component.

    Performance: the whole popover body is behind <template x-if="open">, so the
    hundreds of sticker cells are NOT in the DOM until the picker is opened. Once
    open, x-sticker-grid mounts a player only for the cells currently on screen
    (paused first frame, playing on hover) and tears them down on scroll-away.

    Selecting a sticker is fully client-side (Alpine `select()` → `stage-sticker`
    event); nothing hits the server until the comment is submitted.

    Passed in from comment-form:
      $packs   — collection of active packs available to the user (each with ->stickers)
      $recent  — collection of the user's recently used Sticker models
--}}
<div
    x-data="stickerPicker()"
    class="relative inline-block"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
>
    <button
        type="button"
        @click="toggle()"
        :class="open ? 'border-orange-500 text-orange-400' : 'border-gray-800 text-gray-400'"
        class="w-9 h-9 flex items-center justify-center rounded-xl border bg-[#0f1117] hover:border-orange-500 hover:text-orange-400 active:scale-95 transition-all"
        title="Стикеры"
    >
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16v9l-7 7H4z" />
            <path d="M13 20v-5a2 2 0 0 1 2-2h5" />
        </svg>
    </button>

    {{-- x-show (not x-if): the picker is built ONCE and kept in the DOM so it
         reopens instantly — no re-clone, no re-fetch/re-decode of stickers on
         every open. x-sticker-grid still lazy-mounts only the visible players. --}}
    <div
        x-show="open"
        x-cloak
        x-transition.origin.bottom.left
        {{-- Mobile: a fixed bottom-sheet spanning the screen width (the 22rem
             popover was cramped/clipped on phones). sm+: the anchored popover. --}}
        class="fixed sm:absolute z-[60] inset-x-2 bottom-2 sm:inset-x-auto sm:bottom-full sm:mb-2 sm:left-0 w-auto sm:w-[22rem] sm:max-w-[calc(100vw-1.5rem)] bg-gradient-to-b from-[#13161d] to-[#0d0f15] border border-gray-800 rounded-2xl shadow-2xl shadow-black/60 overflow-hidden"
    >
            @if($packs->isEmpty() && $recent->isEmpty())
                <div class="h-56 flex flex-col items-center justify-center text-center gap-2 px-4">
                    <div class="text-4xl">🪄</div>
                    <p class="text-xs text-gray-400">У вас пока нет стикерпаков.</p>
                    <a href="{{ route('market.index') }}" class="text-xs font-bold text-orange-400 hover:text-orange-300">Купить в магазине →</a>
                </div>
            @else
                {{-- Top pack bar (Telegram-style): jumps to a section and recenters --}}
                <div x-ref="tabs"
                     class="flex items-center gap-1.5 px-2.5 py-2 border-b border-gray-800/80 bg-[#0b0d12] overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @if($recent->isNotEmpty())
                        <button type="button" data-tab="recent" @click="scrollTo('recent')"
                                :class="current === 'recent' ? 'bg-orange-500/15 text-orange-400 ring-1 ring-orange-500/50' : 'text-gray-500 hover:bg-gray-800/70'"
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl transition-all duration-200" title="Недавние">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />
                            </svg>
                        </button>
                        <span class="flex-shrink-0 w-px h-6 bg-gray-800"></span>
                    @endif

                    @foreach($packs as $pack)
                        @php $cover = $pack->stickers->first(); @endphp
                        <button type="button" data-tab="{{ $pack->slug }}" @click="scrollTo('{{ $pack->slug }}')"
                                :class="current === '{{ $pack->slug }}' ? 'bg-orange-500/15 ring-1 ring-orange-500/50 scale-110' : 'opacity-60 hover:opacity-100 hover:bg-gray-800/70'"
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl p-1 transition-all duration-200"
                                title="{{ $pack->name }}">
                            @if($cover)
                                @include('partials.pack-preview', ['src' => Storage::url($cover->file_path), 'size' => '1.75rem', 'autoplay' => true])
                            @else
                                <span class="text-lg leading-none">📦</span>
                            @endif
                        </button>
                    @endforeach
                </div>

                {{-- Continuous scroll with sticky section headers (like Telegram).
                     `relative` so section.offsetTop is measured against this box.
                     `max-h-80` (not a fixed height) so the popover is only as tall as
                     its stickers — a small collection shows a short menu, a big one
                     caps here and scrolls. --}}
                <div x-ref="body" x-sticker-grid class="relative max-h-[55vh] sm:max-h-80 overflow-y-auto p-2.5 space-y-3 [scrollbar-width:thin]">
                    @if($recent->isNotEmpty())
                        <section data-section="recent">
                            <p class="sticky top-0 z-10 bg-[#0d0f15]/95 backdrop-blur px-1 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-500">Недавние</p>
                            <div class="grid grid-cols-5 gap-1">
                                @foreach($recent as $sticker)
                                    @include('partials.sticker-button', ['sticker' => $sticker, 'ctx' => 'recent'])
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @foreach($packs as $pack)
                        <section data-section="{{ $pack->slug }}">
                            <p class="sticky top-0 z-10 bg-[#0d0f15]/95 backdrop-blur px-1 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ $pack->name }}</p>
                            <div class="grid grid-cols-5 gap-1">
                                @forelse($pack->stickers as $sticker)
                                    @include('partials.sticker-button', ['sticker' => $sticker, 'ctx' => $pack->slug])
                                @empty
                                    <p class="col-span-5 text-center text-xs text-gray-600 py-4">Пусто</p>
                                @endforelse
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
</div>
