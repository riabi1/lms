<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticatedSessionController extends Controller
{
  public function create()
  {
    return view('auth.admin-login');
  }

  public function store(AdminLoginRequest $request): RedirectResponse
  {
    $credentials = $request->only('email', 'password');
    if (Auth::guard('admin')->attempt($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended(route('admin.dashboard'));
    }
    return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
  }

  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/admin/login');
  }
}
