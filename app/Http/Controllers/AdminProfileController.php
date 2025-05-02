<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    public function index ()
    {
        return Inertia::render('Admin/Profile/Index',[
            'user' => auth()->user()->only('id', 'name', 'email', 'role')
        ]);
    }
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['sometimes', 'required', 'current_password'],
            'password' => ['sometimes', 'required', 'confirmed', 'min:8'],
        ]);
    
        // Update basic info
        $user->update($validated);
    
        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }
    
        return back()->with('success', 'Profile updated successfully');
    }
}
