<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $categoryFilter = $request->input('category');
        $suggest = $request->boolean('suggest');
        
        $products = [];
        $suggestions = [];
        $matchedCategory = null;
    
        if ($query) {
            // First check if query matches a category name exactly
            $matchedCategory = Category::where('name', 'like', "%{$query}%")->first();
    
            $productQuery = Product::query()
                ->with(['category', 'additions'])
                ->when($query, function($q) use ($query, $matchedCategory) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('desc', 'like', "%{$query}%")
                      ->when($matchedCategory, function($q) use ($matchedCategory) {
                          $q->orWhere('category_id', $matchedCategory->id);
                      });
                })
                ->when($categoryFilter, function($q) use ($categoryFilter) {
                    $q->where('category_id', $categoryFilter);
                });
    
            // Get suggestions
            if ($suggest && strlen($query) >= 2) {
                $suggestions = Product::query()
                    ->where('name', 'like', "%{$query}%")
                    ->limit(5)
                    ->pluck('name')
                    ->toArray();
    
                // Add category names to suggestions if they match
                $categorySuggestions = Category::query()
                    ->where('name', 'like', "%{$query}%")
                    ->limit(3)
                    ->pluck('name')
                    ->toArray();
    
                $suggestions = array_merge($suggestions, $categorySuggestions);
            }
    
            $products = $productQuery->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => $product->image,
                        'price' => $product->price,
                        'category' => $product->category->name,
                        'category_id' => $product->category->id,
                        'route' => route('menudetails', $product->category->slug),
                        'rating' => $product->rating ?? 0,
                        'review_count' => $product->review_count ?? 0,
                    ];
                });
        }
    
        $categories = Category::all()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ];
        });
    
        return Inertia::render('Search/Index', [
            'query' => $query,
            'products' => $products,
            'suggestions' => $suggestions,
            'categories' => $categories,
            'filters' => [
                'category' => $matchedCategory ? $matchedCategory->id : $categoryFilter,
                'min_price' => $request->input('min_price'),
                'max_price' => $request->input('max_price'),
            ],
            'matchedCategory' => $matchedCategory,
        ]);
    }
}