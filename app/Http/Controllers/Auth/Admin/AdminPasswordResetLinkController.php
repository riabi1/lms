<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AdminPasswordResetLinkController extends Controller
{
  public function create()
  {
    return view('admin.admin-forgot-password');
  }

  public function store(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $status = Password::broker('admins')->sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? back()->with('status', __($status))
      : back()->withErrors(['email' => __($status)]);
  }
}
