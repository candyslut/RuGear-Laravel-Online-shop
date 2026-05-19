<?php

namespace App\Models\Spec;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use App\Models\Product;

class HeadphoneSpecification extends Model
{
    protected $fillable = [
        'sound_type',
        'drivers',
        'frequency',
        'impedance',
        'connection',
        'microphone',
        'battery_life'
    ];

    public function product(): MorphOne
    {
        return $this->morphOne(Product::class, 'specification');
    }
}
