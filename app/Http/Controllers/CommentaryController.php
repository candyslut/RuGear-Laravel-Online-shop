<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentaryRequest;
use App\Services\CommentaryService;

use Illuminate\Support\Facades\Auth;

use App\Models\Product;

class CommentaryController extends Controller
{
    public function __construct(
        protected CommentaryService $commentaryService
    ) {}

    public function store(CommentaryRequest $commentaryRequest, Product $product)
    {
        $this->commentaryService->addComment($commentaryRequest, $product, Auth::user());
        return redirect()->back();
    }
}
