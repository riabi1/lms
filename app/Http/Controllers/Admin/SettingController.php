<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Intervention\Image\Facades\Image;

class SettingController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function siteSetting()
    {
        $site = SiteSetting::firstOrCreate(
            ['id' => 1], // Assuming single-record settings table
            [
                'phone' => 'N/A',
                'email' => 'example@example.com',
                'address' => 'N/A',
                'copyright' => '© ' . date('Y') . ' LMS',
                'logo' => null,
            ]
        );
        return view('admin.site.site_update', compact('site'));
    }

    /**
     * Update the site settings.
     */
    public function updateSite(Request $request)
    {
        $siteId = $request->id;

        $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string|max:255',
          
            'copyright' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $site = SiteSetting::findOrFail($siteId);
        $data = [
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
           
            'copyright' => $request->copyright,
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists and is not the default
            if ($site->logo && file_exists(public_path($site->logo)) && $site->logo !== 'images/default-logo.png') {
                unlink(public_path($site->logo));
            }

            $image = $request->file('logo');
            $nameGen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $savePath = 'upload/logo/' . $nameGen;
            Image::make($image)->resize(140, 41)->save(public_path($savePath));
            $data['logo'] = $savePath;
        }

        $site->update($data);

        return redirect()->back()->with([
            'message' => 'Site Settings Updated Successfully',
            'alert-type' => 'success'
        ]);
    }
}