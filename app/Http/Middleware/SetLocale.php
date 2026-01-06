<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);
        
        App::setLocale($locale);
        
        $response = $next($request);
        
        // For guests, set a long-lived cookie to remember the locale
        if (! auth()->check() && $request->hasCookie('locale') === false) {
            Cookie::queue('locale', $locale, 525600); // 1 year in minutes
        }
        
        return $response;
    }
    
    /**
     * Get the locale from the request.
     */
    private function getLocale(Request $request): string
    {
        // For authenticated users, use their saved locale
        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }
        
        // For guests, check the cookie
        if ($request->hasCookie('locale')) {
            return $request->cookie('locale');
        }
        
        // Default to the app's configured locale
        return config('app.locale', 'en');
    }
}
