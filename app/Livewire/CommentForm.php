<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Services\CommentaryService;
use Illuminate\Support\Facades\Auth;

class CommentForm extends Component
{
    public Product $product;
    public string $content = '';

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function submitComment()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Вы должны быть авторизованы для добавления комментария');
            return;
        }

        $this->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);

        $user = Auth::user();
        $commentaryService = app(CommentaryService::class);
        $awarded = $commentaryService->addComment($this->content, $this->product, $user);

        $this->content = '';
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
