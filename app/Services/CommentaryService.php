<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Commentary;
use App\Models\Achievement;

use App\Http\Requests\CommentaryRequest;

class CommentaryService
{
    public function addComment($contentOrRequest, Product $product, User $user): ?array
    {
        $content = is_string($contentOrRequest)
            ? $contentOrRequest
            : $contentOrRequest->content;

        Commentary::create([
           'content'    => $content,
           'user_id'    => $user->id,
           'product_id' => $product->id,
        ]);

        $count = $user->commentaries()->count();

        $slugMap = [
            1 => 'comment_1',
            3 => 'comment_3',
            5 => 'comment_5',
        ];

        if (isset($slugMap[$count])) {
            $achievement = Achievement::where('slug', $slugMap[$count])->first();
            if ($achievement) {
                $result = $user->awardAchievement($achievement);
                if ($result) {
                    return [
                        'achievement' => [
                            'title'      => $achievement->title,
                            'experience' => $achievement->experience,
                            'coins'      => $achievement->coins,
                        ],
                        'level_up' => $result['leveled_up'] ? [
                            'level' => $result['new_level'],
                            'coins' => $result['level_coins'],
                        ] : null,
                    ];
                }
            }
        }

        return null;
    }
}
