<?php

namespace App\Http\Controllers\Auth\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\InstructorLoginRequest;
use Illuminate\Http\Request;

class InstructorAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.instructor-login');
    }

    public function store(InstructorLoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        return $request->user('instructor')->hasVerifiedEmail()
            ? redirect()->route('instructor.dashboard')
            : redirect()->route('instructor.verification.notice');
    }

    public function destroy(Request $request)
    {
        auth()->guard('instructor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}