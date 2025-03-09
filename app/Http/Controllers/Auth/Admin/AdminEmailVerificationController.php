<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AdminEmailVerificationController extends Controller
{
  public function notice(Request $request)
  {
    if ($request->user('admin') && $request->user('admin')->hasVerifiedEmail()) {
      return redirect()->route('admin.dashboard');
    }
    return view('admin.admin-verify-email');
  }

  public function verify($id, $hash, Request $request): RedirectResponse
  {
    $admin = Admin::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
      return redirect()->route('admin.login')->with('error', 'Lien de vérification invalide.');
    }
    if (!$admin->hasVerifiedEmail()) {
      $admin->markEmailAsVerified();
      Auth::guard('admin')->login($admin); // Explicitly log in with 'admin' guard
    }
    return redirect()->route('admin.verification.notice');
  }

  public function resend(Request $request): RedirectResponse
  {
    $request->user('admin')->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé.');
  }
}
