<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MigrateGuestCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (Auth::check() && session()->has('cart_migrated') === false) {
            $request->session()->put('cart_migrated', true);
            app('App\Http\Controllers\CartController')->migrateCart($request);
        }
        
        return $response;
    }

}
