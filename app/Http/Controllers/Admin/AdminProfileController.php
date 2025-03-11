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
    public function edit(Request $request): \Illuminate\Contracts\View\View
    {
        return view('auth.admin-profile-edit', [
            'admin' => $request->user('admin'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $admin = $request->user('admin');
        $admin->update(['name' => $request->input('name')]);

        return redirect()->route('admin.profile.edit')->with('status', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password:admin'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $admin = $request->user('admin');
        $admin->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->route('admin.profile.edit')->with('status', 'Mot de passe mis à jour avec succès.');
    }
}