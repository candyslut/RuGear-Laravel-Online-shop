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
        $awardedAchievement = $commentaryService->addComment($this->content, $this->product, $user);

        $this->content = '';
        $this->dispatch('comment-added')->to('comments-list');

        if ($awardedAchievement) {
            // Dispatch as array that will be converted to object
            $this->dispatch('show-toast', $awardedAchievement);
        }
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
