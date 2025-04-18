<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function siteSetting()
    {
        $site = SiteSetting::firstOrCreate(
            ['id' => 1],
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'logo.mimes' => 'The logo must be a JPEG, PNG, or JPG file.',
            'logo.max' => 'The logo must not exceed 2MB.',
        ]);

        $site = SiteSetting::findOrFail($siteId);
        $data = [
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'copyright' => $request->copyright,
        ];

        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if it exists and isn't default
                if ($site->logo && $site->logo !== 'images/default-logo.png' && Storage::disk('public')->exists($site->logo)) {
                    Storage::disk('public')->delete($site->logo);
                }

                $image = $request->file('logo');
                $nameGen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
                $savePath = 'logo/' . $nameGen;

                // Store the image
                $image->storeAs('public', $savePath);

                $data['logo'] = $savePath;
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'message' => 'Failed to upload logo: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ])->withInput();
            }
        }

        $site->update($data);

        return redirect()->back()->with([
            'message' => 'Site Settings Updated Successfully',
            'alert-type' => 'success'
        ]);
    }
}