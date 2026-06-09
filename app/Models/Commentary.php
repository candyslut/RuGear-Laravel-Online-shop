<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commentary extends Model

{
    use HasFactory;

    protected $fillable = ['content', 'user_id', 'product_id', 'parent_id', 'sticker_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sticker() {
        return $this->belongsTo(Sticker::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function parent() {
        return $this->belongsTo(Commentary::class, 'parent_id');
    }

    public function replies() {
        return $this->hasMany(Commentary::class, 'parent_id');
    }

    public function photos() {
        return $this->hasMany(CommentaryPhoto::class);
    }

    public function likes() {
        return $this->hasMany(CommentLike::class);
    }
}
