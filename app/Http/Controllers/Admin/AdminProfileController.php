<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
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
                'photo' => 'nullable|image|max:5120|mimes:jpeg,png,jpg',
                'cv' => 'nullable|file|max:2048|mimes:pdf',
                'new_password' => 'nullable|string|min:8|confirmed',
            ]);

            $admin = Auth::guard('admin')->user();

            // Update basic information
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $admin->address = $request->address;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if it exists
                if ($admin->photo && file_exists(public_path('upload/admin_images/' . $admin->photo))) {
                    unlink(public_path('upload/admin_images/' . $admin->photo));
                }
                $file = $request->file('photo');
                $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/admin_images'), $filename);
                $admin->photo = $filename;
            }

            // Handle CV upload
            if ($request->hasFile('cv')) {
                // Delete old CV if it exists
                if ($admin->cv && file_exists(public_path('upload/admin_cvs/' . $admin->cv))) {
                    unlink(public_path('upload/admin_cvs/' . $admin->cv));
                }
                $cvFile = $request->file('cv');
                $cvFilename = date('YmdHi') . '_cv_' . $cvFile->getClientOriginalName();
                $cvFile->move(public_path('upload/admin_cvs'), $cvFilename);
                $admin->cv = $cvFilename;
            }

            // Handle password
            if ($request->filled('new_password')) {
                $admin->password = Hash::make($request->new_password);
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