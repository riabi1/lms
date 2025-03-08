<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EmailVerificationController extends Controller
{
  public function notice(Request $request)
  {
    // If already verified, redirect to dashboard immediately
    if ($request->user() && $request->user()->hasVerifiedEmail()) {
      return redirect()->route('dashboard');
    }
    return view('auth.verify-email');
  }

  public function verify($id, $hash, Request $request): RedirectResponse
  {
    $user = User::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
      return redirect()->route('login')->with('error', 'Lien de vérification invalide.');
    }
    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
      auth()->login($user); // Ensure user is logged in
    }
    // Redirect back to the notice page instead of login
    return redirect()->route('verification.notice');
  }

  public function resend(Request $request): RedirectResponse
  {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
  }
}
