<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Services\CommentaryService;
use Illuminate\Support\Facades\Auth;

class CommentForm extends Component
{
    use WithFileUploads;

    public Product $product;
    public string $content = '';
    public array $photos = [];

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function removePhoto(int $index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function submitComment()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Вы должны быть авторизованы для добавления комментария');
            return;
        }

        $this->validate([
            'content' => 'required|string|min:3|max:1000',
            'photos'   => 'array|max:4',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();
        $commentaryService = app(CommentaryService::class);
        $awarded = $commentaryService->addComment($this->content, $this->product, $user, $this->photos);

        $this->content = '';
        $this->photos = [];
        $this->dispatch('comment-added')->to('comments-list');

        if ($awarded) {
            $this->dispatch('show-achievement', $awarded['achievement']);
            if ($awarded['level_up']) {
                $this->dispatch('show-levelup', $awarded['level_up']);
            }
        }
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
