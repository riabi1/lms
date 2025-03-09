<?php

namespace App\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('instructor.edit', [
            'user' => $request->user('instructor'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:instructors,email,' . $request->user('instructor')->id],
        ]);

        $request->user('instructor')->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('instructor.profile.edit')->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password:instructor'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $request->user('instructor')->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('instructor.profile.edit')->with('status', 'password-updated');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password:instructor'],
        ]);

        $instructor = $request->user('instructor');
        $instructor->delete();

        auth()->guard('instructor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}