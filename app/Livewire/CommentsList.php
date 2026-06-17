<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Commentary;
use App\Models\CommentLike;
use App\Models\Achievement;
use App\Livewire\Concerns\InteractsWithStickerPicker;
use App\Services\CommentaryService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class CommentsList extends Component
{
    use WithFileUploads;
    use InteractsWithStickerPicker;

    public Product $product;

    // Inline reply state (one open reply form at a time).
    public ?int $replyingTo = null;
    public string $replyContent = '';
    public array $replyPhotos = [];
    public ?int $replyStickerId = null;

    #[\Livewire\Attributes\Locked]
    public $listId;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->listId = 'comments-list-' . $product->id;
    }

    /**
     * The comment thread. Computed (not a stored public property) so the entire
     * thread is NOT serialized into the Livewire snapshot on every request —
     * which, on a busy product, was shipping the whole comment list back and
     * forth on every keystroke/submit and made sticker sends feel sluggish.
     * It is recomputed from the DB on each render and the cache is busted with
     * unset($this->comments) whenever the thread changes.
     */
    #[Computed]
    public function comments()
    {
        $uid = Auth::id();
        // Whether the current user has liked each row, resolved in the same query
        // (aliased count → 0/1) so the view needs no per-comment lookups.
        $mine = ['likes as liked_by_me' => fn ($q) => $q->where('user_id', $uid)];

        return $this->product->commentaries()
            ->whereNull('parent_id')
            ->withCount('likes')
            ->withCount($mine)
            ->with([
                'user', 'photos', 'sticker',
                'replies' => fn ($q) => $q
                    ->withCount('likes')
                    ->withCount($mine)
                    ->with(['user', 'photos', 'sticker']),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Toggle the current user's like on a comment/reply. Liking awards the
     * comment's author +1 XP, plus +5 coins on every 5th like the comment
     * receives, and drops a "liked your comment" notification in their bell.
     * You can't like your own review. Unliking simply removes the like (no
     * claw-back). The heart updates optimistically client-side; this just
     * persists the truth.
     */
    public function toggleLike(int $commentId)
    {
        if (!Auth::check()) {
            return;
        }

        $user    = Auth::user();
        $comment = Commentary::find($commentId);

        // Missing comment, or your own review — ignore.
        if (!$comment || $comment->user_id === $user->id) {
            return;
        }

        $existing = $comment->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $comment->likes()->create(['user_id' => $user->id]);

            $author = $comment->user;
            if ($author) {
                $author->addExperience(1); // +1 XP per like received
                $likeCount = $comment->likes()->count();
                if ($likeCount % 5 === 0) {
                    $author->addCoins(5);  // +5 coins every 5 likes
                }
                // Popularity achievements for the comment's author.
                $this->awardBySlug($author, [10 => 'liked_10', 50 => 'liked_50'], $likeCount);
                app(NotificationService::class)->commentLiked($comment, $user);
            }

            // Engagement achievements for the liker (likes they've handed out).
            $givenCount = CommentLike::where('user_id', $user->id)->count();
            $this->awardBySlug($user, [1 => 'like_giver_1', 25 => 'like_giver_25', 100 => 'like_giver_100'], $givenCount);

            // Quest: giving likes to other people's comments.
            if ($done = app(\App\Services\DailyQuestService::class)->progress($user, 'like_give')) {
                $this->dispatch('quests-completed', quests: $done);
                // Live-sync the viewer's coin chip (header, outside Livewire).
                $this->dispatch('coins-changed', coins: (int) $user->fresh()->coins);
            }
            $this->dispatch('profile-refresh'); // live-sync coins / quest panel
        }

        unset($this->comments);
    }

    /**
     * Grant the achievement mapped to $count, if any. The map is keyed by the
     * exact threshold (mirrors the comment-milestone style); since likes move
     * by one at a time, each threshold is hit precisely once. awardAchievement()
     * dedupes, so this stays a no-op after the first grant.
     *
     * When the awarded user is the one acting in this request, the grant is
     * also surfaced as the bottom-right toast (`achievements-awarded` browser
     * event, handled globally in shop-layout). Awards for another user (the
     * comment's author getting liked_10/50) can only reach them via the bell.
     */
    private function awardBySlug(\App\Models\User $user, array $thresholds, int $count): void
    {
        if (!isset($thresholds[$count])) {
            return;
        }

        $achievement = Achievement::where('slug', $thresholds[$count])->first();
        if ($achievement && $user->awardAchievement($achievement) && $user->id === auth()->id()) {
            $this->dispatch('achievements-awarded', achievements: [[
                'title'      => $achievement->title,
                'experience' => $achievement->experience,
                'coins'      => $achievement->coins,
            ]]);
        }
    }

    public function startReply(int $commentId)
    {
        $this->replyingTo = $commentId;
        $this->replyContent = '';
        $this->replyPhotos = [];
        $this->replyStickerId = null;
        $this->resetErrorBag();
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyContent = '';
        $this->replyPhotos = [];
        $this->replyStickerId = null;
    }

    public function removeReplyPhoto(int $index)
    {
        unset($this->replyPhotos[$index]);
        $this->replyPhotos = array_values($this->replyPhotos);
    }

    public function submitReply()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Вы должны быть авторизованы для ответа');
            return;
        }

        if ($this->replyingTo === null) {
            return;
        }

        $this->validate([
            // A reply may be text, a sticker, or both.
            'replyContent'  => 'required_without:replyStickerId|nullable|string|min:1|max:1000',
            'replyStickerId' => 'nullable|exists:stickers,id',
            'replyPhotos'   => 'array|max:4',
            'replyPhotos.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Only top-level reviews can be replied to (one level deep).
        $parent = $this->product->commentaries()
            ->whereNull('parent_id')
            ->findOrFail($this->replyingTo);

        app(CommentaryService::class)->addComment(
            $this->replyContent,
            $this->product,
            Auth::user(),
            $this->replyPhotos,
            $parent->id,
            $this->replyStickerId,
        );

        // Tell the picker to prepend this sticker to its "Recent" tab live.
        if ($payload = $this->stickerUsedPayload($this->replyStickerId)) {
            $this->dispatch('sticker-used', sticker: $payload);
        }

        unset($this->recentStickers);
        $this->cancelReply();
        unset($this->comments);
    }

    #[\Livewire\Attributes\On('comment-added')]
    public function onCommentAdded()
    {
        unset($this->comments);
    }

    public function render()
    {
        return view('livewire.comments-list');
    }
}
