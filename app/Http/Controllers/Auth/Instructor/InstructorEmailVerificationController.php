<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InstructorEmailVerificationController extends Controller
{
  public function notice(Request $request)
  {
    if ($request->user('instructor') && $request->user('instructor')->hasVerifiedEmail()) {
      return redirect()->route('instructor.dashboard');
    }
    return view('auth.instructor-verify-email');
  }

  public function verify($id, $hash, Request $request): RedirectResponse
  {
    $instructor = Instructor::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($instructor->getEmailForVerification()))) {
      return redirect()->route('instructor.login')->with('error', 'Lien de vérification invalide.');
    }
    if (!$instructor->hasVerifiedEmail()) {
      $instructor->markEmailAsVerified();
      Auth::guard('instructor')->login($instructor); // Explicitly log in with 'instructor' guard
    }
    return redirect()->route('instructor.verification.notice');
  }

  public function resend(Request $request): RedirectResponse
  {
    $request->user('instructor')->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé.');
  }
}
