<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sticker extends Model
{
    use HasFactory;

    protected $fillable = ['sticker_pack_id', 'file_path', 'is_animated', 'thumb_path', 'emoji', 'sort_order'];

    protected $casts = [
        'is_animated' => 'boolean',
    ];

    public function pack()
    {
        return $this->belongsTo(StickerPack::class, 'sticker_pack_id');
    }
}
