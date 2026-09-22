<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Theme
        if (!Session::has('theme')) {
            Session::put('theme', 'dark'); // Default theme
        }

        // Language
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        // Currency (Default to INR)
        if (!Session::has('currency')) {
            Session::put('currency', ['code' => 'INR', 'symbol' => '₹', 'rate' => 1.0]);
        }

        return $next($request);
    }
}
