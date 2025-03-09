<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InstructorProfileController extends Controller
{
  /**
   * Display the instructor profile edit form.
   */
  public function edit(Request $request)
  {
    return view('auth.instructor-profile-edit', [
      'instructor' => $request->user('instructor'),
    ]);
  }

  /**
   * Update the instructor's profile information (name).
   */
  public function update(Request $request): RedirectResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    $instructor = $request->user('instructor');
    $instructor->name = $request->input('name');
    $instructor->save();

    return redirect()->route('instructor.profile.edit')->with('status', 'Profil mis à jour avec succès.');
  }

  /**
   * Update the instructor's password.
   */
  public function updatePassword(Request $request): RedirectResponse
  {
    $request->validate([
      'current_password' => ['required', 'string', 'current_password:instructor'],
      'password' => ['required', 'string', Password::defaults(), 'confirmed'],
    ]);

    $instructor = $request->user('instructor');
    $instructor->password = Hash::make($request->input('password'));
    $instructor->save();

    return redirect()->route('instructor.profile.edit')->with('status', 'Mot de passe mis à jour avec succès.');
  }

}
