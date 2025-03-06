<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticatedSessionController extends Controller
{
  //
  public function create()
  {
    return view('auth.admin-login'); // Create this view
  }

  public function store(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (Auth::guard('admin')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
      $request->session()->regenerate();
      return redirect()->intended(route('admin.dashboard')); // Define this route
    }

    return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ]);
  }

  public function destroy(Request $request)
  {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/admin/login');
  }
}
