<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
  /**
   * Display the admin profile edit form.
   */
  public function edit(Request $request)
  {
    return view('auth.admin-profile-edit', [
      'admin' => $request->user('admin'),
    ]);
  }

  /**
   * Update the admin's profile information (name).
   */
  public function update(Request $request): RedirectResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    $admin = $request->user('admin');
    $admin->name = $request->input('name');
    $admin->save();

    return redirect()->route('admin.profile.edit')->with('status', 'Profil mis à jour avec succès.');
  }

  /**
   * Update the admin's password.
   */
  public function updatePassword(Request $request): RedirectResponse
  {
    $request->validate([
      'current_password' => ['required', 'string', 'current_password:admin'],
      'password' => ['required', 'string', Password::defaults(), 'confirmed'],
    ]);

    $admin = $request->user('admin');
    $admin->password = Hash::make($request->input('password'));
    $admin->save();

    return redirect()->route('admin.profile.edit')->with('status', 'Mot de passe mis à jour avec succès.');
  }

}
