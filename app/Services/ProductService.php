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

        // Логика сохранения загруженного изображения
        $imagePath = 'images/products/default.png'; // Дефолт на крайний случай
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Генерируем уникальное имя файла, чтобы избежать перезаписи при одинаковых названиях
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Переносим файл в публичную директорию public/images/products/
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

        // Создаем пустую спецификацию на основе выбранного типа категории
        $specification = $this->createSpecification(
            $request->category_type,
            $request
        );

        if ($specification) {
            $product->specification()->associate($specification);
        }

        $product->save();
    }

    /**
     * Обновление основных полей девайса, замена картинки и обновление характеристик
     */
    public function update(Product $product, Request $request): void
    {
        // Вытаскиваем только базовые поля для самой таблицы products
        $productData = $request->only(['name', 'price', 'quantity', 'description']);

        // Если админ загрузил новую картинку
        if ($request->hasFile('image')) {
            // Удаляем старый файл с диска, если он существует и это не дефолтная заглушка
            if ($product->image && $product->image !== 'images/products/default.png' && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }

            // Сохраняем новое изображение
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/products'), $filename);

            $productData['image'] = 'images/products/' . $filename;
        }

        // Синхронизируем базовые данные в БД
        $product->update($productData);

        // Если у товара уже создана связь со спецификацией — обновляем её поля
        if ($product->specification) {

            // Собираем массив абсолютно всех возможных характеристик девайсов
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

            // Фильтруем строго через замыкание, отсекая исключительно null.
            // Пустые строки и значения "0" (например, вес или батарея) успешно пройдут дальше.
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
        // Зачищаем файл изображения девайса с сервера
        if ($product->image && $product->image !== 'images/products/default.png' && file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }

        // Удаляем запись из дочерней таблицы характеристик
        if ($product->specification) {
            $product->specification()->delete();
        }

        // Удаляем сам продукт
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
