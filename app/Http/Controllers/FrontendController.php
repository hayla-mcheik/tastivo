<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontendController extends Controller
{
    public function home() 
    {
       $sliders = Slider::all();
       $categories = Category::all();
       $products = Product::all();
       return Inertia::render('Home' , [
        'sliders' => $sliders,
        'products' => $products,
        'categories' => $categories

       ]);
    }

    public function menudetails($slug)
    {
        $category = Category::with('products')->where('slug', $slug)->firstOrFail();
   
        $categories = Category::all();
        $products = Product::where('category_id' , $category->id)->get();
        return Inertia::render('Menu', [  // Changed from 'Menu' to 'MenuDetails'
            'category' => $category,
            'categories' => $categories,
            'products' => $products
        ]);
    }
    
    public function cart()
    {
return Inertia::render('Cart');
    }
}
