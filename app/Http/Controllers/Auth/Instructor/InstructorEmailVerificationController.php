<?php

namespace App\Http\Controllers\Auth\Instructor;
use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class InstructorEmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice()
    {
        return view('auth.instructor-verify-email');
    }

    /**
     * Handle the email verification for instructors.
     */
    public function verify($id, $hash, Request $request): RedirectResponse
    {
        $instructor = Instructor::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($instructor->getEmailForVerification()))) {
            return redirect()->route('instructor.login')->with('error', 'Lien de vérification invalide.');
        }
        if ($instructor->hasVerifiedEmail()) {
            return redirect()->route('instructor.login')->with('message', 'Email déjà vérifié.');
        }
        $instructor->markEmailAsVerified();
        auth()->guard('instructor')->logout();
        return redirect()->route('instructor.login')->with('message', 'Email vérifié avec succès. Veuillez vous connecter.');
    }
}