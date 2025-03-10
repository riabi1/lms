<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function edit()
    {
        $profileData = Auth::guard('web')->user();
        return view('frontend.dashboard.edit_profile', compact('profileData'));
    }

   public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . Auth::guard('web')->id(),
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'photo' => 'nullable|image|max:5120|mimes:jpg,png',
    ]);

    $user = Auth::guard('web')->user();

    if ($request->hasFile('photo')) {
        if ($user->photo && Storage::exists('public/upload/user_images/' . $user->photo)) {
            Storage::delete('public/upload/user_images/' . $user->photo);
        }
        $photo = $request->file('photo');
        $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
        $image = Image::make($photo)->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save(storage_path('app/public/upload/user_images/' . $filename));
        $user->photo = $filename;
    }

    $user->update($request->only('name', 'email', 'phone', 'address'));
    $profileData = $user->fresh();

    return view('frontend.dashboard.edit_profile', compact('profileData'))
        ->with('status', 'Profile updated successfully!');
}

}