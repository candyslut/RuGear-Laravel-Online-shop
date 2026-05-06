<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Commentary;

use App\Http\Requests\CommentaryRequest;

class CommentaryService
{
    public function addComment(CommentaryRequest $commentaryRequest, Product $product, User $user) {
        Commentary::create([
           'content' => $commentaryRequest->content,
           'user_id' => $user->id,
           'product_id' => $product->id,
        ]);
    }
}