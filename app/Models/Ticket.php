<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'category',
        'content'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
