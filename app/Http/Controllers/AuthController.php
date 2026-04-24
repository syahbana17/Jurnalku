<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Redirect ke Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Callback dari Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }

        // Cari atau buat user berdasarkan google_id
        // Cari user by google_id, kalau tidak ada cari by email, kalau tidak ada buat baru
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                // User sudah ada via email, link google_id-nya
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'google_id' => $googleUser->getId(),
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'avatar'    => $googleUser->getAvatar(),
                    'password'  => \Illuminate\Support\Facades\Hash::make(uniqid('', true)),
                ]);
            }
        } else {
            // Update avatar kalau berubah
            $user->update(['avatar' => $googleUser->getAvatar()]);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
