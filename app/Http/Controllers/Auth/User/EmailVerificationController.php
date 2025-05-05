<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Affiche la page de vérification si l'email n'est pas vérifié.
     */
    public function notice(Request $request)
    {
        if ($request->user('web') && $request->user('web')->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        return view('User.user-verify-email');
    }

    /**
     * Vérifie l'email via le lien et redirige vers le dashboard.
     */
    public function verify($id, $hash, Request $request): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Lien de vérification invalide.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified(); // Définit email_verified_at
            Auth::guard('web')->login($user);
        }

        return redirect()->route('dashboard'); // Redirection vers dashboard
    }

    /**
     * Renvoie un email de vérification.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->user('web')->sendEmailVerificationNotification();
        return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
    }
}