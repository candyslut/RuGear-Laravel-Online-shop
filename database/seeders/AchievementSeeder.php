<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'slug'        => 'order_10k',
                'title'       => 'Крупная покупка',
                'description' => 'Вы оформили заказ на сумму от 10 000 ₽. Серьёзный подход!',
                'experience'  => 75,
                'coins'       => 40,
                'icon'        => null,
            ],
            [
                'slug'        => 'order_50k',
                'title'       => 'Кит',
                'description' => 'Заказ на 50 000 ₽ и выше. Вы настоящий энтузиаст!',
                'experience'  => 200,
                'coins'       => 150,
                'icon'        => null,
            ],
            [
                'slug'        => 'all_categories',
                'title'       => 'Полный арсенал',
                'description' => 'Вы заказали товары из всех категорий: клавиатуру, мышь, наушники и коврик.',
                'experience'  => 150,
                'coins'       => 75,
                'icon'        => null,
            ],
            [
                'slug'        => 'registered',
                'title'       => 'Первое достижение',
                'description' => 'Вы получили достижение за регистрацию на RuGear.',
                'experience'  => 50,
                'coins'       => 5,
                'icon'        => null,
            ],
            [
                'slug'        => 'comment_1',
                'title'       => 'Первый комментарий',
                'description' => 'Спасибо за первый комментарий — добро пожаловать в обсуждение!',
                'experience'  => 10,
                'coins'       => 5,
                'icon'        => null,
            ],
            [
                'slug'        => 'comment_3',
                'title'       => 'Тридцать? Нет — три комментария',
                'description' => 'Вы оставили три комментария — вы активный участник сообщества!',
                'experience'  => 30,
                'coins'       => 10,
                'icon'        => null,
            ],
            [
                'slug'        => 'comment_5',
                'title'       => 'Пятикратный комментатор',
                'description' => 'Пять комментариев — класс! Спасибо за вклад в обсуждения.',
                'experience'  => 60,
                'coins'       => 20,
                'icon'        => null,
            ],
            [
                'slug'        => 'first_order',
                'title'       => 'Первый заказ',
                'description' => 'Вы оформили свой первый заказ на RuGear. Добро пожаловать в семью покупателей!',
                'experience'  => 100,
                'coins'       => 25,
                'icon'        => null,
            ],
        ];

        foreach ($achievements as $data) {
            Achievement::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
