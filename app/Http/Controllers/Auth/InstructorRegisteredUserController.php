<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth;
use App\Models\Instructor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

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
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $instructor = Instructor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($instructor)); // Envoie l’email de vérification

        return redirect()->route('instructor.verification.notice')->with('status', 'Veuillez vérifier votre email pour compléter l’inscription.');
    }
}