<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:admin');
  }

  public function edit()
  {
    $admin = Auth::guard('admin')->user();
    return view('admin.admin_edit_profile', compact('admin'));
  }

  public function update(Request $request)
  {
    try {
      $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:admins,email,' . Auth::guard('admin')->id(),
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'photo' => 'nullable|image|max:5120|mimes:jpg,png',
      ]);

      $admin = Auth::guard('admin')->user();

      // Update basic fields
      $admin->name = $request->name;
      $admin->email = $request->email;
      $admin->phone = $request->phone;
      $admin->address = $request->address;

      // Handle photo upload
      if ($request->hasFile('photo')) {
        // Delete old photo if exists
        if ($admin->photo && Storage::exists('public/upload/admin_images/' . $admin->photo)) {
          Storage::delete('public/upload/admin_images/' . $admin->photo);
        }

        $file = $request->file('photo');
        $filename = date('YmdHi') . $file->getClientOriginalName();
        // Use Storage instead of move() for better Laravel integration
        $file->storeAs('public/upload/admin_images', $filename);
        $admin->photo = $filename;
      }

      $admin->save();

      return redirect()->route('admin.profile.edit')->with('status', 'Profile updated successfully!');
    } catch (\Exception $e) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Failed to update profile: ' . $e->getMessage());
    }
  }

  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/admin/login');
  }
}
