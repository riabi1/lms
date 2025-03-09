<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstructorRegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.instructor-register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:instructors'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $instructor = Instructor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        auth()->guard('instructor')->login($instructor);
        return redirect()->route('instructor.verification.notice');
    }
}