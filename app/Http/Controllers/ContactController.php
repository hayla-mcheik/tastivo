<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        $locations = Location::all()->map(function($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'image' => $location->image,
                'address' => $location->address,
                'opening_hours' => $location->opening_hours,
                'phone' => $location->phone,
                'email' => $location->email,
                'delivery_available' => $location->delivery_available,
                'delivery_areas' => $location->delivery_areas,
                'is_open' => $location->is_open,
                'created_at' => $location->created_at->format('d/m/Y'),
                'updated_at' => $location->updated_at->format('d/m/Y'),
            ];
        });

        return Inertia::render('Admin/Contact/Index', [
            'locations' => $locations
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Contact/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Changed to file validation
            'address' => ['required', 'string'],
            'opening_hours' => ['required', 'array'],
            'phone' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'delivery_available' => ['boolean'],
            'delivery_areas' => ['nullable', 'string'],
            'is_open' => ['boolean']
        ]);
    
        // Handle file upload
        $imagePath = $request->file('image')->store('images/restaurants', 'public');
    
        // Create location with validated data
        Location::create([
            'name' => $validated['name'],
            'image' => $imagePath,
            'address' => $validated['address'],
            'opening_hours' => json_encode($validated['opening_hours']), // Convert array to JSON
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'delivery_available' => $validated['delivery_available'] ?? false,
            'delivery_areas' => $validated['delivery_areas'] ?? null,
            'is_open' => $validated['is_open'] ?? true
        ]);
    
        return redirect()->route('admin.contact.index')
            ->with('success', 'Location created successfully.');
    }

    public function edit(Location $location)
    {
        return Inertia::render('Admin/Contact/Edit', [
            'location' => $location
        ]);
    }

    public function update(Request $request, Location $location)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'image' => ['required', 'url'],
            'address' => ['required', 'string'],
            'opening_hours' => ['required', 'array'],
            'phone' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'delivery_available' => ['boolean'],
            'delivery_areas' => ['nullable', 'string'],
            'is_open' => ['boolean']
        ])->validate();

        $location->update($request->all());

        return redirect()->route('admin.contact.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('admin.contact.index')
            ->with('success', 'Location deleted successfully.');
    }
}