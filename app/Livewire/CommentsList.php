<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class CommentsList extends Component
{
    public Product $product;
    public $comments = [];

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
            ->with('user')
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
