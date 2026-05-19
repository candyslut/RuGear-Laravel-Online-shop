<?php

namespace App\Models\Spec;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use App\Models\Product;

class MouseSpecification extends Model
{
    protected $fillable = [
        'sensor',
        'max_dpi',
        'polling_rate',
        'switches',
        'connection',
        'battery_life',
        'weight'
    ];

    public function product(): MorphOne
    {
        return $this->morphOne(Product::class, 'specification');
    }
}
