<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
  /**
   * Display the user's profile form.
   */
  public function edit(Request $request): View
  {
    return view('profile.edit', [
      'user' => $request->user(),
    ]);
  }

  /**
   * Update the user's name.
   */
  public function update(Request $request): RedirectResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    $user = $request->user();
    $user->name = $request->input('name');
    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
  }

  /**
   * Update the user's password.
   */
  public function updatePassword(Request $request): RedirectResponse
  {
    $request->validate([
      'current_password' => ['required', 'string', 'current_password:web'],
      'password' => ['required', 'string', Password::defaults(), 'confirmed'],
    ]);

    $user = $request->user();
    $user->password = Hash::make($request->input('password'));
    $user->save();

    return Redirect::route('profile.edit')->with('status', 'password-updated');
  }

}
