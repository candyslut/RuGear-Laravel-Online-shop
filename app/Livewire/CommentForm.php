<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Achievement;
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
        $user->load('commentaries');
        $countBefore = $user->commentaries()->count();

        $commentaryService = app(CommentaryService::class);
        $commentaryService->addComment($this->content, $this->product, $user);

        // Refresh user data
        $user->refresh();
        $countAfter = $user->commentaries()->count();

        // Check if achievement was awarded in this request
        $slugMap = [1 => 'comment_1', 3 => 'comment_3', 5 => 'comment_5'];
        if (isset($slugMap[$countAfter]) && $countAfter > $countBefore) {
            $achievement = Achievement::where('slug', $slugMap[$countAfter])->first();
            if ($achievement) {
                $this->dispatch('show-achievement-toast', achievement: [
                    'title' => $achievement->title,
                    'description' => $achievement->description,
                    'experience' => $achievement->experience,
                ]);
            }
        }

        $this->content = '';
        $this->dispatch('comment-added');
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
