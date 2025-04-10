<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionForGuestCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            // Ensure session exists for guests
            if (!$request->hasSession()) {
                $request->session()->regenerate();
            }
            
            // Optionally set a session flag for guest users
            $request->session()->put('is_guest', true);
        }
        
        return $next($request);
    }
}
