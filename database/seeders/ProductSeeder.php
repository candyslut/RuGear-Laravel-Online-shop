<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Клавиатуры' => [
                'model' => Category::create(['name' => 'Клавиатуры']),
                'image' => 'images/products/keyboard.png',
                'prefix' => ['ZaryaTech', 'Volna', 'SibirKey', 'UralBoard', 'Nordic'],
                'min_price' => 4000,
                'max_price' => 12000
            ],
            'Мыши' => [
                'model' => Category::create(['name' => 'Мыши']),
                'image' => 'images/products/mouse.png',
                'prefix' => ['Taiga', 'Volna', 'UralTech', 'SibirClick', 'Zarya'],
                'min_price' => 1500,
                'max_price' => 7000
            ],
            'Наушники' => [
                'model' => Category::create(['name' => 'Наушники']),
                'image' => 'images/products/earphones.png',
                'prefix' => ['ZaryaSound', 'Baikal', 'SibirSound', 'UralAudio', 'Echo'],
                'min_price' => 3500,
                'max_price' => 15000
            ],
            'Ковры' => [
                'model' => Category::create(['name' => 'Ковры']),
                'image' => 'images/products/cover.png',
                'prefix' => ['Taiga Pad', 'Ural Surface', 'Volna Minimal', 'SibirMat', 'Glow'],
                'min_price' => 800,
                'max_price' => 3000
            ],
        ];

        foreach ($categories as $catName => $data) {
            for ($i = 1; $i <= 10; $i++) {
                $brand = $data['prefix'][array_rand($data['prefix'])];
                
                Product::create([
                    'name' => "$brand " . $this->getSuffix($catName) . " #$i",
                    'price' => rand($data['min_price'], $data['max_price']),
                    'image' => $data['image'],
                    'category_id' => $data['model']->id,
                ]);
            }
        }
    }

    /**
     */
    private function getSuffix($category): string
    {
        return match ($category) {
            'Клавиатуры' => 'Mechanical K-' . rand(100, 999),
            'Мыши'       => 'Gaming Mouse M-' . rand(10, 99),
            'Наушники'   => 'Wireless Headset H-' . rand(500, 800),
            'Ковры'      => 'Control Pad Pro ' . rand(1, 5),
            default      => 'Device',
        };
    }
}