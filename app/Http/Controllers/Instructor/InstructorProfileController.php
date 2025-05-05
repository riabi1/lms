<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InstructorProfileController extends Controller
{
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
                'photo' => 'nullable|image|max:5120|mimes:jpg,png,jpeg',
                'cv' => 'nullable|file|max:2048|mimes:pdf',
                'bio' => 'nullable|string',
                'experience' => 'nullable|string',
                'specialty' => 'nullable|string|max:255',
                'education' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            $instructor = Auth::guard('instructor')->user();

            $instructor->name = $request->name;
            $instructor->email = $request->email;
            $instructor->phone = $request->phone;
            $instructor->address = $request->address;
            $instructor->bio = $request->bio;
            $instructor->experience = $request->experience;
            $instructor->specialty = $request->specialty;
            $instructor->education = $request->education;
            $instructor->website = $request->website;
            $instructor->location = $request->location;

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if it exists
                if ($instructor->photo && file_exists(public_path('upload/instructor_images/' . $instructor->photo))) {
                    unlink(public_path('upload/instructor_images/' . $instructor->photo));
                }
                $file = $request->file('photo');
                $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/instructor_images'), $filename);
                $instructor->photo = $filename;
            }

            // Handle CV upload
            if ($request->hasFile('cv')) {
                // Delete old CV if it exists
                if ($instructor->cv && file_exists(public_path('upload/instructor_cvs/' . $instructor->cv))) {
                    unlink(public_path('upload/instructor_cvs/' . $instructor->cv));
                }
                $cvFile = $request->file('cv');
                $cvFilename = date('YmdHi') . '_cv_' . $cvFile->getClientOriginalName();
                $cvFile->move(public_path('upload/instructor_cvs'), $cvFilename);
                $instructor->cv = $cvFilename;
            }

            $instructor->save();

            return redirect()->route('instructor.profile.edit')
                ->with('status', 'Profile updated successfully!');
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