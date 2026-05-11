<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function commentaries()
    {
        return $this->hasMany(Commentary::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });

        $query->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('category_id', $category);
        });

        $query->when($filters['min_price'] ?? null, function ($query, $min) {
            $query->where('price', '>=', $min);
        });

        $query->when($filters['max_price'] ?? null, function ($query, $max) {
            $query->where('price', '<=', $max);
        });
    }
}
