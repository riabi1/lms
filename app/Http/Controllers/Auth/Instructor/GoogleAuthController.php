<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirige vers la page OAuth de Google avec l'URL instructor.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('instructor.social.google.callback')) // URL spécifique pour instructors
            ->redirect();
    }

    /**
     * Gère le callback Google, vérifie automatiquement l'email, et redirige vers instructor/dashboard.
     */
    public function handleGoogleCallback()
    {
        try {
            // Récupérer les informations de l'utilisateur Google avec l'URL instructor
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('instructor.social.google.callback'))
                ->user();

            // Enregistrer ou mettre à jour dans la table 'instructors'
            $instructor = Instructor::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?? 'Unknown',
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(), // Vérification automatique
                    'google_id' => $googleUser->getId(),
                ]
            );

            // Forcer la vérification si nécessaire
            if (!$instructor->hasVerifiedEmail()) {
                $instructor->email_verified_at = now();
                $instructor->save();
            }

            // Connexion avec le guard 'instructor'
            Auth::guard('instructor')->login($instructor, true);

            // Redirection vers le dashboard instructor
            return redirect()->route('instructor.dashboard');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('instructor.login')->with('error', 'Google authentication failed due to invalid state.');
        } catch (\Exception $e) {
            return redirect()->route('instructor.login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}