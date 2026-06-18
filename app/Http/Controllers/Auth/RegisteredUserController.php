<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Surface the new player in the public Live Feed (the "registered"
        // achievement is kept out of the feed to avoid a duplicate entry).
        app(\App\Services\FeedService::class)->record('registration', $user);

        // firstOrCreate: награды задаёт AchievementSeeder, существующая запись
        // не перезатирается значениями из кода.
        $achievement = Achievement::firstOrCreate(
            ['slug' => 'registered'],
            [
                'title'       => 'Первое достижение',
                'description' => 'Вы получили достижение за регистрацию на RuGear.',
                'experience'  => 50,
                'coins'       => 2,
            ]
        );

        $result = $user->awardAchievement($achievement);

        $redirect = redirect(route('dashboard', absolute: false))
            ->with('achievement_awarded', [
                'title'      => $achievement->title,
                'experience' => $achievement->experience,
                'coins'      => $achievement->coins,
            ]);

        if ($result && $result['leveled_up']) {
            $redirect = $redirect->with('level_up', [
                'level' => $result['new_level'],
                'coins' => $result['level_coins'],
            ]);
        }

        return $redirect;
    }
}
