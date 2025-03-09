<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,admin,instructor');
    }

    public function check(Request $request)
    {
        $user = $request->user('web') ?? $request->user('admin') ?? $request->user('instructor');
        return response()->json(['verified' => $user && $user->hasVerifiedEmail()]);
    }
}