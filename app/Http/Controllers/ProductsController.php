<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category; 
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->filter($request->only(['search', 'category', 'min_price', 'max_price']))
            ->paginate(12)
            ->withQueryString(); 

        $categories = Category::all();

        return view('welcome', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'commentaries.user']);
        return view('products.show', compact('product'));
    }
}