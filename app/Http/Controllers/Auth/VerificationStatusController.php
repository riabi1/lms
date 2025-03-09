<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationStatusController extends Controller
{
  public function check(Request $request)
  {
    $guards = ['web', 'admin', 'instructor'];
    foreach ($guards as $guard) {
      if ($user = $request->user($guard)) {
        return response()->json(['verified' => $user->hasVerifiedEmail()]);
      }
    }
    return response()->json(['verified' => false]);
  }
}
