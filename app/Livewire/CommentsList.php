<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Livewire\Concerns\InteractsWithStickerPicker;
use App\Services\CommentaryService;
use Illuminate\Support\Facades\Auth;

class CommentsList extends Component
{
    use WithFileUploads;
    use InteractsWithStickerPicker;

    public Product $product;
    public $comments = [];

    // Inline reply state (one open reply form at a time).
    public ?int $replyingTo = null;
    public string $replyContent = '';
    public array $replyPhotos = [];
    public ?int $replyStickerId = null;

    #[\Livewire\Attributes\Locked]
    public $listId;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->listId = 'comments-list-' . $product->id;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->product->refresh();
        $this->comments = $this->product->commentaries()
            ->whereNull('parent_id')
            ->with(['user', 'photos', 'sticker', 'replies.user', 'replies.photos', 'replies.sticker'])
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    public function startReply(int $commentId)
    {
        $this->replyingTo = $commentId;
        $this->replyContent = '';
        $this->replyPhotos = [];
        $this->replyStickerId = null;
        $this->resetErrorBag();
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyContent = '';
        $this->replyPhotos = [];
        $this->replyStickerId = null;
    }

    public function removeReplyPhoto(int $index)
    {
        unset($this->replyPhotos[$index]);
        $this->replyPhotos = array_values($this->replyPhotos);
    }

    public function submitReply()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Вы должны быть авторизованы для ответа');
            return;
        }

        if ($this->replyingTo === null) {
            return;
        }

        $this->validate([
            // A reply may be text, a sticker, or both.
            'replyContent'  => 'required_without:replyStickerId|nullable|string|min:1|max:1000',
            'replyStickerId' => 'nullable|exists:stickers,id',
            'replyPhotos'   => 'array|max:4',
            'replyPhotos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Only top-level reviews can be replied to (one level deep).
        $parent = $this->product->commentaries()
            ->whereNull('parent_id')
            ->findOrFail($this->replyingTo);

        app(CommentaryService::class)->addComment(
            $this->replyContent,
            $this->product,
            Auth::user(),
            $this->replyPhotos,
            $parent->id,
            $this->replyStickerId,
        );

        // Tell the picker to prepend this sticker to its "Recent" tab live.
        if ($payload = $this->stickerUsedPayload($this->replyStickerId)) {
            $this->dispatch('sticker-used', sticker: $payload);
        }

        unset($this->recentStickers);
        $this->cancelReply();
        $this->loadComments();
    }

    #[\Livewire\Attributes\On('comment-added')]
    public function onCommentAdded()
    {
        $this->loadComments();
    }

    public function render()
    {
        return view('livewire.comments-list');
    }
}
