<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{


    public function index(Request $request)
    {
        $cartItems = Auth::check()
            ? Auth::user()->cartItems()->with('product')->get()
            : Cart::with('product')->where('session_id', $request->session()->getId())->get();

        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return Inertia::render('Frontend/Cart', [
            'cartItems' => $cartItems,
            'cartTotal' => $total,
            'itemsCount' => $cartItems->count()
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (Auth::check() && Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Admins cannot add items to cart'], 403);
        }

        $cartData = [
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => $validated['quantity']
        ];

        if (Auth::check()) {
            $cartItem = Auth::user()->cartItems()
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $validated['quantity']);
            } else {
                Auth::user()->cartItems()->create($cartData);
            }
        } else {
            $cartItem = Cart::where('session_id', $request->session()->getId())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $validated['quantity']);
            } else {
                $cartData['session_id'] = $request->session()->getId();
                Cart::create($cartData);
            }
        }

        $cartCount = Auth::check()
            ? Auth::user()->cartItemsCount()
            : Cart::where('session_id', $request->session()->getId())->count();

        return response()->json([
            'message' => 'Product added to cart',
            'cart_count' => $cartCount
        ]);
    }

    public function removeFromCart(Request $request, Cart $cartItem)
    {
        // Verify ownership
        if (Auth::check()) {
            if ($cartItem->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            if ($cartItem->session_id !== $request->session()->getId()) {
                abort(403);
            }
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart');
    }

    public function updateQuantity(Request $request, Cart $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        // Verify ownership (same as removeFromCart)
        // ...

        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'message' => 'Quantity updated',
            'item_total' => $cartItem->price * $cartItem->quantity
        ]);
    }

}
