<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstructorRegisteredUserController extends Controller
{
    public function create()
    {
        return view('instructor.instructor_register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:instructors'],
            'password' => ['required', 'confirmed', 'min:8'], // Added min:8 for better security
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:2048'], // CV validation: optional, PDF only, max 2MB
        ]);

        // Handle CV file upload if provided
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('upload/instructor_cvs', 'public');
        }

        // Create the instructor record
        $instructor = Instructor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cv' => $cvPath, // Store the CV file path
        ]);

        event(new Registered($instructor)); 
        return redirect()->route('instructor.verification.notice')->with('success', 'Registration successful! Please verify your email.');
    }
}