<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ProductService;

class AdminController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index()
    {
        $users = User::withCount('tickets')->latest()->paginate(12);

        return view('admin.users', compact('users'));
    }

    public function productsIndex(Request $request)
    {
        $search = $request->get('search');

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.products.index', compact(
            'products',
            'categories',
            'search'
        ));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'category_type' => 'required|string',
        ]);

        $this->productService->store($request);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Девайс успешно добавлен в систему.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $this->productService->update($product, $request);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Данные девайса успешно обновлены прямо из списка.');
    }

    public function destroy(Product $product)
    {
        $this->productService->destroy($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Девайс удален из каталога.');
    }

    public function deleteUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()
                ->back()
                ->with('error', 'Вы не можете удалить собственный аккаунт.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', "Пользователь {$user->name} успешно удален.");
    }
}
