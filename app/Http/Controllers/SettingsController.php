<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'in:male,female'],
            'about'  => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Unchecked checkbox isn't submitted — resolve it explicitly so toggling
        // the game-stats privacy off actually persists.
        $data['stats_public'] = $request->boolean('stats_public');

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        return redirect()->route('settings.edit')->with('success', 'Настройки сохранены');
    }
}
