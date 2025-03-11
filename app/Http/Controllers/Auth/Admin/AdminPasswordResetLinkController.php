<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class AdminPasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('admin.admin-forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Password reset link sent successfully!')
            : back()->withErrors(['email' => __($status)]);
    }
}