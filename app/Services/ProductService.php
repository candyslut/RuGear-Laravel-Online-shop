<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Models\Spec\PadSpecification;
use App\Models\Spec\MouseSpecification;
use App\Models\Spec\KeyboardSpecification;
use App\Models\Spec\HeadphoneSpecification;

class ProductService
{
    public function store(Request $request): void
    {
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
        $product->update(
            $request->only([
                'name',
                'price',
                'description'
            ])
        );

        if ($product->specification) {

            $specFields = $request->only([
                'sensor',
                'max_dpi',
                'polling_rate',
                'switches',
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

            $product->specification->update(
                array_filter(
                    $specFields,
                    fn($value) => !is_null($value)
                )
            );
        }
    }

    public function destroy(Product $product): void
    {
        if ($product->specification) {
            $product->specification()->delete();
        }

        $product->delete();
    }

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