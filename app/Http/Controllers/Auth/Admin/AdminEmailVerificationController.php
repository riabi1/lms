<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminEmailVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->only(['notice', 'resend']);
    }

    public function notice(Request $request)
    {
        if ($request->user('admin')->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-verify-email');
    }

    public function verify($id, $hash, Request $request): RedirectResponse
    {
        $admin = Admin::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
            return redirect()->route('admin.login')->with('error', 'Lien de vérification invalide.');
        }
        if (!$admin->hasVerifiedEmail()) {
            $admin->markEmailAsVerified();
            auth()->guard('admin')->login($admin);
        }
        return redirect()->route('admin.verification.notice');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->user('admin')->sendEmailVerificationNotification();
        return back()->with('message', 'Un nouveau lien de vérification a été envoyé.');
    }
}