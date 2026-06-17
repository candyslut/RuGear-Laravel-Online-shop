<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'      => 'admin',
            'full_name' => 'Администратор RuGear',
            'email'     => 'admin@mail.com',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
        ]);

        // coins, level/experience и поля стрика не входят в #[Fillable],
        // поэтому задаём их напрямую (через create() они молча отбрасываются).
        // Стрик уже на 43-м дне: засчитан сегодня (следующий вход — завтра).
        $admin->coins            = 5000;
        $admin->streak           = 43;
        $admin->best_streak      = 43;
        $admin->last_active_date = Carbon::today();
        $admin->save();

        // Базовая ачивка за регистрацию + уже пройденные вехи серии входов
        // (7 и 30 дней; 180 ещё впереди).
        foreach (['registered', 'streak_7', 'streak_30'] as $slug) {
            $achievement = Achievement::where('slug', $slug)->first();

            if ($achievement) {
                $admin->awardAchievement($achievement);
            }
        }

        // Фиксируем 100-й уровень ПОСЛЕ выдачи ачивок: awardAchievement()
        // через addExperience() пересчитывает level из experience и иначе
        // сбросил бы заданный уровень. experience выравниваем по порогу 100-го
        // уровня (100 XP за уровень), чтобы шкала прогресса в HUD читалась чисто.
        $admin->level      = 100;
        $admin->experience = 9900;
        $admin->save();
    }
}
