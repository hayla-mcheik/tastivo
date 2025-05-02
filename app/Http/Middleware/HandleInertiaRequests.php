<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Dynamically determine the root template based on the route prefix.
     */
    public function rootView(Request $request): string
    {
        return str_starts_with($request->path(), 'admin') ? 'admin' : 'app';
    }

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        
        return array_merge(parent::share($request), [
            'locale' => 'en',
            'localeDirection' => 'ltr',
            'auth' => [
                'user' => fn () => $request->user()
                    ? $request->user()->only('id', 'name','email', 'role')
                    : null,
            ],
            'settings' => [
                'site_logo' => \App\Models\Setting::get('site_logo'),
                'site_name' => \App\Models\Setting::get('site_name', config('app.name')),
            ],
        ]);
    }
}
