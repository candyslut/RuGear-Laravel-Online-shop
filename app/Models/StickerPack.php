<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StickerPack extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'thumb_path', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stickers()
    {
        return $this->hasMany(Sticker::class)->orderBy('sort_order');
    }

    /** The shop listing that sells this pack (null = free pack, available to all). */
    public function shopItem()
    {
        return $this->hasOne(ShopItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** A pack is premium when it is sold in the shop and must be purchased. */
    public function isPremium(): bool
    {
        return $this->shopItem !== null;
    }
}
