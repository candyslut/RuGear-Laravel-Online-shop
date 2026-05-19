<?php

namespace App\Models\Spec;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use App\Models\Product;

class KeyboardSpecification extends Model
{
    protected $fillable = [
        'switch_type',
        'form_factor',
        'keycap_material',
        'hotswap',
        'connection',
        'illumination',
        'construction'
    ];

    public function product(): MorphOne
    {
        return $this->morphOne(Product::class, 'specification');
    }
}
