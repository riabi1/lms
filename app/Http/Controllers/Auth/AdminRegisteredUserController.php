<?php

namespace App\Http\Controllers\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AdminRegisteredUserController extends Controller
{
  //
  /**
   * Display the admin registration view.
   */
  public function create()
  {
    return view('auth.admin-register');
  }

  /**
   * Handle an incoming admin registration request.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
      'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $admin = Admin::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    event(new Registered($admin));

    Auth::guard('admin')->login($admin);
    
    // Do not log in immediately; redirect to verification notice
    return redirect()->route('admin.verification.notice');
  }
}
