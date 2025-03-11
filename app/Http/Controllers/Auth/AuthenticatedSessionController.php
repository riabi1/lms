<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
  public function create()
  {
    return view('frontend.dashboard.login');
  }
  
  public function store(LoginRequest $request): RedirectResponse
  {
    $credentials = $request->only('email', 'password');
    if (Auth::guard('web')->attempt($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
  }

  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
  }
}
