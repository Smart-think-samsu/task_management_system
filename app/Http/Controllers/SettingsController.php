<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('settings'), only: ['index']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $smsSetting = Setting::where('type', 1)->first();
        return view('settings.index', compact('smsSetting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $setting = Setting::first();
        return view('settings.create', compact('setting'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
//        $request->validate([
//            'title' => 'required|string|max:255',
//            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//            'fav_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
//        ]);

        // Define the custom storage path


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Find the settings record by id
        $settings = Setting::first();

        // Define the custom storage path
        $storagePath = 'assets/dist/img/site_image';

        // Ensure the directory exists
        if (!file_exists(public_path($storagePath))) {
            mkdir(public_path($storagePath), 0755, true);
        }

        // Handle the file uploads
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path($storagePath), $logoName);
            $logoPath = $storagePath . '/' . $logoName;

            // Delete the old logo if it exists
            if ($settings->logo && file_exists(public_path($settings->logo))) {
                unlink(public_path($settings->logo));
            }
        } else {
            $logoPath = $settings->logo;
        }

        if ($request->hasFile('fav_icon')) {
            $favIconFile = $request->file('fav_icon');
            $favIconName = 'fav_icon_' . time() . '.' . $favIconFile->getClientOriginalExtension();
            $favIconFile->move(public_path($storagePath), $favIconName);
            $favIconPath = $storagePath . '/' . $favIconName;

            // Delete the old fav_icon if it exists
            if ($settings->fav_icon && file_exists(public_path($settings->fav_icon))) {
                unlink(public_path($settings->fav_icon));
            }
        } else {
            $favIconPath = $settings->fav_icon;
        }

        // Update the settings data
        $settings->title = $request->input('title');
        $settings->logo = $logoPath;
        $settings->fav_icon = $favIconPath;
        $settings->save();

        // Return a response
        return redirect()->back()->with('success', 'Settings have been updated successfully.');

    }

    public function smsSettingUpdate(Request $request)
    {
        $request->validate([
            'status' => 'required|in:ON,OFF',
        ]);

        $setting = Setting::where('type', 1)->first();

        $setting->status = $request->status;
        $setting->save();

        return response()->json(['message' => 'SMS Setting updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
