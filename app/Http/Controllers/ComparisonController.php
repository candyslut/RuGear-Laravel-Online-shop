<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function show()
    {
        $comparisonIds = session('comparison', []);

        if (empty($comparisonIds)) {
            return redirect('/')->with('message', 'Сравнение пусто');
        }

        $products = Product::with('category', 'specification')
            ->whereIn('id', $comparisonIds)
            ->get();

        if ($products->count() < count($comparisonIds)) {
            session(['comparison' => []]);
            return redirect('/')->with('message', 'Некоторые продукты удалены');
        }

        return view('comparison.show', compact('products'));
    }

    public function clear()
    {
        session(['comparison' => []]);
        return response()->json(['success' => true]);
    }
}
