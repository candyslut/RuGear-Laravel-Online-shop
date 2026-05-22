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
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $registrationAchievement = Achievement::firstOrCreate(
            ['slug' => 'registered'],
            [
                'title' => 'Первое достижение',
                'description' => 'Вы получили достижение за регистрацию на RuGear.',
                'experience' => 50,
            ]
        );

        $user->awardAchievement($registrationAchievement);

        return redirect(route('dashboard', absolute: false))
            ->with('achievement_awarded', [
                'title' => $registrationAchievement->title,
                'description' => $registrationAchievement->description,
                'experience' => $registrationAchievement->experience,
            ]);
    }
}
