<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Category; // Assuming a Category model for preferences
use App\Http\Controllers\Controller;
class ProfileController extends Controller
{
    public function edit()
    {
        $profileData = Auth::user(); // Changed variable name to match Blade
        $categories = Category::all(); // Fetch categories for preferences
        return view('User.edit_profile', compact('profileData', 'categories'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:5120|mimes:jpeg,png,jpg',
                'preference' => 'nullable|array|max:3',
                'preference.*' => 'exists:categories,id',
                'grade_select' => 'required|string|max:255',
                'grade_custom' => 'nullable|string|max:255|required_if:grade_select,Other',
                'new_password' => 'nullable|string|min:8|confirmed',
            ]);

            $user = Auth::user();

            // Update basic information
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;

            // Handle grade
            $user->grade = $request->grade_select === 'Other' ? $request->grade_custom : $request->grade_select;

            // Handle preferences
            $user->preference = $request->preference ? json_encode($request->preference) : null;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if it exists
                if ($user->photo && file_exists(public_path('upload/user_images/' . $user->photo))) {
                    unlink(public_path('upload/user_images/' . $user->photo));
                }
                $file = $request->file('photo');
                $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/user_images'), $filename);
                $user->photo = $filename;
            }


            // Handle password
            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            return redirect()->route('profile.edit')->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}