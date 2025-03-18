<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class InstructorProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:instructor');
    }

    public function edit()
    {
        $instructor = Auth::guard('instructor')->user();
        return view('instructor.instructor_edit_profile', compact('instructor'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:instructors,email,' . Auth::guard('instructor')->id(),
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:5120|mimes:jpg,png,jpeg', // Ajout de 'jpeg'
            ]);

            $instructor = Auth::guard('instructor')->user();

            // Mise à jour des champs de base
            $instructor->name = $request->name;
            $instructor->email = $request->email;
            $instructor->phone = $request->phone;
            $instructor->address = $request->address;

            // Gestion de l'upload de la photo
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($instructor->photo && Storage::exists('public/instructor_images/' . $instructor->photo)) {
                    Storage::delete('public/instructor_images/' . $instructor->photo);
                }

                $file = $request->file('photo');
                $filename = date('YmdHi') . '_' . $file->getClientOriginalName(); // Ajout d'un underscore pour éviter les conflits
                $file->storeAs('public/instructor_images', $filename);
                $instructor->photo = $filename;
            }

            $instructor->save();

            return redirect()->route('instructor.profile.edit')->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $instructor = Auth::guard('instructor')->user();

        if (!Hash::check($request->current_password, $instructor->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $instructor->password = Hash::make($request->new_password);
        $instructor->save();

        return redirect()->route('instructor.profile.edit')->with('status', 'Password updated successfully!');
    }
}