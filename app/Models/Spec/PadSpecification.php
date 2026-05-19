<?php

namespace App\Models\Spec;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use App\Models\Product;

class PadSpecification extends Model
{
    protected $fillable = [
        'surface',
        'material',
        'base_material',
        'dimensions',
        'thickness',
        'edges'
    ];

    public function product(): MorphOne
    {
        return $this->morphOne(Product::class, 'specification');
    }
}
