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
            'Клавиатуры' => Category::create(['name' => 'Клавиатуры']),
            'Мыши'       => Category::create(['name' => 'Мыши']),
            'Наушники'   => Category::create(['name' => 'Наушники']),
            'Ковры'      => Category::create(['name' => 'Ковры']),
        ];

        $products = [
            ['name' => 'ZaryaTech Механическая клавиатура ZK-87', 'price' => 6900, 'cat' => 'Клавиатуры'],
            ['name' => 'Volna Беспроводная клавиатура VLK-300', 'price' => 4100, 'cat' => 'Клавиатуры'],
            ['name' => 'SibirKey Игровая клавиатура SK-RGB', 'price' => 5800, 'cat' => 'Клавиатуры'],

            ['name' => 'Taiga Devices Игровая мышь TD-GM1', 'price' => 3200, 'cat' => 'Мыши'],
            ['name' => 'Volna Беспроводная мышь VM-200', 'price' => 1700, 'cat' => 'Мыши'],
            ['name' => 'UralTech Эргономичная мышь UT-Vertical', 'price' => 2900, 'cat' => 'Мыши'],

            ['name' => 'ZaryaSound Накладные наушники ZS-500', 'price' => 7200, 'cat' => 'Наушники'],
            ['name' => 'Baikal Audio Беспроводные наушники BA-Air', 'price' => 6500, 'cat' => 'Наушники'],
            ['name' => 'SibirSound Игровая гарнитура SS-Pro', 'price' => 4800, 'cat' => 'Наушники'],

            ['name' => 'Taiga Pad Игровой коврик XL', 'price' => 1400, 'cat' => 'Ковры'],
            ['name' => 'Ural Surface Коврик с подсветкой US-RGB', 'price' => 2100, 'cat' => 'Ковры'],
            ['name' => 'Volna Minimal Коврик черный', 'price' => 800, 'cat' => 'Ковры'],
        ];

        foreach ($products as $item) {
            Product::create([
                'name' => $item['name'],
                'price' => $item['price'],
                'category_id' => $categories[$item['cat']]->id,
            ]);
        }
    }
}