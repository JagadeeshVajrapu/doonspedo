<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\DriverSettingsMiddleware::class,
            \App\Http\Middleware\LocalizationMiddleware::class,
        ]);

        // Multi-guard guest redirects (admin/branch must not fall through to rider /login)
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('branch') || $request->is('branch/*')) {
                return route('branch.login');
            }

            return route('login.choose');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $redirectExpiredSession = function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login')
                    ->with('error', 'Your session expired. Please login again.');
            }

            if ($request->is('branch') || $request->is('branch/*')) {
                return redirect()->route('branch.login')
                    ->with('error', 'Your session expired. Please login again.');
            }

            if ($request->is('driver') || $request->is('driver/*')) {
                return redirect()->route('driver.login')
                    ->with('error', 'Your session expired. Please login again.');
            }

            return redirect()->route('login')
                ->with('error', 'Your session expired. Please login again.');
        };

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) use ($redirectExpiredSession) {
            return $redirectExpiredSession($request);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) use ($redirectExpiredSession) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            return $redirectExpiredSession($request);
        });
    })->create();
