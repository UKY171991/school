<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Storage;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $settings = GeneralSetting::first();
        return view('admin.general_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:255',
            'school_phone' => 'nullable|string|max:20',
            'school_email' => 'nullable|email|max:255',
            'footer_text' => 'nullable|string|max:255',
            'currency_symbol' => 'nullable|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,svg|max:1024',
        ]);

        $settings = GeneralSetting::firstOrFail();

        $data = $request->only([
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'footer_text',
            'currency_symbol',
        ]);

        if ($request->hasFile('logo')) {
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $data['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        $settings->update($data);

        return response()->json(['success' => 'Settings updated successfully', 'logo_url' => $data['logo'] ?? $settings->logo, 'favicon_url' => $data['favicon'] ?? $settings->favicon]);
    }
}
