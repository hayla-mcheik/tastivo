<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addition;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // Handle both authenticated and guest users
        if (Auth::check()) {
            $cartItems = Auth::user()->cartItems()->with('product')->get();
        } else {
            // Ensure we have a session ID
            $sessionId = $request->session()->getId();
            $cartItems = Cart::with('product')
                ->where('session_id', $sessionId)
                ->get();
                
            // Create new session if needed
            if (!$request->hasSession()) {
                $request->session()->regenerate();
            }
        }
        
        return Inertia::render('Cart', [
            'cartItems' => $cartItems,
            'cartTotal' => $this->calculateTotal($cartItems),
            'itemsCount' => $cartItems->sum('quantity'),
            'isGuest' => !Auth::check()
        ]);
    }
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'additions' => 'nullable|array',
            'additions.*' => 'exists:additions,id'
        ]);
    
        $product = Product::with('additions')->findOrFail($validated['product_id']);
    
        // Prepare additions data
        $additionsData = [];
        if (!empty($validated['additions'])) {
            $additions = Addition::whereIn('id', $validated['additions'])->get();
            $additionsData = $additions->map(function($addition) {
                return [
                    'id' => $addition->id,
                    'name' => $addition->name,
                    'price' => $addition->price
                ];
            })->toArray();
        }
    
        // Check if product already in cart
        $cartItem = Cart::where([
            'user_id' => auth()->id() ?? null,
            'session_id' => auth()->guest() ? session()->getId() : null,
            'product_id' => $product->id
        ])->first();
    
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $validated['quantity'],
                'additions' => $additionsData
            ]);
        } else {
            $cartItem = Cart::create([
                'user_id' => auth()->id() ?? null,
                'session_id' => auth()->guest() ? session()->getId() : null,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'additions' => $additionsData
            ]);
        }
    
        // Get all cart items for the current user/session
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id() ?? null)
            ->where('session_id', auth()->guest() ? session()->getId() : null)
            ->get();
    
        return response()->json([
            'items' => $cartItems,
            'count' => $cartItems->sum('quantity'),
            'total' => $this->calculateTotal($cartItems),
            'isGuest' => auth()->guest()
        ]);
    }
    public function removeFromCart(Cart $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return $this->index(request());
    }

    public function updateQuantity(Request $request, Cart $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
            'additions' => 'nullable|array',
            'additions.*' => 'exists:additions,id'
        ]);

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'additions' => $validated['additions'] ?? $cartItem->additions
        ]);

        return $this->index($request);
    }

    // Guest cart methods
    public function guestIndex(Request $request)
    {
        $cartItems = Cart::with(['product', 'product.additions'])
            ->where('session_id', $request->session()->getId())
            ->get();

        return response()->json([
            'items' => $cartItems,
            'total' => $this->calculateTotal($cartItems),
            'count' => $cartItems->sum('quantity')
        ]);
    }

    public function guestAddToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'additions' => 'nullable|array',
            'additions.*' => 'exists:additions,id'
        ]);
    
        $product = Product::with('additions')->findOrFail($validated['product_id']);
    
        // Prepare additions data with names and prices
        $additionsData = [];
        if (!empty($validated['additions'])) {
            $additions = Addition::whereIn('id', $validated['additions'])->get();
            $additionsData = $additions->map(function($addition) {
                return [
                    'id' => $addition->id,
                    'name' => $addition->name,
                    'price' => $addition->price
                ];
            })->toArray();
        }
    
        $cartItem = Cart::where('session_id', $request->session()->getId())
            ->where('product_id', $product->id)
            ->first();
    
        if ($cartItem) {
            $cartItem->increment('quantity', $validated['quantity']);
            $cartItem->update(['additions' => $additionsData]);
        } else {
            Cart::create([
                'session_id' => $request->session()->getId(),
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'additions' => $additionsData
            ]);
        }
    
        return $this->guestIndex($request);
    }
    public function guestRemoveFromCart(Request $request, Cart $cartItem)
    {
        if ($cartItem->session_id !== $request->session()->getId()) {
            abort(403);
        }

        $cartItem->delete();

        return $this->guestIndex($request);
    }

// In CartController.php


public function guestClearCart(Request $request)
{
    try {
        $sessionId = $request->session()->getId();
        $deleted = Cart::where('session_id', $sessionId)->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0 
                ? 'Cart cleared successfully!' 
                : 'Your cart was already empty',
            'items' => [],
            'count' => 0,
            'total' => 0
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to clear cart',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function guestUpdateQuantity(Request $request, Cart $cartItem)
{
    if ($cartItem->session_id !== $request->session()->getId()) {
        abort(403);
    }

    $validated = $request->validate([
        'quantity' => 'required|integer|min:1|max:10',
        'additions' => 'nullable|array',
        'additions.*' => 'exists:additions,id'
    ]);

    // Prepare additions data if provided
    $additionsData = $cartItem->additions; // Keep existing if not provided
    if (isset($validated['additions'])) {
        $additions = Addition::whereIn('id', $validated['additions'])->get();
        $additionsData = $additions->map(function($addition) {
            return [
                'id' => $addition->id,
                'name' => $addition->name,
                'price' => $addition->price
            ];
        })->toArray();
    }

    $cartItem->update([
        'quantity' => $validated['quantity'],
        'additions' => $additionsData
    ]);

    return $this->guestIndex($request);
}
    protected function calculateTotal($items)
    {
        return $items->reduce(function ($total, $item) {
            $additionsTotal = 0;
            
            // Handle both array of IDs and array of addition objects
            if (!empty($item->additions)) {
                // If additions is an array of IDs
                if (is_array($item->additions) && count($item->additions) > 0 && is_numeric($item->additions[0])) {
                    $additions = Addition::whereIn('id', $item->additions)->get();
                    $additionsTotal = $additions->sum('price') * $item->quantity;
                } 
                // If additions is an array of objects with price
                else if (is_array($item->additions) && count($item->additions) > 0 && isset($item->additions[0]['price'])) {
                    $additionsTotal = array_reduce($item->additions, function($sum, $addition) {
                        return $sum + $addition['price'];
                    }, 0) * $item->quantity;
                }
            }
            
            return $total + ($item->price * $item->quantity) + $additionsTotal;
        }, 0);
    }
    public function additions()
    {
        return Addition::all(); // or you can return specific fields
    }
}