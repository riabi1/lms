<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\Request;

class AdminAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.admin-login');
    }

    public function store(AdminLoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        return $request->user('admin')->hasVerifiedEmail()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.verification.notice');
    }

    public function destroy(Request $request)
    {
        auth()->guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}