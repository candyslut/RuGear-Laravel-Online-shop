<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['slug', 'category', 'name', 'description', 'price', 'css_value'])]
class ShopItem extends Model
{
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_shop_items')
            ->withPivot('purchased_at');
    }
}
