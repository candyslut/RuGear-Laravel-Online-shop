<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'     => 'admin',
            'email'    => 'admin@mail.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'coins' => 5000,
        ]);

        $achievement = Achievement::where('slug', 'registered')->first();

        if ($achievement) {
            $admin->awardAchievement($achievement);
        }
    }
}
