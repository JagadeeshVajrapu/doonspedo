<?php

namespace App\Providers;

use App\Services\Aadhaar\AadhaarVerificationProvider;
use App\Services\Aadhaar\UnconfiguredAadhaarProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AadhaarVerificationProvider::class, UnconfiguredAadhaarProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share settings globally to all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('sys_settings', load_sys_settings());

            // Share default currency globally
            try {
                $default_currency = \App\Models\Currency::where('is_default', true)->first()
                                 ?? \App\Models\Currency::where('is_active', true)->first();
            } catch (\Exception $e) {
                $default_currency = null;
            }
            $view->with('default_currency', $default_currency);
        });
    }
}
