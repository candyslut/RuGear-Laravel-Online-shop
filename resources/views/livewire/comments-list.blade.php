<div class="space-y-4">
    @forelse($comments as $comment)
        <div
            wire:key="comment-{{ $comment['id'] }}"
            class="bg-[#161920]/40 border border-gray-800 rounded-xl p-5 shadow-sm comment-item"
            @if($loop->first) style="animation: comment-slide-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);" @endif
        >
            @include('partials.commentary-author', ['author' => $comment['user'], 'createdAt' => $comment['created_at']])

            @if(!empty($comment['content']))
                <p class="text-gray-400 font-light text-sm italic pl-11">
                    « {{ $comment['content'] }} »
                </p>
            @endif

            @if(!empty($comment['sticker']))
                @include('partials.commentary-sticker', ['sticker' => $comment['sticker'], 'size' => 'md', 'wkey' => 'c' . $comment['id']])
            @endif

            {{-- Review photos --}}
            @if(!empty($comment['photos']))
                <div class="flex flex-wrap gap-2 mt-3 pl-11">
                    @foreach($comment['photos'] as $photo)
                        <a href="{{ Storage::url($photo['path']) }}" target="_blank" data-lightbox
                           class="block w-20 h-20 rounded-lg overflow-hidden border border-gray-800 hover:border-orange-500 transition-colors">
                            <img src="{{ Storage::url($photo['path']) }}" class="w-full h-full object-cover">
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Reply action --}}
            @auth
                <div class="pl-11 mt-3">
                    <button
                        type="button"
                        wire:click="startReply({{ $comment['id'] }})"
                        class="text-[11px] font-mono uppercase tracking-wider text-gray-500 hover:text-orange-500 transition-colors"
                    >Ответить</button>
                </div>
            @endauth

            {{-- Inline reply form --}}
            @auth
                @if($replyingTo === $comment['id'])
                    <form wire:submit="submitReply" class="pl-11 mt-3 space-y-2" wire:key="reply-form-{{ $comment['id'] }}"
                        x-data="{ staged: null }"
                        @stage-sticker="staged = $event.detail; $wire.set('replyStickerId', $event.detail.id, false)"
                    >
                        <textarea
                            wire:model="replyContent"
                            id="reply-textarea-{{ $comment['id'] }}"
                            rows="2"
                            placeholder="Ваш ответ..."
                            class="w-full bg-[#0f1117] border border-gray-800 rounded-xl p-3 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-orange-500 transition-colors resize-none"
                        ></textarea>
                        @error('replyContent')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror

                        @if(!empty($replyPhotos))
                            <div class="flex flex-wrap gap-2">
                                @foreach($replyPhotos as $i => $photo)
                                    <div wire:key="reply-preview-{{ $i }}" class="relative w-14 h-14 rounded-lg overflow-hidden border border-gray-800">
                                        @if(is_object($photo) && method_exists($photo, 'temporaryUrl'))
                                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="removeReplyPhoto({{ $i }})"
                                            class="absolute top-0.5 right-0.5 w-4 h-4 flex items-center justify-center rounded-full bg-black/70 text-white text-[10px] hover:bg-red-500 transition-colors"
                                        >&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('replyPhotos.*')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror

                        {{-- Staged sticker preview (client-side; sent only on submit) --}}
                        <div x-show="staged" class="flex items-center gap-2" style="display:none;">
                            <div wire:ignore class="relative w-16 h-16 rounded-lg border border-gray-800 bg-[#0f1117] flex items-center justify-center">
                                <template x-if="staged && staged.type === 'lottie'">
                                    <canvas x-lottie.loop="staged.url" class="w-12 h-12" width="120" height="120"></canvas>
                                </template>
                                <template x-if="staged && staged.type === 'video'">
                                    <video :src="staged.url" class="w-12 h-12 object-contain" autoplay loop muted playsinline></video>
                                </template>
                                <template x-if="staged && staged.type === 'image'">
                                    <img :src="staged.url" class="w-12 h-12 object-contain">
                                </template>
                                <button
                                    type="button"
                                    @click="staged = null; $wire.set('replyStickerId', null, false)"
                                    class="absolute top-0.5 right-0.5 w-4 h-4 flex items-center justify-center rounded-full bg-black/70 text-white text-[10px] hover:bg-red-500 transition-colors"
                                >&times;</button>
                            </div>
                            <span class="text-[11px] text-gray-500">Стикер прикреплён</span>
                        </div>
                        @error('replyStickerId')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center gap-2">
                            <label
                                class="inline-flex items-center gap-1.5 h-8 px-2.5 rounded-lg border border-gray-800 bg-[#0f1117] text-gray-400 hover:border-orange-500 hover:text-orange-400 active:scale-95 transition-all cursor-pointer"
                                title="Прикрепить фото (до 4)"
                            >
                                <input type="file" wire:model="replyPhotos" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 8.5A1.5 1.5 0 0 1 4.5 7h1.6a1 1 0 0 0 .8-.4l.9-1.2a1 1 0 0 1 .8-.4h4.8a1 1 0 0 1 .8.4l.9 1.2a1 1 0 0 0 .8.4h1.6A1.5 1.5 0 0 1 21 8.5v9A1.5 1.5 0 0 1 19.5 19h-15A1.5 1.5 0 0 1 3 17.5z" />
                                    <circle cx="12" cy="12.5" r="3.2" />
                                </svg>
                                @if(count($replyPhotos))
                                    <span class="text-[10px] font-mono text-orange-400">{{ count($replyPhotos) }}/4</span>
                                @endif
                            </label>

                            {{-- Sticker picker for replies --}}
                            @include('partials.sticker-picker', [
                                'recent' => $this->recentStickers,
                                'packs'  => $this->stickerPacks,
                            ])

                            <span wire:loading wire:target="replyPhotos" class="text-xs text-gray-500 font-mono">Загрузка…</span>

                            <div class="flex-1"></div>

                            <button
                                type="button"
                                wire:click="cancelReply"
                                class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors"
                            >Отмена</button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="submitReply,replyPhotos"
                                class="px-4 py-2 bg-white hover:bg-orange-500 hover:text-black text-black text-[11px] font-bold uppercase tracking-wider rounded-lg transition-all active:scale-95 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="submitReply">Ответить</span>
                                <span wire:loading wire:target="submitReply">...</span>
                            </button>
                        </div>
                    </form>
                @endif
            @endauth

            {{-- Replies --}}
            @if(!empty($comment['replies']))
                <div class="mt-4 pl-6 ml-5 border-l border-gray-800 space-y-4">
                    @foreach($comment['replies'] as $reply)
                        <div wire:key="reply-{{ $reply['id'] }}" class="pt-1">
                            @include('partials.commentary-author', ['author' => $reply['user'], 'createdAt' => $reply['created_at'], 'size' => 'sm'])
                            @if(!empty($reply['content']))
                                <p class="text-gray-400 font-light text-sm pl-9">
                                    {{ $reply['content'] }}
                                </p>
                            @endif
                            @if(!empty($reply['sticker']))
                                @include('partials.commentary-sticker', ['sticker' => $reply['sticker'], 'size' => 'sm', 'wkey' => 'r' . $reply['id']])
                            @endif
                            @if(!empty($reply['photos']))
                                <div class="flex flex-wrap gap-2 mt-2 pl-9">
                                    @foreach($reply['photos'] as $photo)
                                        <a href="{{ Storage::url($photo['path']) }}" target="_blank" data-lightbox
                                           class="block w-16 h-16 rounded-lg overflow-hidden border border-gray-800 hover:border-orange-500 transition-colors">
                                            <img src="{{ Storage::url($photo['path']) }}" class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-8 text-gray-600 font-mono text-xs uppercase tracking-wider opacity-50">
            Отзывов пока нет.
        </div>
    @endforelse
</div>

<style>
    @keyframes comment-slide-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .comment-item {
        transition: all 0.3s ease;
    }
</style>
