<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Livewire\Concerns\InteractsWithStickerPicker;
use App\Services\CommentaryService;
use Illuminate\Support\Facades\Auth;

class CommentForm extends Component
{
    use WithFileUploads;
    use InteractsWithStickerPicker;

    public Product $product;
    public string $content = '';
    public array $photos = [];
    public ?int $stickerId = null;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function removePhoto(int $index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    /**
     * Validate photos as soon as they are attached so the user gets immediate
     * feedback (too large / wrong type) instead of only on submit.
     */
    public function updatedPhotos()
    {
        $this->validate($this->photoRules(), $this->validationMessages());
    }

    public function submitComment()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Вы должны быть авторизованы для добавления комментария');
            return;
        }

        $this->validate([
            // A comment is valid with text, a sticker, OR just photo(s) —
            // text is only required when nothing else is attached.
            'content'   => 'required_without_all:stickerId,photos|nullable|string|min:3|max:1000',
            'stickerId' => 'nullable|exists:stickers,id',
            ...$this->photoRules(),
        ], $this->validationMessages());

        $user = Auth::user();
        $commentaryService = app(CommentaryService::class);
        $awarded = $commentaryService->addComment($this->content, $this->product, $user, $this->photos, null, $this->stickerId);

        // Tell the picker to prepend this sticker to its "Recent" tab live.
        if ($payload = $this->stickerUsedPayload($this->stickerId)) {
            $this->dispatch('sticker-used', sticker: $payload);
        }

        $this->content = '';
        $this->photos = [];
        $this->stickerId = null;
        unset($this->recentStickers);
        $this->dispatch('comment-added')->to('comments-list');
        $this->dispatch('sticker-sent'); // clears the client-side staged preview

        if ($awarded) {
            $this->dispatch('show-achievement', $awarded['achievement']);
            if ($awarded['level_up']) {
                $this->dispatch('show-levelup', $awarded['level_up']);
            }
        }

        // Quest: leaving a top-level review (replies don't count, mirroring the
        // comment achievements). Surfaced via a browser event toast since this
        // is a Livewire round-trip, not a full page load.
        if ($done = app(\App\Services\DailyQuestService::class)->progress($user, 'comment')) {
            $this->dispatch('quests-completed', quests: $done);
            $this->dispatch('profile-refresh'); // live-sync the stat matrix + quest panel
        }
    }

    /**
     * Up to 4 photos, each a real image (jpg/png/webp) of at most 5 MB.
     */
    protected function photoRules(): array
    {
        return [
            'photos'   => 'array|max:4',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'content.required_without_all' => 'Напишите отзыв, прикрепите фото или стикер.',
            'content.min'                  => 'Отзыв должен содержать не менее 3 символов.',
            'content.max'                  => 'Отзыв не должен превышать 1000 символов.',
            'photos.max'                   => 'Можно прикрепить не более 4 фотографий.',
            'photos.*.image'               => 'Файл должен быть изображением.',
            'photos.*.mimes'               => 'Допустимые форматы: JPG, PNG или WEBP.',
            'photos.*.max'                 => 'Размер фото не должен превышать 5 МБ.',
        ];
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
