<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $locale = $request->header('Accept-Language');

        if (! $locale) {
            $locale = config('app.locale');
        }

        // You might want to strip quality weights e.g. "en-US,en;q=0.9" -> "en-US"
        // For simplicity, taking the first 2 chars or checking existence in array could be good.
        // But per user request: "Reads Accept-Language header, Falls back to config('app.locale'), Calls app()->setLocale()"

        // Simple logic as requested:
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
