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
        return Inertia::render('Admin/Category/Index',[
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
            $fields['image'] = $request->file('image')->store('images/categories', 'public'); // Fixed
        }
    
        Category::create($fields);
    
        return redirect()->route('categories.index')->with('status', 'Category created successfully.');
    }
    
    public function edit($id)
    {
        $categories = Category::find($id);
        return Inertia::render('Admin/Category/Edit',[
            'categories' => $categories
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
    
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            // Store new image
            $fields['image'] = $request->file('image')->store('images/categories', 'public');
        }
    
        $category->update($fields); // Update the category
    
        return redirect()->route('categories.index')->with('status', 'Category updated successfully.');
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
    
        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
    
        $category->delete();
    
        return redirect()->route('categories.index')->with('status', 'Category deleted successfully.');
    }
}
