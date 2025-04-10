<?php

namespace App\Http\Controllers;

use App\Models\Addition;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontendController extends Controller
{
    public function home() 
    {
       $sliders = Slider::all();
       $categories = Category::with('translations')->get();
       $products = Product::all();
       return Inertia::render('Home' , [
        'sliders' => $sliders,
        'products' => $products,
        'categories' => $categories

       ]);
    }

    public function menudetails($slug)
    {
        $category = Category::with(['translations', 'products.additions'])
            ->where('slug', $slug)
            ->firstOrFail();
        
            $categories = Category::with('translations')->get(); 
        
        $products = Product::with('additions')
            ->where('category_id', $category->id)
            ->get();
        
        $additions = Addition::whereHas('products', function($query) use ($category) {
            $query->where('category_id', $category->id);
        })->get();
        
        return Inertia::render('Menu', [
            'initialCategory' => $category->id,
            'category' => $category,
            'categories' => $categories,
            'products' => $products,
            'additions' => $additions,
            'allProducts' => Product::with('additions')->get()
        ]);
    }

    public function contact()
    {
        $locations = Location::all()->map(function($location) {
            return [
                'name' => $location->name,
                'image' => $location->image ? asset('storage/' . $location->image) : 'https://via.placeholder.com/800x400',
                'address' => $location->address,
                'hours' => json_decode($location->opening_hours, true) ?? [],
                'phone' => $location->phone,
                'email' => $location->email,
                'delivery' => (bool)$location->delivery_available,
                'deliveryAreas' => $location->delivery_areas,
                'isOpen' => (bool)$location->is_open,
            ];
        });

        return Inertia::render('Info', [
            'locations' => $locations
        ]);
    }
    public function cart()
    {
return Inertia::render('Cart');
    }
}
