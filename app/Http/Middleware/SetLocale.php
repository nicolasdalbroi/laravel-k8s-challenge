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
     * Supported locales
     */
    private const SUPPORTED_LOCALES = ['en', 'da', 'de', 'es', 'fr', 'it', 'nl', 'pt', 'sv'];

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

        // For guests, set/update the locale cookie
        if (! auth()->check()) {
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
            $userLocale = auth()->user()->locale;
            if ($this->isValidLocale($userLocale)) {
                return $userLocale;
            }
        }

        // For guests, check the cookie
        if ($request->hasCookie('locale')) {
            $cookieLocale = $request->cookie('locale');
            if ($this->isValidLocale($cookieLocale)) {
                return $cookieLocale;
            }
        }

        // Default to the app's configured locale
        return config('app.locale', 'en');
    }

    /**
     * Check if the locale is supported.
     */
    private function isValidLocale(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true);
    }
}
