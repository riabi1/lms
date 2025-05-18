<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class ProfileController extends Controller
{
    public function edit()
    {
        $profileData = Auth::guard('web')->user();
        $categories = Category::all();
        return view('User.edit_profile', compact('profileData', 'categories'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . Auth::guard('web')->id(),
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:5120|mimes:jpg,png,jpeg',
                'new_password' => 'nullable|string|min:8|confirmed',
                'preference' => 'nullable|array|max:3',
                'preference.*' => 'exists:categories,id',
                'grade_select' => 'required|string|max:255',
                'grade_custom' => 'nullable|string|max:255|required_if:grade_select,Other',
            ]);

            $user = Auth::guard('web')->user();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->preference = $request->preference ? json_encode($request->preference) : null;
            $user->grade = $request->grade_select === 'Other' ? $request->grade_custom : $request->grade_select;

            if ($request->hasFile('photo')) {
                if ($user->photo && Storage::disk('public')->exists('upload/user_images/' . $user->photo)) {
                    Storage::disk('public')->delete('upload/user_images/' . $user->photo);
                }

                $file = $request->file('photo');
                $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
                $file->storeAs('upload/user_images', $filename, 'public');
                $user->photo = $filename;
            }

            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            $user->save();

            return redirect()->back()->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
} 