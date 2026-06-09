<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['slug', 'category', 'sticker_pack_id', 'name', 'description', 'price', 'css_value'])]
class ShopItem extends Model
{
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_shop_items')
            ->withPivot('purchased_at');
    }

    public function stickerPack(): BelongsTo
    {
        return $this->belongsTo(StickerPack::class);
    }
}
