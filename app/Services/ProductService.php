<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Models\Spec\PadSpecification;
use App\Models\Spec\MouseSpecification;
use App\Models\Spec\KeyboardSpecification;
use App\Models\Spec\HeadphoneSpecification;

class ProductService
{
    /**
     * Создание нового товара, загрузка изображения и привязка характеристик
     */
    public function store(Request $request): void
    {
        $categoryName = match ($request->category_type) {
            'mouse' => 'Мыши',
            'keyboard' => 'Клавиатуры',
            'headphone' => 'Наушники',
            'pad' => 'Ковры',
            default => null
        };

        $category = Category::where('name', $categoryName)->first();

        $imagePath = 'images/products/default.png';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $filename);
            $imagePath = 'images/products/' . $filename;
        }

        $product = new Product([
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'category_id' => $category?->id,
            'image' => $imagePath,
        ]);

        $specification = $this->createSpecification(
            $request->category_type,
            $request
        );

        if ($specification) {
            $product->specification()->associate($specification);
        }

        $product->save();
    }

    public function update(Product $product, Request $request): void
    {
        $productData = $request->only(['name', 'price', 'quantity', 'description']);

        if ($request->hasFile('image')) {
            // Проверяем, что картинка не дефолтная и путь вообще заполнен
            if ($product->image && $product->image !== 'images/products/default.png') {

                // ВНИМАНИЕ: Проверяем, есть ли ДРУГИЕ товары с точно такой же картинкой
                $isShared = Product::where('id', '!=', $product->id)
                    ->where('image', $product->image)
                    ->exists();

                // Удаляем файл физически только если он больше никому не нужен
                if (!$isShared && file_exists(public_path($product->image))) {
                    @unlink(public_path($product->image));
                }
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $filename);

            $productData['image'] = 'images/products/' . $filename;
        }

        $product->update($productData);

        if ($product->specification) {

            $specFields = $request->only([
                'sensor',
                'max_dpi',
                'polling_rate',
                'switches',
                'connection',
                'battery_life',
                'weight',
                'switch_type',
                'form_factor',
                'keycap_material',
                'hotswap',
                'illumination',
                'construction',
                'sound_type',
                'drivers',
                'frequency',
                'impedance',
                'microphone',
                'surface',
                'material',
                'base_material',
                'dimensions',
                'thickness',
                'edges'
            ]);

            $filteredSpecs = array_filter(
                $specFields,
                fn($value) => !is_null($value)
            );

            $product->specification->update($filteredSpecs);
        }
    }

    /**
     * Полное удаление товара, связанных характеристик и файла изображения с диска
     */
    public function destroy(Product $product): void
    {
        // Зачищаем файл изображения девайса только если он уникален
        if ($product->image && $product->image !== 'images/products/default.png') {
            $isShared = Product::where('id', '!=', $product->id)
                ->where('image', $product->image)
                ->exists();

            if (!$isShared && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }
        }

        if ($product->specification) {
            $product->specification()->delete();
        }

        $product->delete();
    }

    /**
     * Фабричный метод для первичной генерации моделей характеристик
     */
    private function createSpecification(string $type, Request $request)
    {
        return match ($type) {
            'mouse' => MouseSpecification::create(
                $request->only([
                    'sensor',
                    'max_dpi',
                    'polling_rate',
                    'switches',
                    'connection',
                    'battery_life',
                    'weight'
                ])
            ),

            'keyboard' => KeyboardSpecification::create(
                $request->only([
                    'switch_type',
                    'form_factor',
                    'keycap_material',
                    'hotswap',
                    'connection',
                    'illumination',
                    'construction'
                ])
            ),

            'headphone' => HeadphoneSpecification::create(
                $request->only([
                    'sound_type',
                    'drivers',
                    'frequency',
                    'impedance',
                    'connection',
                    'microphone',
                    'battery_life'
                ])
            ),

            'pad' => PadSpecification::create(
                $request->only([
                    'surface',
                    'material',
                    'base_material',
                    'dimensions',
                    'thickness',
                    'edges'
                ])
            ),

            default => null,
        };
    }
}
