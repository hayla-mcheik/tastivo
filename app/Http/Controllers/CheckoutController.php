<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function checkout()
    {
        return Inertia::render('Checkout');
    }
}
