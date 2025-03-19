<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InstructorEmailVerificationController extends Controller
{
    /**
     * Affiche la page de vérification si l'email n'est pas vérifié.
     */
    public function notice(Request $request)
    {
        if ($request->user('instructor') && $request->user('instructor')->hasVerifiedEmail()) {
            return redirect()->route('instructor.dashboard')->with('message', 'Email already verified.');
        }
        return view('instructor.instructor-verify-email');
    }

    /**
     * Vérifie l'email via le lien et redirige vers le dashboard.
     */
    public function verify($id, $hash, Request $request): RedirectResponse
    {
        $instructor = Instructor::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($instructor->getEmailForVerification()))) {
            return redirect()->route('instructor.login')->with('error', 'Lien de vérification invalide.');
        }

        if (!$instructor->hasVerifiedEmail()) {
            $instructor->markEmailAsVerified();
            Auth::guard('instructor')->login($instructor);
            $request->session()->regenerate();
        }

        // Corrigé : Redirige vers 'instructor.dashboard' au lieu de 'instructor.verification.notice'
        return redirect()->route('instructor.dashboard')->with('message', 'Email vérifié avec succès !');
    }

    /**
     * Renvoie un email de vérification.
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->user('instructor')->sendEmailVerificationNotification();
        return back()->with('message', 'Un nouveau lien de vérification a été envoyé.');
    }
}