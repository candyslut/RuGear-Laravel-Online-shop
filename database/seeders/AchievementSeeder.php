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

            // ── Серия входов (streak) ──
            [
                'slug'        => 'streak_7',
                'title'       => 'Неделя в строю',
                'description' => 'Вы заходили семь дней подряд. Отличная привычка!',
                'experience'  => 40,
                'coins'       => 20,
                'icon'        => null,
            ],
            [
                'slug'        => 'streak_30',
                'title'       => 'Месяц без пропусков',
                'description' => 'Серия входов длиной в 30 дней. Железная дисциплина!',
                'experience'  => 90,
                'coins'       => 45,
                'icon'        => null,
            ],
            [
                'slug'        => 'streak_180',
                'title'       => 'Полгода преданности',
                'description' => 'Вы продержали серию входов целых 180 дней — главный приз заслужен!',
                'experience'  => 250,
                'coins'       => 150,
                'icon'        => null,
            ],

            // ── Лайки: за активность (вы ставите лайки) ──
            [
                'slug'        => 'like_giver_1',
                'title'       => 'Первый лайк',
                'description' => 'Вы впервые отметили чужой комментарий лайком. Поддержка ценится!',
                'experience'  => 10,
                'coins'       => 5,
                'icon'        => null,
            ],
            [
                'slug'        => 'like_giver_25',
                'title'       => 'Щедрый на лайки',
                'description' => 'Вы поставили 25 лайков чужим комментариям. Душа сообщества!',
                'experience'  => 40,
                'coins'       => 15,
                'icon'        => null,
            ],
            [
                'slug'        => 'like_giver_100',
                'title'       => 'Король лайков',
                'description' => 'Сотня лайков от вас — вы делаете обсуждения теплее.',
                'experience'  => 100,
                'coins'       => 50,
                'icon'        => null,
            ],

            // ── Лайки: за популярность (ваши комментарии лайкают) ──
            [
                'slug'        => 'liked_10',
                'title'       => 'Народное признание',
                'description' => 'Ваш комментарий набрал 10 лайков. Людям нравится то, что вы пишете!',
                'experience'  => 50,
                'coins'       => 20,
                'icon'        => null,
            ],
            [
                'slug'        => 'liked_50',
                'title'       => 'Голос сообщества',
                'description' => 'Один из ваших комментариев собрал 50 лайков. Настоящий хит!',
                'experience'  => 120,
                'coins'       => 60,
                'icon'        => null,
            ],

            // ── Магазин ──
            [
                'slug'        => 'store_complete',
                'title'       => 'Скупил весь магазин',
                'description' => 'Вы выкупили всё, что можно купить: каждый косметический предмет и набор стикеров. Коллекционер!',
                'experience'  => 300,
                'coins'       => 250,
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
