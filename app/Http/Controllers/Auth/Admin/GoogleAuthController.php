<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirige vers la page OAuth de Google avec l'URL admin.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('admin.social.google.callback')) // URL spécifique pour admin
            ->redirect();
    }

    /**
     * Gère le callback Google, vérifie automatiquement l'email, et redirige vers admin/dashboard.
     */
    public function handleGoogleCallback()
    {
        try {
            // Récupérer les informations de l'utilisateur Google avec l'URL admin
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('admin.social.google.callback'))
                ->user();

            // Enregistrer ou mettre à jour dans la table 'admins'
            $admin = Admin::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?? 'Unknown',
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->getId(),
                ]
            );

            // Forcer la vérification si nécessaire
            if (!$admin->hasVerifiedEmail()) {
                $admin->email_verified_at = now();
                $admin->save();
            }

            // Connexion avec le guard 'admin'
            Auth::guard('admin')->login($admin, true);

            // Redirection vers le dashboard admin
            return redirect()->route('admin.dashboard');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('admin.login')->with('error', 'Google authentication failed due to invalid state.');
        } catch (\Exception $e) {
            return redirect()->route('admin.login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}