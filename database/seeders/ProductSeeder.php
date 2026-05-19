<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Spec\MouseSpecification;
use App\Models\Spec\KeyboardSpecification;
use App\Models\Spec\HeadphoneSpecification;
use App\Models\Spec\PadSpecification;
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

                // Генерируем количество: в 20% случаев товар закончился (0), в остальных — от 1 до 15 шт.
                $quantity = (rand(1, 100) <= 20) ? 0 : rand(1, 15);

                $product = new Product([
                    'name' => "$brand " . $this->getSuffix($catName) . " #$i",
                    'price' => rand($data['min_price'], $data['max_price']),
                    'image' => $data['image'],
                    'category_id' => $data['model']->id,
                    'quantity' => $quantity, // Передаем сгенерированное количество в модель
                    'description' => "Отличный девайс из линейки $brand. Высокое качество сборки и максимальный комфорт в использовании.",
                ]);

                $specification = match ($catName) {
                    'Мыши' => MouseSpecification::create([
                        'sensor' => ['PixArt PAW3395', 'Hero 25K', 'Focus Pro 30K'][rand(0, 2)],
                        'max_dpi' => [16000, 26000, 30000][rand(0, 2)],
                        'polling_rate' => [1000, 4000, 8000][rand(0, 2)],
                        'switches' => ['Huano Blue Shell', 'Omron 20M', 'Kailh GM 8.0'][rand(0, 2)],
                        'connection' => ['Проводное (USB)', 'Беспроводное (2.4 ГГц, Bluetooth)'][rand(0, 1)],
                        'weight' => rand(49, 75),
                        'battery_life' => rand(0, 1) ? rand(40, 90) : null,
                    ]),

                    'Клавиатуры' => KeyboardSpecification::create([
                        'switch_type' => ['Линейные (Red)', 'Тактильные (Brown)', 'Кликающие (Blue)'][rand(0, 2)],
                        'form_factor' => ['60%', '75%', 'TKL (80%)', 'Полноразмерная (100%)'][rand(0, 3)],
                        'keycap_material' => ['PBT Double-shot', 'ABS'][rand(0, 1)],
                        'hotswap' => ['Есть', 'Нет'][rand(0, 1)],
                        'connection' => ['Type-C кабель', 'Беспроводная (Съемный кабель / Радиоканал)'][rand(0, 1)],
                        'illumination' => ['RGB (Настраиваемая)', 'Однотонная белая', 'Без подсветки'][rand(0, 2)],
                        'construction' => ['Gasket Mount', 'Classic', 'Skeleton'][rand(0, 2)],
                    ]),

                    'Наушники' => HeadphoneSpecification::create([
                        'sound_type' => ['Стерео 2.0', 'Виртуальный объемный 7.1'][rand(0, 1)],
                        'drivers' => rand(40, 53) . ' мм',
                        'frequency' => '20 Гц - 20000 Гц',
                        'impedance' => [16, 32, 64][rand(0, 2)] . ' Ом',
                        'connection' => ['Радиоканал 2.4G / Bluetooth / Кабель', '3.5 мм Джек / USB'][rand(0, 1)],
                        'microphone' => ['Съемный, с шумоподавлением', 'Встроенный'][rand(0, 1)],
                        'battery_life' => rand(0, 1) ? rand(20, 50) : null,
                    ]),

                    'Ковры' => PadSpecification::create([
                        'surface' => ['Speed', 'Control', 'Hybrid'][rand(0, 2)],
                        'material' => ['Влагостойкая ткань', 'Текстурированная ткань Кордура'][rand(0, 1)],
                        'base_material' => ['Вспененная резина', 'Полиуретан (Poron)'][rand(0, 1)],
                        'dimensions' => ['450 x 400 мм', '900 x 400 мм', '360 x 300 мм'][rand(0, 2)],
                        'thickness' => rand(3, 5) . ' мм',
                        'edges' => ['Оверлок (Прошитые края)', 'Тонкий износостойкий шов'][rand(0, 1)],
                    ]),

                    default => null
                };

                if ($specification) {
                    $product->specification()->associate($specification);
                }

                $product->save();
            }
        }
    }

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
