<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
        ->when($request->search , function($query) use ($request){
            $query->where('name', 'like', '%'.$request->search.'%')
            ->orWhereHas('category', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
  })
        ->latest()->paginate(6);
        return Inertia::render('Admin/Products/Index',[
            'products' => $products,
            'searchTerm' => $request->search,
        ]);
    }
    public function create()
    {
        $categories = Category::all();
        return Inertia::render('Admin/Products/Create',[
            'categories' => $categories
        ]);  
    }
    public function store(Request $request)
    {
        $fields = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'max:255'],
            'desc' => ['required'],
            'price' => ['nullable', 'numeric'], // Fixed
            'quantity' => ['nullable', 'integer'], // Fixed
            'rate' => ['nullable', 'integer'], // Fixed
            'image' => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp'],
            'status' => 'nullable|boolean'
        ]);
    
        $fields['status'] = $fields['status'] ?? 0;
    
        if ($request->hasFile('image')) {
            $fields['image'] = $request->file('image')->store('images/products', 'public'); // Fixed
        }
    
        Product::create($fields);
    
        return redirect()->route('products.index')->with('status', 'Product created successfully.');
    }
    
    public function edit($id)
    {
        $categories = Category::all();
        $product = Product::find($id);
        return Inertia::render('Admin/Products/Edit',[
            'product' => $product,
            'categories' => $categories
        ]);  
    }
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        $fields = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'], // Added category_id validation
            'name' => ['required', 'max:255'],
            'desc' => ['required'],
            'price' => ['nullable', 'numeric'], // Fixed
            'quantity' => ['nullable', 'integer'], // Fixed
            'rate' => ['nullable', 'integer'], // Fixed
            'image' => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp'],
            'status' => ['nullable', 'boolean']
        ]);
    
        $fields['status'] = $fields['status'] ?? 0;
    
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // Store new image
            $fields['image'] = $request->file('image')->store('images/products', 'public');
        }
    
        $product->update($fields); // Update the product
    
        return redirect()->route('products.index')->with('status', 'Product updated successfully.');
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
    
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
    
        $product->delete();
    
        return redirect()->route('products.index')->with('status', 'Product deleted successfully.');
    }
    
}
