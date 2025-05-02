<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class SettingController extends Controller
{
    public function index()
    {
        return inertia('Admin/Settings/Index', [
            'settings' => [
                'site_logo' => Setting::get('site_logo'),
                'site_name' => Setting::get('site_name', config('app.name')),
            ]
        ]);
    }

    public function update(Request $request)
    {
        // Add debug logging
        \Log::debug('Settings update request:', $request->all());
        \Log::debug('Has site_logo file:', [$request->hasFile('site_logo')]);
    
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Debug before saving
        \Log::debug('Before saving - site_name:', [$request->site_name]);
    
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('public/settings');
            $url = Storage::url($path);
            \Log::debug('Saving site_logo:', [$url]);
            Setting::set('site_logo', $url);
        }
    
        if ($request->has('site_name')) {
            \Log::debug('Saving site_name:', [$request->site_name]);
            Setting::set('site_name', $request->site_name);
        }
    
        // Debug after saving
        \Log::debug('After saving - all settings:', Setting::all()->toArray());
    
        return back()->with('success', 'Settings updated successfully');
    }
}
