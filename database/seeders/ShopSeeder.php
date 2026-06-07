<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ── Avatar borders ─────────────────────────────────────
            [
                'slug'        => 'border_orange',
                'category'    => 'border',
                'name'        => 'Оранжевый огонь',
                'description' => 'Классика RuGear',
                'price'       => 50,
                'css_value'   => '0 0 0 3px #f97316,0 0 10px rgba(249,115,22,0.45)',
            ],
            [
                'slug'        => 'border_blue',
                'category'    => 'border',
                'name'        => 'Синий неон',
                'description' => 'Холодное свечение',
                'price'       => 80,
                'css_value'   => '0 0 0 3px #38bdf8,0 0 14px rgba(56,189,248,0.5)',
            ],
            [
                'slug'        => 'border_purple',
                'category'    => 'border',
                'name'        => 'Фиолетовый неон',
                'description' => 'Мистический ореол',
                'price'       => 120,
                'css_value'   => '0 0 0 3px #a855f7,0 0 14px rgba(168,85,247,0.5)',
            ],
            [
                'slug'        => 'border_gold',
                'category'    => 'border',
                'name'        => 'Золото',
                'description' => 'Для настоящих китов',
                'price'       => 200,
                'css_value'   => '0 0 0 3px #fbbf24,0 0 18px rgba(251,191,36,0.65)',
            ],
            [
                'slug'        => 'border_rainbow',
                'category'    => 'border',
                'name'        => 'Радуга',
                'description' => 'Анимированное переливание — выделяйся',
                'price'       => 400,
                'css_value'   => 'rainbow',
            ],

            // ── Nickname colors ────────────────────────────────────
            [
                'slug'        => 'nick_blue',
                'category'    => 'nickname_color',
                'name'        => 'Синий',
                'description' => 'Спокойно и уверенно',
                'price'       => 50,
                'css_value'   => '#60a5fa',
            ],
            [
                'slug'        => 'nick_green',
                'category'    => 'nickname_color',
                'name'        => 'Зелёный неон',
                'description' => 'Хакерский стиль',
                'price'       => 75,
                'css_value'   => '#4ade80',
            ],
            [
                'slug'        => 'nick_cyan',
                'category'    => 'nickname_color',
                'name'        => 'Циан',
                'description' => 'Стиль Buzzword Blast',
                'price'       => 100,
                'css_value'   => '#20f8c0',
            ],
            [
                'slug'        => 'nick_purple',
                'category'    => 'nickname_color',
                'name'        => 'Фиолетовый',
                'description' => 'Загадочный и редкий',
                'price'       => 130,
                'css_value'   => '#c084fc',
            ],
            [
                'slug'        => 'nick_red',
                'category'    => 'nickname_color',
                'name'        => 'Красный',
                'description' => 'Агрессивно и ярко',
                'price'       => 160,
                'css_value'   => '#f87171',
            ],
            [
                'slug'        => 'nick_gold',
                'category'    => 'nickname_color',
                'name'        => 'Золотой',
                'description' => 'Только для элиты',
                'price'       => 250,
                'css_value'   => '#fbbf24',
            ],
        ];

        // Шрифты убраны из мини-маркета — удаляем ранее посеянные записи
        // (pivot user_shop_items чистится каскадом) и сбрасываем надетые шрифты
        ShopItem::where('category', 'font')->delete();
        User::whereNotNull('cosmetic_font')->update(['cosmetic_font' => null]);

        foreach ($items as $data) {
            ShopItem::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
