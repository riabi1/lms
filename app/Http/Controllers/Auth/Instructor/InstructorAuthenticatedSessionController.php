<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\InstructorLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InstructorAuthenticatedSessionController extends Controller
{
  public function create()
  {
    return view('auth.instructor-login');
  }

  public function store(InstructorLoginRequest $request): RedirectResponse
  {
    $credentials = $request->only('email', 'password');
    if (Auth::guard('instructor')->attempt($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended(route('instructor.dashboard'));
    }
    return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
  }

  public function destroy(Request $request): RedirectResponse
  {
    Auth::guard('instructor')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/instructor/login');
  }
}
