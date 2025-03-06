<?php

namespace App\Http\Controllers\Auth;

use App\Models\Instructor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class InstructorRegisteredUserController extends Controller
{
  //
  /**
   * Display the instructor registration view.
   */
  public function create()
  {
    return view('auth.instructor-register');
  }

  /**
   * Handle an incoming instructor registration request.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:instructors'],
      'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $instructor = Instructor::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    event(new Registered($instructor));

    Auth::guard('instructor')->login($instructor);

    // Do not log in immediately; redirect to verification notice
    return redirect()->route('instructor.verification.notice');
  }
}
