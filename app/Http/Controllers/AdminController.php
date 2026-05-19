<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::withCount('tickets')->latest()->paginate(12);

        return view('admin.users', compact('users'));
    }

    public function productsIndex()
    {
        $products = Product::with('category')->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
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

        $categoryName = match ($request->category_type) {
            'mouse' => 'Мыши',
            'keyboard' => 'Клавиатуры',
            'headphone' => 'Наушники',
            'pad' => 'Ковры',
        };
        $category = Category::where('name', $categoryName)->first();

        $product = new Product([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $category->id,
            'image' => 'images/products/' . $request->category_type . '.png',
        ]);

        $specification = $this->createSpecification($request->category_type, $request);

        if ($specification) {
            $product->specification()->associate($specification);
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Девайс успешно добавлен в систему.');
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

        $product->update($request->only(['name', 'price', 'description']));

        if ($product->specification) {
            // Массив полей, которые мы ожидаем для обновления спецификаций
            $specFields = $request->only([
                'sensor',
                'max_dpi',
                'polling_rate',
                'switches',
                'battery_life',
                'weight', // мышь
                'switch_type',
                'form_factor',
                'keycap_material',
                'hotswap',
                'illumination',
                'construction', // клава
                'sound_type',
                'drivers',
                'frequency',
                'impedance',
                'microphone', // уши
                'surface',
                'material',
                'base_material',
                'dimensions',
                'thickness',
                'edges' // коврик
            ]);

            // Отфильтруем null-значения, чтобы не затереть то, чего нет в текущей форме
            $product->specification->update(array_filter($specFields, fn($value) => !is_null($value)));
        }

        return redirect()->route('admin.products.index')->with('success', 'Данные девайса успешно обновлены прямо из списка.');
    }

    public function destroy(Product $product)
    {
        if ($product->specification) {
            $product->specification()->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Девайс удален из каталога.');
    }

    public function deleteUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Вы не можете удалить собственный аккаунт.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', "Пользователь {$user->name} успешно удален.");
    }

    private function createSpecification($type, Request $request)
    {
        return match ($type) {
            'mouse' => \App\Models\Spec\MouseSpecification::create($request->only(['sensor', 'max_dpi', 'polling_rate', 'switches', 'connection', 'battery_life', 'weight'])),
            'keyboard' => \App\Models\Spec\KeyboardSpecification::create($request->only(['switch_type', 'form_factor', 'keycap_material', 'hotswap', 'connection', 'illumination', 'construction'])),
            'headphone' => \App\Models\Spec\HeadphoneSpecification::create($request->only(['sound_type', 'drivers', 'frequency', 'impedance', 'connection', 'microphone', 'battery_life'])),
            'pad' => \App\Models\Spec\PadSpecification::create($request->only(['surface', 'material', 'base_material', 'dimensions', 'thickness', 'edges'])),
            default => null,
        };
    }
}
