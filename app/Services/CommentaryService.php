<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Commentary;
use App\Models\Achievement;

use App\Http\Requests\CommentaryRequest;

class CommentaryService
{
    public function addComment($contentOrRequest, Product $product, User $user) {
        // Support both string content and CommentaryRequest
        $content = is_string($contentOrRequest) 
            ? $contentOrRequest 
            : $contentOrRequest->content;

        Commentary::create([
           'content' => $content,
           'user_id' => $user->id,
           'product_id' => $product->id,
        ]);

        // Award comment-based achievements (1, 3, 5 comments)
        $count = $user->commentaries()->count();

        $slugMap = [
            1 => 'comment_1',
            3 => 'comment_3',
            5 => 'comment_5',
        ];

        if (isset($slugMap[$count])) {
            $achievement = Achievement::where('slug', $slugMap[$count])->first();
            if ($achievement) {
                $awarded = $user->awardAchievement($achievement);
                if ($awarded) {
                    session()->flash('achievement_awarded', [
                        'title' => $achievement->title,
                        'description' => $achievement->description,
                        'experience' => $achievement->experience,
                    ]);
                }
            }
        }
    }
}