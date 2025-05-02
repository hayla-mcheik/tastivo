<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        return Inertia::render('Admin/Category/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Category/Create');  
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'slug' => 'required',
            'image' => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp'],
            'status' => 'nullable|boolean'
        ]);
    
        $fields['status'] = $fields['status'] ?? 0;
    
        if ($request->hasFile('image')) {
            $fields['image'] = $request->file('image')->store('images/categories', 'public');
        }
    
        Category::create($fields);
    
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }
    
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return Inertia::render('Admin/Category/Edit', [
            'category' => $category
        ]);  
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
    
        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'slug' => 'required',
            'image' => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp'],
            'status' => ['nullable', 'boolean']
        ]);
    
        $fields['status'] = $fields['status'] ?? 0;
    
        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            // Store new image
            $fields['image'] = $request->file('image')->store('images/categories', 'public');
        } else {
            // Keep the existing image if no new image is uploaded
            unset($fields['image']); // Remove image from fields if not updated
        }
    
        $category->update($fields);
    
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
    
        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
    
        $category->delete();
    
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}