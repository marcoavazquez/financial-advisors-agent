<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile', 'https://www.googleapis.com/auth/calendar'])
            ->redirect();
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();

            $authUser = User::firstOrCreate([
                'google_id' => $user->getId(),
            ], [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'google_token' => $user->token,
                'google_refresh_token' => $user->refreshToken,
                'google_token_expires_at' => now()->addSeconds($user->expiresIn),
            ]);

            Auth::login($authUser);
            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Failed to login with Google.']);
        }
    }
}
