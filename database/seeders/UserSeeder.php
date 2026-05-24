<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $ach = Achievement::all()->keyBy('slug');

        // Logical achievement chains (no contradictions):
        // first_order must precede order_10k, order_10k before order_50k, all_categories
        // comment_1 before comment_3 before comment_5
        $chains = [
            ['comment_1', 'comment_3'],
            ['comment_1', 'comment_3', 'comment_5'],
            ['first_order', 'comment_1'],
            ['first_order', 'order_10k'],
            ['first_order', 'all_categories'],
            ['first_order', 'order_10k', 'all_categories'],
            ['first_order', 'order_10k', 'order_50k'],
            ['comment_1', 'comment_3', 'first_order'],
            ['comment_1', 'first_order', 'order_10k'],
            ['comment_1', 'first_order', 'all_categories'],
        ];

        $users = [
            // Males
            ['name' => 'КиберМедведь',      'gender' => 'male',   'chain' => 0, 'about' => 'Собираю кастомные сборки уже 8 лет. Клавиатуры — моя страсть.'],
            ['name' => 'ТихийСапёр',         'gender' => 'male',   'chain' => 1, 'about' => 'Тихий снаружи, громкий в игре. В поисках идеального звука.'],
            ['name' => 'РжавыйПиксель',      'gender' => 'male',   'chain' => 2, 'about' => 'Ретро-геймер из Новосибирска. Люблю всё механическое.'],
            ['name' => 'Артём_Мрак',         'gender' => 'male',   'chain' => 3, 'about' => 'Ночной стример. RGB — обязательно.'],
            ['name' => 'НеЧеловек99',        'gender' => 'male',   'chain' => 4, 'about' => 'Занимаюсь системным администрированием уже 12 лет.'],
            ['name' => 'ВасяДрон',           'gender' => 'male',   'chain' => 5, 'about' => 'FPV-пилот и геймер. Скорость — моё всё.'],
            ['name' => 'СтёпаНулевой',       'gender' => 'male',   'chain' => 6, 'about' => 'Программист фронтенда. Ценю качество и эргономику.'],
            ['name' => 'Vlad_Киборг',        'gender' => 'male',   'chain' => 7, 'about' => 'Собираю ПК на заказ. Более 200 довольных клиентов.'],
            ['name' => 'МаксНейрон',         'gender' => 'male',   'chain' => 8, 'about' => 'ML-инженер. Компьютер — рабочий инструмент и поле боя.'],
            ['name' => 'ДимонБезРук',        'gender' => 'male',   'chain' => 9, 'about' => 'Руки на самом деле есть. Просто очень быстрые.'],
            // Females
            ['name' => 'МашаГроза',          'gender' => 'female', 'chain' => 0, 'about' => 'Играю в шутеры, но выгляжу мило. Не обманывайтесь.'],
            ['name' => 'КристинаZ',          'gender' => 'female', 'chain' => 1, 'about' => 'Дизайнер интерфейсов. Обожаю минимализм и тёмные темы.'],
            ['name' => 'СнежнаяКиса',        'gender' => 'female', 'chain' => 2, 'about' => 'Коллекционирую механические клавиатуры. Уже 11 штук.'],
            ['name' => 'АнгелинаШторм',      'gender' => 'female', 'chain' => 3, 'about' => 'Стримлю по вечерам, пишу код по ночам.'],
            ['name' => 'Виолетта_Нойз',      'gender' => 'female', 'chain' => 4, 'about' => 'Музыкант и геймер. Звук для меня — всё.'],
            // Other / creative nicks
            ['name' => 'Киборг404',          'gender' => 'other',  'chain' => 5, 'about' => 'Не найден. Хотя вот он я.'],
            ['name' => 'ЗвукТишины',         'gender' => 'other',  'chain' => 6, 'about' => 'Слушаю ультразвук и читаю даташиты перед сном.'],
            ['name' => 'НоутбукДух',         'gender' => 'other',  'chain' => 7, 'about' => 'Призрак в машине. Буквально.'],
            ['name' => 'ПиксельнаяПустота',  'gender' => 'female', 'chain' => 8, 'about' => 'Арт-директор. Каждый пиксель имеет значение.'],
            ['name' => 'НейронИкс',          'gender' => 'male',   'chain' => 9, 'about' => 'Строю нейросети и разрушаю стереотипы.'],
        ];

        foreach ($users as $data) {
            $user = User::factory()->create([
                'name'   => $data['name'],
                'gender' => $data['gender'],
                'about'  => $data['about'],
            ]);

            if ($ach->has('registered')) {
                $user->awardAchievement($ach->get('registered'));
            }

            foreach ($chains[$data['chain']] as $slug) {
                if ($ach->has($slug)) {
                    $user->awardAchievement($ach->get($slug));
                }
            }
        }
    }
}
