<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function __construct()
    {
        if (Auth::id() !== 1) {
            // abort(403, 'Not allowed');
            if(Auth::check())
            {
                return redirect('/');
            }
            
            return redirect('/');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurants = Restaurant::all();
        return $restaurants;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'create form';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name'       => 'required|string|unique:users,name',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:8|confirmed',
            
            'name'            => 'required|string|unique:restaurants,name',
            'phone'           => 'required|string|unique:restaurants,phone',
            'location'        => 'required|string|unique:restaurants,location',
            'location_link'   => 'nullable|url',
        ]);

        $user = new User();
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $restaurant = new Restaurant();
        $restaurant->user_id = $user->id;
        $restaurant->name = $request->name;
        $restaurant->slug =  Str::slug($request->name);
        $restaurant->phone = $request->phone;
        $restaurant->location = $request->location;
        $restaurant->location_link = $request->location_link;
        $restaurant->save();

        return redirect(route('restaurant.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $restaurant = Restaurant::find($id);
        if($restaurant)
        {
            return 'restaurant view page';
        }

        return redirect(route('restaurant.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $restaurant = Restaurant::find($id);
        if(!$restaurant)
        {
            return redirect(route('restaurant.index'));
        }

        return 'restaurant edit page';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);
        if(!$restaurant)
        {
            return redirect(route('restaurant.index'));
        }

        $user = User::find($restaurant->user_id);
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        if($user->password && $user->password_confirmation)
        {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $restaurant->user_id = $user->id;
        $restaurant->name = $request->name;
        $restaurant->slug =  Str::slug($request->name);
        $restaurant->phone = $request->phone;
        $restaurant->location = $request->location;
        $restaurant->location_link = $request->location_link;
        $restaurant->save();

        return redirect(route('restaurant.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $restaurant = Restaurant::find($id);
        if(!$restaurant)
        {
            return redirect(route('restaurant.index'));
        }

        foreach($restaurant->categories as $category)
        {
            foreach ($category->products as $product) 
            {
                $product->delete();
            }
            $category->delete();
        }

        return redirect(route('restaurant.index'));
    }
}
