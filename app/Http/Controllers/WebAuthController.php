<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user = User::query()->create($validated);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('family-chronicles.index');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
