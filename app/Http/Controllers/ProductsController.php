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
        $topProducts = Product::with('category')->latest('created_at')->take(4)->get();
        $featuredProduct = Product::with('category')->where('quantity', '>', 0)->inRandomOrder()->first();
        $categories = Category::withCount('products')->get();

        return view('welcome', compact('products', 'categories', 'topProducts', 'featuredProduct'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'commentaries.user']);

        // Quest: viewing product pages. Deduped per product per day via the
        // session so refreshing one card can't farm the counter.
        if ($user = auth()->user()) {
            $seen = session('quest_viewed_products', []);
            if (($seen['date'] ?? null) !== now()->toDateString()) {
                $seen = ['date' => now()->toDateString(), 'ids' => []];
            }
            if (!in_array($product->id, $seen['ids'], true)) {
                $seen['ids'][] = $product->id;
                session(['quest_viewed_products' => $seen]);

                if ($done = app(\App\Services\DailyQuestService::class)->progress($user, 'product_view')) {
                    session()->flash('quests_completed', $done);
                }
                if ($rankUp = $user->lastRankUp) {
                    session()->flash('rank_up', $rankUp);
                }
            }
        }

        return view('products.show', compact('product'));
    }
}
