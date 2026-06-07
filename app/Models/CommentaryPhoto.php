<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentaryPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['commentary_id', 'path'];

    public function commentary()
    {
        return $this->belongsTo(Commentary::class);
    }
}
