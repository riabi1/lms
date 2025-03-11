<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $profileData = Auth::guard('web')->user();
        return view('frontend.dashboard.edit_profile', compact('profileData'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . Auth::guard('web')->id(),
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:5120|mimes:jpg,png',
            ]);

            $user = Auth::guard('web')->user();

            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo && Storage::exists('public/upload/user_images/' . $user->photo)) {
                    Storage::delete('public/upload/user_images/' . $user->photo);
                }

                $photo = $request->file('photo');
                $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = storage_path('app/public/upload/user_images/' . $filename);

                // Resize image using GD library
                $this->resizeImage($photo->getRealPath(), $path, 200, 200);

                $user->photo = $filename;
            }

            // Update user details
            $user->update($request->only('name', 'email', 'phone', 'address'));
            $profileData = $user->fresh();

            return view('frontend.dashboard.edit_profile', compact('profileData'))
                ->with('status', 'Profile updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    private function resizeImage($sourcePath, $destinationPath, $width, $height)
    {
        // Get original image dimensions
        list($originalWidth, $originalHeight, $type) = getimagesize($sourcePath);

        // Calculate aspect ratio
        $aspectRatio = $originalWidth / $originalHeight;
        $newWidth = $width;
        $newHeight = $height;

        if ($aspectRatio > 1) {
            $newHeight = $width / $aspectRatio;
        } else {
            $newWidth = $height * $aspectRatio;
        }

        // Create a new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Create source image based on file type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                // Preserve PNG transparency
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        // Resize the image
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $originalWidth,
            $originalHeight
        );

        // Save the image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $destinationPath, 90); // 90 is quality
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $destinationPath, 9); // 9 is compression level
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }
}