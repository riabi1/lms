<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
  public function notice(Request $request)
  {
    if ($request->user('web') && $request->user('web')->hasVerifiedEmail()) {
      return redirect()->route('dashboard');
    }
    return view('frontend.dashboard.user-verify-email');
  }

  public function verify($id, $hash, Request $request): RedirectResponse
  {
    $user = User::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
      return redirect()->route('user.login')->with('error', 'Lien de vérification invalide.');
    }
    if (!$user->hasVerifiedEmail()) {
      $user->markEmailAsVerified();
      Auth::guard('web')->login($user); // Explicitly log in with 'web' guard
    }
    return redirect()->route('verification.notice');
  }

  public function resend(Request $request): RedirectResponse
  {
    $request->user('web')->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
  }
}
