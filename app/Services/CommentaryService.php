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

        $awardedAchievement = null;
        if (isset($slugMap[$count])) {
            $achievement = Achievement::where('slug', $slugMap[$count])->first();
            if ($achievement) {
                $awarded = $user->awardAchievement($achievement);
                if ($awarded) {
                    $awardedAchievement = [
                        'title' => $achievement->title,
                        'description' => $achievement->description,
                        'experience' => $achievement->experience,
                    ];
                    session()->flash('achievement_awarded', $awardedAchievement);
                }
            }
        }

        return $awardedAchievement;
    }
}