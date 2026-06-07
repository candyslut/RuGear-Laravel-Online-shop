<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Commentary;
use App\Models\Achievement;
use App\Services\NotificationService;

use App\Http\Requests\CommentaryRequest;

class CommentaryService
{
    /**
     * @param  string|CommentaryRequest  $contentOrRequest
     * @param  array  $photos  Array of uploaded image files (TemporaryUploadedFile|UploadedFile)
     * @param  int|null  $parentId  When set, the comment is a reply to that commentary
     */
    public function addComment($contentOrRequest, Product $product, User $user, array $photos = [], ?int $parentId = null): ?array
    {
        $content = is_string($contentOrRequest)
            ? $contentOrRequest
            : $contentOrRequest->content;

        $commentary = Commentary::create([
           'content'    => $content,
           'user_id'    => $user->id,
           'product_id' => $product->id,
           'parent_id'  => $parentId,
        ]);

        foreach ($photos as $photo) {
            if (!$photo) {
                continue;
            }

            $commentary->photos()->create([
                'path' => $photo->store('comment-photos', 'public'),
            ]);
        }

        // Notify the original reviewer that someone replied to them.
        if ($parentId !== null) {
            app(NotificationService::class)->commentReply($commentary);

            // Achievements are tied to leaving reviews, not to replies.
            return null;
        }

        $count = $user->commentaries()->whereNull('parent_id')->count();

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
