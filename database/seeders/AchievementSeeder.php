<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        Achievement::firstOrCreate(
            ['slug' => 'registered'],
            [
                'title' => 'Первое достижение',
                'description' => 'Вы получили достижение за регистрацию на RuGear.',
                'experience' => 50,
                'icon' => null,
            ]
        );

        Achievement::firstOrCreate(
            ['slug' => 'comment_1'],
            [
                'title' => 'Первый комментарий',
                'description' => 'Спасибо за первый комментарий — добро пожаловать в обсуждение!',
                'experience' => 10,
                'icon' => null,
            ]
        );

        Achievement::firstOrCreate(
            ['slug' => 'comment_3'],
            [
                'title' => 'Тридцать? Нет — три комментария',
                'description' => 'Вы оставили три комментария — вы активный участник сообщества!',
                'experience' => 30,
                'icon' => null,
            ]
        );

        Achievement::firstOrCreate(
            ['slug' => 'comment_5'],
            [
                'title' => 'Пятикратный комментатор',
                'description' => 'Пять комментариев — класс! Спасибо за вклад в обсуждения.',
                'experience' => 60,
                'icon' => null,
            ]
        );

        Achievement::firstOrCreate(
            ['slug' => 'first_order'],
            [
                'title' => 'Первый заказ',
                'description' => 'Вы оформили свой первый заказ на RuGear. Добро пожаловать в семью покупателей!',
                'experience' => 100,
                'icon' => null,
            ]
        );
    }
}
