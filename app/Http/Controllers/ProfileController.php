<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
  public function edit()
  {
    $profileData = Auth::guard('web')->user();
    return view('frontend.dashboard.edit_profile', compact('profileData'));
  }

  public function update(Request $request)
  {
    try {
      $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . Auth::guard('web')->id(),
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'photo' => 'nullable|image|max:5120|mimes:jpg,png',
      ]);

      $user = Auth::guard('web')->user();

      // Update basic fields
      $user->name = $request->name;
      $user->email = $request->email;
      $user->phone = $request->phone;
      $user->address = $request->address;

      // Handle photo upload
      if ($request->hasFile('photo')) {
        // Delete old photo if exists
        if ($user->photo && file_exists(public_path('storage/upload/user_images/' . $user->photo))) {
          unlink(public_path('storage/upload/user_images/' . $user->photo));
        }

        $file = $request->file('photo');
        $filename = date('YmdHi') . $file->getClientOriginalName();
        $file->move(public_path('storage/upload/user_images'), $filename);
        $user->photo = $filename;
      }

      $user->save();

      return redirect()->back()->with('status', 'Profile updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Failed to update profile: ' . $e->getMessage());
    }
  }
}
