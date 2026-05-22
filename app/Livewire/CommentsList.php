<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class CommentsList extends Component
{
    public Product $product;
    public $comments = [];

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->product->refresh();
        $this->comments = $this->product->commentaries()
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
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
