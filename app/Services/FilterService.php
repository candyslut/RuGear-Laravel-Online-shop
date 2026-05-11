<?php

namespace App\Services;

class FilterService
{
    public function applyFilters($queryObj, array $data): void
    {
        if (isset($data['search']) && !empty($data['search'])) {
            $queryObj->where('name', 'like', '%' . $data['search'] . '%');
        }

        if (isset($data['category']) && !empty($data['category'])) {
            $queryObj->where('category_id', $data['category']);
        }

        if (isset($data['min_price']) && !empty($data['min_price'])) {
            $queryObj->where('price', '>=', $data['min_price']);
        }

        if (isset($data['max_price']) && !empty($data['max_price'])) {
            $queryObj->where('price', '<=', $data['max_price']);
        }
    }
}