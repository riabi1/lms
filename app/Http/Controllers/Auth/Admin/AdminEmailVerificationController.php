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
        if ($request->user('admin')->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard')
                ->with('message', 'Email already verified.');
        }
        return view('admin.admin-verify-email');
    }

    public function verify($id, $hash, Request $request): RedirectResponse
    {
        $admin = Admin::findOrFail($id);
        if (!hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
            return redirect()->route('admin.login')->with('error', 'Invalid verification link.');
        }
        if (!$admin->hasVerifiedEmail()) {
            $admin->markEmailAsVerified();
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();
        }
        return redirect()->route('admin.dashboard')
            ->with('message', 'Email verified successfully!');
    }

    public function resend(Request $request): RedirectResponse
    {
        $request->user('admin')->sendEmailVerificationNotification();
        return back()->with('message', 'A new verification link has been sent.');
    }
}