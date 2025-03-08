<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminEmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice()
    {
        return view('auth.admin-verify-email');
    }

    /**
     * Handle the email verification for admins.
     */
    public function verify($id, $hash, Request $request): RedirectResponse
    {
        $admin = Admin::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
            return redirect()->route('admin.login')->with('error', 'Lien de vérification invalide.');
        }
        if ($admin->hasVerifiedEmail()) {
            return redirect()->route('admin.login')->with('message', 'Email déjà vérifié.');
        }
        $admin->markEmailAsVerified();
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login')->with('message', 'Email vérifié avec succès. Veuillez vous connecter.');
    }
}
