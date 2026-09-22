<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share settings globally to all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $settingsFile = storage_path('app/settings.json');
            $sys_settings = [];
            if (\Illuminate\Support\Facades\File::exists($settingsFile)) {
                $sys_settings = json_decode(\Illuminate\Support\Facades\File::get($settingsFile), true);
            }
            $view->with('sys_settings', $sys_settings);

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
