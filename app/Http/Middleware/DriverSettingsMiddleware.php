<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\DriverRegistration;
use App\Models\Language;
use App\Models\Currency;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class DriverSettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('driver_id')) {
            $driver = DriverRegistration::find(session('driver_id'));
            
            if ($driver) {
                // Set Locale
                $locale = $driver->locale ?: config('app.locale');
                App::setLocale($locale);
                
                // Set Direction (LTR/RTL)
                $language = Language::where('code', $locale)->first();
                $direction = $language ? $language->direction : 'ltr';
                View::share('dir', $direction);
                
                // Set Theme
                View::share('theme', $driver->theme ?: 'light');
                
                // Set Currency
                $currencyCode = $driver->currency_code ?: 'INR';
                $currency = Currency::where('code', $currencyCode)->first();
                View::share('driverCurrency', $currency);
            }
        } else {
            View::share('dir', 'ltr');
            View::share('theme', 'light');
        }

        return $next($request);
    }
}
