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

    /**
     * Список всех пользователей в админ-панели
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort   = $request->input('sort', 'newest');

        $query = User::withCount(['tickets', 'orders'])->with('achievements');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->when($sort === 'level',  fn($q) => $q->orderByDesc('level')->orderByDesc('experience'))
              ->when($sort === 'coins',  fn($q) => $q->orderByDesc('coins'))
              ->when($sort === 'oldest', fn($q) => $q->oldest())
              ->when(!in_array($sort, ['level','coins','oldest']), fn($q) => $q->latest());

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users', compact('users', 'search', 'sort'));
    }

    public function addCoins(Request $request, User $user)
    {
        $request->validate(['amount' => 'required|integer|min:1|max:99999']);
        $user->addCoins($request->amount);

        return redirect()->back()->with('success', "Начислено {$request->amount} монет пользователю {$user->name}.");
    }

    /**
     * Список всех товаров с возможностью поиска
     */
    public function productsIndex(Request $request)
    {
        $search   = $request->input('search');
        $sort     = $request->input('sort', 'newest');
        $category = $request->input('category', 'all');

        $categories = Category::all();

        $query = Product::with('category');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category !== 'all') {
            $query->whereHas('category', fn($q) => $q->where('name', 'like', "%{$category}%"));
        }

        $query->when($sort === 'oldest',     fn($q) => $q->oldest())
              ->when($sort === 'price_asc',  fn($q) => $q->orderBy('price'))
              ->when($sort === 'price_desc', fn($q) => $q->orderByDesc('price'))
              ->when($sort === 'stock',      fn($q) => $q->orderByDesc('quantity'))
              ->when(!in_array($sort, ['oldest','price_asc','price_desc','stock']), fn($q) => $q->latest());

        $products = $query->paginate(16)->withQueryString();

        $counts = ['all' => Product::count()];
        foreach ($categories as $cat) {
            $counts[$cat->name] = $cat->products()->count();
        }

        return view('admin.products.index', compact(
            'products', 'categories', 'search', 'sort', 'category', 'counts'
        ));
    }

    /**
     * Форма создания товара (если используется отдельная страница)
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Создание нового товара и его характеристик
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_type' => 'required|string|in:mouse,keyboard,headphone,pad',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Фото обязательно при создании
        ]);

        // Передаем сырой $request, так как сервису нужен доступ к файлу и методу hasFile()
        $this->productService->store($request);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Девайс успешно добавлен в систему.');
    }

    /**
     * Форма редактирования товара (если используется отдельная страница)
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Обновление товара, его характеристик и изображения
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
            
            'sensor' => 'nullable|string',
            'max_dpi' => 'nullable|integer',
            'polling_rate' => 'nullable|integer',
            'switches' => 'nullable|string',
            'connection' => 'nullable|string',
            'battery_life' => 'nullable|integer',
            'weight' => 'nullable|integer',

            'switch_type' => 'nullable|string',
            'form_factor' => 'nullable|string',
            'keycap_material' => 'nullable|string',
            'hotswap' => 'nullable|string',
            'illumination' => 'nullable|string',
            'construction' => 'nullable|string',

            'sound_type' => 'nullable|string',
            'drivers' => 'nullable|string',
            'frequency' => 'nullable|string',
            'impedance' => 'nullable|string',
            'microphone' => 'nullable|string',

            'surface' => 'nullable|string',
            'material' => 'nullable|string',
            'base_material' => 'nullable|string',
            'dimensions' => 'nullable|string',
            'thickness' => 'nullable|string',
            'edges' => 'nullable|string'
        ]);

        // Передаем $request, чтобы внутри сервиса вытащить загруженный файл
        $this->productService->update($product, $request);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Данные девайса успешно обновлены.');
    }

    /**
     * Полное удаление девайса и связанных спецификаций
     */
    public function destroy(Product $product)
    {
        $this->productService->destroy($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Девайс удален из каталога.');
    }

    /**
     * Удаление пользователя админом
     */
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