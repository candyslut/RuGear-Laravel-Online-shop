<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StickerUsage extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'sticker_id', 'used_at'];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
