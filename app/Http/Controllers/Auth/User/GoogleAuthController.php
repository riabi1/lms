<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google’s OAuth page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google, auto-verify email, and redirect to dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Récupérer les informations de l'utilisateur depuis Google
            $googleUser = Socialite::driver('google')->user();

            // Créer ou mettre à jour l'utilisateur avec email vérifié
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?? 'Unknown',
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(), // Email vérifié automatiquement
                    'google_id' => $googleUser->getId(),
                ]
            );

            // Vérifier explicitement que l'email est marqué comme vérifié
            if (!$user->hasVerifiedEmail()) {
                $user->email_verified_at = now();
                $user->save();
            }

            // Connecter l'utilisateur avec le guard 'web'
            Auth::guard('web')->login($user, true);

            // Rediriger directement vers le tableau de bord
            return redirect()->route('dashboard');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login')->with('error', 'Google authentication failed due to invalid state.');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}