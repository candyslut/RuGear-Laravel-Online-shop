<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedEvent extends Model
{
    protected $fillable = ['type', 'user_id', 'name', 'avatar', 'title', 'color'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
