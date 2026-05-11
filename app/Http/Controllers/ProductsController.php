<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\FilterRequest;

use App\Services\FilterService;

class ProductsController extends Controller
{

    protected $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function index(FilterRequest $request)
    {
        $query = Product::with('category');

        $this->filterService->applyFilters($query, $request->validated());

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('welcome', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'commentaries.user']);
        return view('products.show', compact('product'));
    }
}
